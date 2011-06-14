<?php
namespace socialportal\model;

/**
 * Meta
 *
 * @Table(
 *	name="meta"
 * )
 * @Entity
 */
class Meta{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $objectType
     *
     * @Column(name="object_type", type="string", length=16, nullable=false)
     */
    private $objectType;

    /**
     * @var bigint $objectId
     *
     * @Column(name="object_id", type="bigint", nullable=false)
     */
    private $objectId;

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

    /** Set objectType @param string $objectType */
    public function setObjectType($objectType){ $this->objectType = $objectType; }

    /** Get objectType @return string $objectType */
    public function getObjectType(){ return $this->objectType; }

    /** Set objectId @param bigint $objectId */
    public function setObjectId($objectId){ $this->objectId = $objectId; }

    /** Get objectId @return bigint $objectId */
    public function getObjectId(){ return $this->objectId; }

    /** Set metaKey @param string $metaKey */
    public function setMetaKey($metaKey){ $this->metaKey = $metaKey; }

    /** Get metaKey @return string $metaKey */
    public function getMetaKey(){ return $this->metaKey; }

    /** Set metaValue @param text $metaValue */
    public function setMetaValue($metaValue){ $this->metaValue = $metaValue; }

    /** Get metaValue @return text $metaValue */
    public function getMetaValue(){ return $this->metaValue; }
}