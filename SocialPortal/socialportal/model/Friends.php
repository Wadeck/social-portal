<?php
namespace socialportal\model;

/**
 * Friends
 *
 * @Table(
 *	name="friends"
 * )
 * @Entity
 */
class Friends{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $initiatorUserId
     *
     * @Column(name="initiator_user_id", type="bigint", nullable=false)
     */
    private $initiatorUserId;

    /**
     * @var bigint $friendUserId
     *
     * @Column(name="friend_user_id", type="bigint", nullable=false)
     */
    private $friendUserId;

    /**
     * @var boolean $isConfirmed
     *
     * @Column(name="is_confirmed", type="boolean", nullable=true)
     */
    private $isConfirmed;

    /**
     * @var boolean $isLimited
     *
     * @Column(name="is_limited", type="boolean", nullable=true)
     */
    private $isLimited;

    /**
     * @var datetime $dateCreated
     *
     * @Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set initiatorUserId @param bigint $initiatorUserId */
    public function setInitiatorUserId($initiatorUserId){ $this->initiatorUserId = $initiatorUserId; }

    /** Get initiatorUserId @return bigint $initiatorUserId */
    public function getInitiatorUserId(){ return $this->initiatorUserId; }

    /** Set friendUserId @param bigint $friendUserId */
    public function setFriendUserId($friendUserId){ $this->friendUserId = $friendUserId; }

    /** Get friendUserId @return bigint $friendUserId */
    public function getFriendUserId(){ return $this->friendUserId; }

    /** Set isConfirmed @param boolean $isConfirmed */
    public function setIsConfirmed($isConfirmed){ $this->isConfirmed = $isConfirmed; }

    /** Get isConfirmed @return boolean $isConfirmed */
    public function getIsConfirmed(){ return $this->isConfirmed; }

    /** Set isLimited @param boolean $isLimited */
    public function setIsLimited($isLimited){ $this->isLimited = $isLimited; }

    /** Get isLimited @return boolean $isLimited */
    public function getIsLimited(){ return $this->isLimited; }

    /** Set dateCreated @param datetime $dateCreated */
    public function setDateCreated($dateCreated){ $this->dateCreated = $dateCreated; }

    /** Get dateCreated @return datetime $dateCreated */
    public function getDateCreated(){ return $this->dateCreated; }
}