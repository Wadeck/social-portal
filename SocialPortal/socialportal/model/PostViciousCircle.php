<?php
namespace socialportal\model;

/**
 * PostViciousCircle
 *
 * @Table(
 *	name="post_vicious_circle", 
 *	indexes={
 *		@Index(name="idx_b26b65376fcbe53b", columns={"postBase_id"})
 *	}
 * )
 * @Entity
 */
class PostViciousCircle{
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
     * @var socialportal\model\PostBase
     *
     * @ManyToOne(targetEntity="socialportal\model\PostBase")
     * @JoinColumns({
     *   @JoinColumn(name="postBase_id", referencedColumnName="id")
     * })
     */
    private $postbase;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set content @param text $content */
    public function setContent($content){ $this->content = $content; }

    /** Get content @return text $content */
    public function getContent(){ return $this->content; }

    /** Set potentialSolution @param text $potentialSolution */
    public function setPotentialSolution($potentialSolution){ $this->potentialSolution = $potentialSolution; }

    /** Get potentialSolution @return text $potentialSolution */
    public function getPotentialSolution(){ return $this->potentialSolution; }

    /** Set strategy @param text $strategy */
    public function setStrategy($strategy){ $this->strategy = $strategy; }

    /** Get strategy @return text $strategy */
    public function getStrategy(){ return $this->strategy; }

    /** Set postbase @param socialportal\model\PostBase $postbase */
    public function setPostbase(\socialportal\model\PostBase $postbase){ $this->postbase = $postbase; }

    /** Get postbase @return socialportal\model\PostBase $postbase */
    public function getPostbase(){ return $this->postbase; }
}