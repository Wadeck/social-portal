<?php
namespace socialportal\model;

/**
 * MessagesRecipients
 *
 * @Table(
 *	name="messages_recipients"
 * )
 * @Entity
 */
class MessagesRecipients{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $userId
     *
     * @Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var bigint $threadId
     *
     * @Column(name="thread_id", type="bigint", nullable=false)
     */
    private $threadId;

    /**
     * @var integer $unreadCount
     *
     * @Column(name="unread_count", type="integer", nullable=false)
     */
    private $unreadCount;

    /**
     * @var boolean $senderOnly
     *
     * @Column(name="sender_only", type="boolean", nullable=false)
     */
    private $senderOnly;

    /**
     * @var boolean $isDeleted
     *
     * @Column(name="is_deleted", type="boolean", nullable=false)
     */
    private $isDeleted;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set threadId @param bigint $threadId */
    public function setThreadId($threadId){ $this->threadId = $threadId; }

    /** Get threadId @return bigint $threadId */
    public function getThreadId(){ return $this->threadId; }

    /** Set unreadCount @param integer $unreadCount */
    public function setUnreadCount($unreadCount){ $this->unreadCount = $unreadCount; }

    /** Get unreadCount @return integer $unreadCount */
    public function getUnreadCount(){ return $this->unreadCount; }

    /** Set senderOnly @param boolean $senderOnly */
    public function setSenderOnly($senderOnly){ $this->senderOnly = $senderOnly; }

    /** Get senderOnly @return boolean $senderOnly */
    public function getSenderOnly(){ return $this->senderOnly; }

    /** Set isDeleted @param boolean $isDeleted */
    public function setIsDeleted($isDeleted){ $this->isDeleted = $isDeleted; }

    /** Get isDeleted @return boolean $isDeleted */
    public function getIsDeleted(){ return $this->isDeleted; }
}