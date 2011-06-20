<?php

namespace core\topics\templates;

use core\tools\Utils;

use core\user\UserRoles;

use core\security\TopicToolSecurity;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use core\user\UserHelper;

use core\topics\templates\iTopicTemplate;

/**
 * Display only the topic part, i.e. the upper part of the topic view
 */
abstract class AbstractTopicTemplate implements iTopicTemplate {
	/** @var EntityManager */
	protected $em;
	/** @var FrontController */
	protected $front;
	
	public function setFrontController(FrontController $front){
		$this->front = $front;
	}
	
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	public function insertTopic($topic){
		if(!$topic){
			return;
		}
		$base = $topic->getTopicbase();
		$topicId = $base->getId();
		$numPosts = $base->getNumPosts();
		$title = $base->getTitle();
		$author = $base->getPoster();
		$userHelper = new UserHelper();
		$userHelper->setCurrentUser($author);
		$tagRepo = $this->em->getRepository('TermRelation');
		$tags = $tagRepo->getAllTags($topicId);
		?>
		<!-- Topic initial post -->
			<div class="rounded-box-topic" id="topic">
				<table id="topic-<?php echo $topicId; ?>">
					<tr>
						<!-- row of avatar box -->	
						<td class="avatar-box"><!-- cell of avatar box -->
							<?php $userHelper->insertLinkToProfile(); ?>
							<?php $userHelper->insertAvatar(60); ?>
						</td>
						
						<!-- cell of title / date / tags -->
						<td id="topic-meta-box">
							<div id="topic-title">
								<h3><?php echo "$title ($numPosts)"; ?></h3>
							</div>

							<?php if( !$tags ): ?>
								<div id="topic-tags">
									<?php echo __( 'Tags: '); ?>
									<?php foreach($tags as $tag):
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
								</div>
							<?php endif; ?>
						</td>
					</tr>
					<tr><!-- row of content -->
						<td colspan="2" id="topic-content">
							<!-- insert content here -->
							<?php $this->insertTopicBody($topic); ?>
						</td>
					</tr>
					<tr><!-- row of admin tool -->
						<td colspan="2" id="topic-bottom">
							<span id="topic-tools">
							<?php if($author->getId() === $this->front->getCurrentUser()->getId() || $this->front->getViewHelper()->currentUserIs(UserRoles::$admin_role)){
								$this->insertAdminTools($topic);//TODO change this when capabilities will be implemented
							}
							$this->insertUserTools($topic);
							?>
							</span>

							<!-- publication date -->
							<span id="topic-date">
								<!-- publication date -->
								<?php Utils::getDataSince($base->getStartTime()); ?>
							</span>
						</td>
					</tr>
				</table>
			</div><!-- end of first post -->
		<?php
	}
	
	/** Displayed the body of the topic in the internal process */
	protected abstract function insertTopicBody($topic);
	
	/**
	 * Only for admin/moderator/author
	 * Insert all the administrative stuff
	 * edit / stick / close / delete
	 */
	protected function insertAdminTools($topic){
		$base = $topic->getTopicbase();
		$customTypeId = $base->getCustomType();
		$topicId = $base->getId();
		$forumId = $base->getForum()->getId();
		?>
		<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('editTopic', 'Topic', 'edit', array($customTypeId, $forumId, $topicId)); ?>"
			title="<?php echo __( 'Modify the topic content or title' ); ?>"><?php echo __('Edit'); ?></a>
		&nbsp;|&nbsp;
		
		<?php if($base->isSticky()): ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('unstickTopic', 'Topic', 'unstick', array($topicId)); ?>"
				title="<?php echo __( 'Unstick the topic, it will be shown like other topics by order of last modification' ); ?>"><?php echo __('Unstick'); ?></a>
		<?php else: ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('stickTopic', 'Topic', 'stick', array($topicId)); ?>"
				title="<?php echo __( 'Stick the topic at the top of the forum' ); ?>"><?php echo __('Stick'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
		<?php if($base->isOpen()): ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('closeTopic', 'Topic', 'close', array($topicId)); ?>"
				title="<?php echo __( 'Close the topic, so that no other comments could be left, and so the topic falls in the forget' ); ?>"><?php echo __('Close'); ?></a>
		<?php else: ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('openTopic', 'Topic', 'open', array($topicId)); ?>"
				title="<?php echo __( 'Open the topic, it will re accept comments' ); ?>"><?php echo __('Open'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
		<?php if($base->isDeleted()): ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('undeleteTopic', 'Topic', 'undelete', array($topicId)); ?>"
				title="<?php echo __( 'Restore the topic from the database trash' ); ?>"><?php echo __('Undelete'); ?></a>
		<?php else: ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('deleteTopic', 'Topic', 'delete', array($topicId)); ?>"
				title="<?php echo __( 'Delete the topic, stay in database but it is no more displayed' ); ?>"><?php echo __('Delete'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
	<?php
	}
	
	/**
	 * For everybody
	 * Insert all the administrative stuff
	 * report / -quote- / permalink 
	 */
	protected function insertUserTools($topic){
		?>
		<a href="<?php $this->front->getViewHelper()->insertHref('Topic', 'report', array($topic->getId())); ?>"
			title="<?php echo __( 'Report abuse to the moderators' ); ?>"><?php echo __('Report'); ?></a>
		&nbsp;|&nbsp;
		<!--<a href="#comment" onClick="onQuoteClick(); return true"
			title="<?php echo __( 'Quote this topic in your answer' ); ?>"><?php echo __('Quote'); ?></a>
		&nbsp;|&nbsp;
		--><a href="#topic-<?php echo $topic->getId(); ?>"
			title="<?php echo __( 'Permanent link to this post' ); ?>">#</a>
								
	<?php
	}	
}