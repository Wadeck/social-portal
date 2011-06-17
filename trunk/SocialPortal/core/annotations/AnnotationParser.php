<?php

namespace core\annotations;

use Doctrine\Common\Annotations;

use Doctrine\Common\Annotations\Lexer;

use core\ClassLoader;

class AnnotationParser{
	/**
	 * Some common tags that are stripped prior to parsing in order to reduce parsing overhead.
	 *
	 * @var array
	 */
	private static $strippedTags = array(
        "{@internal", "{@inheritdoc", "{@link", "{@param", "{@return", "{@doc", "{@see", 
	);

	/**
	 * The lexer.
	 *
	 * @var Doctrine\Common\Annotations\Lexer
	 */
	private $lexer;

	/**
	 * Flag to control if the current annotation is nested or not.
	 *
	 * @var boolean
	 */
	protected $isNestedAnnotation = false;

	/**
	 * Default namespace for annotations.
	 *
	 * @var string
	 */
	private $defaultAnnotationNamespace = '';

	/** @var ClassLoader */
	private $classLoader;

	/**
	 * Constructs a new AnnotationParser.
	 */
	public function __construct(ClassLoader $classLoader, $defaultNamespace = '', Lexer  $lexer = null){
		$this->lexer = $lexer ?: new Lexer;
		$this->classLoader = $classLoader;
		$this->defaultAnnotationNamespace = $defaultNamespace;
	}

	/**
	 * Gets the lexer used by this parser.
	 *
	 * @return Lexer The lexer.
	 */
	public function getLexer(){
		return $this->lexer;
	}

	/**
	 * Parses the given docblock string for annotations.
	 *
	 * @param string $docBlockString The docblock string to parse.
	 * @return array Array of annotations. If no annotations are found, an empty array is returned.
	 */
	public function parse($docBlockString) {
		// Strip out some known inline tags.
		$input = str_replace(self::$strippedTags, '', $docBlockString);

		// Cut of the beginning of the input until the first '@'.
		$input = substr($input, strpos($input, '@'));

		$this->lexer->reset();
		$this->lexer->setInput(trim($input, '* /'));
		$this->lexer->moveNext();

		if ($this->lexer->isNextToken(Lexer::T_AT)) {
			return $this->parseAnnotations();
		}

		return array();
	}

	/**
	 * Attempts to match the given token with the current lookahead token.
	 * If they match, updates the lookahead token; otherwise raises a syntax error.
	 *
	 * @param int Token type.
	 * @return bool True if tokens match; false otherwise.
	 */
	private function match($token){
		if ( ! ($this->lexer->lookahead['type'] === $token)) {
			$this->syntaxError($this->lexer->getLiteral($token));
		}
		$this->lexer->moveNext();
	}

	/**
	 * Generates a new syntax error.
	 *
	 * @param string $expected Expected string.
	 * @param array $token Optional token.
	 * @throws AnnotationException
	 */
	private function syntaxError($expected, $token = null){
		if ($token === null) {
			$token = $this->lexer->lookahead;
		}

		$message =  "Expected {$expected}, got ";

		if ($this->lexer->lookahead === null) {
			$message .= 'end of string';
		} else {
			$message .= "'{$token['value']}' at position {$token['position']}";
		}

		$message .= '.';

		throw Annotations\AnnotationException::syntaxError($message);
	}

	/**
	 * Annotations ::= Annotation {[ "*" ]* [Annotation]}*
	 *
	 * @return array
	 */
	private function parseAnnotations(){
		$this->isNestedAnnotation = false;

		$annotations = array();
		$annot = $this->parseAnnotation();

		if ($annot !== false) {
			$annotations[get_class($annot)] = $annot;
			$this->lexer->skipUntil(Lexer::T_AT);
		}

		while ($this->lexer->lookahead !== null && $this->lexer->isNextToken(Lexer::T_AT)) {
			$this->isNestedAnnotation = false;
			$annot = $this->parseAnnotation();

			if ($annot !== false) {
				//TODO here to change the way they are indexed
				$annotations[$this->getClassSimpleName($annot)] = $annot;
				$this->lexer->skipUntil(Lexer::T_AT);
			}
		}

		return $annotations;
	}
	
	private function getClassSimpleName($classInstance){
		$class = explode('\\', get_class($classInstance));
    	return $class[count($class) - 1];
	}

	/**
	 * Annotation     ::= "@" AnnotationName ["(" [Values] ")"]
	 * AnnotationName ::= QualifiedName | SimpleName | AliasedName
	 * QualifiedName  ::= NameSpacePart "\" {NameSpacePart "\"}* SimpleName
	 * AliasedName    ::= Alias ":" SimpleName
	 * NameSpacePart  ::= identifier
	 * SimpleName     ::= identifier
	 * Alias          ::= identifier
	 *
	 * @return mixed False if it is not a valid annotation.
	 */
	private function parseAnnotation(){
		$values = array();
		$nameParts = array();

		$this->match(Lexer::T_AT);
		if ($this->isNestedAnnotation === false) {
			if ($this->lexer->lookahead['type'] !== Lexer::T_IDENTIFIER) {
				return false;
			}
			$this->lexer->moveNext();
		} else {
			$this->match(Lexer::T_IDENTIFIER);
		}
		$nameParts[] = $this->lexer->token['value'];

		while ($this->lexer->isNextToken(Lexer::T_NAMESPACE_SEPARATOR)) {
			$this->match(Lexer::T_NAMESPACE_SEPARATOR);
			$this->match(Lexer::T_IDENTIFIER);
			$nameParts[] = $this->lexer->token['value'];
		}

		// change here the comment and add alias to enable them, not necessary for the moment
		// Effectively pick the name of the class (append default NS if none, grab from NS alias, etc)
//		if (strpos($nameParts[0], ':')) {
//			list ($alias, $nameParts[0]) = explode(':', $nameParts[0]);
//
//			// If the namespace alias doesnt exist, skip until next annotation
//			if ( ! isset($this->namespaceAliases[$alias])) {
//				$this->lexer->skipUntil(Lexer::T_AT);
//				return false;
//			}
//
//			$name = $this->namespaceAliases[$alias] . implode('\\', $nameParts);
//		} else
		if (count($nameParts) == 1) {
			$name = $this->defaultAnnotationNamespace . $nameParts[0];
		} else {
			$name = implode('\\', $nameParts);
		}

		// Does the annotation class exist?
		
		if( !class_exists($name) && !$this->classLoader->loadClass($name) ){
			$this->lexer->skipUntil(Lexer::T_AT);
			return false;
		}

		// Next will be nested
		$this->isNestedAnnotation = true;

		if ($this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
			$this->match(Lexer::T_OPEN_PARENTHESIS);

			if ( ! $this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
				$values = $this->parseValues();
			}

			$this->match(Lexer::T_CLOSE_PARENTHESIS);
		}

		return $this->newAnnotation($name, $values);
	}

	/**
	 * Values ::= Array | Value {"," Value}*
	 *
	 * @return array
	 */
	private function parseValues(){
		$values = array();

		// Handle the case of a single array as value, i.e. @Foo({....})
		if ($this->lexer->isNextToken(Lexer::T_OPEN_CURLY_BRACES)) {
			$values['value'] = $this->parseValue();
			return $values;
		}

		$values[] = $this->parseValue();

		while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
			$this->match(Lexer::T_COMMA);
			$value = $this->parseValue();

			if ( ! is_array($value)) {
				$this->syntaxError('Value', $value);
			}

			$values[] = $value;
		}

		foreach ($values as $k => $value) {
			if (is_array($value) && is_string(key($value))) {
				$key = key($value);
				$values[$key] = $value[$key];
			} else {
				$values['value'] = $value;
			}

			unset($values[$k]);
		}

		return $values;
	}

	/**
	 * Value ::= PlainValue | FieldAssignment
	 *
	 * @return mixed
	 */
	private function parseValue() {
		$peek = $this->lexer->glimpse();

		if ($peek['value'] === '=') {
			return $this->parseFieldAssignment();
		}

		return $this->parsePlainValue();
	}

	/**
	 * PlainValue ::= integer | string | float | boolean | Array | Annotation
	 *
	 * @return mixed
	 */
	private function parsePlainValue(){
		if ($this->lexer->isNextToken(Lexer::T_OPEN_CURLY_BRACES)) {
			return $this->parseArrayx();
		}

		if ($this->lexer->isNextToken(Lexer::T_AT)) {
			return $this->parseAnnotation();
		}

		switch ($this->lexer->lookahead['type']) {
			case Lexer::T_IDENTIFIER:
				$this->match(Lexer::T_IDENTIFIER);
				return $this->lexer->token['value'];
			case Lexer::T_STRING: 
				$this->match(Lexer::T_STRING);
				return $this->lexer->token['value'];

			case Lexer::T_INTEGER:
				$this->match(Lexer::T_INTEGER);
				return (int)$this->lexer->token['value'];

			case Lexer::T_FLOAT:
				$this->match(Lexer::T_FLOAT);
				return (float)$this->lexer->token['value'];

			case Lexer::T_TRUE:
				$this->match(Lexer::T_TRUE);
				return true;

			case Lexer::T_FALSE:
				$this->match(Lexer::T_FALSE);
				return false;

			default:
				$this->syntaxError('PlainValue');
		}
	}

	/**
	 * FieldAssignment ::= FieldName "=" PlainValue
	 * FieldName ::= identifier
	 *
	 * @return array
	 */
	private function parseFieldAssignment(){
		$this->match(Lexer::T_IDENTIFIER);
		$fieldName = $this->lexer->token['value'];
		$this->match(Lexer::T_EQUALS);

		return array($fieldName => $this->parsePlainValue());
	}

	/**
	 * Array ::= "{" ArrayEntry {"," ArrayEntry}* "}"
	 *
	 * @return array
	 */
	private function parseArrayx(){
		$array = $values = array();

		$this->match(Lexer::T_OPEN_CURLY_BRACES);
		$values[] = $this->parseArrayEntry();

		while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
			$this->match(Lexer::T_COMMA);
			$values[] = $this->parseArrayEntry();
		}

		$this->match(Lexer::T_CLOSE_CURLY_BRACES);

		foreach ($values as $value) {
			list ($key, $val) = $value;

			if ($key !== null) {
				$array[$key] = $val;
			} else {
				$array[] = $val;
			}
		}

		return $array;
	}

	/**
	 * ArrayEntry ::= Value | KeyValuePair
	 * KeyValuePair ::= Key "=" PlainValue
	 * Key ::= string | integer
	 *
	 * @return array
	 */
	private function parseArrayEntry(){
		$peek = $this->lexer->glimpse();

		if ($peek['value'] == '=') {
			$this->match(
			$this->lexer->isNextToken(Lexer::T_INTEGER) ? Lexer::T_INTEGER : Lexer::T_STRING
			);

			$key = $this->lexer->token['value'];
			$this->match(Lexer::T_EQUALS);

			return array($key, $this->parsePlainValue());
		}

		return array(null, $this->parseValue());
	}

	/**
	 * Constructs a new annotation with a given map of values.
	 *
	 * The default construction procedure is to instantiate a new object of a class
	 * with the same name as the annotation. Subclasses can override this method to
	 * change the construction process of new annotations.
	 *
	 * @param string The name of the annotation.
	 * @param array The map of annotation values.
	 * @return mixed The new annotation with the given values.
	 */
	protected function newAnnotation($name, array $values){
		return new $name($values);
	}
}