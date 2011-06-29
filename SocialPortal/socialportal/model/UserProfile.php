<?php
namespace socialportal\model;

/**
 * UserProfile
 *
 * @Table(
 *	name="user_profile"
 * )
 * @Entity
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
     * @var bigint $userid
     *
     * @Column(name="userId", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var boolean $gender
     *
     * @Column(name="gender", type="boolean", nullable=true)
     */
    private $gender;

    /**
     * @var date $birth
     *
     * @Column(name="birth", type="date", nullable=true)
     */
    private $birth;

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


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userid @param bigint $userid */
    public function setUserid($userid){ $this->userid = $userid; }

    /** Get userid @return bigint $userid */
    public function getUserid(){ return $this->userid; }

    /** Set gender @param boolean $gender */
    public function setGender($gender){ $this->gender = $gender; }

    /** Get gender @return boolean $gender */
    public function getGender(){ return $this->gender; }

    /** Set birth @param date $birth */
    public function setBirth($birth){ $this->birth = $birth; }

    /** Get birth @return date $birth */
    public function getBirth(){ return $this->birth; }

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
}