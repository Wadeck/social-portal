<?php
namespace socialportal\model;

/**
 * PostStory
 *
 * @Table(
 *	name="post_story", 
 *	indexes={
 *		@Index(name="idx_7c4d88d76fcbe53b", columns={"postBase_id"})
 *	}
 * )
 * @Entity
 */
class PostStory{
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

    /** Set postbase @param socialportal\model\PostBase $postbase */
    public function setPostbase(\socialportal\model\PostBase $postbase){ $this->postbase = $postbase; }

    /** Get postbase @return socialportal\model\PostBase $postbase */
    public function getPostbase(){ return $this->postbase; }
}