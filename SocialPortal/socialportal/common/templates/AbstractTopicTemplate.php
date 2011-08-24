<?php

namespace socialportal\common\templates;

use socialportal\model\ForumMeta;

use core\tools\Utils;

use core\user\UserRoles;

use core\security\TopicToolSecurity;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use core\user\UserHelper;

/**
 * Display only the topic part, i.e. the upper part of the topic view
 */
abstract class AbstractTopicTemplate implements iInsertable {
	/** @var EntityManager */
	protected $em;
	/** @var FrontController */
	protected $front;
	/** @var Topic */
	protected $topic;
	/** @var string Url to the topic, page 1, default number of post per page */
	protected $permalink;
	
	protected $supportVote = true;
	
	protected $highlight = false;
	
	public function setFrontController(FrontController $front){
		$this->front = $front;
	}
	
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	/**
	 * @param $value boolean
	 */
	public function setHighlightTopic($value){
		$this->highlight = $value;
	}
	
	/**
	 * @param CustomTopic $topic
	 */
	public function setTopic($topic){
		$this->topic = $topic;
	}
	
	public function setPermalink($permalink){
		$this->permalink = $permalink;
	}
	
	public function insert(){
		if(!$this->topic){
			return;
		}
		$base = $this->topic->getTopicbase();
		$topicId = $base->getId();
		// will not load the forum, but only forumProxy, with id
		$forum = $base->getForum();
		$forumId = $forum->getId();
		$numPosts = $base->getNumPosts();
		$title = $base->getTitle();
		$author = $base->getPoster();
		$userHelper = new UserHelper($this->front);
		$userHelper->setCurrentUser($author);
		
		//TODO implement that in the future
//		$tagRepo = $this->em->getRepository('TermRelation');
//		$tags = $tagRepo->getAllTags($topicId);
		$tags = array();
		
//		$isAdmin = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$admin_role);
		$isModo = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$currentUserId = $this->front->getCurrentUser()->getId();
		$isFullUser = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role);
		?>
		<!-- Topic initial post -->
			<div class="rounded-box<?php if($this->highlight) echo ' highlight-topic' ?>" id="topic">
				<table id="topic-<?php echo $topicId; ?>">
					<tr>
						<!-- row of avatar box -->	
						<td class="avatar-box"><!-- cell of avatar box -->
							<?php $userHelper->insertLinkToProfile(); ?>
							<?php $userHelper->insertAvatar(75); ?>
						</td>
						
						<!-- cell of title / date / tags -->
						<td id="topic-meta-box">
							<?php if($this->supportVote): ?>
								<div id="vote-box">
									<a class="button"
										href="<?php $this->front->getViewHelper()->insertHrefWithNonce('voteTopic', 'Vote', 'voteTopic', array('topicId'=>$topicId, 'forumId' => $forumId) );?>"
										><?php echo __('I like'); ?></a>
								</div>
							<?php endif; ?>
							<span id="topic-title">
								<?php /*echo "$title ($numPosts)";*/ echo "$title"; ?>
							</span>

							<?php if( $tags ): ?>
								<span id="topic-tags">
									<?php echo __( 'Tags: '); ?>
									<?php foreach($tags as $tag):
									// TODO implement search
										$link = $this->front->getViewHelper()->createHref('Search', 'byTag', array($tag->getId()));
										$tagName = $tag->getName()
									?>
										<a class="button"
											href="<?php echo $link; ?>"
											title="<?php echo $tagName ; ?>"
											rel='tag' >
											<?php echo $tagName ; ?>
										</a> &nbsp;
									<?php endforeach; ?>
								</span>
							<?php endif; ?>
							<span id="topic-content">
								<?php $this->insertTopicBody($this->topic); ?>
							</span>
						</td>
					</tr>
					<tr><!-- row of admin tool -->
						<td colspan="2" id="topic-bottom">
							<span id="topic-tools">
							<?php 
							//TODO change this when capabilities will be implemented
							if( $author->getId() === $currentUserId || $isModo ){
								// only for author / admin/moderator
								$this->insertEditTool($this->topic);
							}
							if( $isModo ){
								$this->insertModeratorTools($this->topic);
							}
							if( $isFullUser ){
								$this->insertFullUserTools($this->topic, $forum);	
							}
							// for everybody 
							$this->insertUserTools($this->topic);
							?>
							</span>

							<!-- publication date -->
							<span id="topic-date">
								<!-- publication date -->
								<?php echo Utils::getDataSince($base->getStartTime()); ?>
							</span>
						</td>
					</tr>
				</table>
			</div><!-- end of first post -->
		<?php
	}
	
	/** Displayed the body of the topic in the internal process */
	protected abstract function insertTopicBody($topic);
	
	protected function insertEditTool($topic){
		$base = $topic->getTopicbase();
		$customTypeId = $base->getCustomType();
		$topicId = $base->getId();
		$forumId = $base->getForum()->getId();
		$viewHelper = $this->front->getViewHelper();
		?>
		<a href="<?php $viewHelper->insertHrefWithNonce('displayForm', 'Topic', 'displayForm', array('typeId'=>$customTypeId, 'forumId'=>$forumId,'topicId'=> $topicId)); ?>"
			title="<?php echo __( 'Modify the topic content or title' ); ?>"><?php echo __('Edit'); ?></a>
		&nbsp;|&nbsp;
	<?php
	}	
	
	/**
	 * Only for admin/moderator/author
	 * Insert all the administrative stuff
	 * edit / stick / close / delete
	 */
	protected function insertModeratorTools($topic){
		$base = $topic->getTopicbase();
		$customTypeId = $base->getCustomType();
		$topicId = $base->getId();
		$forumId = $base->getForum()->getId();
		$viewHelper = $this->front->getViewHelper();
		$typeId = $base->getCustomType();
		$link = $viewHelper->createHrefWithNonce('move', 'Topic', 'move', array( 'forumIdFrom' => $forumId, 'topicId' => $topicId, 'forumIdTo' => '%forumIdTo%' ));
		// we retrieve all the forums that could receive that topic, except the current one, if nothing found, we'll display a message instead of combobox
		$this->insertMoveTool($link, $typeId, $forumId);
		?>
		
		&nbsp;|&nbsp;
		<?php if($base->getIsSticky()): ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('unstickTopic', 'Topic', 'unstick', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Unstick the topic, it will be shown like other topics by order of last modification' ); ?>"><?php echo __('Unstick'); ?></a>
		<?php else: ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('stickTopic', 'Topic', 'stick', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Stick the topic at the top of the forum' ); ?>"><?php echo __('Stick'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
		<?php if($base->getIsOpen()): ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('closeTopic', 'Topic', 'close', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Close the topic, so that no other comments could be left, and so the topic falls in the forget' ); ?>"><?php echo __('Close'); ?></a>
		<?php else: ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('openTopic', 'Topic', 'open', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Open the topic, it will re accept comments' ); ?>"><?php echo __('Open'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
		<?php if($base->getIsDeleted()): ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('undeleteTopic', 'Topic', 'undelete', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Restore the topic from the database trash' ); ?>"><?php echo __('Undelete'); ?></a>
		<?php else: ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('deleteTopic', 'Topic', 'delete', array('forumId'=>$forumId, 'topicId'=>$topicId)); ?>"
				title="<?php echo __( 'Delete the topic, stay in database but it is no more displayed' ); ?>"<?php $viewHelper->insertConfirmLink(__('Do you really want to delete this topic ?'));?>><?php echo __('Delete'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
	<?php
	}
	
	protected function insertMoveTool($link, $typeId, $currentForumId){
		$this->front->getViewHelper()->addJavascriptFile( 'jquery.js' );
		$this->front->getViewHelper()->addJavascriptFile( 'move_topic.js' );
		
		// retrieve all forums
		$potentialForums = $this->em->getRepository('ForumMeta')->findAllForumAcceptableTopics();
		// filter all that cannot receive the topic
		$potentialForums = array_filter($potentialForums, function( ForumMeta $meta ) use ($typeId, $currentForumId){
			if( $currentForumId === $meta->getForumId() ){
				// no double
				return false;
			}
			$metas = unserialize( $meta->getMetaValue() );
			if(in_array($typeId, $metas)){
				return true;
			}else{
				return false;
			}
		});
		
		$count = count($potentialForums);
		if($count){
			$em = $this->em;
			array_walk($potentialForums, function( ForumMeta &$item, $var ) use ($link, $em) {
				$forumId = $item->getForumId();
				$info['forumId'] = $forumId;
				$info['link'] = strtr($link, array('%forumIdTo%' => $forumId) );
				$forum = $em->find('Forum', $forumId);
				if($forum){
					$info['title'] = $forum->getName();
				}else{
					$info['title'] = "Not found ($forumId)";
				}
				
				$item = $info;
			});
		}
		$targetId = 'hiddenMove';
		if( 0 === $count ) {
			// only a message
			?>
			<span style="display: none;" id="<?php echo $targetId; ?>" class="move_message">
				<?php echo __('No compatible forum found'); ?>
			</span> 
			<?php
		}else if( 1 === $count ){
			// only a link
			$info = array_shift($potentialForums);
			?>
			<a style="display: none;" id="<?php echo $targetId; ?>" href="<?php echo $info['link']; ?>" title="<?php echo $info['title'] ; ?>"><?php echo __('Move to %forum_name%', array('%forum_name%' => $info['title'] )) ; ?></a>
			<?php
		}else{
			// a combo box
			$this->insertComboBoxMove($potentialForums, $targetId);
		}
		
		?>
		<a href="#" title="<?php echo __('Move'); ?>" onClick="hideMe(this); displayIt('<?php echo $targetId; ?>'); return false;"><?php echo __('Move'); ?></a>
	<?php
	}
	
	private function insertComboBoxMove( array $potentialForums, $targetId){
		echo '<select style="display: none;" id="' . $targetId . '" onChange="onChange(this)">';
		echo '<option value="0">' . __( 'Select the forum' ) . '</OPTION>';
		foreach($potentialForums as $info){
			echo '<option value="' . $info['link']. '">' . $info['title'] . '</OPTION>';
		}
		echo '</select>';
	}
	
	/**
	 * For full user
	 * report / -quote- / permalink 
	 */
	protected function insertFullUserTools($topic, $forum){
		$topicBase = $topic->getTopicBase();
		$topicId = $topicBase->getId();
		$forumId = $forum->getId();
		
		// check first if the current user has already reported the topic
		$result = $this->em->getRepository('ReportTopic')->findBy( array( "topicId" => $topicId, "userId" => $this->front->getCurrentUser()->getId(), "isDeleted" => 0 ) );
		if(!$result){
			// this is not the case, so we propose to him the tool to report
		?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce( 'reportTopic', 'Report', 'reportTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) ); ?>"
			title="<?php echo __( 'Report abuse to the moderators' ); ?>"><?php echo __('Report'); ?></a>
		&nbsp;|&nbsp;
		<?php } else {
			// the user has already reported, we let him the possibility to remove the report
			$reportIdRequest=$this->em->getRepository('ReportTopic')->findBy( array( "topicId" => $topicId, "userId" => $this->front->getCurrentUser()->getId(), "isDeleted" => 0 ) );
			$reportId=$reportIdRequest[0]->getId();
		?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce( 'removeReportTopic', 'Report', 'removeReportTopic', array('reportId' => $reportId, 'topicId' => $topicId, 'forumId' => $forumId ) ); ?>"
			title="<?php echo __( 'Remove report abuse to the moderators' ); ?>"><?php echo __('Remove report'); ?></a>
			&nbsp;|&nbsp;
		<?php } ?>
		<!--<a href="#comment" onClick="onQuoteClick(); return true"
			title="<?php //echo __( 'Quote this topic in your answer' ); ?>"><?php //echo __('Quote'); ?></a>
		&nbsp;|&nbsp;
		--><a href="<?php echo $this->permalink. '#topic-'. $topic->getId(); ?>"
			title="<?php echo __( 'Permanent link to this post' ); ?>">#</a>
								
	<?php
	}	
	
	/**
	 * For everybody (included anonymous)
	 */
	protected function insertUserTools($topic){
		return;
	}	
}