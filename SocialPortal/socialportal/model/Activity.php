<?php
namespace socialportal\model;

/**
 * Activity
 *
 * @Table(
 *	name="activity"
 * )
 * @Entity
 */
class Activity{
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
     * @Comment("sdfcribe blabla True chose")
     */
    private $userId;

    /**
     * @var string $component
     *
     * @Column(name="component", type="string", length=75, nullable=false, default="bidule")
     */
    private $component;

    /**
     * @var string $type
     *
     * @Column(name="type", type="string", length=75, nullable=true)
     */
    private $type;

    /**
     * @var text $action
     *
     * @Column(name="action", type="text", nullable=false)
     */
    private $action;

    /**
     * @var text $content
     *
     * @Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string $primaryLink
     *
     * @Column(name="primary_link", type="string", length=150, nullable=false, default="NULL")
     */
    private $primaryLink;

    /**
     * @var string $itemId
     *
     * @Column(name="item_id", type="string", length=75, nullable=false)
     */
    private $itemId;

    /**
     * @var string $secondaryItemId
     *
     * @Column(name="secondary_item_id", type="string", length=75, nullable=true)
     */
    private $secondaryItemId;

    /**
     * @var datetime $dateRecorded
     *
     * @Column(name="date_recorded", type="datetime", nullable=false)
     */
    private $dateRecorded;

    /**
     * @var boolean $hideSitewide
     *
     * @Column(name="hide_sitewide", type="boolean", nullable=true)
     */
    private $hideSitewide;

    /**
     * @var integer $mpttLeft
     *
     * @Column(name="mptt_left", type="integer", nullable=false)
     */
    private $mpttLeft;

    /**
     * @var integer $mpttRight
     *
     * @Column(name="mptt_right", type="integer", nullable=false, default="4")
     */
    private $mpttRight;

    public function __construct(){
        $this->component = 'bidule';
        $this->primaryLink = 'NULL';
        $this->mpttRight = '4';
        
    }
    
    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set component @param string $component */
    public function setComponent($component){ $this->component = $component; }

    /** Get component @return string $component */
    public function getComponent(){ return $this->component; }

    /** Set type @param string $type */
    public function setType($type){ $this->type = $type; }

    /** Get type @return string $type */
    public function getType(){ return $this->type; }

    /** Set action @param text $action */
    public function setAction($action){ $this->action = $action; }

    /** Get action @return text $action */
    public function getAction(){ return $this->action; }

    /** Set content @param text $content */
    public function setContent($content){ $this->content = $content; }

    /** Get content @return text $content */
    public function getContent(){ return $this->content; }

    /** Set primaryLink @param string $primaryLink */
    public function setPrimaryLink($primaryLink){ $this->primaryLink = $primaryLink; }

    /** Get primaryLink @return string $primaryLink */
    public function getPrimaryLink(){ return $this->primaryLink; }

    /** Set itemId @param string $itemId */
    public function setItemId($itemId){ $this->itemId = $itemId; }

    /** Get itemId @return string $itemId */
    public function getItemId(){ return $this->itemId; }

    /** Set secondaryItemId @param string $secondaryItemId */
    public function setSecondaryItemId($secondaryItemId){ $this->secondaryItemId = $secondaryItemId; }

    /** Get secondaryItemId @return string $secondaryItemId */
    public function getSecondaryItemId(){ return $this->secondaryItemId; }

    /** Set dateRecorded @param datetime $dateRecorded */
    public function setDateRecorded($dateRecorded){ $this->dateRecorded = $dateRecorded; }

    /** Get dateRecorded @return datetime $dateRecorded */
    public function getDateRecorded(){ return $this->dateRecorded; }

    /** Set hideSitewide @param boolean $hideSitewide */
    public function setHideSitewide($hideSitewide){ $this->hideSitewide = $hideSitewide; }

    /** Get hideSitewide @return boolean $hideSitewide */
    public function getHideSitewide(){ return $this->hideSitewide; }

    /** Set mpttLeft @param integer $mpttLeft */
    public function setMpttLeft($mpttLeft){ $this->mpttLeft = $mpttLeft; }

    /** Get mpttLeft @return integer $mpttLeft */
    public function getMpttLeft(){ return $this->mpttLeft; }

    /** Set mpttRight @param integer $mpttRight */
    public function setMpttRight($mpttRight){ $this->mpttRight = $mpttRight; }

    /** Get mpttRight @return integer $mpttRight */
    public function getMpttRight(){ return $this->mpttRight; }
}