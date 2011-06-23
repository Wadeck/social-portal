<?php

namespace core\templates;

class ActivityTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
		?>
<p><?php
		echo $topic->getContent();
		?></p>
<?php
	}
}