<?php

namespace socialportal\common\templates\posts;

class StoryPostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post) {
	?>
		<p><?php echo $post->getContent(); ?></p>
		
		<?php if( $post->getAutomaticThoughts() ) : ?>
			<h5><?php echo __( 'Automatic thoughts' ); ?></h5>
			<p><?php echo $post->getAutomaticThoughts(); ?></p>
		<?php endif; ?>
		
		<?php if( $post->getAlternativeThoughts() ) : ?>
			<h5><?php echo __( 'Alternative thoughts' ); ?></h5>
			<p><?php echo $post->getAlternativeThoughts(); ?></p>
		<?php endif; ?>
		
		<?php if( $post->getRealisticThoughts() ) : ?>
			<h5><?php echo __( 'Realistic thoughts' ); ?></h5>
			<p><?php echo $post->getRealisticThoughts(); ?></p>
		<?php endif; ?>
	<?php
	}
}