<?php
namespace socialportal\model;

/**
 * TopicVoteStats
 *
 * @Table(
 *	name="topic_vote_stats"
 * )
 * @Entity(repositoryClass="TopicVoteStatsRepository")
 */
class TopicVoteStats{
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
     * @Column(name="topic_id", type="bigint", nullable=false)
     */
    private $topicId;

    /**
     * @var integer $voteTotal
     *
     * @Column(name="vote_total", type="integer", nullable=false)
     */
    private $voteTotal;

    /**
     * @var integer $voteRelative
     *
     * @Column(name="vote_relative", type="integer", nullable=false)
     */
    private $voteRelative;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set topicId @param bigint $topicId */
    public function setTopicId($topicId){ $this->topicId = $topicId; }

    /** Get topicId @return bigint $topicId */
    public function getTopicId(){ return $this->topicId; }

    /** Set voteTotal @param integer $voteTotal */
    public function setVoteTotal($voteTotal){ $this->voteTotal = $voteTotal; }

    /** Get voteTotal @return integer $voteTotal */
    public function getVoteTotal(){ return $this->voteTotal; }

    /** Set voteRelative @param integer $voteRelative */
    public function setVoteRelative($voteRelative){ $this->voteRelative = $voteRelative; }

    /** Get voteRelative @return integer $voteRelative */
    public function getVoteRelative(){ return $this->voteRelative; }
}