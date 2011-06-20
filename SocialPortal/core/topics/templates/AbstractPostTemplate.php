<?php

namespace core\topics\templates;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use core\tools\Utils;

use core\user\UserRoles;

use core\user\UserHelper;

use core\topics\templates\iPostsTemplate;

abstract class AbstractPostTemplate implements iPostsTemplate{
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
	
	public function insertPosts(array $posts){
		if(!$posts){
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
				<div id="message" class="info">
					<p><?php __( 'There are no posts for this topic.' ) ?></p>
				</div>
			<?php
			return;
		}
		$userHelper = new UserHelper();
		?>
		<ol id="topic-post-list" class="commentlist">
		<?php
		foreach($posts as $post):
			$postAuthor = $post->getPoster();
			$postId = $post->getId();
			$userHelper->setCurrentUser($postAuthor);
			
		?>
			<!-- Here to add the condition on the post status -->
			<li id="post-<?php echo $postId; ?>">
				<a name="post-<?php echo $postId; ?>" ></a>
				<table>
			    	<tr>
			    		<!-- row of avatar box -->	
						<td class="avatar-box"><!-- cell of avatar box -->
							<?php $userHelper->insertLinkToProfile(); ?>
							<?php $userHelper->insertAvatar(60); ?>
						</td>

			    		<!-- Content of the comment -->
			    		<td class="comment-right-part">
							<?php $this->insertPostContent($post); ?>
			    		</td>
					</tr>
					<tr><!-- row of admin tool -->
						<td colspan="2" id="topic-bottom">
							<span id="topic-tools">
							<?php if($postAuthor->getId() === $this->front->getCurrentUser()->getId() || $this->front->getViewHelper()->currentUserIs(UserRoles::$admin_role)){
								$this->insertAdminTools($post);//TODO change this when capabilities will be implemented
							}
							$this->insertUserTools($post);
							?>
							</span>

							<!-- publication date -->
							<span id="topic-date">
								<!-- publication date -->
								<?php Utils::getDataSince($post->getTime()); ?>
							</span>
						</td>
					</tr>
				</table>
			</li>
		<?php endforeach ?>
		</ol>
	<?php
	}
	
	protected abstract function insertPostContent($post);

	
	/**
	 * Only for admin/moderator/author
	 * Insert all the administrative stuff
	 * edit / stick / close / delete
	 */
	protected function insertAdminTools($post){
		$base = $post->getTopicbase();
		$customTypeId = $base->getCustomType();
		$topicId = $base->getId();
		$forumId = $base->getForum()->getId();
		?>
		<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('editTopic', 'Post', 'edit', array($customTypeId, $forumId, $topicId)); ?>"
			title="<?php echo __( 'Modify the topic content or title' ); ?>"><?php echo __('Edit'); ?></a>
		&nbsp;|&nbsp;
		
		<?php if($base->isDeleted()): ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('undeleteTopic', 'Post', 'undelete', array($topicId)); ?>"
				title="<?php echo __( 'Restore the post from the database trash' ); ?>"><?php echo __('Undelete'); ?></a>
		<?php else: ?>
			<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('deleteTopic', 'Post', 'delete', array($topicId)); ?>"
				title="<?php echo __( 'Delete the post, stay in database but it is no more displayed' ); ?>"><?php echo __('Delete'); ?></a>
		<?php endif ?>
		&nbsp;|&nbsp;
		
	<?php
	}
	
	/**
	 * For everybody
	 * Insert all the administrative stuff
	 * report / -quote- / permalink 
	 */
	protected function insertUserTools($post){
		?>
		<a href="<?php $this->front->getViewHelper()->insertHref('Topic', 'report', array($post->getId())); ?>"
			title="<?php echo __( 'Report abuse to the moderators' ); ?>"><?php echo __('Report'); ?></a>
		&nbsp;|&nbsp;
		<a href="#comment" onClick="onQuoteClick(); return true"
			title="<?php echo __( 'Quote this post in your answer' ); ?>"><?php echo __('Quote'); ?></a>
		&nbsp;|&nbsp;
		<a href="#topic-<?php echo $post->getId(); ?>"
			title="<?php echo __( 'Permanent link to this post' ); ?>">#</a>
								
	<?php
	}	
}