<?php

namespace socialportal\common\templates\posts;

use socialportal\common\templates\iInsertable;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use core\tools\Utils;

use core\user\UserRoles;

use core\user\UserHelper;

abstract class AbstractPostTemplate implements iInsertable{
	/** @var EntityManager */
	protected $em;
	/** @var FrontController */
	protected $front;
	/** @var array of posts */
	protected $posts;
	/** @var string url to the given post with p/n parameters to avoid relativity */
	protected $permalink;
	
	protected $topicBase ;
	protected $topicId ;
	protected $forumId ;
	protected $supportVote = true;
	
	protected $highlightPost = false;
	
	public function setFrontController(FrontController $front){
		$this->front = $front;
	}
	
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	public function setTopicBase(TopicBase $topic){
		$this->topicBase = $topic;
		$this->topicId = $topic->getId();
		$this->forumId = $topic->getForum()->getId();
	}
	
	/**
	 * @param $value int
	 */
	public function setHighlightPost($value){
		$this->highlightPost = $value;
	}
	/**
	 * @param array of CustomPost $posts
	 */
	public function setPosts(array $posts){
		$this->posts = $posts;
	}
	
	/**
	 * @param string $permalink url with n/p parameters, to avoid lastPage, etc that are relative to a moment
	 */
	public function setPermalink($permalink){
//		if(false !== ($index = strpos($permalink, '#') ) ){
//			$permalink = substr($permalink, $index);
//		}
		$this->permalink = $permalink;
	}
	
	public function insert(){
		if(!$this->posts){
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
				<div class="message info centered">
					<p><?php echo __( 'There are no posts for this topic.' ) ?></p>
				</div>
			<?php
			return;
		}
		$userHelper = new UserHelper($this->front);
		
//		$isAdmin = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$admin_role);
		$isModo = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$isFullUser = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role);
		$currentUserId = $this->front->getCurrentUser()->getId();
		
		?>
		<ol id="topic-post-list" class="commentlist">
		<?php
		foreach($this->posts as $post):
			$base = $post->getPostbase();
			$postAuthor = $base->getPoster();
			$postId = $base->getId();
			$userHelper->setCurrentUser($postAuthor);
		?>
			<!-- Here to add the condition on the post status -->
			<li class="rounded-box<?php 
					if($base->getIsDeleted()) echo ' deleted'; 
					if($postId === $this->highlightPost) echo ' highlight-post'; ?>" id="post-<?php echo $postId; ?>">
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
			    			<?php if($this->supportVote): ?>
								<div id="vote-box">
									<a class="button"
										href="<?php $this->front->getViewHelper()->insertHrefWithNonce('votePost', 'Vote', 'votePost', 
											array('postId' => $postId, 'topicId' => $this->topicId, 'forumId' => $this->forumId) );?>"
										><?php echo __('I like'); ?></a>
								</div>
							<?php endif; ?>
							<?php $this->insertPostContent($post); ?>
			    		</td>
					</tr>
					<tr><!-- row of admin tool -->
						<td colspan="2" id="topic-bottom">
							<span id="topic-tools">
							<?php
							//TODO change this when capabilities will be implemented
							if( $postAuthor->getId() === $currentUserId || $isModo ){
								$this->insertEditTool($post);
							}
							
							if( $isModo ){
								$this->insertModeratorTools($post);
							}
							if($isFullUser){
								$this->insertUserTools($post);
							}
							?>
							</span>

							<!-- publication date -->
							<span id="topic-date">
								<!-- publication date -->
								<?php echo Utils::getDataSince($base->getTime()); ?>
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

	
	protected function insertEditTool($post){
		$base = $post->getPostbase();
		$customTypeId = $base->getCustomType();
		$postId = $base->getId();
		$topic = $base->getTopic();
		$topicId = $topic->getId();
		$forumId = $topic->getForum()->getId();
		$viewHelper = $this->front->getViewHelper();
		?>
		
		<a href="<?php $viewHelper->insertHrefWithNonce('displayPostForm', 'Post', 'displayForm', array('typeId'=>$customTypeId, 'topicId'=>$topicId, 'forumId'=>$forumId, 'postId'=>$postId)); ?>"
			title="<?php echo __( 'Modify the topic content or title' ); ?>"><?php echo __('Edit'); ?></a>
		&nbsp;|&nbsp;
		
	<?php
	}
	
	/**
	 * Only for admin/moderator/author
	 * Insert all the administrative stuff
	 * edit / stick / close / delete
	 */
	protected function insertModeratorTools($post){
		$base = $post->getPostbase();
		$customTypeId = $base->getCustomType();
		$postId = $base->getId();
		$topic = $base->getTopic();
		$topicId = $topic->getId();
		$forumId = $topic->getForum()->getId();
		$viewHelper = $this->front->getViewHelper();
		?>
		<?php if($base->getIsDeleted()): ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('undeletePost', 'Post', 'undelete', array('postId'=>$postId, 'topicId'=>$topicId, 'forumId'=>$forumId)); ?>"
				title="<?php echo __( 'Restore the post from the database trash' ); ?>"><?php echo __('Undelete'); ?></a>
		<?php else: ?>
			<a href="<?php $viewHelper->insertHrefWithNonce('deletePost', 'Post', 'delete', array('postId'=>$postId, 'topicId'=>$topicId, 'forumId'=>$forumId)); ?>"
				title="<?php echo __( 'Delete the post, stay in database but it is no more displayed' ); ?>"<?php $viewHelper->insertConfirmLink(__('Do you really want to delete this post ?'));?>><?php echo __('Delete'); ?></a>
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
		$postBase = $post->getPostBase();
		$postId = $postBase->getId();
		
		//TODO quote function!
		$result = $this->em->getRepository('ReportPost')->findBy(array("postId"=>$post->getId(), "userId" => $this->front->getCurrentUser()->getId(), "isDeleted"=>0));				
		if(!$result){
		?>
		<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('reportPost', 'Report', 'reportPost', array( 'postId' => $postId, 'topicId' => $this->topicId, 'forumId' => $this->forumId)); ?>"
			title="<?php echo __( 'Report abuse to the moderators' ); ?>"><?php echo __('Report'); ?></a>
		&nbsp;|&nbsp;
		<?php }else{
		$reportIdRequest = $this->em->getRepository('ReportPost')->findBy(array("postId" => $post->getId(), "userId" => $this->front->getCurrentUser()->getId(), "isDeleted" => 0));
		$reportId = $reportIdRequest[0]->getId();?>
		<a href="<?php $this->front->getViewHelper()->insertHrefWithNonce('removeReportPost', 'Report', 'removeReportPost', array( 'reportId' => $reportId, "postId" => $postId, 'forumId' => $this->forumId)); ?>"
			title="<?php echo __( 'Remove report abuse to the moderators' ); ?>"><?php echo __('Remove Report'); ?></a>
			
		<?php }?>	
		<!--  <a class="unimplemented" href="#comment" onClick="onQuoteClick(); return true"
			title="<?php //echo __( 'Quote this post in your answer' ); ?>"><?php //echo __('Quote'); ?></a> 
		&nbsp;|&nbsp;-->
		<a href="<?php echo $this->permalink . '#post-' . $post->getId(); ?>"
			title="<?php echo __( 'Permanent link to this post' ); ?>">#</a>
								
	<?php
	}	
}