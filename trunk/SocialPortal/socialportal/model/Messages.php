<?php
namespace socialportal\model;

/**
 * Messages
 *
 * @Table(
 *	name="messages"
 * )
 * @Entity
 */
class Messages{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $threadId
     *
     * @Column(name="thread_id", type="bigint", nullable=false)
     */
    private $threadId;

    /**
     * @var bigint $senderId
     *
     * @Column(name="sender_id", type="bigint", nullable=false)
     */
    private $senderId;

    /**
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=200, nullable=false)
     */
    private $subject;

    /**
     * @var text $message
     *
     * @Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var datetime $dateSent
     *
     * @Column(name="date_sent", type="datetime", nullable=false)
     */
    private $dateSent;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set threadId @param bigint $threadId */
    public function setThreadId($threadId){ $this->threadId = $threadId; }

    /** Get threadId @return bigint $threadId */
    public function getThreadId(){ return $this->threadId; }

    /** Set senderId @param bigint $senderId */
    public function setSenderId($senderId){ $this->senderId = $senderId; }

    /** Get senderId @return bigint $senderId */
    public function getSenderId(){ return $this->senderId; }

    /** Set subject @param string $subject */
    public function setSubject($subject){ $this->subject = $subject; }

    /** Get subject @return string $subject */
    public function getSubject(){ return $this->subject; }

    /** Set message @param text $message */
    public function setMessage($message){ $this->message = $message; }

    /** Get message @return text $message */
    public function getMessage(){ return $this->message; }

    /** Set dateSent @param datetime $dateSent */
    public function setDateSent($dateSent){ $this->dateSent = $dateSent; }

    /** Get dateSent @return datetime $dateSent */
    public function getDateSent(){ return $this->dateSent; }
}