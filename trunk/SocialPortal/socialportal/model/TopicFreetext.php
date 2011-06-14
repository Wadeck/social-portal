<?php
namespace socialportal\model;

/**
 * TopicFreetext
 *
 * @Table(
 *	name="topic_freetext", 
 *	indexes={
 *		@Index(name="idx_ed44245efcee3286", columns={"topicBase_id"})
 *	}
 * )
 * @Entity
 */
class TopicFreetext{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var text $content
     *
     * @Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var socialportal\model\TopicBase
     *
     * @ManyToOne(targetEntity="socialportal\model\TopicBase")
     * @JoinColumns({
     *   @JoinColumn(name="topicBase_id", referencedColumnName="id")
     * })
     */
    private $topicbase;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set content @param text $content */
    public function setContent($content){ $this->content = $content; }

    /** Get content @return text $content */
    public function getContent(){ return $this->content; }

    /** Set topicbase @param socialportal\model\TopicBase $topicbase */
    public function setTopicbase(\socialportal\model\TopicBase $topicbase){ $this->topicbase = $topicbase; }

    /** Get topicbase @return socialportal\model\TopicBase $topicbase */
    public function getTopicbase(){ return $this->topicbase; }
}