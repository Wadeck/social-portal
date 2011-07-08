<?php
namespace socialportal\model;

/**
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
     * @Column(name="expiration_date", type="datetime", nullable=true)
     * @Comment("After this date, the token must be invalid and could be removed without care, if null, means infinite");
     */
    private $expirationDate;

    /**
     * @Column(name="token", type="string", nullable=false, index=true)
     */
    private $token;
    
    /**
     * @Column(name="validation_key", type="string", length=8, nullable=false, index=true)
     */
    private $type;

    /**
     * @Column(name="meta", type="string", nullable=false)
     * @Comment("Could be used to stored information like role or array serialized")
     */
    private $meta;
}