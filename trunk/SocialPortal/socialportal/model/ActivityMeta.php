<?php
namespace socialportal\model;

/**
 * ActivityMeta
 *
 * @Table(
 *	name="activity_meta"
 * )
 * @Entity
 */
class ActivityMeta{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $activityId
     *
     * @Column(name="activity_id", type="bigint", nullable=false)
     */
    private $activityId;

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

    /** Set activityId @param bigint $activityId */
    public function setActivityId($activityId){ $this->activityId = $activityId; }

    /** Get activityId @return bigint $activityId */
    public function getActivityId(){ return $this->activityId; }

    /** Set metaKey @param string $metaKey */
    public function setMetaKey($metaKey){ $this->metaKey = $metaKey; }

    /** Get metaKey @return string $metaKey */
    public function getMetaKey(){ return $this->metaKey; }

    /** Set metaValue @param text $metaValue */
    public function setMetaValue($metaValue){ $this->metaValue = $metaValue; }

    /** Get metaValue @return text $metaValue */
    public function getMetaValue(){ return $this->metaValue; }
}