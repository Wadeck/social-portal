<?php

namespace core\topics\templates;

class FreetextPostTemplate extends AbstractPostTemplate {
	protected function insertPostContent($post){
		?>
		<p><?php echo $post->getContent(); ?></p>
		<?php 
	}
}