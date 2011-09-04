<?php

namespace socialportal\common\templates\posts;

class ViciousCirclePostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post) {
		?>
<p><?php
		echo $post->getContent();
		?></p>
<?php
	}
}