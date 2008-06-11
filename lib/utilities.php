<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created Mar 5, 2008
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

	/** 
	 * Removes path characters (/,\,..) from $text to prevent
	 * unauthroized file access
	 *
	 */
	function stripPaths($text)
	{
		$text = preg_replace("/(\\.\\.|[\\\\\\/])/", "", $text);
		return $text; 
	}
	
	function pw_encode($password)
	{
		return sha1($password);
	}


	function getAvailableThemes()
	{
		global $CONFIG;
		
		$themes=dir($CONFIG['unixroot'] .'/themes');
		
		$i=0;
		
		while ($theme = $themes->read())
		{			
			if (is_dir($CONFIG['unixroot'] .'/themes/'. $theme) && file_exists($CONFIG['unixroot'] .'/themes/'. $theme .'/METADATA'))
			{
				$foundthemes[$i] = parse_ini_file($CONFIG['unixroot'] .'/themes/'. $theme .'/METADATA', FALSE);
				$foundthemes[$i]['dir'] = $theme;
				
				$i++;
				
			}		
		}
		
		return $foundthemes;
	}


	function isValidDate($date)
	{
		$year  = '(19[0-9]{2}|20[0-9]{2})';
		$month = '([0]?[1-9]|1[0-2])';
		$day   = '([0]?[1-9]|[1-2][0-9]|[3][0-1])';
		$sep   = '([-\/\s])';  //FIXME: backslashes should be accepted too but they break the regex 
		
		$matchYMD = "/^{$year}{$sep}{$month}{$sep}{$day}\$/";
		$matchDMY = "/^{$day}{$sep}{$month}{$sep}{$year}\$/";
		$matchMDY = "/^{$month}{$sep}{$day}{$sep}{$year}\$/";
		
		//see if it's in proper ISO format
		if (preg_match($matchYMD, $date, $matches))
		{
			if (strlen($matches[3]) == 1) $matches[3] = "0" . $matches[3];
			if (strlen($matches[5]) == 1) $matches[5] = "0" . $matches[5];
			return "{$matches[1]}-{$matches[3]}-{$matches[5]}";
		}
		
		//see if it's american-style M-D-Y
		if (preg_match($matchMDY, $date, $matches))
		{
			if (strlen($matches[3]) == 1) $matches[3] = "0" . $matches[3];
			if (strlen($matches[5]) == 1) $matches[5] = "0" . $matches[5];
			return "{$matches[5]}-{$matches[1]}-{$matches[3]}";
		}
			
		//see if it's european-style D-M-Y
		if (preg_match($matchDMY, $date, $matches))
		{
			if (strlen($matches[3]) == 1) $matches[3] = "0" . $matches[3];
			if (strlen($matches[5]) == 1) $matches[5] = "0" . $matches[5];
			return "{$matches[5]}-{$matches[3]}-{$matches[1]}";
		}	
		
		return false;
	}
?>
