<?php

namespace socialportal\repository;

use Doctrine\ORM\Mapping\ClassMetadata;

use socialportal\model\Instruction;

use core\Config;

use core\debug\Logger;
use Exception;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;
use DateTime;

class InstructionRepository extends EntityRepository {
	public static $prefixTopicType;
	public static $prefixEmail;
	
	/**
	 * @param EntityManager $em The EntityManager to use.
	 * @param ClassMetadata $classMetadata The class descriptor.
	 */
	public function __construct($em, ClassMetadata $class) {
		parent::__construct($em, $class);
		self::$prefixTopicType = Config::get('prefix_topic_type', 'topic_type_');
		self::$prefixEmail = Config::get('prefix_email', 'email_');
	}
	
	private function constructName($prefix, $name){
		return $prefix . $name;
	}
	
	/**
	 * @param string $prefix InstructionRepository#prefixTopicType 
	 * @param string $topicTypeName name of the topic
	 */
	public function getInstruction($prefix, $name) {
		$query = $this->_em->createQuery( 'SELECT i FROM Instruction i WHERE i.name = :name' );
		$name = $this->constructName($prefix, $name);
		$query->setParameter('name', $name);
		$result = $query->getSingleResult();
		return $result;
	}
	
	/**
	 * @param string $prefix InstructionRepository#prefixTopicType
	 * @param string $name 
	 * @param strinc $content The content of the instruction, should be already translated
	 * @return Entity that is persisted but not flushed
	 */
	public function createInstruction($prefix, $name, $content){
		$instruction = new Instruction();
		$name = $this->constructName($prefix, $name);
		$instruction->setName($name);
		$date = new DateTime('now');
		$instruction->setLastModification($date);
		$instruction->setInstructions($content);
		
		$this->_em->persist($instruction);
		return $instruction;
	}
	
	/**
	 * Warning, after this call the forum entity must be reloaded to have the correct value
	 * 
	 * @param string $prefix InstructionRepository#prefixTopicType
	 * @param string $name 
	 * @param strinc $content The content of the instruction, should be already translated
	 */
	public function updateInstructionForTopicType($prefix, $name, $content) {
		$query = $this->_em->createQuery( 'UPDATE Instruction i SET i.instructions = :content AND i.lastModification = :time WHERE i.name = :name' );
		$query->setParameter( 'content', $content );
		$name = $this->constructName($prefix, $name);
		$query->setParameter( 'name', $name );
		$date = new DateTime( 'now' );
		$query->setParameter( 'time', $date, \Doctrine\DBAL\Types\Type::DATETIME );
		return $query->execute();
	}
}