<?php

namespace socialportal\common\templates\topics;

use socialportal\model\TopicViciousCircle;

class ViciousCircleTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
	?>
		<p><?php echo $topic->getLowSelfEsteem(); ?></p>
		
		<?php if( $topic->getLowSelfEsteem() ) : ?>
			<h5><?php echo __( 'Low Self Esteem' ); ?></h5>
			<p><?php echo $topic->getLowSelfEsteem(); ?></p>
		<?php endif; ?>
		
		<?php if( $topic->getPotentialSolution() ) : ?>
			<h5><?php echo __( 'Potential Solution' ); ?></h5>
			<p><?php echo $topic->getPotentialSolution(); ?></p>
		<?php endif; ?>
		
		<?php if( $topic->getStrategy() ) : ?>
			<h5><?php echo __( 'Strategy' ); ?></h5>
			<p><?php echo $topic->getStrategy(); ?></p>
		<?php endif; ?>
		
		<?php if( $topic->getEvaluation() ) : ?>
			<h5><?php echo __( 'Evaluation' ); ?></h5>
			<p><?php echo $topic->getEvaluation(); ?></p>
		<?php endif; ?>
		
		<?php if( $topic->getPs() ) : ?>
			<h5><em><?php echo __( 'PS' ); ?></em></h5>
			<p><?php echo $topic->getPs(); ?></p>
		<?php endif; ?>
	<?php
	}
}