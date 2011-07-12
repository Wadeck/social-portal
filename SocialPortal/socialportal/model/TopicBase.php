<?php
namespace socialportal\model;

/**
 * TopicBase
 *
 * @Table(
 *	name="topic__base", 
 *	indexes={
 *		@Index(name="idx_26a222185bb66c05", columns={"poster_id"}),
 *		@Index(name="idx_26a2221829ccbad0", columns={"forum_id"}),
 *		@Index(name="idx_26a22218d176cfb", columns={"lastposter_id"})
 *	}
 * )
 * @Entity(repositoryClass="TopicBaseRepository")
 */
class TopicBase{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var smallint $customType
     *
     * @Column(name="custom_type", type="smallint", nullable=false)
     * @Comment("1: activity, 2: freetext, 3: story, 4: strategy")
     */
    private $customType;

    /**
     * @var string $title
     *
     * @Column(name="title", type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @var datetime $startTime
     *
     * @Column(name="start_time", type="datetime", nullable=false)
     * @Comment("Time of creation, never modified afterwards")
     */
    private $startTime;

    /**
     * @var datetime $time
     *
     * @Column(name="time", type="datetime", nullable=false)
     * @Comment("Time of last modification")
     */
    private $time;

    /**
     * @var boolean $isDeleted
     *
     * @Column(name="is_deleted", type="boolean", nullable=false)
     * @Comment("0: the item is not deleted, 1: the item is deleted")
     */
    private $isDeleted;

    /**
     * @var boolean $isOpen
     *
     * @Column(name="is_open", type="boolean", nullable=false, default="1")
     * @Comment("Only an open topic can accept feedbacks, 0: the topic is open, 1: the topic is close")
     */
    private $isOpen;

    /**
     * @var boolean $isSticky
     *
     * @Column(name="is_sticky", type="boolean", nullable=false)
     * @Comment("0: normally displayed, 1: the topic is placed at the top of the forum, could be used for rules, policy, important message from moderators")
     */
    private $isSticky;

    /**
     * @var bigint $numPosts
     *
     * @Column(name="num_posts", type="bigint", nullable=true)
     */
    private $numPosts;

    /**
     * @var bigint $tagCount
     *
     * @Column(name="tag_count", type="bigint", nullable=true)
     */
    private $tagCount;

    /**
     * @var socialportal\model\User
     *
     * @ManyToOne(targetEntity="socialportal\model\User")
     * @JoinColumns({
     *   @JoinColumn(name="poster_id", referencedColumnName="id")
     * })
     */
    private $poster;

    /**
     * @var socialportal\model\Forum
     *
     * @ManyToOne(targetEntity="socialportal\model\Forum")
     * @JoinColumns({
     *   @JoinColumn(name="forum_id", referencedColumnName="id")
     * })
     */
    private $forum;

    /**
     * @var socialportal\model\User
     *
     * @ManyToOne(targetEntity="socialportal\model\User")
     * @JoinColumns({
     *   @JoinColumn(name="lastposter_id", referencedColumnName="id")
     * })
     */
    private $lastposter;

    public function __construct(){
        $this->isOpen = '1';
        
    }
    
    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set customType @param smallint $customType */
    public function setCustomType($customType){ $this->customType = $customType; }

    /** Get customType @return smallint $customType */
    public function getCustomType(){ return $this->customType; }

    /** Set title @param string $title */
    public function setTitle($title){ $this->title = $title; }

    /** Get title @return string $title */
    public function getTitle(){ return $this->title; }

    /** Set startTime @param datetime $startTime */
    public function setStartTime($startTime){ $this->startTime = $startTime; }

    /** Get startTime @return datetime $startTime */
    public function getStartTime(){ return $this->startTime; }

    /** Set time @param datetime $time */
    public function setTime($time){ $this->time = $time; }

    /** Get time @return datetime $time */
    public function getTime(){ return $this->time; }

    /** Set isDeleted @param boolean $isDeleted */
    public function setIsDeleted($isDeleted){ $this->isDeleted = $isDeleted; }

    /** Get isDeleted @return boolean $isDeleted */
    public function getIsDeleted(){ return $this->isDeleted; }

    /** Set isOpen @param boolean $isOpen */
    public function setIsOpen($isOpen){ $this->isOpen = $isOpen; }

    /** Get isOpen @return boolean $isOpen */
    public function getIsOpen(){ return $this->isOpen; }

    /** Set isSticky @param boolean $isSticky */
    public function setIsSticky($isSticky){ $this->isSticky = $isSticky; }

    /** Get isSticky @return boolean $isSticky */
    public function getIsSticky(){ return $this->isSticky; }

    /** Set numPosts @param bigint $numPosts */
    public function setNumPosts($numPosts){ $this->numPosts = $numPosts; }

    /** Get numPosts @return bigint $numPosts */
    public function getNumPosts(){ return $this->numPosts; }

    /** Set tagCount @param bigint $tagCount */
    public function setTagCount($tagCount){ $this->tagCount = $tagCount; }

    /** Get tagCount @return bigint $tagCount */
    public function getTagCount(){ return $this->tagCount; }

    /** Set poster @param socialportal\model\User $poster */
    public function setPoster(\socialportal\model\User $poster){ $this->poster = $poster; }

    /** Get poster @return socialportal\model\User $poster */
    public function getPoster(){ return $this->poster; }

    /** Set forum @param socialportal\model\Forum $forum */
    public function setForum(\socialportal\model\Forum $forum){ $this->forum = $forum; }

    /** Get forum @return socialportal\model\Forum $forum */
    public function getForum(){ return $this->forum; }

    /** Set lastposter @param socialportal\model\User $lastposter */
    public function setLastposter(\socialportal\model\User $lastposter){ $this->lastposter = $lastposter; }

    /** Get lastposter @return socialportal\model\User $lastposter */
    public function getLastposter(){ return $this->lastposter; }
}