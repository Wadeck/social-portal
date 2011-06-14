<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace core;

//TODO possible optimisation: permettre a un meme loader de prendre en charge plusieur namespace avec chacun un path different
/**
 * A <tt>ClassLoader</tt> is an autoloader for class files that can be
 * installed on the SPL autoload stack. It is a class loader that either loads only classes
 * of a specific namespace or all namespaces and it is suitable for working together
 * with other autoloaders in the SPL autoload stack.
 *
 * If no include path is configured through the constructor or {@link setIncludePath}, a ClassLoader
 * relies on the PHP <code>include_path</code>.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
class ClassLoader {
	/** @var ClassLoader */
	private static $instance;
	/** @var string */
	private static $fileExtension = '.php';
	/** Namespace separator @var string */
	private static $sep = '\\';
	/** @var array of string $nss */
	private $nss = array();
	/** @var array of string $includes */
	private $includes = array();
	/** @var string */
	private $rootDir;
	/** @var string The default namespace that is checked after all other */
	private $defaultNS = null;
	
	/**
	 * Creates a new <tt>ClassLoader</tt> that loads classes of the
	 * specified namespace from the specified include path.
	 *
	 * If no include path is given, the ClassLoader relies on the PHP include_path.
	 * If neither a namespace nor an include path is given, the ClassLoader will
	 * be responsible for loading all classes, thereby relying on the PHP include_path.
	 */
	private function __construct() {}
	
	public static function getInstance() {
		if( !self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/** @param $rootDir without separator ! @return ClassLoader */
	public function setRootDirectory($rootDir) {
		$this->rootDir = $rootDir;
		return $this;
	}
	/**
	 * @param string $ns The namespace of the classes to load.
	 * @param string $includePath The base include path to use.
	 * @return ClassLoader
	 */
	public function addMatch($ns, $includePath = null) {
		if( !$ns ) {
			return this;
		}
		$this->nss[] = $ns;
		$this->includes[] = $includePath ? $includePath : '';
		
		return $this;
	}
	/**
	 * Used only when all the other namespace cannot matched
	 * @param string $includePath The base include path to use.
	 * @return ClassLoader
	 */
	public function addDefaultMatch($includePath) {
		$this->defaultNS = $includePath;
		return $this;
	}
	
	/**
	 * Registers this ClassLoader on the SPL autoload stack.
	 */
	public function register() {
		spl_autoload_register( array( $this, 'loadClass' ) );
	}
	
	/**
	 * Removes this ClassLoader from the SPL autoload stack.
	 */
	public function unregister() {
		spl_autoload_unregister( array( $this, 'loadClass' ) );
	}
	
	/**
	 * Loads the given class or interface.
	 *
	 * @param string $classname The name of the class to load.
	 * The namespace should be include before and the extension after like usually
	 * @return boolean TRUE if the class has been successfully loaded, FALSE otherwise.
	 */
	public function loadClass($className) {
		$size = count( $this->nss );
		for( $i = 0; $i < $size; $i++ ) {
			if( strpos( $className, $this->nss[$i] . self::$sep ) !== 0 ) {
				continue;
			}
			$name = $this->constructName( $this->includes[$i], $className );
			if( !file_exists( $name ) ) {
				continue;
			}
			require $name;
			return true;
		}
		if( $this->defaultNS ) {
			$name = $this->constructName( $this->defaultNS, $className );
			if( file_exists( $name ) ) {
				require $name;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param string $fileName
	 * @param string $ext like '.php' or '.phtml'
	 * @return string The file name desired or false
	 */
	public function getFileName($fileName, $ext = null) {
		$ext = $ext ? $ext : self::$fileExtension;
		
		$size = count( $this->nss );
		for( $i = 0; $i < $size; $i++ ) {
			if( strpos( $fileName, $this->nss[$i] . self::$sep ) !== 0 ) {
				continue;
			}
			$name = $this->constructName( $this->includes[$i], $fileName, $ext );
			
			if( !file_exists( $name ) ) {
				continue;
			}
			return $name;
		}
		return false;
	}
	
	private final function constructName($includePath, $className, $fileExt = null) {
		$includePath = $includePath != '' ? ($includePath . DIRECTORY_SEPARATOR) : '';
		$ext = $fileExt ? $fileExt : self::$fileExtension;
		if( strpos( $ext, '.' ) !== 0 ) {
			$ext = '.' . $ext;
		}
		$name = '';
		if( $this->rootDir ) {
			$name .= $this->rootDir . DIRECTORY_SEPARATOR;
		}
		$name .= $includePath;
		$name .= str_replace( self::$sep, DIRECTORY_SEPARATOR, $className );
		$name .= $ext;
		
		return $name;
	}
	
	/**
	 * Asks this ClassLoader whether it can potentially load the class (file) with
	 * the given name.
	 *
	 * @param string $className The fully-qualified name of the class.
	 * @return boolean TRUE if this ClassLoader can load the class, FALSE otherwise.
	 */
	public function canLoadClass($className) {
		$size = count( $this->nss );
		for( $i = 0; $i < $size; $i++ ) {
			if( strpos( $className, $this->nss[$i] . self::$sep ) !== 0 ) {
				return false;
			}
			$name = self::constructName( $i, $className );
			if( file_exists( $name ) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks whether a class with a given name exists. A class "exists" if it is either
	 * already defined in the current request or if there is an autoloader on the SPL
	 * autoload stack that is a) responsible for the class in question and b) is able to
	 * load a class file in which the class definition resides.
	 *
	 * If the class is not already defined, each autoloader in the SPL autoload stack
	 * is asked whether it is able to tell if the class exists. If the autoloader is
	 * a <tt>ClassLoader</tt>, {@link canLoadClass} is used, otherwise the autoload
	 * function of the autoloader is invoked and expected to return a value that
	 * evaluates to TRUE if the class (file) exists. As soon as one autoloader reports
	 * that the class exists, TRUE is returned.
	 *
	 * Note that, depending on what kinds of autoloaders are installed on the SPL
	 * autoload stack, the class (file) might already be loaded as a result of checking
	 * for its existence. This is not the case with a <tt>ClassLoader</tt>, who separates
	 * these responsibilities.
	 *
	 * @param string $className The fully-qualified name of the class.
	 * @return boolean TRUE if the class exists as per the definition given above, FALSE otherwise.
	 */
	public static function classExists($className) {
		if( class_exists( $className, false ) ) {
			return true;
		}
		
		foreach( spl_autoload_functions() as $loader ) {
			if( is_array( $loader ) ) { // array(???, ???)
				if( is_object( $loader[0] ) ) {
					if( $loader[0] instanceof ClassLoader ) { // array($obj, 'methodName')
						if( $loader[0]->canLoadClass( $className ) ) {
							return true;
						}
					} else if( $loader[0]->{$loader[1]}( $className ) ) {
						return true;
					}
				} else if( $loader[0]::$loader[1]( $className ) ) { // array('ClassName', 'methodName')
					return true;
				}
			} else if( $loader instanceof \Closure ) { // function($className) {..}
				if( $loader( $className ) ) {
					return true;
				}
			} else if( is_string( $loader ) && $loader( $className ) ) { // "MyClass::loadClass"
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Gets the <tt>ClassLoader</tt> from the SPL autoload stack that is responsible
	 * for (and is able to load) the class with the given name.
	 *
	 * @param string $className The name of the class.
	 * @return The <tt>ClassLoader</tt> for the class or NULL if no such <tt>ClassLoader</tt> exists.
	 */
	public static function getClassLoader($className) {
		foreach( spl_autoload_functions() as $loader ) {
			if( is_array( $loader ) && $loader[0] instanceof ClassLoader && $loader[0]->canLoadClass( $className ) ) {
				return $loader[0];
			}
		}
		
		return null;
	}
}