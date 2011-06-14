<?php
namespace socialportal\model;

/**
 * Options
 *
 * @Table(
 *	name="options"
 * )
 * @Entity
 */
class Options{
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
     * @Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var text $value
     *
     * @Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @var boolean $autoload
     *
     * @Column(name="autoload", type="boolean", nullable=false)
     */
    private $autoload;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set value @param text $value */
    public function setValue($value){ $this->value = $value; }

    /** Get value @return text $value */
    public function getValue(){ return $this->value; }

    /** Set autoload @param boolean $autoload */
    public function setAutoload($autoload){ $this->autoload = $autoload; }

    /** Get autoload @return boolean $autoload */
    public function getAutoload(){ return $this->autoload; }
}