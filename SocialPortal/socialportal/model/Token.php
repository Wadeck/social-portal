<?php
namespace socialportal\model;

/**
 * Token
 *
 * @Table(
 *	name="token", 
 *	indexes={
 *		@Index(name="token_index", columns={"token"}),
 *		@Index(name="validation_key_index", columns={"validation_key"})
 *	}
 * )
 * @Entity
 */
class Token{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $expirationDate
     *
     * @Column(name="expiration_date", type="datetime", nullable=true)
     * @Comment("After this date, the token must be invalid and could be removed without care, if null, means infinite")
     */
    private $expirationDate;

    /**
     * @var string $token
     *
     * @Column(name="token", type="string", length=255, nullable=false, index=true)
     */
    private $token;

    /**
     * @var string $validationKey
     *
     * @Column(name="validation_key", type="string", length=8, nullable=false, index=true)
     */
    private $validationKey;

    /**
     * @var string $meta
     *
     * @Column(name="meta", type="string", length=255, nullable=false)
     * @Comment("Could be used to stored information like role or array serialized")
     */
    private $meta;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set expirationDate @param datetime $expirationDate */
    public function setExpirationDate($expirationDate){ $this->expirationDate = $expirationDate; }

    /** Get expirationDate @return datetime $expirationDate */
    public function getExpirationDate(){ return $this->expirationDate; }

    /** Set token @param string $token */
    public function setToken($token){ $this->token = $token; }

    /** Get token @return string $token */
    public function getToken(){ return $this->token; }

    /** Set validationKey @param string $validationKey */
    public function setValidationKey($validationKey){ $this->validationKey = $validationKey; }

    /** Get validationKey @return string $validationKey */
    public function getValidationKey(){ return $this->validationKey; }

    /** Set meta @param string $meta */
    public function setMeta($meta){ $this->meta = $meta; }

    /** Get meta @return string $meta */
    public function getMeta(){ return $this->meta; }
}