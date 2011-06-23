<?php

namespace core\templates;

class StrategyPostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post) {
		?>
<p><?php
		echo $post->getContent();
		?></p>
<?php
	}
}