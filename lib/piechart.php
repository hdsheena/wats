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

	$width = 300; 
	$height = 300;

	$pie = imagecreatetruecolor  ($width + 1 , $height + 1);
	
	
	
	$color['white'] =	imagecolorallocate($pie, 0xFF, 0xFF, 0xFF) ;
	$color['black'] =	imagecolorallocate($pie, 0x00, 0x00, 0x00) ;
	$color['transparent'] = imagecolorallocatealpha($pie, 0x00, 0x00, 0x00, 50) ;
	
	$color[0] =	imagecolorallocate($pie, 0xCC, 0xCC, 0xFF); //blue
	$color[1] =	imagecolorallocate($pie, 0xFF, 0xDD, 0xCC); //orange
	$color[2] =	imagecolorallocate($pie, 0xCC, 0xFF, 0xCC); //green
	$color[3] =	imagecolorallocate($pie, 0xFF, 0xCC, 0xCC); //red
	$color[4] =	imagecolorallocate($pie, 0xCC, 0xFF, 0xFF); //cyan
	$color[5] =	imagecolorallocate($pie, 0xFF, 0xCC, 0xFF); //magenta
	$color[6] =	imagecolorallocate($pie, 0xFF, 0xFF, 0xCC); //yellow
	$color[7] = imagecolorallocate($pie, 0xCC, 0xCC, 0xCC); //grey
	
	
	imagefill($pie, 0, 0, $color['white']);
	
	
	$theta = 0; 
	
	imageline($pie, $width/2, $height/2, cos($theta) * $width/2+ ($width/2), sin($theta) * $height/2+ ($height/2), $color['black']);
	
	imagearc($pie, $width/2, $height/2, $width, $height, 0, 360, $color['black']);

	if (isset($_REQUEST['slice']))
	{
		foreach ($_REQUEST['slice'] as $num=>$slice)
		{
			if ($slice['size'] == 0 ) continue;
			$alpha = (($slice['size']/100) *2*  pi()) + $theta;

			$x = round(cos($alpha) * ($width/2) + ($width/2));
			$y = round(sin($alpha) * ($height/2) + ($height/2));
		
			$fillX = round(cos(($theta + $alpha)/2) * (($width/2)-3) + ($width/2));
			$fillY = round(sin(($theta + $alpha)/2) * (($width/2)-3) + ($height/2));

			$clr = ($num < 8)?$num: $num % 7;

			imageline($pie, $width/2, $height/2, $x, $y, $color['black']);			
			imagefill($pie, $fillX, $fillY, $color[$clr]);

			if ($slice['size'] > 3)
			{
//print_r($box);
				if (($theta < pi()/2) || ($theta > (3*pi()/2) && $theta < 2*pi()))
				{
					$box = imageftbbox(10, 180+rad2deg(pi() - $theta), $fontfile, $slice['name']);
					$l = ($box[4] - $box[0]); 
					$h = ($box[3] - $box[0]);
					
					$newX = round(cos($alpha-.05) * (39) + ($width/2));
					$newY = round(sin($alpha-.05) * (39) + ($height/2));
					$beta = rad2deg(-$alpha);
				}
				else
				{
					$newX = round(cos($theta + (.01*$alpha)) * (($width/2)-3) + ($width/2));
					$newY = round(sin($theta + (.01*$alpha)) * (($width/2)-3) + ($height/2));
					$beta = rad2deg(pi()-$theta);
				}
				
				imagesetpixel($pie, $fillX, $fillY, imagecolorallocate($pie, 0x00, 0x00, 0xFF));
				imagesetpixel($pie, $newX, $newY, imagecolorallocate($pie, 0xFF, 0x00, 0x00));

				imagefttext($pie, 10, $beta, $newX, $newY, $color['black'], $fontfile, $slice['name']);
			}

			$theta = $alpha;
		}
	}
	
	header("Content-type: image/png");
	imagepng($pie);
	imagedestroy($pie);
?>
