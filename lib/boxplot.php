<?php
/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created April 27, 2008
	 * 
	 * @copyright: (C)2008 The Evergreen School
	 * 
	 * This program is free software: you can redistribute it and/or modify 
	 * it under the terms of the GNU General Public License version 3 as published by
	 * the Free Software Foundation.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/gpl-3.0.html>. 
	 */
	

	$fontfile = "/usr/share/fonts/ttf-bitstream-vera/Vera.ttf";

	$width = 400; 
	$height = 80;

	$box = imagecreatetruecolor  ($width + 1 , $height + 1);
	
	
	
	$color['white'] =	imagecolorallocate($box, 0xFF, 0xFF, 0xFF) ;
	$color['black'] =	imagecolorallocate($box, 0x00, 0x00, 0x00) ;
	$color['transparent'] = imagecolorallocatealpha($box, 0x00, 0x00, 0x00, 50) ;
	
	$color[0] =	imagecolorallocate($box, 0xCC, 0xCC, 0xFF); //blue
	$color[1] =	imagecolorallocate($box, 0xFF, 0xDD, 0xCC); //orange
	$color[2] =	imagecolorallocate($box, 0xCC, 0xFF, 0xCC); //green
	$color[3] =	imagecolorallocate($box, 0xFF, 0xCC, 0xCC); //red
	$color[4] =	imagecolorallocate($box, 0xCC, 0xFF, 0xFF); //cyan
	$color[5] =	imagecolorallocate($box, 0xFF, 0xCC, 0xFF); //magenta
	$color[6] =	imagecolorallocate($box, 0xFF, 0xFF, 0xCC); //yellow
	$color[7] = imagecolorallocate($box, 0xCC, 0xCC, 0xCC); //grey
	
	
	imagefill($box, 0, 0, $color['white']);
	
	imagefilledellipse($box,  10, 20, 10, 10, $color['black']);
	imagefilledellipse($box, 380, 20, 10, 10, $color['black']);
	
	
	
	header("Content-type: image/png");
	imagepng($box);
	imagedestroy($box);
?>
