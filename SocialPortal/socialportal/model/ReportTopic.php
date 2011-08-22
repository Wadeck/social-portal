<?php
namespace socialportal\model;

/**
 * ReportTopic
 *
 * @Table(
 *	name="report_topic"
 * )
 * @Entity
 */
class ReportTopic{
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
     * @var bigint $topicId
     *
     * @Column(name="topic_id", type="bigint", nullable=false)
     */
    private $topicId;

    /**
     * @var datetime $date
     *
     * @Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var boolean $isviewed
     *
     * @Column(name="isViewed", type="boolean", nullable=false)
     */
    private $isviewed;

    /**
     * @var boolean $istreated
     *
     * @Column(name="isTreated", type="boolean", nullable=false)
     */
    private $istreated;

    /**
     * @var boolean $isdeleted
     *
     * @Column(name="isDeleted", type="boolean", nullable=false)
     */
    private $isdeleted;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set topicId @param bigint $topicId */
    public function setTopicId($topicId){ $this->topicId = $topicId; }

    /** Get topicId @return bigint $topicId */
    public function getTopicId(){ return $this->topicId; }

    /** Set date @param datetime $date */
    public function setDate($date){ $this->date = $date; }

    /** Get date @return datetime $date */
    public function getDate(){ return $this->date; }

    /** Set isviewed @param boolean $isviewed */
    public function setIsviewed($isviewed){ $this->isviewed = $isviewed; }

    /** Get isviewed @return boolean $isviewed */
    public function getIsviewed(){ return $this->isviewed; }

    /** Set istreated @param boolean $istreated */
    public function setIstreated($istreated){ $this->istreated = $istreated; }

    /** Get istreated @return boolean $istreated */
    public function getIstreated(){ return $this->istreated; }

    /** Set isdeleted @param boolean $isdeleted */
    public function setIsdeleted($isdeleted){ $this->isdeleted = $isdeleted; }

    /** Get isdeleted @return boolean $isdeleted */
    public function getIsdeleted(){ return $this->isdeleted; }
}