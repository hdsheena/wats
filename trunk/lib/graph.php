<?php
/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created September 29, 2008
	 * 
	 * @copyright: (C)2008 The Evergreen School
	 * 
	 * This program is free software: you can redistribute it and/or modify 
	 * it under the terms of the GNU Affero General Public License version 3 as published by
	 * the Free Software Foundation.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU Affero General Public License for more details.
	 * 
	 * You should have received a copy of the GNU Affero General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/agpl-3.0.html>.
	 */
error_reporting(E_ALL);	

	$fontfile = "/usr/share/fonts/ttf-bitstream-vera/Vera.ttf";

	$width = 500; 
	$height = 400;
	
	$xoffset = 59;
	$yoffset = 59;
	
	$xscale = 1; //for the moment, assume a 1:1 relationship between the  
	$yscale = 1; // x/y values and the pixels. 

	
	$X = $_REQUEST['X'];
	
	$xmax = 0; 
	$xmin = 9999;
	
	foreach ($X as $x=>$y)
	{
		if ($xmin > $x)
			$xmin = $x;
		
		if ($xmax < $x)
			$xmax = $x;
	}
	
	$ymin = min($X);
	$ymax = max($X);
	
	$xscale = floor(($width - $xoffset) / ($xmax-$xmin)); 
	$yscale = floor(($height - $yoffset) / ($ymax-$ymin));
	
	ksort($X);
	reset($X);

	$box = imagecreatetruecolor  ($width + 1 , $height + 1);
	
	$color['white'] =	imagecolorallocate($box, 0xFF, 0xFF, 0xFF) ;
	$color['black'] =	imagecolorallocate($box, 0x00, 0x00, 0x00) ;
	$color['transparent'] = imagecolorallocatealpha($box, 0x00, 0x00, 0x00, 50) ;
	
	$color[0] =	imagecolorallocate($box, 0x00, 0x00, 0xFF); //blue
	$color[1] =	imagecolorallocate($box, 0xFF, 0xDD, 0xCC); //orange
	$color[2] =	imagecolorallocate($box, 0xCC, 0xFF, 0xCC); //green
	$color[3] =	imagecolorallocate($box, 0xFF, 0xCC, 0xCC); //red
	$color[4] =	imagecolorallocate($box, 0xCC, 0xFF, 0xFF); //cyan
	$color[5] =	imagecolorallocate($box, 0xFF, 0xCC, 0xFF); //magenta
	$color[6] =	imagecolorallocate($box, 0xFF, 0xFF, 0xCC); //yellow
	$color[7] = imagecolorallocate($box, 0xCC, 0xCC, 0xCC); //grey
	
	
	imagefill($box, 0, 0, $color['white']);
	
	//vertical axis
	imageline($box, $xoffset, 0, $xoffset, $height-$yoffset, $color['black']);
	imagettftext($box, 12, 90, 20, $height-$yoffset, $color['black'], $fontfile, $_REQUEST['ylabel']);
	
	//horizontal axis
	imageline($box, $xoffset, $height - $yoffset, $width, $height-$yoffset, $color['black']); 
	imagettftext($box, 12, 0, $xoffset, $height-20, $color['black'], $fontfile, $_REQUEST['xlabel']);
	

	
	//the markings on the axis
	$stepX = round(($xmax-$xmin)/10);
	$stepY = round(($ymax-$ymin)/10);
	
	//first the horisontal axis	
	for ($x = $xmin; $x<=$xmax; $x = $x + $stepX)
	{	
		imageline($box, transX($x), $height-$yoffset, transX($x), $height-$yoffset+3, $color['black']);
		imagettftext($box, 8, 0, transX($x),  $height-$yoffset+15, $color['black'], $fontfile, $x);
	}
	
	for ($y = $ymin; $y<=$ymax; $y = $y + $stepY)
	{	
		imageline($box, $xoffset, transY($y), $xoffset-3, transY($y), $color['black']);
		imagettftext($box, 8, 90, $xoffset-12,  transY($y), $color['black'], $fontfile, $y);
	}	
	
	
	//the acutal plotting..
	$lastX = null;
	$lastY = null;
	foreach ($X as $x=>$y)
	{
		if (isset($lastX, $lastY))
		{
			imagesetthickness($box, 1);
			imageline($box, transX($lastX), transY($lastY), transX($x), transY($y), $color[3]);
		}
		
		imagesetthickness($box,2);
		imagerectangle($box, transX($x), transY($y),  transX($x), transY($y),$color[0]);
		
		$lastX = $x;
		$lastY = $y;
	}
	
	

	
	header("Content-type: image/png");
	imagepng($box);
	imagedestroy($box);
	
	return;
	
	//turns an x function value into an x pixel value 
	function transX($x)
	{
		global $xscale, $xoffset;
		
		return ($x  * $xscale) + $xoffset ; //off-by-1 somehow
	}
	
	//turns an y function value into a y pixel value 
	function transY($y)
	{
		global $yscale, $yoffset, $height;
		
		return (-$y * $yscale) + ($height - $yoffset);
	}
?>
