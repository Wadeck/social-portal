<?php

namespace socialportal\common\templates\topics;

class StrategyTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
		$this->front->getViewHelper()->addCssFile('strategy_list.css');
		$listRepo = $this->em->getRepository('TopicStrategyItem');
		// use the strategy topic id, not the base one
		$items = $listRepo->findAllItems($topic->getId());
		?>
			<p><?php
		echo $topic->getDescription();
		?></p>
		<ol class="strategy_list">
			<?php foreach($items as $item): ?>
				<li><?php echo $item->getContent(); ?></li>
			<?php endforeach; ?>
		</ol>

	<?php
	}
}