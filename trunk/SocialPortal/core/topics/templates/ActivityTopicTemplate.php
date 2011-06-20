<?php

namespace core\topics\templates;

class ActivityTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic){
		?>
		<p><?php echo $topic->getContent(); ?></p>
		<?php 
	}
}