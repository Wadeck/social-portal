<?php
namespace socialportal\model;

/**
 * ProfileData
 *
 * @Table(
 *	name="profile_data"
 * )
 * @Entity
 */
class ProfileData{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $fieldId
     *
     * @Column(name="field_id", type="bigint", nullable=false)
     */
    private $fieldId;

    /**
     * @var bigint $userId
     *
     * @Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var text $value
     *
     * @Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @var datetime $lastUpdated
     *
     * @Column(name="last_updated", type="datetime", nullable=false)
     */
    private $lastUpdated;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set fieldId @param bigint $fieldId */
    public function setFieldId($fieldId){ $this->fieldId = $fieldId; }

    /** Get fieldId @return bigint $fieldId */
    public function getFieldId(){ return $this->fieldId; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set value @param text $value */
    public function setValue($value){ $this->value = $value; }

    /** Get value @return text $value */
    public function getValue(){ return $this->value; }

    /** Set lastUpdated @param datetime $lastUpdated */
    public function setLastUpdated($lastUpdated){ $this->lastUpdated = $lastUpdated; }

    /** Get lastUpdated @return datetime $lastUpdated */
    public function getLastUpdated(){ return $this->lastUpdated; }
}