<?php
namespace socialportal\model;

/**
 * SubsetTopic
 *
 * @Table(
 *	name="subset_topic"
 * )
 * @Entity(repositoryClass="SubsetTopicRepository")
 */
class SubsetTopic{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $expirationDate
     *
     * @Column(name="expiration_date", type="datetime", nullable=false)
     * @Comment("After this date, the subset must be recomputed")
     */
    private $expirationDate;

    /**
     * @var bigint $topicId
     *
     * @Column(name="topic_id", type="bigint", nullable=false)
     */
    private $topicId;

    /**
     * @var bigint $forumId
     *
     * @Column(name="forum_id", type="bigint", nullable=false)
     */
    private $forumId;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set expirationDate @param datetime $expirationDate */
    public function setExpirationDate($expirationDate){ $this->expirationDate = $expirationDate; }

    /** Get expirationDate @return datetime $expirationDate */
    public function getExpirationDate(){ return $this->expirationDate; }

    /** Set topicId @param bigint $topicId */
    public function setTopicId($topicId){ $this->topicId = $topicId; }

    /** Get topicId @return bigint $topicId */
    public function getTopicId(){ return $this->topicId; }

    /** Set forumId @param bigint $forumId */
    public function setForumId($forumId){ $this->forumId = $forumId; }

    /** Get forumId @return bigint $forumId */
    public function getForumId(){ return $this->forumId; }
}