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
//used only to debug directly in eclipse (argument passing is not working)
$_SERVER['argv'][] = 'orm:generate-proxies';
//$_SERVER['argv'][] = 'orm:schema-tool:create';
//used only to create annotations from database
//$_SERVER['argv'][] = 'orm:convert-mapping';
//$_SERVER['argv'][] = '--from-database';
//$_SERVER['argv'][] = 'annotation'; 
//$_SERVER['argv'][] = '.\mapping';

require '../core/ClassLoader.php';
core\ClassLoader::getInstance()
->addMatch('socialportal')
->addMatch('Doctrine', 'lib')
->addMatch('Symfony', 'lib\Doctrine')
->addMatch('Proxy', '\\socialportal\\proxy')
->addMatch('core')
->setRootDirectory( implode( DIRECTORY_SEPARATOR, array_slice( explode( DIRECTORY_SEPARATOR, getcwd() ), 0, -1 ) ) )
->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataDriverImpl( $config->newDefaultAnnotationDriver( __DIR__ . '\\socialportal\\model' ) );
$config->setMetadataCacheImpl( new \Doctrine\Common\Cache\ArrayCache() );
$config->addEntityNamespace( 'socialportal\\model2', 'socialportal\\model3' );
$config->setProxyDir( __DIR__ . '/Proxies' );
$config->setProxyNamespace( 'Proxies' );

//TODO refactor this with create_database.php
$connectionParams = array( 'driver' => 'pdo_mysql', 'user' => 'doctrine_user', 'password' => 'doctrine_s3cr3t', 
		'dbname' => 'social_portal', 'host' => 'localhost', 'collation' => 'utf8_general_ci' );

$em = \Doctrine\ORM\EntityManager::create( $connectionParams, $config );

$helperSet = new \Symfony\Component\Console\Helper\HelperSet( 
		array( 'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper( $entityManager ) ) );

foreach( $GLOBALS as $helperSetCandidate ) {
	if( $helperSetCandidate instanceof \Symfony\Component\Console\Helper\HelperSet ) {
		$helperSet = $helperSetCandidate;
		break;
	}
}

$helperSet = ($helperSet) ?  : new \Symfony\Component\Console\Helper\HelperSet();

\Doctrine\ORM\Tools\Console\ConsoleRunner::run( $helperSet );
