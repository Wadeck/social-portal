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

    /**
     * @var smallint $genderPrivacy
     *
     * @Column(name="gender_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $genderPrivacy;

    /**
     * @var smallint $birthPrivacy
     *
     * @Column(name="birth_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $birthPrivacy;

    /**
     * @var smallint $descriptionPrivacy
     *
     * @Column(name="description_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $descriptionPrivacy;

    /**
     * @var smallint $objectivesPrivacy
     *
     * @Column(name="objectives_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $objectivesPrivacy;

    /**
     * @var smallint $quotePrivacy
     *
     * @Column(name="quote_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $quotePrivacy;

    /**
     * @var smallint $hobbiesPrivacy
     *
     * @Column(name="hobbies_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $hobbiesPrivacy;

    /**
     * @var smallint $countryPrivacy
     *
     * @Column(name="country_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $countryPrivacy;

    /**
     * @var smallint $statePrivacy
     *
     * @Column(name="state_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $statePrivacy;

    /**
     * @var smallint $activityPrivacy
     *
     * @Column(name="activity_privacy", type="smallint", nullable=false, default="1")
     * @Comment("0: non-set, 1: public, 2: user, 3: friends of friends, 4:friends, 5:myself only ")
     */
    private $activityPrivacy;

    /**
     * @var smallint $bmiPrivacy
     *
     * @Column(name="bmi_privacy", type="smallint", nullable=false, default="3")
     * @Comment("0: non-set, 1: public, 2: user, 3:myself only")
     */
    private $bmiPrivacy;

    /**
     * @var smallint $moodPrivacy
     *
     * @Column(name="mood_privacy", type="smallint", nullable=false, default="3")
     * @Comment("0: non-set, 1: public, 2: user, 3:myself only")
     */
    private $moodPrivacy;

    public function __construct(){
        $this->genderPrivacy = '1';
        $this->birthPrivacy = '1';
        $this->descriptionPrivacy = '1';
        $this->objectivesPrivacy = '1';
        $this->quotePrivacy = '1';
        $this->hobbiesPrivacy = '1';
        $this->countryPrivacy = '1';
        $this->statePrivacy = '1';
        $this->activityPrivacy = '1';
        $this->bmiPrivacy = '3';
        $this->moodPrivacy = '3';
        
    }
    
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

    /** Set genderPrivacy @param smallint $genderPrivacy */
    public function setGenderPrivacy($genderPrivacy){ $this->genderPrivacy = $genderPrivacy; }

    /** Get genderPrivacy @return smallint $genderPrivacy */
    public function getGenderPrivacy(){ return $this->genderPrivacy; }

    /** Set birthPrivacy @param smallint $birthPrivacy */
    public function setBirthPrivacy($birthPrivacy){ $this->birthPrivacy = $birthPrivacy; }

    /** Get birthPrivacy @return smallint $birthPrivacy */
    public function getBirthPrivacy(){ return $this->birthPrivacy; }

    /** Set descriptionPrivacy @param smallint $descriptionPrivacy */
    public function setDescriptionPrivacy($descriptionPrivacy){ $this->descriptionPrivacy = $descriptionPrivacy; }

    /** Get descriptionPrivacy @return smallint $descriptionPrivacy */
    public function getDescriptionPrivacy(){ return $this->descriptionPrivacy; }

    /** Set objectivesPrivacy @param smallint $objectivesPrivacy */
    public function setObjectivesPrivacy($objectivesPrivacy){ $this->objectivesPrivacy = $objectivesPrivacy; }

    /** Get objectivesPrivacy @return smallint $objectivesPrivacy */
    public function getObjectivesPrivacy(){ return $this->objectivesPrivacy; }

    /** Set quotePrivacy @param smallint $quotePrivacy */
    public function setQuotePrivacy($quotePrivacy){ $this->quotePrivacy = $quotePrivacy; }

    /** Get quotePrivacy @return smallint $quotePrivacy */
    public function getQuotePrivacy(){ return $this->quotePrivacy; }

    /** Set hobbiesPrivacy @param smallint $hobbiesPrivacy */
    public function setHobbiesPrivacy($hobbiesPrivacy){ $this->hobbiesPrivacy = $hobbiesPrivacy; }

    /** Get hobbiesPrivacy @return smallint $hobbiesPrivacy */
    public function getHobbiesPrivacy(){ return $this->hobbiesPrivacy; }

    /** Set countryPrivacy @param smallint $countryPrivacy */
    public function setCountryPrivacy($countryPrivacy){ $this->countryPrivacy = $countryPrivacy; }

    /** Get countryPrivacy @return smallint $countryPrivacy */
    public function getCountryPrivacy(){ return $this->countryPrivacy; }

    /** Set statePrivacy @param smallint $statePrivacy */
    public function setStatePrivacy($statePrivacy){ $this->statePrivacy = $statePrivacy; }

    /** Get statePrivacy @return smallint $statePrivacy */
    public function getStatePrivacy(){ return $this->statePrivacy; }

    /** Set activityPrivacy @param smallint $activityPrivacy */
    public function setActivityPrivacy($activityPrivacy){ $this->activityPrivacy = $activityPrivacy; }

    /** Get activityPrivacy @return smallint $activityPrivacy */
    public function getActivityPrivacy(){ return $this->activityPrivacy; }

    /** Set bmiPrivacy @param smallint $bmiPrivacy */
    public function setBmiPrivacy($bmiPrivacy){ $this->bmiPrivacy = $bmiPrivacy; }

    /** Get bmiPrivacy @return smallint $bmiPrivacy */
    public function getBmiPrivacy(){ return $this->bmiPrivacy; }

    /** Set moodPrivacy @param smallint $moodPrivacy */
    public function setMoodPrivacy($moodPrivacy){ $this->moodPrivacy = $moodPrivacy; }

    /** Get moodPrivacy @return smallint $moodPrivacy */
    public function getMoodPrivacy(){ return $this->moodPrivacy; }
}