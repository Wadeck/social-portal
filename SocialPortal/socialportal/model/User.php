<?php
namespace socialportal\model;

/**
 * User
 *
 * @Table(
 *	name="user", 
 *	uniqueConstraints={
 *		@UniqueConstraint(name="username_unique", columns={"username"})
 *	}
 * )
 * @Entity(repositoryClass="UserRepository")
 */
class User{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $username
     *
     * @Column(name="username", type="string", length=60, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string $randomKey
     *
     * @Column(name="random_key", type="string", length=32, nullable=false)
     */
    private $randomKey;

    /**
     * @var string $password
     *
     * @Column(name="password", type="string", length=40, nullable=false)
     */
    private $password;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var datetime $registered
     *
     * @Column(name="registered", type="datetime", nullable=false)
     */
    private $registered;

    /**
     * @var string $activationKey
     *
     * @Column(name="activation_key", type="string", length=60, nullable=true)
     */
    private $activationKey;

    /**
     * @var integer $status
     *
     * @Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var integer $roles
     *
     * @Column(name="roles", type="integer", nullable=false)
     */
    private $roles;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set username @param string $username */
    public function setUsername($username){ $this->username = $username; }

    /** Get username @return string $username */
    public function getUsername(){ return $this->username; }

    /** Set randomKey @param string $randomKey */
    public function setRandomKey($randomKey){ $this->randomKey = $randomKey; }

    /** Get randomKey @return string $randomKey */
    public function getRandomKey(){ return $this->randomKey; }

    /** Set password @param string $password */
    public function setPassword($password){ $this->password = $password; }

    /** Get password @return string $password */
    public function getPassword(){ return $this->password; }

    /** Set email @param string $email */
    public function setEmail($email){ $this->email = $email; }

    /** Get email @return string $email */
    public function getEmail(){ return $this->email; }

    /** Set registered @param datetime $registered */
    public function setRegistered($registered){ $this->registered = $registered; }

    /** Get registered @return datetime $registered */
    public function getRegistered(){ return $this->registered; }

    /** Set activationKey @param string $activationKey */
    public function setActivationKey($activationKey){ $this->activationKey = $activationKey; }

    /** Get activationKey @return string $activationKey */
    public function getActivationKey(){ return $this->activationKey; }

    /** Set status @param integer $status */
    public function setStatus($status){ $this->status = $status; }

    /** Get status @return integer $status */
    public function getStatus(){ return $this->status; }

    /** Set roles @param integer $roles */
    public function setRoles($roles){ $this->roles = $roles; }

    /** Get roles @return integer $roles */
    public function getRoles(){ return $this->roles; }
}