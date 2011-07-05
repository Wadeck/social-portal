<?php
namespace socialportal\model;

/**
 * UserProfileState
 *
 * @Table(
 *	name="user_profile_state"
 * )
 * @Entity
 */
class UserProfileState{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $countryId
     *
     * @Column(name="country_id", type="bigint", nullable=false)
     */
    private $countryId;

    /**
     * @var string $stateName
     *
     * @Column(name="state_name", type="string", length=255, nullable=false)
     */
    private $stateName;

    /**
     * @var string $shortName
     *
     * @Column(name="short_name", type="string", length=2, nullable=true)
     */
    private $shortName;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set countryId @param bigint $countryId */
    public function setCountryId($countryId){ $this->countryId = $countryId; }

    /** Get countryId @return bigint $countryId */
    public function getCountryId(){ return $this->countryId; }

    /** Set stateName @param string $stateName */
    public function setStateName($stateName){ $this->stateName = $stateName; }

    /** Get stateName @return string $stateName */
    public function getStateName(){ return $this->stateName; }

    /** Set shortName @param string $shortName */
    public function setShortName($shortName){ $this->shortName = $shortName; }

    /** Get shortName @return string $shortName */
    public function getShortName(){ return $this->shortName; }
}