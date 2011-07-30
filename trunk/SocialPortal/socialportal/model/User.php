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
     * @Comment("Key that is used to hash to password, it is fixed at the creation, should never be changed or the password retrieval become impossible")
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
     * @Comment("The date when the user was registered")
     */
    private $registered;

    /**
     * @var string $activationKey
     *
     * @Column(name="activation_key", type="string", length=60, nullable=true)
     * @Comment("The key that is used by the user to register his account, could be given by other application")
     */
    private $activationKey;

    /**
     * @var smallint $status
     *
     * @Column(name="status", type="smallint", nullable=false)
     * @Comment("0: normal status, 1: pending email activation, 2: banned")
     */
    private $status;

    /**
     * @var integer $roles
     *
     * @Column(name="roles", type="integer", nullable=false)
     * @Comment("1: admin, 2:moderator, 4:full_user, 8:anonymous, see UserRoles static attributes")
     */
    private $roles;

    /**
     * @var smallint $avatarType
     *
     * @Column(name="avatar_type", type="smallint", nullable=false)
     * @Comment("0: gravatar geometric type, 1: custom image")
     */
    private $avatarType;

    /**
     * @var string $avatarKey
     *
     * @Column(name="avatar_key", type="string", length=255, nullable=true)
     * @Comment("The key that will be used to retrieve avatar from Gravatar, to be consistent with user avatar on gravatar, we must use the email as key. If the user has uploaded an image, this will represent the link to that file")
     */
    private $avatarKey;


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

    /** Set status @param smallint $status */
    public function setStatus($status){ $this->status = $status; }

    /** Get status @return smallint $status */
    public function getStatus(){ return $this->status; }

    /** Set roles @param integer $roles */
    public function setRoles($roles){ $this->roles = $roles; }

    /** Get roles @return integer $roles */
    public function getRoles(){ return $this->roles; }

    /** Set avatarType @param smallint $avatarType */
    public function setAvatarType($avatarType){ $this->avatarType = $avatarType; }

    /** Get avatarType @return smallint $avatarType */
    public function getAvatarType(){ return $this->avatarType; }

    /** Set avatarKey @param string $avatarKey */
    public function setAvatarKey($avatarKey){ $this->avatarKey = $avatarKey; }

    /** Get avatarKey @return string $avatarKey */
    public function getAvatarKey(){ return $this->avatarKey; }
}