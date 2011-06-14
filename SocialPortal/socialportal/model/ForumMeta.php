<?php
namespace socialportal\model;

/**
 * ForumMeta
 *
 * @Table(
 *	name="forum_meta", 
 *	indexes={
 *		@Index(name="idx_1413b4e229ccbad0", columns={"forum_id"})
 *	}
 * )
 * @Entity(repositoryClass="ForumMetaRepository")
 */
class ForumMeta{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $metaKey
     *
     * @Column(name="meta_key", type="string", length=255, nullable=false)
     */
    private $metaKey;

    /**
     * @var string $metaValue
     *
     * @Column(name="meta_value", type="string", length=255, nullable=false)
     */
    private $metaValue;

    /**
     * @var integer $forumId
     *
     * @Column(name="forum_id", type="integer", nullable=false, index=true)
     */
    private $forumId;


    /** Get id @return integer $id */
    public function getId(){ return $this->id; }

    /** Set metaKey @param string $metaKey */
    public function setMetaKey($metaKey){ $this->metaKey = $metaKey; }

    /** Get metaKey @return string $metaKey */
    public function getMetaKey(){ return $this->metaKey; }

    /** Set metaValue @param string $metaValue */
    public function setMetaValue($metaValue){ $this->metaValue = $metaValue; }

    /** Get metaValue @return string $metaValue */
    public function getMetaValue(){ return $this->metaValue; }

    /** Set forumId @param integer $forumId */
    public function setForumId($forumId){ $this->forumId = $forumId; }

    /** Get forumId @return integer $forumId */
    public function getForumId(){ return $this->forumId; }
}