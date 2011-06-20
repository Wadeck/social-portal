<?php

namespace core\topics\templates;

class StoryTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic){
		?>
		<p><?php echo $topic->getStoryContent(); ?></p>
		<?php if($topic->getAutomaticThoughts()):?>
			<h3><?php echo __('Automatic thoughts'); ?></h3>
			<p><?php echo $topic->getAutomaticThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getAlternativeThoughts()):?>
			<h3><?php echo __('Alternative thoughts'); ?></h3>
			<p><?php echo $topic->getAlternativeThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getRealisticThoughts()):?>
			<h3><?php echo __('Realistic thoughts'); ?></h3>
			<p><?php echo $topic->getRealisticThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getPs()):?>
			<h4><?php echo __('Ps'); ?></h4>
			<p><?php echo $topic->getPs(); ?></p>
		<?php endif;?>
	<?php 
	}
}