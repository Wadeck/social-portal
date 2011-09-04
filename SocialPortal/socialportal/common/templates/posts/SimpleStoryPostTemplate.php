<?php

namespace socialportal\common\templates\posts;

class SimpleStoryPostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post) {
		?>
<p><?php
		echo $post->getContent();
		?></p>
<?php
	}
}