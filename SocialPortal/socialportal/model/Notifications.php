<?php
namespace socialportal\model;

/**
 * Notifications
 *
 * @Table(
 *	name="notifications"
 * )
 * @Entity
 */
class Notifications{
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
     * @var bigint $itemId
     *
     * @Column(name="item_id", type="bigint", nullable=false)
     */
    private $itemId;

    /**
     * @var bigint $secondaryItemId
     *
     * @Column(name="secondary_item_id", type="bigint", nullable=true)
     */
    private $secondaryItemId;

    /**
     * @var string $componentName
     *
     * @Column(name="component_name", type="string", length=75, nullable=false)
     */
    private $componentName;

    /**
     * @var string $componentAction
     *
     * @Column(name="component_action", type="string", length=75, nullable=false)
     */
    private $componentAction;

    /**
     * @var datetime $dateNotified
     *
     * @Column(name="date_notified", type="datetime", nullable=false)
     */
    private $dateNotified;

    /**
     * @var boolean $isNew
     *
     * @Column(name="is_new", type="boolean", nullable=false)
     */
    private $isNew;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set itemId @param bigint $itemId */
    public function setItemId($itemId){ $this->itemId = $itemId; }

    /** Get itemId @return bigint $itemId */
    public function getItemId(){ return $this->itemId; }

    /** Set secondaryItemId @param bigint $secondaryItemId */
    public function setSecondaryItemId($secondaryItemId){ $this->secondaryItemId = $secondaryItemId; }

    /** Get secondaryItemId @return bigint $secondaryItemId */
    public function getSecondaryItemId(){ return $this->secondaryItemId; }

    /** Set componentName @param string $componentName */
    public function setComponentName($componentName){ $this->componentName = $componentName; }

    /** Get componentName @return string $componentName */
    public function getComponentName(){ return $this->componentName; }

    /** Set componentAction @param string $componentAction */
    public function setComponentAction($componentAction){ $this->componentAction = $componentAction; }

    /** Get componentAction @return string $componentAction */
    public function getComponentAction(){ return $this->componentAction; }

    /** Set dateNotified @param datetime $dateNotified */
    public function setDateNotified($dateNotified){ $this->dateNotified = $dateNotified; }

    /** Get dateNotified @return datetime $dateNotified */
    public function getDateNotified(){ return $this->dateNotified; }

    /** Set isNew @param boolean $isNew */
    public function setIsNew($isNew){ $this->isNew = $isNew; }

    /** Get isNew @return boolean $isNew */
    public function getIsNew(){ return $this->isNew; }
}