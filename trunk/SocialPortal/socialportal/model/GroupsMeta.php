<?php
namespace socialportal\model;

/**
 * GroupsMeta
 *
 * @Table(
 *	name="groups_meta"
 * )
 * @Entity
 */
class GroupsMeta{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $groupId
     *
     * @Column(name="group_id", type="bigint", nullable=false)
     */
    private $groupId;

    /**
     * @var string $metaKey
     *
     * @Column(name="meta_key", type="string", length=255, nullable=true)
     */
    private $metaKey;

    /**
     * @var text $metaValue
     *
     * @Column(name="meta_value", type="text", nullable=true)
     */
    private $metaValue;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set groupId @param bigint $groupId */
    public function setGroupId($groupId){ $this->groupId = $groupId; }

    /** Get groupId @return bigint $groupId */
    public function getGroupId(){ return $this->groupId; }

    /** Set metaKey @param string $metaKey */
    public function setMetaKey($metaKey){ $this->metaKey = $metaKey; }

    /** Get metaKey @return string $metaKey */
    public function getMetaKey(){ return $this->metaKey; }

    /** Set metaValue @param text $metaValue */
    public function setMetaValue($metaValue){ $this->metaValue = $metaValue; }

    /** Get metaValue @return text $metaValue */
    public function getMetaValue(){ return $this->metaValue; }
}