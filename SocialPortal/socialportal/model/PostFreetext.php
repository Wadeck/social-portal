<?php
namespace socialportal\model;

/**
 * PostFreetext
 *
 * @Table(
 *	name="post_freetext", 
 *	indexes={
 *		@Index(name="idx_a49820f96fcbe53b", columns={"postBase_id"})
 *	}
 * )
 * @Entity
 */
class PostFreetext{
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

    /** Set postbase @param socialportal\model\PostBase $postbase */
    public function setPostbase(\socialportal\model\PostBase $postbase){ $this->postbase = $postbase; }

    /** Get postbase @return socialportal\model\PostBase $postbase */
    public function getPostbase(){ return $this->postbase; }
}