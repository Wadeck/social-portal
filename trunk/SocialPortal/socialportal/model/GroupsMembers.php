<?php
namespace socialportal\model;

/**
 * GroupsMembers
 *
 * @Table(
 *	name="groups_members"
 * )
 * @Entity
 */
class GroupsMembers{
    /**
     * @var bigint $id
     *
     * @Column(name="id", type="bigint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bigint $groupId
     *
     * @Column(name="group_id", type="bigint", nullable=false)
     */
    private $groupId;

    /**
     * @var bigint $userId
     *
     * @Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var bigint $inviterId
     *
     * @Column(name="inviter_id", type="bigint", nullable=false)
     */
    private $inviterId;

    /**
     * @var boolean $isAdmin
     *
     * @Column(name="is_admin", type="boolean", nullable=false)
     */
    private $isAdmin;

    /**
     * @var boolean $isMod
     *
     * @Column(name="is_mod", type="boolean", nullable=false)
     */
    private $isMod;

    /**
     * @var string $userTitle
     *
     * @Column(name="user_title", type="string", length=100, nullable=false)
     */
    private $userTitle;

    /**
     * @var datetime $dateModified
     *
     * @Column(name="date_modified", type="datetime", nullable=false)
     */
    private $dateModified;

    /**
     * @var text $comments
     *
     * @Column(name="comments", type="text", nullable=false)
     */
    private $comments;

    /**
     * @var boolean $isConfirmed
     *
     * @Column(name="is_confirmed", type="boolean", nullable=false)
     */
    private $isConfirmed;

    /**
     * @var boolean $isBanned
     *
     * @Column(name="is_banned", type="boolean", nullable=false)
     */
    private $isBanned;

    /**
     * @var boolean $inviteSent
     *
     * @Column(name="invite_sent", type="boolean", nullable=false)
     */
    private $inviteSent;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set groupId @param bigint $groupId */
    public function setGroupId($groupId){ $this->groupId = $groupId; }

    /** Get groupId @return bigint $groupId */
    public function getGroupId(){ return $this->groupId; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set inviterId @param bigint $inviterId */
    public function setInviterId($inviterId){ $this->inviterId = $inviterId; }

    /** Get inviterId @return bigint $inviterId */
    public function getInviterId(){ return $this->inviterId; }

    /** Set isAdmin @param boolean $isAdmin */
    public function setIsAdmin($isAdmin){ $this->isAdmin = $isAdmin; }

    /** Get isAdmin @return boolean $isAdmin */
    public function getIsAdmin(){ return $this->isAdmin; }

    /** Set isMod @param boolean $isMod */
    public function setIsMod($isMod){ $this->isMod = $isMod; }

    /** Get isMod @return boolean $isMod */
    public function getIsMod(){ return $this->isMod; }

    /** Set userTitle @param string $userTitle */
    public function setUserTitle($userTitle){ $this->userTitle = $userTitle; }

    /** Get userTitle @return string $userTitle */
    public function getUserTitle(){ return $this->userTitle; }

    /** Set dateModified @param datetime $dateModified */
    public function setDateModified($dateModified){ $this->dateModified = $dateModified; }

    /** Get dateModified @return datetime $dateModified */
    public function getDateModified(){ return $this->dateModified; }

    /** Set comments @param text $comments */
    public function setComments($comments){ $this->comments = $comments; }

    /** Get comments @return text $comments */
    public function getComments(){ return $this->comments; }

    /** Set isConfirmed @param boolean $isConfirmed */
    public function setIsConfirmed($isConfirmed){ $this->isConfirmed = $isConfirmed; }

    /** Get isConfirmed @return boolean $isConfirmed */
    public function getIsConfirmed(){ return $this->isConfirmed; }

    /** Set isBanned @param boolean $isBanned */
    public function setIsBanned($isBanned){ $this->isBanned = $isBanned; }

    /** Get isBanned @return boolean $isBanned */
    public function getIsBanned(){ return $this->isBanned; }

    /** Set inviteSent @param boolean $inviteSent */
    public function setInviteSent($inviteSent){ $this->inviteSent = $inviteSent; }

    /** Get inviteSent @return boolean $inviteSent */
    public function getInviteSent(){ return $this->inviteSent; }
}