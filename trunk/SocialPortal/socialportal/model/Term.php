<?php
namespace socialportal\model;

/**
 * Term
 *
 * @Table(
 *	name="term"
 * )
 * @Entity
 */
class Term{
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
     * @Column(name="name", type="string", length=55, nullable=false)
     */
    private $name;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }
}