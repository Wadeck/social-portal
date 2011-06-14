<?php
namespace socialportal\model;

/**
 * ProfileGroups
 *
 * @Table(
 *	name="profile_groups"
 * )
 * @Entity
 */
class ProfileGroups{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var boolean $canDelete
     *
     * @Column(name="can_delete", type="boolean", nullable=false)
     */
    private $canDelete;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set description @param text $description */
    public function setDescription($description){ $this->description = $description; }

    /** Get description @return text $description */
    public function getDescription(){ return $this->description; }

    /** Set canDelete @param boolean $canDelete */
    public function setCanDelete($canDelete){ $this->canDelete = $canDelete; }

    /** Get canDelete @return boolean $canDelete */
    public function getCanDelete(){ return $this->canDelete; }
}