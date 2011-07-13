<?php
namespace socialportal\model;

/**
 * Token
 *
 * @Table(
 *	name="token", 
 *	indexes={
 *		@Index(name="expiration_date_index", columns={"expiration_date"})
 *	}, 
 *	uniqueConstraints={
 *		@UniqueConstraint(name="token_unique", columns={"token"})
 *	}
 * )
 * @Entity(repositoryClass="TokenRepository")
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
     * @Column(name="expiration_date", type="datetime", nullable=true, index=true)
     * @Comment("After this date, the token must be invalid and could be removed without care, if null, means infinite")
     */
    private $expirationDate;

    /**
     * @var string $token
     *
     * @Column(name="token", type="string", length=32, nullable=false, unique=true)
     * @Comment("The random value that is the secret, must be unique, so could generate error in creation")
     */
    private $token;

    /**
     * @var text $meta
     *
     * @Column(name="meta", type="text", nullable=false)
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

    /** Set meta @param text $meta */
    public function setMeta($meta){ $this->meta = $meta; }

    /** Get meta @return text $meta */
    public function getMeta(){ return $this->meta; }
}