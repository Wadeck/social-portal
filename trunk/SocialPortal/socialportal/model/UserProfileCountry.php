<?php
namespace socialportal\model;

/**
 * UserProfileCountry
 *
 * @Table(
 *	name="user_profile_country"
 * )
 * @Entity
 */
class UserProfileCountry{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $phoneCode
     *
     * @Column(name="phone_code", type="integer", nullable=false)
     */
    private $phoneCode;

    /**
     * @var string $countryName
     *
     * @Column(name="country_name", type="string", length=255, nullable=false)
     */
    private $countryName;

    /**
     * @var string $countryCode
     *
     * @Column(name="country_code", type="string", length=2, nullable=false)
     */
    private $countryCode;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set phoneCode @param integer $phoneCode */
    public function setPhoneCode($phoneCode){ $this->phoneCode = $phoneCode; }

    /** Get phoneCode @return integer $phoneCode */
    public function getPhoneCode(){ return $this->phoneCode; }

    /** Set countryName @param string $countryName */
    public function setCountryName($countryName){ $this->countryName = $countryName; }

    /** Get countryName @return string $countryName */
    public function getCountryName(){ return $this->countryName; }

    /** Set countryCode @param string $countryCode */
    public function setCountryCode($countryCode){ $this->countryCode = $countryCode; }

    /** Get countryCode @return string $countryCode */
    public function getCountryCode(){ return $this->countryCode; }
}