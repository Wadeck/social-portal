<?php
namespace socialportal\model;

/**
 * PostVoteStats
 *
 * @Table(
 *	name="post_vote_stats", 
 *	indexes={
 *		@Index(name="topic_id_index", columns={"topic_id"}),
 *		@Index(name="post_id_index", columns={"post_id"})
 *	}
 * )
 * @Entity(repositoryClass="PostVoteStatsRepository")
 */
class PostVoteStats{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $topicId
     *
     * @Column(name="topic_id", type="bigint", nullable=false, index=true)
     */
    private $topicId;

    /**
     * @var bigint $postId
     *
     * @Column(name="post_id", type="bigint", nullable=false, index=true)
     */
    private $postId;

    /**
     * @var integer $voteTotal
     *
     * @Column(name="vote_total", type="integer", nullable=false)
     */
    private $voteTotal;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set topicId @param bigint $topicId */
    public function setTopicId($topicId){ $this->topicId = $topicId; }

    /** Get topicId @return bigint $topicId */
    public function getTopicId(){ return $this->topicId; }

    /** Set postId @param bigint $postId */
    public function setPostId($postId){ $this->postId = $postId; }

    /** Get postId @return bigint $postId */
    public function getPostId(){ return $this->postId; }

    /** Set voteTotal @param integer $voteTotal */
    public function setVoteTotal($voteTotal){ $this->voteTotal = $voteTotal; }

    /** Get voteTotal @return integer $voteTotal */
    public function getVoteTotal(){ return $this->voteTotal; }
}