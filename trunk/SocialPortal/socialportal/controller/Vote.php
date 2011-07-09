<?php

namespace socialportal\controller;

use core\FrontController;

use core\AbstractController;
use socialportal\model\VoteTopic as VoteEntity;
class Vote extends AbstractController {
	
	/**
	 * @Nonce(voteTopic)
	 * @GetAttributes({topicId, forumId})
	 */
	public function voteTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		
		$now = $this->frontController->getRequest()->getRequestDateTime();

		$vote = new VoteEntity();
		$vote->setTopicId($topicId);
		$vote->setUserId($this->frontController->getCurrentUser()->getId());
		$vote->setDate($now);
		$this->em->persist($vote);
		if( !$this->em->flushSafe($vote) ){
			$this->frontController->addMessage(__('There was a problem during the vote process'), 'error');
		}else{
			$this->frontController->addMessage(__('Your vote was taking into account, thank you'), 'correct');
		}
		
		$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
	}


}