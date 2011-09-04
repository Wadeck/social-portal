<?php

namespace socialportal\common\templates\topics;

class SimpleStoryTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
	?>
		<p><?php echo $topic->getStoryContent(); ?></p>
		
		<?php if( $topic->getPs() ) : ?>
			<h5><em><?php echo __( 'PS' ); ?></em></h5>
			<p><?php echo $topic->getPs(); ?></p>
		<?php endif; ?>
	<?php
	}
}