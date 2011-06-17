<?php

/*  Copyright 2011 Eric Martin (eric@ericmmartin.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

namespace core\tools;

use core\FrontController;
class Paginator {
	/**
	 * Represents the link format
	 * @var string
	 */
	private $base_link = '';
	/** @var FrontController */
	private $frontController;
	/** @var string The pagination in string */
	private $cache;
	
	public function insert() {
		$this->frontController->getViewHelper()->addCssFile( 'paginate.css' );
		echo $this->cache;
	}
	
	/**
	 * 
	 * @param int $page current page
	 * @param int $pages total number of page
	 * @param int $per_page number of item per page
	 * @param string $base_url url with in it two specific tag : %#p% == n-th page and %#n% == number per page
	 * @param string $firstPage translated name of the link
	 * @param string $LastPage translated name of the link
	 * @param string $previousPage translated name of the link
	 * @param string $nextPage translated name of the link
	 * @param string $title translated name that is displayed before pagination
	 */
	public function paginate(FrontController $front, $page, $pages, $per_page, $base_url, $firstPage, $lastPage, $previousPage, $nextPage, $title = '') {
		$this->frontController = $front;
		if( $this->cache ) {
			return $this->cache;
		}
		if( !$pages ) {
			$this->cache = '';
			return $this->cache;
		}
		$before = '<div class="navigation">';
		$after = '</div>';
		$range = 3;
		$anchor = 1;
		$gap = 3;
		
		// number of unit per page
		$pag_num = -1;
		if( isset( $per_page ) ) {
			$pag_num = $per_page;
		}
		
		// the pag_num is constant for a given pagination
		$this->base_link = str_replace( '%#n%', $pag_num, $base_url );
		
		$firstlink = $this->get_url( 1 );
		$prevlink = $this->get_url( $page - 1 );
		$nextlink = $this->get_url( $page + 1 );
		$lastlink = $this->get_url( $pages );
		
		$output = stripslashes( $before );
		if( $pages > 1 ) {
			$output .= sprintf( '<ol class="wp-paginate%s">', ($this->type === 'posts') ? '' : ' wp-paginate-comments' );
			if( $title ) {
				$output .= sprintf( '<li><span class="title">%s</span></li>', $title );
			}
			$ellipsis = "<li><span class='gap'>...</span></li>";
			
			if( $page > 2 && !empty( $firstPage ) ) {
				$output .= sprintf( '<li><a href="%s" class="first target-1">%s</a></li>', $firstlink, $firstPage );
			}
			if( $page > 1 && !empty( $previousPage ) ) {
				$output .= sprintf( '<li><a href="%s" class="prev">%s</a></li>', $prevlink, $previousPage );
			}
			
			$min_links = $range * 2 + 1;
			$block_min = min( $page - $range, $pages - $min_links );
			$block_high = max( $page + $range, $min_links );
			$left_gap = (($block_min - $anchor - $gap) > 0) ? true : false;
			$right_gap = (($block_high + $anchor + $gap) < $pages) ? true : false;
			
			if( $left_gap && !$right_gap ) {
				$output .= sprintf( '%s%s%s', $this->paginate_loop( 1, $anchor ), $ellipsis, $this->paginate_loop( $block_min, $pages, $page ) );
			} else if( $left_gap && $right_gap ) {
				$output .= sprintf( '%s%s%s%s%s', $this->paginate_loop( 1, $anchor ), $ellipsis, $this->paginate_loop( $block_min, $block_high, $page ), $ellipsis, $this->paginate_loop( ($pages - $anchor + 1), $pages ) );
			} else if( $right_gap && !$left_gap ) {
				$output .= sprintf( '%s%s%s', $this->paginate_loop( 1, $block_high, $page ), $ellipsis, $this->paginate_loop( ($pages - $anchor + 1), $pages ) );
			} else {
				$output .= $this->paginate_loop( 1, $pages, $page );
			}
			
			if( $page < $pages && !empty( $nextPage ) ) {
				$output .= sprintf( '<li><a href="%s" class="next">%s</a></li>', $nextlink, stripslashes( $nextPage ) );
			}
			if( $page < $pages - 1 && !empty( $lastPage ) ) {
				//target-page-%d is to store information for jquery
				$output .= sprintf( '<li><a href="%s" id="target-page-%d" class="last">%s</a></li>', $lastlink, $pages, stripslashes( $lastPage ) );
			}
			$output .= "</ol>";
		}
		$output .= $after;
		$this->cache = $output;
		return $this->cache;
	}
	
	private function get_url($index) {
		return str_replace( '%#p%', ($index), $this->base_link );
	}
	
	/**
	 * Helper function for pagination which builds the page links.
	 */
	private function paginate_loop($start, $max, $page = 0) {
		$output = "";
		for( $i = $start; $i <= $max; $i++ ) {
			$p = $this->get_url( $i );
			
			$output .= ($page == intval( $i )) ? "<li><span class='page current'>$i</span></li>" : "<li><a href='$p' title='$i' class='page'>$i</a></li>";
		}
		return $output;
	}
}
