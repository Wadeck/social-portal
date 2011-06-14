<?php
namespace socialportal\model;

/**
 * TopicStory
 *
 * @Table(
 *	name="topic_story", 
 *	indexes={
 *		@Index(name="idx_5065a85afcee3286", columns={"topicBase_id"})
 *	}
 * )
 * @Entity
 */
class TopicStory{
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
     * @var text $automaticThoughts
     *
     * @Column(name="automatic_thoughts", type="text", nullable=false)
     */
    private $automaticThoughts;

    /**
     * @var text $alternativeThoughts
     *
     * @Column(name="alternative_thoughts", type="text", nullable=false)
     */
    private $alternativeThoughts;

    /**
     * @var text $realisticThoughts
     *
     * @Column(name="realistic_thoughts", type="text", nullable=false)
     */
    private $realisticThoughts;

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

    /** Set automaticThoughts @param text $automaticThoughts */
    public function setAutomaticThoughts($automaticThoughts){ $this->automaticThoughts = $automaticThoughts; }

    /** Get automaticThoughts @return text $automaticThoughts */
    public function getAutomaticThoughts(){ return $this->automaticThoughts; }

    /** Set alternativeThoughts @param text $alternativeThoughts */
    public function setAlternativeThoughts($alternativeThoughts){ $this->alternativeThoughts = $alternativeThoughts; }

    /** Get alternativeThoughts @return text $alternativeThoughts */
    public function getAlternativeThoughts(){ return $this->alternativeThoughts; }

    /** Set realisticThoughts @param text $realisticThoughts */
    public function setRealisticThoughts($realisticThoughts){ $this->realisticThoughts = $realisticThoughts; }

    /** Get realisticThoughts @return text $realisticThoughts */
    public function getRealisticThoughts(){ return $this->realisticThoughts; }

    /** Set ps @param text $ps */
    public function setPs($ps){ $this->ps = $ps; }

    /** Get ps @return text $ps */
    public function getPs(){ return $this->ps; }

    /** Set topicbase @param socialportal\model\TopicBase $topicbase */
    public function setTopicbase(\socialportal\model\TopicBase $topicbase){ $this->topicbase = $topicbase; }

    /** Get topicbase @return socialportal\model\TopicBase $topicbase */
    public function getTopicbase(){ return $this->topicbase; }
}