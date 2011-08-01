<?php
namespace socialportal\model;

/**
 * ChartMood
 *
 * @Table(
 *	name="chart_mood"
 * )
 * @Entity(repositoryClass="ChartMoodRepository")
 */
class ChartMood{
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
     * @var datetime $date
     *
     * @Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var smallint $item
     *
     * @Column(name="item", type="smallint", nullable=false)
     */
    private $item;


    /** Get id @return bigint $id */
    public function getId(){ return $this->id; }

    /** Set userId @param bigint $userId */
    public function setUserId($userId){ $this->userId = $userId; }

    /** Get userId @return bigint $userId */
    public function getUserId(){ return $this->userId; }

    /** Set date @param datetime $date */
    public function setDate($date){ $this->date = $date; }

    /** Get date @return datetime $date */
    public function getDate(){ return $this->date; }

    /** Set item @param smallint $item */
    public function setItem($item){ $this->item = $item; }

    /** Get item @return smallint $item */
    public function getItem(){ return $this->item; }
}