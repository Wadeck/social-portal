<?php

namespace core\templates;

class StrategyTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
		?>
<p><?php
		echo $topic->getDescription();
		?></p>
<?php
	}
}