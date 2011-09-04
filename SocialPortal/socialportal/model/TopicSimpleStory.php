<?php
namespace socialportal\model;

/**
 * TopicSimpleStory
 *
 * @Table(
 *	name="topic_simple_story", 
 *	indexes={
 *		@Index(name="idx_74169e6fcee3286", columns={"topicBase_id"})
 *	}
 * )
 * @Entity
 */
class TopicSimpleStory{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var text $storyContent
     *
     * @Column(name="story_content", type="text", nullable=false)
     */
    private $storyContent;

    /**
     * @var text $ps
     *
     * @Column(name="ps", type="text", nullable=false)
     */
    private $ps;

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

    /** Set storyContent @param text $storyContent */
    public function setStoryContent($storyContent){ $this->storyContent = $storyContent; }

    /** Get storyContent @return text $storyContent */
    public function getStoryContent(){ return $this->storyContent; }

    /** Set ps @param text $ps */
    public function setPs($ps){ $this->ps = $ps; }

    /** Get ps @return text $ps */
    public function getPs(){ return $this->ps; }

    /** Set topicbase @param socialportal\model\TopicBase $topicbase */
    public function setTopicbase(\socialportal\model\TopicBase $topicbase){ $this->topicbase = $topicbase; }

    /** Get topicbase @return socialportal\model\TopicBase $topicbase */
    public function getTopicbase(){ return $this->topicbase; }
}