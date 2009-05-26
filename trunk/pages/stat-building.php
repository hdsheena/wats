<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created may 27, 2008
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
	 
	 //assume we've been included in an index.php. if not, bail
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$TITLE = "Devices By Building"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Devices By Building"));
						
	
	$buildings = getBuildings();
	while ($building = dbEnumerateRows($buildings))
	{		
		$devs = getDevicesByBuilding($building['buildingID']);
		
		$stats = array();
		$num = 0;
		while ($dev = dbEnumerateRows($devs))
		{
			$stats[$dev['typeName']] ++;
			$num++;
		}
		
		$pie="";
		$pieurl="";
		if ($num)
		{
			$i=0;
			foreach ($stats as $name=>$stat)
			{
				$i++;
				$percent = round(100*$stat/$num, 1);
				$pieurl .= "&slice[$i][size]=$percent&slice[$i][name]=$name";
			}
		}
		
		$pie = "<img style='float: right; clear:both' src='{$CONFIG['webroot']}/lib/piechart.php?q$pieurl' alt='piechart'>";
		
		print mainContentBox($building['buildingName'], NULL, print_r($stats, true) . $pie);
	} 					
?>
