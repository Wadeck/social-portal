<?php
namespace socialportal\model;

/**
 * MessagesNotices
 *
 * @Table(
 *	name="messages_notices"
 * )
 * @Entity
 */
class MessagesNotices{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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

    /**
     * @var boolean $isActive
     *
     * @Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

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

    /** Set isActive @param boolean $isActive */
    public function setIsActive($isActive){ $this->isActive = $isActive; }

    /** Get isActive @return boolean $isActive */
    public function getIsActive(){ return $this->isActive; }
}