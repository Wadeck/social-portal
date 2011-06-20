<?php

namespace socialportal\model\proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class ForumProxy extends \socialportal\model\Forum implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function setName($name)
    {
        $this->_load();
        return parent::setName($name);
    }

    public function getName()
    {
        $this->_load();
        return parent::getName();
    }

    public function setDescription($description)
    {
        $this->_load();
        return parent::setDescription($description);
    }

    public function getDescription()
    {
        $this->_load();
        return parent::getDescription();
    }

    public function setParent($parent)
    {
        $this->_load();
        return parent::setParent($parent);
    }

    public function getParent()
    {
        $this->_load();
        return parent::getParent();
    }

    public function setPosition($position)
    {
        $this->_load();
        return parent::setPosition($position);
    }

    public function getPosition()
    {
        $this->_load();
        return parent::getPosition();
    }

    public function setNumTopics($numTopics)
    {
        $this->_load();
        return parent::setNumTopics($numTopics);
    }

    public function getNumTopics()
    {
        $this->_load();
        return parent::getNumTopics();
    }

    public function setNumPosts($numPosts)
    {
        $this->_load();
        return parent::setNumPosts($numPosts);
    }

    public function getNumPosts()
    {
        $this->_load();
        return parent::getNumPosts();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'description', 'parent', 'position', 'numTopics', 'numPosts');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}