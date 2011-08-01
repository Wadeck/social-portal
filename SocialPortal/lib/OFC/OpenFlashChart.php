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

namespace OFC;

class OpenFlashChart{
	private static $open_flash_chart_seqno = null;
	public static function getObject($width, $height, $url, $baseSwf, $baseJs, $use_swfobject = true){
		// escape the & and stuff:
		$url_notEncoded = $url;
		$url = urlencode ( $url );
		// output buffer
		$out = array ();
		// check for http or https:
		if (isset ( $_SERVER ['HTTPS'] )) {
			if (strtoupper ( $_SERVER ['HTTPS'] ) == 'ON') {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}
		} else {
			$protocol = 'http';
		}
		
		// if there are more than one charts on the
		// page, give each a different ID
		$obj_id = 'chart';
		$div_name = 'flashcontent';

		if ( null === self::$open_flash_chart_seqno ) {
			$open_flash_chart_seqno = 1;
			$out [] = '<script type="text/javascript" src="' . $baseJs . 'swfobject.js"></script>';
		} else {
			$open_flash_chart_seqno ++;
			$obj_id .= '_' . $open_flash_chart_seqno;
			$div_name .= '_' . $open_flash_chart_seqno;
		}
		
		if ($use_swfobject) {
			// Using library for auto-enabling Flash object on IE, disabled-Javascript proof
			$out [] = '<div id="' . $div_name . '"></div>';
			$out [] = '<script type="text/javascript">';
//			$out [] = 'var so = new swfobject("' . $baseSwf . 'open-flash-chart.swf", "' . $obj_id . '", "' . $width . '", "' . $height . '", "9", "#FFFFFF");';
//			$out [] = 'so.addVariable("data-file", "' . $url . '");';
//			$out [] = 'so.addParam("allowScriptAccess", "always" );//"sameDomain");';

//			$out [] = 'var so = swfobject.embedSWF("' . $baseSwf . 'open-flash-chart.swf", "' . $obj_id . '", "' . $width . '", "' . $height . '", "9", "#FFFFFF", flashvars, params);';
			$out [] = 'var flashvars = { "data-file": "' . $url . '" };';
			$out [] = 'var params = { "allowScriptAccess": "always" };';
			$out [] = 'var so = swfobject.embedSWF("' . $baseSwf . 'open-flash-chart.swf", "' . $div_name . '", "' . $width . '", "' . $height . '", "9", "#FFFFFF", flashvars, params);';
//			
//			
//			var flashvars = {"data-file", "' . $url . '" };
//			var params = { "allowScriptAccess", "always" };
//
//swfobject.embedSWF("myContent.swf", "myContent", "300", "120", "9.0.0","expressInstall.swf", flashvars, params, attributes);
			
			
//			$out [] = 'so.write("' . $div_name . '");';
			$out [] = '</script>';
			$out [] = '<noscript>';
		}
		
		$out [] = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="' . $protocol . '://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" ';
		$out [] = 'width="' . $width . '" height="' . $height . '" id="ie_' . $obj_id . '" align="middle">';
		$out [] = '<param name="allowScriptAccess" value="sameDomain" />';
		$out [] = '<param name="movie" value="' . $baseSwf . 'open-flash-chart.swf?data=' . $url . '" />';
		$out [] = '<param name="quality" value="high" />';
		$out [] = '<param name="bgcolor" value="#FFFFFF" />';
		$out [] = '<embed src="' . $baseSwf . 'open-flash-chart.swf?data=' . $url . '" quality="high" bgcolor="#FFFFFF" width="' . $width . '" height="' . $height . '" name="' . $obj_id . '" align="middle" allowScriptAccess="sameDomain" ';
		$out [] = 'type="application/x-shockwave-flash" pluginspage="' . $protocol . '://www.macromedia.com/go/getflashplayer" id="' . $obj_id . '"/>';
		$out [] = '</object>';
		
		if ($use_swfobject) {
			$out [] = '</noscript>';
		}
		
		return implode ( "\n", $out );				
	}
}
