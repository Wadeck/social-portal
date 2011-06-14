<?php
namespace socialportal\model;

/**
 * TermRelation
 *
 * @Table(
 *	name="term_relation"
 * )
 * @Entity
 */
class TermRelation{
    /**
     * @var bigint $objectId
     *
     * @Column(name="object_id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $objectId;

    /**
     * @var bigint $termId
     *
     * @Column(name="term_id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $termId;

    /**
     * @var bigint $userId
     *
     * @Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var integer $termOrder
     *
     * @Column(name="term_order", type="integer", nullable=false)
     */
    private $termOrder;


    /** Set objectId @param bigint $objectId */
    public function setObjectId($objectId){ $this->objectId = $objectId; }

    /** Get objectId @return bigint $objectId */
    public function getObjectId(){ return $this->objectId; }

    /** Set termId @param bigint $termId */
    public function setTermId($termId){ $this->termId = $termId; }

    /** Get termId @return bigint $termId */
    public function getTermId(){ return $this->termId; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set termOrder @param integer $termOrder */
    public function setTermOrder($termOrder){ $this->termOrder = $termOrder; }

    /** Get termOrder @return integer $termOrder */
    public function getTermOrder(){ return $this->termOrder; }
}