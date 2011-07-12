<?php
namespace socialportal\model;

/**
 * Instruction
 *
 * @Table(
 *	name="instruction", 
 *	indexes={
 *		@Index(name="name_index", columns={"name"})
 *	}
 * )
 * @Entity(repositoryClass="InstructionRepository")
 */
class Instruction{
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
     * @Column(name="name", type="string", length=255, nullable=false, index=true)
     */
    private $name;

    /**
     * @var datetime $lastModification
     *
     * @Column(name="last_modification", type="datetime", nullable=true)
     */
    private $lastModification;

    /**
     * @var text $instructions
     *
     * @Column(name="instructions", type="text", nullable=false)
     */
    private $instructions;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set lastModification @param datetime $lastModification */
    public function setLastModification($lastModification){ $this->lastModification = $lastModification; }

    /** Get lastModification @return datetime $lastModification */
    public function getLastModification(){ return $this->lastModification; }

    /** Set instructions @param text $instructions */
    public function setInstructions($instructions){ $this->instructions = $instructions; }

    /** Get instructions @return text $instructions */
    public function getInstructions(){ return $this->instructions; }
}