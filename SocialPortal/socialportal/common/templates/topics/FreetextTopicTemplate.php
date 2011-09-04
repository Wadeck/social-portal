<?php

namespace socialportal\common\templates\topics;

class FreetextTopicTemplate extends AbstractTopicTemplate {
	protected function insertTopicBody($topic) {
		?>
<p><?php
		echo $topic->getLowSelfEsteem();
		?></p>
<?php
	}
}