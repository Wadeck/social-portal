<?php

namespace core\templates;

/**
 * Template for the home block that display topics in a list
 *	with links to view the forum, add new topic and read specific topic
 *
 */
use socialportal\model\TopicBase;

use socialportal\model\Forum;

use core\user\UserRoles;

use core\tools\Utils;

use core\FrontController;
use core\templates\iInsertable;

class HomeBlockTemplate implements iInsertable{
	/** @var FrontController */
	protected $front;
	/** @var array */
	protected $topics;
	/** @var string */
	protected $name;
	/** @var Forum */
	protected $forum;
	/** @var string */
	protected $formattedLink;
	/** @var int */
	protected $forumId;
	/** @var string */
	protected $linkDisplayForum;
	//
	// not really good stuff below
	protected $rightToSeeForum ;
	protected $rightToCreateNew ;
	protected $typeId;
	
	/**
	 * 
	 * @param Forum $forum
	 * @param array $topics
	 * @param string $name Translated
	 * @param string $formattedLink link with %forumId% and %topicId% placeholder
	 */
	public function __construct(FrontController $front, Forum $forum, array $topics, $typeId, $name, $formattedLink, $linkDisplayForum){
		$this->front = $front;
		$this->forum = $forum;
		$this->topics = $topics;
		$this->name = $name;
		$this->forumId = $forum->getId();
		$this->typeId = $typeId;
		$this->formattedLink = $formattedLink;
		$this->linkDisplayForum = $linkDisplayForum;
		
		//TODO replace with capabilities
		$this->rightToSeeForum = !$this->front->getViewHelper()->currentUserIs(UserRoles::$anonymous_role);
		$this->rightToCreateNew = $this->rightToSeeForum;
	}
	
	public function insert(){
		$this->front->getViewHelper()->addCssFile('home_box.css');
		?>
		<div class="box">
			<?php $this->insertHeader(); ?>
			<div class="box-center">
				<div class="box-content">
					<?php if(!$this->topics): $this->front->getViewHelper()->addCssFile('messages.css'); ?>
					<div class="message info"><?php echo __('There is no topic in this category'); ?></div>
					<?php else: foreach($this->topics as $topic) $this->insertTopic($topic); ?>
					<?php endif ?>
				</div>
			</div>
		</div>
		<?php 
	}
	
	protected function insertHeader(){
		?>
		<div class="box-upper">
			<?php $this->insertTool(); ?>
			<div class="box-logo" id="icon-xx"><!-- Could be used to put some icon --></div>
			<!-- case we need to place a right icon <div class="box-toggle">right</div> -->
			<div class="box-title"><h3>
			<?php if($this->rightToSeeForum): ?>
				<a href="<?php echo $this->linkDisplayForum; ?>"><?php echo $this->name; ?></a>
			<?php else: echo $this->name; endif ?>
			</h3></div>
			
		</div>
	<?php 
	}
	
	protected function insertTool(){
		$seeAll = __('see all');
		$new = __('new');
		$linkToCreate = $this->front->getViewHelper()->createHrefWithNonce('displayTopicForm', 'Topic', 'displayForm',  array('typeId' => $this->typeId, 'forumId' => $this->forumId ));

		$links = array();
		if($this->rightToCreateNew){
			$links[] = '<a class="tool" href="' . $linkToCreate . '">'. $new .'</a>';
		}
		if($this->rightToSeeForum){
			$links[] = '<a class="tool" href="' . $this->linkDisplayForum . '">'. $seeAll .'</a>';
		}
		if(!$links){
			return;
		}
		$links = implode(' | ', $links);
		?>
		<div class="box-tool">
			<?php echo $links; ?>
		</div>
	<?php	
	}
	
	protected function insertTopic(TopicBase $topic){
		$topicId = $topic->getId();
		$description = Utils::createExcerpt($topic->getTitle(), 26);
		// we can imagine different forumId if we have a box that display best ranked topic in whole site
		$link = strtr($this->formattedLink, array('%forumId%' => $this->forumId, '%topicId%'=>$topicId));
		$readMore = __('Read more');
		?>
		<div class="box-line">
			<a class="box-line-link" href="<?php echo $link; ?>"><?php echo $readMore; ?></a>
			<div class="box-line-description"><?php echo $description ;?></div>
		</div>
	<?php
	}
}