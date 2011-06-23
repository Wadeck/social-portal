<?php

namespace core\templates;

class FreetextTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
		?>
<p><?php
		echo $topic->getContent();
		?></p>
<?php
	}
}