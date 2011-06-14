<?php
namespace socialportal\model;

/**
 * Groups
 *
 * @Table(
 *	name="groups"
 * )
 * @Entity
 */
class Groups{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $creatorId
     *
     * @Column(name="creator_id", type="bigint", nullable=false)
     */
    private $creatorId;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string $status
     *
     * @Column(name="status", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var datetime $dateCreated
     *
     * @Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set creatorId @param bigint $creatorId */
    public function setCreatorId($creatorId){ $this->creatorId = $creatorId; }

    /** Get creatorId @return bigint $creatorId */
    public function getCreatorId(){ return $this->creatorId; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set description @param text $description */
    public function setDescription($description){ $this->description = $description; }

    /** Get description @return text $description */
    public function getDescription(){ return $this->description; }

    /** Set status @param string $status */
    public function setStatus($status){ $this->status = $status; }

    /** Get status @return string $status */
    public function getStatus(){ return $this->status; }

    /** Set dateCreated @param datetime $dateCreated */
    public function setDateCreated($dateCreated){ $this->dateCreated = $dateCreated; }

    /** Get dateCreated @return datetime $dateCreated */
    public function getDateCreated(){ return $this->dateCreated; }
}