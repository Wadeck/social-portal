<?php

namespace core\topics\templates;

class StoryTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic){
		?>
		<p><?php echo $topic->getStoryContent(); ?></p>
		<?php if($topic->getAutomaticThoughts()):?>
			<h5><?php echo __('Automatic thoughts'); ?></h5>
			<p><?php echo $topic->getAutomaticThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getAlternativeThoughts()):?>
			<h5><?php echo __('Alternative thoughts'); ?></h5>
			<p><?php echo $topic->getAlternativeThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getRealisticThoughts()):?>
			<h5><?php echo __('Realistic thoughts'); ?></h5>
			<p><?php echo $topic->getRealisticThoughts(); ?></p>
		<?php endif;?>
		<?php if($topic->getPs()):?>
			<h5><em><?php echo __('Ps'); ?></em></h5>
			<p><?php echo $topic->getPs(); ?></p>
		<?php endif;?>
	<?php 
	}
}