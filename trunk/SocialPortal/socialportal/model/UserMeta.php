<?php
namespace socialportal\model;

/**
 * UserMeta
 *
 * @Table(
 *	name="user_meta"
 * )
 * @Entity
 */
class UserMeta{
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
     * @var string $metaKey
     *
     * @Column(name="meta_key", type="string", length=255, nullable=true)
     */
    private $metaKey;

    /**
     * @var text $metaValue
     *
     * @Column(name="meta_value", type="text", nullable=true)
     */
    private $metaValue;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set metaKey @param string $metaKey */
    public function setMetaKey($metaKey){ $this->metaKey = $metaKey; }

    /** Get metaKey @return string $metaKey */
    public function getMetaKey(){ return $this->metaKey; }

    /** Set metaValue @param text $metaValue */
    public function setMetaValue($metaValue){ $this->metaValue = $metaValue; }

    /** Get metaValue @return text $metaValue */
    public function getMetaValue(){ return $this->metaValue; }
}