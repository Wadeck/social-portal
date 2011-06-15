<?php
namespace socialportal\model;

/**
 * PostBase
 *
 * @Table(
 *	name="post__base", 
 *	indexes={
 *		@Index(name="idx_a8a02951f55203d", columns={"topic_id"}),
 *		@Index(name="idx_a8a02955bb66c05", columns={"poster_id"})
 *	}
 * )
 * @Entity
 */
class PostBase{
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
     */
    private $customType;

    /**
     * @var datetime $time
     *
     * @Column(name="time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @var string $posterIp
     *
     * @Column(name="poster_ip", type="string", length=15, nullable=false)
     */
    private $posterIp;

    /**
     * @var boolean $isDeleted
     *
     * @Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @var bigint $position
     *
     * @Column(name="position", type="bigint", nullable=false)
     */
    private $position;

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
     * @var socialportal\model\TopicBase
     *
     * @ManyToOne(targetEntity="socialportal\model\TopicBase")
     * @JoinColumns({
     *   @JoinColumn(name="topic_id", referencedColumnName="id")
     * })
     */
    private $topic;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set customType @param smallint $customType */
    public function setCustomType($customType){ $this->customType = $customType; }

    /** Get customType @return smallint $customType */
    public function getCustomType(){ return $this->customType; }

    /** Set time @param datetime $time */
    public function setTime($time){ $this->time = $time; }

    /** Get time @return datetime $time */
    public function getTime(){ return $this->time; }

    /** Set posterIp @param string $posterIp */
    public function setPosterIp($posterIp){ $this->posterIp = $posterIp; }

    /** Get posterIp @return string $posterIp */
    public function getPosterIp(){ return $this->posterIp; }

    /** Set isDeleted @param boolean $isDeleted */
    public function setIsDeleted($isDeleted){ $this->isDeleted = $isDeleted; }

    /** Get isDeleted @return boolean $isDeleted */
    public function getIsDeleted(){ return $this->isDeleted; }

    /** Set position @param bigint $position */
    public function setPosition($position){ $this->position = $position; }

    /** Get position @return bigint $position */
    public function getPosition(){ return $this->position; }

    /** Set poster @param socialportal\model\User $poster */
    public function setPoster(\socialportal\model\User $poster){ $this->poster = $poster; }

    /** Get poster @return socialportal\model\User $poster */
    public function getPoster(){ return $this->poster; }

    /** Set topic @param socialportal\model\TopicBase $topic */
    public function setTopic(\socialportal\model\TopicBase $topic){ $this->topic = $topic; }

    /** Get topic @return socialportal\model\TopicBase $topic */
    public function getTopic(){ return $this->topic; }
}