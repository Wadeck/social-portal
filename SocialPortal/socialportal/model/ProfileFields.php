<?php
namespace socialportal\model;

/**
 * ProfileFields
 *
 * @Table(
 *	name="profile_fields"
 * )
 * @Entity
 */
class ProfileFields{
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
     * @var bigint $parentId
     *
     * @Column(name="parent_id", type="bigint", nullable=false)
     */
    private $parentId;

    /**
     * @var string $type
     *
     * @Column(name="type", type="string", length=150, nullable=false)
     */
    private $type;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var boolean $isRequired
     *
     * @Column(name="is_required", type="boolean", nullable=false)
     */
    private $isRequired;

    /**
     * @var boolean $isDefaultOption
     *
     * @Column(name="is_default_option", type="boolean", nullable=false)
     */
    private $isDefaultOption;

    /**
     * @var bigint $fieldOrder
     *
     * @Column(name="field_order", type="bigint", nullable=false)
     */
    private $fieldOrder;

    /**
     * @var bigint $optionOrder
     *
     * @Column(name="option_order", type="bigint", nullable=false)
     */
    private $optionOrder;

    /**
     * @var string $orderBy
     *
     * @Column(name="order_by", type="string", length=15, nullable=false)
     */
    private $orderBy;

    /**
     * @var boolean $canDelete
     *
     * @Column(name="can_delete", type="boolean", nullable=false)
     */
    private $canDelete;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set groupId @param bigint $groupId */
    public function setGroupId($groupId){ $this->groupId = $groupId; }

    /** Get groupId @return bigint $groupId */
    public function getGroupId(){ return $this->groupId; }

    /** Set parentId @param bigint $parentId */
    public function setParentId($parentId){ $this->parentId = $parentId; }

    /** Get parentId @return bigint $parentId */
    public function getParentId(){ return $this->parentId; }

    /** Set type @param string $type */
    public function setType($type){ $this->type = $type; }

    /** Get type @return string $type */
    public function getType(){ return $this->type; }

    /** Set name @param string $name */
    public function setName($name){ $this->name = $name; }

    /** Get name @return string $name */
    public function getName(){ return $this->name; }

    /** Set description @param text $description */
    public function setDescription($description){ $this->description = $description; }

    /** Get description @return text $description */
    public function getDescription(){ return $this->description; }

    /** Set isRequired @param boolean $isRequired */
    public function setIsRequired($isRequired){ $this->isRequired = $isRequired; }

    /** Get isRequired @return boolean $isRequired */
    public function getIsRequired(){ return $this->isRequired; }

    /** Set isDefaultOption @param boolean $isDefaultOption */
    public function setIsDefaultOption($isDefaultOption){ $this->isDefaultOption = $isDefaultOption; }

    /** Get isDefaultOption @return boolean $isDefaultOption */
    public function getIsDefaultOption(){ return $this->isDefaultOption; }

    /** Set fieldOrder @param bigint $fieldOrder */
    public function setFieldOrder($fieldOrder){ $this->fieldOrder = $fieldOrder; }

    /** Get fieldOrder @return bigint $fieldOrder */
    public function getFieldOrder(){ return $this->fieldOrder; }

    /** Set optionOrder @param bigint $optionOrder */
    public function setOptionOrder($optionOrder){ $this->optionOrder = $optionOrder; }

    /** Get optionOrder @return bigint $optionOrder */
    public function getOptionOrder(){ return $this->optionOrder; }

    /** Set orderBy @param string $orderBy */
    public function setOrderBy($orderBy){ $this->orderBy = $orderBy; }

    /** Get orderBy @return string $orderBy */
    public function getOrderBy(){ return $this->orderBy; }

    /** Set canDelete @param boolean $canDelete */
    public function setCanDelete($canDelete){ $this->canDelete = $canDelete; }

    /** Get canDelete @return boolean $canDelete */
    public function getCanDelete(){ return $this->canDelete; }
}