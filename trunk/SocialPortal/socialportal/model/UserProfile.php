<?php
namespace socialportal\model;

/**
 * UserProfile
 *
 * @Table(
 *	name="user_profile"
 * )
 * @Entity(repositoryClass="UserProfileRepository")
 */
class UserProfile{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $userId
     *
     * @Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var boolean $gender
     *
     * @Column(name="gender", type="boolean", nullable=true)
     * @Comment("0: male, 1: female, null: unknown")
     */
    private $gender;

    /**
     * @var date $birth
     *
     * @Column(name="birth", type="date", nullable=true)
     */
    private $birth;

    /**
     * @var smallint $dateDisplay
     *
     * @Column(name="date_display", type="smallint", nullable=true)
     * @Comment("0: not shown, 1: only day/month, 2: only age, 3: both")
     */
    private $dateDisplay;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var text $objectives
     *
     * @Column(name="objectives", type="text", nullable=true)
     */
    private $objectives;

    /**
     * @var text $quote
     *
     * @Column(name="quote", type="text", nullable=true)
     */
    private $quote;

    /**
     * @var datetime $lastModified
     *
     * @Column(name="last_modified", type="datetime", nullable=true)
     */
    private $lastModified;

    /**
     * @var string $hobbies
     *
     * @Column(name="hobbies", type="string", length=255, nullable=true)
     * @Comment("String representation of an array, use serialize/unserialize to interact with")
     */
    private $hobbies;

    /**
     * @var bigint $country
     *
     * @Column(name="country", type="bigint", nullable=true)
     * @Comment("Id of the country from user_profile_country")
     */
    private $country;

    /**
     * @var bigint $state
     *
     * @Column(name="state", type="bigint", nullable=true)
     * @Comment("Id of the state from user_profile_state")
     */
    private $state;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set gender @param boolean $gender */
    public function setGender($gender){ $this->gender = $gender; }

    /** Get gender @return boolean $gender */
    public function getGender(){ return $this->gender; }

    /** Set birth @param date $birth */
    public function setBirth($birth){ $this->birth = $birth; }

    /** Get birth @return date $birth */
    public function getBirth(){ return $this->birth; }

    /** Set dateDisplay @param smallint $dateDisplay */
    public function setDateDisplay($dateDisplay){ $this->dateDisplay = $dateDisplay; }

    /** Get dateDisplay @return smallint $dateDisplay */
    public function getDateDisplay(){ return $this->dateDisplay; }

    /** Set description @param text $description */
    public function setDescription($description){ $this->description = $description; }

    /** Get description @return text $description */
    public function getDescription(){ return $this->description; }

    /** Set objectives @param text $objectives */
    public function setObjectives($objectives){ $this->objectives = $objectives; }

    /** Get objectives @return text $objectives */
    public function getObjectives(){ return $this->objectives; }

    /** Set quote @param text $quote */
    public function setQuote($quote){ $this->quote = $quote; }

    /** Get quote @return text $quote */
    public function getQuote(){ return $this->quote; }

    /** Set lastModified @param datetime $lastModified */
    public function setLastModified($lastModified){ $this->lastModified = $lastModified; }

    /** Get lastModified @return datetime $lastModified */
    public function getLastModified(){ return $this->lastModified; }

    /** Set hobbies @param string $hobbies */
    public function setHobbies($hobbies){ $this->hobbies = $hobbies; }

    /** Get hobbies @return string $hobbies */
    public function getHobbies(){ return $this->hobbies; }

    /** Set country @param bigint $country */
    public function setCountry($country){ $this->country = $country; }

    /** Get country @return bigint $country */
    public function getCountry(){ return $this->country; }

    /** Set state @param bigint $state */
    public function setState($state){ $this->state = $state; }

    /** Get state @return bigint $state */
    public function getState(){ return $this->state; }
}