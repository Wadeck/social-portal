<?php
/**
 * PHP Integration of Open Flash Chart
 * Copyright (C) 2008 John Glazebrook <open-flash-chart@teethgrinder.co.uk>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace OFC\Charts;

class OFC_Charts_Tooltip {
	function __construct() {
	}
	
	/**
	 * @param $shadow as boolean. Enable drop shadow.
	 */
	function set_shadow($shadow) {
		$this->shadow = $shadow;
	}
	
	/**
	 * @param $stroke as integer, border width in pixels (e.g. 5 )
	 */
	function set_stroke($stroke) {
		$this->stroke = $stroke;
	}
	
	/**
	 * @param $colour as string, HEX colour e.g. '#0000ff'
	 */
	function set_colour($colour) {
		$this->colour = $colour;
	}
	
	/**
	 * @param $bg as string, HEX colour e.g. '#0000ff'
	 */
	function set_background_colour($bg) {
		$this->background = $bg;
	}
	
	/**
	 * @param $style as string. A css style.
	 */
	function set_title_style($style) {
		$this->title = $style;
	}
	
	/**
	 * @param $style as string. A css style.
	 */
	function set_body_style($style) {
		$this->body = $style;
	}
	
	function set_text($text) {
		$this->text = $text;
	}
	
	function set_proximity() {
		$this->mouse = 1;
	}
	
	function set_hover() {
		$this->mouse = 2;
	}
}

