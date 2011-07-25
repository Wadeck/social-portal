<?php

namespace socialportal\controller;

use core\Config;

use core\http\GetSettable;

use core\user\UserManager;

use core\user\UserHelper;

use socialportal\model\TopicVoteStats;

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

		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}

		if( $this->hasUserAlreadyVoted(true, $topicId, $userId) ){
			$this->frontController->addMessage(__('You have already voted for that topic'), 'error');
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
		}
		
		$vote = new VoteTopic();
		$vote->setTopicId($topicId);
		$vote->setUserId($userId);
		$vote->setDate($now);
		$this->em->persist($vote);
		if( !$this->em->flushSafe($vote) ){
			$this->frontController->addMessage(__('There was a problem during the vote process'), 'error');
		}

		$this->em->getRepository('TopicVoteStats')->incrementVote($topicId, 1);
		
		$this->putMarkInUser(true, $topicId, $userId);
		
		$this->frontController->addMessage(__('Your vote was taking into account, thank you'), 'correct');
		$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
	}
	
	/**
	 * @Nonce(votePost)
	 * @GetAttributes({postId, topicId, forumId})
	 */
	public function votePostAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get('postId');
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}

		if( $this->hasUserAlreadyVoted(false, $postId, $userId) ){
			$this->frontController->addMessage(__('You have already voted for that post'), 'error');
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId, 'postIdTarget' => $postId));
		}
		
		$vote = new VotePost();
		$vote->setPostId($postId);
		$vote->setUserId($userId);
		$vote->setDate($now);
		$this->em->persist($vote);
		if( !$this->em->flushSafe($vote) ){
			$this->frontController->addMessage(__('There was a problem during the vote process'), 'error');
		}
		
		$this->em->getRepository('PostVoteStats')->incrementVote($topicId, $postId, 1);
		
		$this->putMarkInUser(false, $postId, $userId);
		
		$this->frontController->addMessage(__('Your vote was taking into account, thank you'), 'correct');
		$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId, 'postIdTarget' => $postId));
	}

	private function hasUserAlreadyVoted($forTopic, $itemId, $userId){
		$session = $this->frontController->getRequest()->getSession();
		if( $this->retrieveFrom($session, false, $forTopic, $itemId, $userId) ){
			return true;
		}
		$cookies = $this->frontController->getRequest()->cookies;
		if( $this->retrieveFrom($cookies, true, $forTopic, $itemId, $userId) ){
			return true;
		}
		return false;
	}
	
	private function putMarkInUser($forTopic, $itemId, $userId){
//		$session = $this->frontController->getRequest()->getSession();
//		$this->putIn($session, false, $forTopic, $itemId, $userId);
		$cookies = $this->frontController->getRequest()->cookies;
		$this->putIn($cookies, true, $forTopic, $itemId, $userId);
	}
	
	private function retrieveFrom(GetSettable $bag, $withSerialization, $forTopic, $itemId, $userId){
		if( $withSerialization ){
			$votes = $bag->get('_votes', false);
			if(false === $votes){
				$votes = array();
			}else{
				$votes = unserialize($votes);
			}
		}else{
			$votes = $bag->get('_votes', array());
		}
		
		$item = false;
		foreach($votes as $vi){
			$item = isset($vi['item']) ? $vi['item'] : false;
			if($item == $itemId){
				if(isset($vi['topic']) && $vi['topic'] == $forTopic){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * _votes = [v1, v2, vi, ...]
	 * vi = [date=time(), item=int, topic=boolean]
	 * 
	 * @param GetSettable $bag
	 * @param boolean $forTopic
	 * @param int $itemId
	 * @param int $userId
	 */
	private function putIn(GetSettable $bag, $withSerialization, $forTopic, $itemId, $userId){
		if( $withSerialization ){
			$votes = $bag->get('_votes', false);
			if(false === $votes){
				$votes = array();
			}else{
				$votes = unserialize($votes);
			}
		}else{
			$votes = $bag->get('_votes', array());
		}
		
		// normally the item is not present, so we add it simply, no care, the tests are done with retrieveFrom
		$vf = array('date' => time(), 'item' => $itemId, 'topic' => $forTopic);
		$votes[] = $vf;
		//TODO remove after debug
//		$maxSize = Config::get('cookie_session_vote_memory_size', 50);
//		$chunk = Config::get('cookie_session_vote_memory_chunk', 10);
		$maxSize = 2;
		$chunk = 3;
		$size = count($votes);
		if($size > $maxSize + $chunk){
			array_splice($votes, 0, $chunk);
		}
		
		if($withSerialization){
			$votes = serialize($votes);
		}
		$bag->set('_votes', $votes);
	}
}