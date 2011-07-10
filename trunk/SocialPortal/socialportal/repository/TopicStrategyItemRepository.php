<?php
namespace socialportal\repository;
use socialportal\model\TopicStrategyItem;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicStrategyItemRepository extends EntityRepository {
	/**
	 * @param int $strategyId Id of the specific topic
	 * @return Array of TopicStrategyItem
	 */
	public function findAllItems($strategyId) {
		$query = $this->_em->createQuery( 'SELECT tsi FROM TopicStrategyItem tsi WHERE tsi.topic = :sid AND tsi.isDeleted = 0' );
		$query->setParameter( 'sid', $strategyId );
		$result = $query->getResult();
		return $result;
	}
}