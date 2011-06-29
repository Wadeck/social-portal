<?php

namespace socialportal\model\proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class UserProxy extends \socialportal\model\User implements \Doctrine\ORM\Proxy\Proxy
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

    public function setUsername($username)
    {
        $this->_load();
        return parent::setUsername($username);
    }

    public function getUsername()
    {
        $this->_load();
        return parent::getUsername();
    }

    public function setRandomKey($randomKey)
    {
        $this->_load();
        return parent::setRandomKey($randomKey);
    }

    public function getRandomKey()
    {
        $this->_load();
        return parent::getRandomKey();
    }

    public function setPassword($password)
    {
        $this->_load();
        return parent::setPassword($password);
    }

    public function getPassword()
    {
        $this->_load();
        return parent::getPassword();
    }

    public function setEmail($email)
    {
        $this->_load();
        return parent::setEmail($email);
    }

    public function getEmail()
    {
        $this->_load();
        return parent::getEmail();
    }

    public function setRegistered($registered)
    {
        $this->_load();
        return parent::setRegistered($registered);
    }

    public function getRegistered()
    {
        $this->_load();
        return parent::getRegistered();
    }

    public function setActivationKey($activationKey)
    {
        $this->_load();
        return parent::setActivationKey($activationKey);
    }

    public function getActivationKey()
    {
        $this->_load();
        return parent::getActivationKey();
    }

    public function setStatus($status)
    {
        $this->_load();
        return parent::setStatus($status);
    }

    public function getStatus()
    {
        $this->_load();
        return parent::getStatus();
    }

    public function setRoles($roles)
    {
        $this->_load();
        return parent::setRoles($roles);
    }

    public function getRoles()
    {
        $this->_load();
        return parent::getRoles();
    }

    public function setAvatarKey($avatarKey)
    {
        $this->_load();
        return parent::setAvatarKey($avatarKey);
    }

    public function getAvatarKey()
    {
        $this->_load();
        return parent::getAvatarKey();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'username', 'randomKey', 'password', 'email', 'registered', 'activationKey', 'status', 'roles', 'avatarKey');
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