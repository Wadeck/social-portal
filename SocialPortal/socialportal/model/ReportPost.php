<?php
namespace socialportal\model;

/**
 * ReportPost
 *
 * @Table(
 *	name="report_post"
 * )
 * @Entity
 */
class ReportPost{
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
     * @var bigint $postId
     *
     * @Column(name="post_id", type="bigint", nullable=false)
     */
    private $postId;

    /**
     * @var datetime $date
     *
     * @Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var boolean $isTreated
     *
     * @Column(name="is_treated", type="boolean", nullable=false)
     */
    private $isTreated;

    /**
     * @var boolean $isDeleted
     *
     * @Column(name="is_deleted", type="boolean", nullable=false)
     */
    private $isDeleted;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set postId @param bigint $postId */
    public function setPostId($postId){ $this->postId = $postId; }

    /** Get postId @return bigint $postId */
    public function getPostId(){ return $this->postId; }

    /** Set date @param datetime $date */
    public function setDate($date){ $this->date = $date; }

    /** Get date @return datetime $date */
    public function getDate(){ return $this->date; }

    /** Set isTreated @param boolean $isTreated */
    public function setIsTreated($isTreated){ $this->isTreated = $isTreated; }

    /** Get isTreated @return boolean $isTreated */
    public function getIsTreated(){ return $this->isTreated; }

    /** Set isDeleted @param boolean $isDeleted */
    public function setIsDeleted($isDeleted){ $this->isDeleted = $isDeleted; }

    /** Get isDeleted @return boolean $isDeleted */
    public function getIsDeleted(){ return $this->isDeleted; }
}