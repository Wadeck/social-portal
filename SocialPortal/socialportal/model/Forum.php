<?php
namespace socialportal\model;

/**
 * Forum
 *
 * @Table(
 *	name="forum", 
 *	uniqueConstraints={
 *		@UniqueConstraint(name="name", columns={"name"})
 *	}
 * )
 * @Entity
 */
class Forum{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=25, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var integer $parent
     *
     * @Column(name="parent", type="integer", nullable=true)
     */
    private $parent;

    /**
     * @var integer $position
     *
     * @Column(name="position", type="integer", nullable=false, default="10")
     */
    private $position;

    /**
     * @var integer $numTopics
     *
     * @Column(name="num_topics", type="integer", nullable=true)
     */
    private $numTopics;

    /**
     * @var integer $numPosts
     *
     * @Column(name="num_posts", type="integer", nullable=true)
     */
    private $numPosts;

    public function __construct(){
        $this->position = '10';
        
    }
    
    /** Get id @return integer $id */
    public function getId(){ return $this->id; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set description @param text $description */
    public function setDescription($description){ $this->description = $description; }

    /** Get description @return text $description */
    public function getDescription(){ return $this->description; }

    /** Set parent @param integer $parent */
    public function setParent($parent){ $this->parent = $parent; }

    /** Get parent @return integer $parent */
    public function getParent(){ return $this->parent; }

    /** Set position @param integer $position */
    public function setPosition($position){ $this->position = $position; }

    /** Get position @return integer $position */
    public function getPosition(){ return $this->position; }

    /** Set numTopics @param integer $numTopics */
    public function setNumTopics($numTopics){ $this->numTopics = $numTopics; }

    /** Get numTopics @return integer $numTopics */
    public function getNumTopics(){ return $this->numTopics; }

    /** Set numPosts @param integer $numPosts */
    public function setNumPosts($numPosts){ $this->numPosts = $numPosts; }

    /** Get numPosts @return integer $numPosts */
    public function getNumPosts(){ return $this->numPosts; }
}