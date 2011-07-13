<?php

namespace socialportal\controller;

use socialportal\model\VotePost;

use core\FrontController;

use core\AbstractController;
use socialportal\model\VoteTopic ;
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

		$vote = new VoteTopic();
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
	
	/**
	 * @Nonce(votePost)
	 * @GetAttributes({postId, topicId, forumId})
	 */
	public function votePostcAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get('postId');
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		
		$now = $this->frontController->getRequest()->getRequestDateTime();

		$vote = new VotePost();
		$vote->setPostId($postId);
		$vote->setUserId($this->frontController->getCurrentUser()->getId());
		$vote->setDate($now);
		$this->em->persist($vote);
		if( !$this->em->flushSafe($vote) ){
			$this->frontController->addMessage(__('There was a problem during the vote process'), 'error');
		}else{
			$this->frontController->addMessage(__('Your vote was taking into account, thank you'), 'correct');
		}
		$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId, 'postIdTarget' => $postId));
	}


}