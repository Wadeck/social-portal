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
     * @var text $automaticthoughts
     *
     * @Column(name="automaticThoughts", type="text", nullable=false)
     */
    private $automaticthoughts;

    /**
     * @var text $alternativethoughts
     *
     * @Column(name="alternativeThoughts", type="text", nullable=false)
     */
    private $alternativethoughts;

    /**
     * @var text $realisticthoughts
     *
     * @Column(name="realisticThoughts", type="text", nullable=false)
     */
    private $realisticthoughts;

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

    /** Set automaticthoughts @param text $automaticthoughts */
    public function setAutomaticthoughts($automaticthoughts){ $this->automaticthoughts = $automaticthoughts; }

    /** Get automaticthoughts @return text $automaticthoughts */
    public function getAutomaticthoughts(){ return $this->automaticthoughts; }

    /** Set alternativethoughts @param text $alternativethoughts */
    public function setAlternativethoughts($alternativethoughts){ $this->alternativethoughts = $alternativethoughts; }

    /** Get alternativethoughts @return text $alternativethoughts */
    public function getAlternativethoughts(){ return $this->alternativethoughts; }

    /** Set realisticthoughts @param text $realisticthoughts */
    public function setRealisticthoughts($realisticthoughts){ $this->realisticthoughts = $realisticthoughts; }

    /** Get realisticthoughts @return text $realisticthoughts */
    public function getRealisticthoughts(){ return $this->realisticthoughts; }

    /** Set postbase @param socialportal\model\PostBase $postbase */
    public function setPostbase(\socialportal\model\PostBase $postbase){ $this->postbase = $postbase; }

    /** Get postbase @return socialportal\model\PostBase $postbase */
    public function getPostbase(){ return $this->postbase; }
}