<?php

namespace core\tools;

use core\ClassLoader;
use	ReflectionClass;
use	ReflectionMethod;
use	ReflectionProperty;

use Doctrine\Common\Annotations;

class AnnotationRetriever{
	/**
	 * Cache salt
	 *
	 * @var string
	 * @static
	 */
	private static $CACHE_SALT = '@<Annot>';

	/**
	 * Annotations Parser
	 *
	 * @var Doctrine\Common\Annotations\Parser
	 */
	private $parser;

	/**
	 * Cache mechanism to store processed Annotations
	 *
	 * @var Doctrine\Common\Cache\Cache
	 */
	private $cache;

	public function __construct(ClassLoader $classLoader, $defaultAnnotationNamespace = '', Cache $cache = null){
		$namespace = ( $defaultAnnotationNamespace != '' ) ?: (__NAMESPACE__.'\\');
		$this->parser = new AnnotationParser($classLoader, $namespace);
		$this->cache = $cache ?: new \Doctrine\Common\Cache\ArrayCache;
	}

	/**
	 * @param string $className
	 * @param string $methodName
	 * @return array of Annotation
	 */
	public function getAnnotationForMethod($className, $methodName){
		$refl = new ReflectionMethod($className, $methodName);
		return $this->getMethodAnnotations($refl);
	}

	/**
	 * @param string $className
	 * @return array of Annotation
	 */
	public function getAnnotationForClass($className){
		$refl = new ReflectionClass($className);
		return $this->getClassAnnotations($refl);
	}

	/**
	 * @param string $className
	 * @param string $propName
	 * @return array of Annotation
	 */
	public function getAnnotationForProperty($className, $propName){
		$refl = new ReflectionProperty($className, $propName);
		return $this->getPropertyAnnotations($refl);
	}

	/**
	 * Gets the annotations applied to a class.
	 *
	 * @param string|ReflectionClass $class The name or ReflectionClass of the class from which
	 * the class annotations should be read.
	 * @return array An array of Annotations.
	 */
	public function getClassAnnotations(ReflectionClass $class){
		$cacheKey = $class->getName() . self::$CACHE_SALT;

		// Attempt to grab data from cache
		if (($data = $this->cache->fetch($cacheKey)) !== false) {
			return $data;
		}

		$annotations = $this->parser->parse($class->getDocComment(), 'class ' . $class->getName());
		$this->cache->save($cacheKey, $annotations, null);

		return $annotations;
	}

	/**
	 * Gets the annotations applied to a property.
	 *
	 * @param string|ReflectionClass $class The name or ReflectionClass of the class that owns the property.
	 * @param string|ReflectionProperty $property The name or ReflectionProperty of the property
	 * from which the annotations should be read.
	 * @return array An array of Annotations.
	 */
	public function getPropertyAnnotations(ReflectionProperty $property){
		$cacheKey = $property->getDeclaringClass()->getName() . '$' . $property->getName() . self::$CACHE_SALT;

		// Attempt to grab data from cache
		if (($data = $this->cache->fetch($cacheKey)) !== false) {
			return $data;
		}

		$context = 'property ' . $property->getDeclaringClass()->getName() . "::\$" . $property->getName();
		$annotations = $this->parser->parse($property->getDocComment(), $context);
		$this->cache->save($cacheKey, $annotations, null);

		return $annotations;
	}
	/**
	 * Gets the annotations applied to a method.
	 *
	 * @param string|ReflectionClass $class The name or ReflectionClass of the class that owns the method.
	 * @param string|ReflectionMethod $property The name or ReflectionMethod of the method from which
	 * the annotations should be read.
	 * @return array An array of Annotations.
	 */
	public function getMethodAnnotations(ReflectionMethod $method) {
		$cacheKey = $method->getDeclaringClass()->getName() . '#' . $method->getName() . self::$CACHE_SALT;

		// Attempt to grab data from cache
		if (($data = $this->cache->fetch($cacheKey)) !== false) {
			return $data;
		}

		$context = 'method ' . $method->getDeclaringClass()->getName() . '::' . $method->getName() . '()';
		$annotations = $this->parser->parse($method->getDocComment(), $context);
		$this->cache->save($cacheKey, $annotations, null);

		return $annotations;
	}
}