<?php
namespace socialportal\model;

/**
 * TopicViciousCircle
 *
 * @Table(
 *	name="topic_vicious_circle", 
 *	indexes={
 *		@Index(name="idx_503b52cffcee3286", columns={"topicBase_id"})
 *	}
 * )
 * @Entity
 */
class TopicViciousCircle{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var text $lowSelfEsteem
     *
     * @Column(name="low_self_esteem", type="text", nullable=false)
     */
    private $lowSelfEsteem;

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

    /** Set lowSelfEsteem @param text $lowSelfEsteem */
    public function setLowSelfEsteem($lowSelfEsteem){ $this->lowSelfEsteem = $lowSelfEsteem; }

    /** Get lowSelfEsteem @return text $lowSelfEsteem */
    public function getLowSelfEsteem(){ return $this->lowSelfEsteem; }

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