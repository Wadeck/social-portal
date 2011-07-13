<?php
namespace socialportal\model;

/**
 * VotePost
 *
 * @Table(
 *	name="vote_post"
 * )
 * @Entity
 */
class VotePost{
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
}