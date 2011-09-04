<?php
namespace socialportal\model;

/**
 * TopicProblem
 *
 * @Table(
 *	name="topic_problem", 
 *	indexes={
 *		@Index(name="idx_5f4c911dfcee3286", columns={"topicBase_id"})
 *	}
 * )
 * @Entity
 */
class TopicProblem{
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
     * @var text $potentialSolution
     *
     * @Column(name="potential_solution", type="text", nullable=false)
     */
    private $potentialSolution;

    /**
     * @var text $strategy
     *
     * @Column(name="strategy", type="text", nullable=false)
     */
    private $strategy;

    /**
     * @var text $evaluation
     *
     * @Column(name="evaluation", type="text", nullable=false)
     */
    private $evaluation;

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

    /** Set potentialSolution @param text $potentialSolution */
    public function setPotentialSolution($potentialSolution){ $this->potentialSolution = $potentialSolution; }

    /** Get potentialSolution @return text $potentialSolution */
    public function getPotentialSolution(){ return $this->potentialSolution; }

    /** Set strategy @param text $strategy */
    public function setStrategy($strategy){ $this->strategy = $strategy; }

    /** Get strategy @return text $strategy */
    public function getStrategy(){ return $this->strategy; }

    /** Set evaluation @param text $evaluation */
    public function setEvaluation($evaluation){ $this->evaluation = $evaluation; }

    /** Get evaluation @return text $evaluation */
    public function getEvaluation(){ return $this->evaluation; }

    /** Set ps @param text $ps */
    public function setPs($ps){ $this->ps = $ps; }

    /** Get ps @return text $ps */
    public function getPs(){ return $this->ps; }

    /** Set topicbase @param socialportal\model\TopicBase $topicbase */
    public function setTopicbase(\socialportal\model\TopicBase $topicbase){ $this->topicbase = $topicbase; }

    /** Get topicbase @return socialportal\model\TopicBase $topicbase */
    public function getTopicbase(){ return $this->topicbase; }
}