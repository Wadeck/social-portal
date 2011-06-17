<?php

namespace core\topics;

use core\FrontController;

class ForumHeader {
	/** @var string */
	private $cache;
	/** @var FrontController */
	private $front;
	
	public function __construct() {
		$this->cache = '';
	}
	/**
	 * Fill the cache with the future displayed information
	 * @param array $forums The list of forum that will be displayed using their name and creating a link
	 */
	public function createHeaders(FrontController $front, array $forums = array(), $indexSelected) {
		$output = '<ul id="forums-header">';
		$size = count( $forums );
		for( $i = 0; $i < $size; $i++ ) {
			$f = $forums[$i];
			$name = $f->getName();
			$id = $f->getId();
			$descr = $f->getDescription();
			$numTopics = $f->getNumTopics();
			$classes = 'forums-tab';
			$link = $front->getViewHelper()->createHref( 'Forum', 'displaySingle', array($id) );
//			if( $i === $indexSelected ) {
//				$classes .= ' selected background_primary';
//			}else{
//				$classes .= ' background_secondary';
//			}
			if( $i === $indexSelected ) {
				$classes .= ' selected';
			}
			$output .= '<li class="' . $classes . '"><a href="' . $link . '" title="' . $descr . '">' . $name . ' (' . $numTopics . ')</a></li>';
		}
		$output .= '</ul>';
		$this->cache = $output;
	}
	
	public function insert() {
		echo $this->cache;
	}
}