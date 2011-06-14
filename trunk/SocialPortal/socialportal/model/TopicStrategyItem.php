<?php
namespace socialportal\model;

/**
 * TopicStrategyItem
 *
 * @Table(
 *	name="topic_strategy_item", 
 *	indexes={
 *		@Index(name="idx_93358a991f55203d", columns={"topic_id"})
 *	}
 * )
 * @Entity
 */
class TopicStrategyItem{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $content
     *
     * @Column(name="content", type="string", length=100, nullable=false)
     */
    private $content;

    /**
     * @var integer $numVote
     *
     * @Column(name="num_vote", type="integer", nullable=false)
     */
    private $numVote;

    /**
     * @var datetime $creationTime
     *
     * @Column(name="creation_time", type="datetime", nullable=false)
     */
    private $creationTime;

    /**
     * @var datetime $lastVoteTime
     *
     * @Column(name="last_vote_time", type="datetime", nullable=false)
     */
    private $lastVoteTime;

    /**
     * @var bigint $author
     *
     * @Column(name="author", type="bigint", nullable=false)
     */
    private $author;

    /**
     * @var boolean $isDeleted
     *
     * @Column(name="is_deleted", type="boolean", nullable=false)
     */
    private $isDeleted;

    /**
     * @var socialportal\model\TopicStrategy
     *
     * @ManyToOne(targetEntity="socialportal\model\TopicStrategy")
     * @JoinColumns({
     *   @JoinColumn(name="topic_id", referencedColumnName="id")
     * })
     */
    private $topic;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set content @param string $content */
    public function setContent($content){ $this->content = $content; }

    /** Get content @return string $content */
    public function getContent(){ return $this->content; }

    /** Set numVote @param integer $numVote */
    public function setNumVote($numVote){ $this->numVote = $numVote; }

    /** Get numVote @return integer $numVote */
    public function getNumVote(){ return $this->numVote; }

    /** Set creationTime @param datetime $creationTime */
    public function setCreationTime($creationTime){ $this->creationTime = $creationTime; }

    /** Get creationTime @return datetime $creationTime */
    public function getCreationTime(){ return $this->creationTime; }

    /** Set lastVoteTime @param datetime $lastVoteTime */
    public function setLastVoteTime($lastVoteTime){ $this->lastVoteTime = $lastVoteTime; }

    /** Get lastVoteTime @return datetime $lastVoteTime */
    public function getLastVoteTime(){ return $this->lastVoteTime; }

    /** Set author @param bigint $author */
    public function setAuthor($author){ $this->author = $author; }

    /** Get author @return bigint $author */
    public function getAuthor(){ return $this->author; }

    /** Set isDeleted @param boolean $isDeleted */
    public function setIsDeleted($isDeleted){ $this->isDeleted = $isDeleted; }

    /** Get isDeleted @return boolean $isDeleted */
    public function getIsDeleted(){ return $this->isDeleted; }

    /** Set topic @param socialportal\model\TopicStrategy $topic */
    public function setTopic(\socialportal\model\TopicStrategy $topic){ $this->topic = $topic; }

    /** Get topic @return socialportal\model\TopicStrategy $topic */
    public function getTopic(){ return $this->topic; }
}