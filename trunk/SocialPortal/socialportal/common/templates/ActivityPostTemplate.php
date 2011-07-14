<?php

namespace socialportal\common\templates;

class ActivityPostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post) {
		?>
<p><?php
		echo $post->getContent();
		?></p>
<?php
	}
}