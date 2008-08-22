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
	 
	 //assume we've been included in an index.php. if not, bail
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$TITLE = "Device Lifespan"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Device Lifespan"));
						
	
	$active = dbEnumerateRows(getStatusByName("Active"));
	
	//these are unique queries that will almost certainly never be repeated...
	$oldquery = "SELECT * FROM `device`
				JOIN `model` using (`modelID`)
				JOIN `type` using (`typeID`)
				JOIN `vendor` USING (`vendorID`)
				WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL AND `statusID`='{$active['statusID']}'
				ORDER BY `datePurchased` 
				LIMIT 5;";
	$olddevices = db_query($oldquery);			
	
	$newquery = "SELECT * FROM `device`
				JOIN `model` using (`modelID`)
				JOIN `type` using (`typeID`)
				JOIN `vendor` USING (`vendorID`)
				WHERE `datePurchased` IS NOT NULL  AND `dateRemoved` IS NULL AND `statusID`='{$active['statusID']}'
				ORDER BY `datePurchased` DESC
				LIMIT 5;";
	$newdevices = db_query($newquery);	
	
	$newoldlist[] = array("Newest Devices", "",  "Oldest Devices");
	
	for ($i=0; $i < 5; $i++)
	{
		$newdevice = dbEnumerateRows($newdevices);
		$olddevice = dbEnumerateRows($olddevices);

		$newdevice['age'] = time() - strtotime($newdevice['datePurchased']);
		$olddevice['age'] = time() - strtotime($olddevice['datePurchased']);

		$newdevice['ageyears'] = floor($newdevice['age'] / 31536000);
		$newdevice['age'] = $newdevice['age'] % 31536000;
		
		$newdevice['ageweeks'] = floor($newdevice['age'] / 604800);
		$newdevice['age'] = $newdevice['age'] % 604800;
		
		$newdevice['agedays'] = floor($newdevice['age'] / 86400);

		$olddevice['ageyears'] = floor($olddevice['age'] / 31536000);
		$olddevice['age'] = $olddevice['age'] % 31536000;
		
		$olddevice['ageweeks'] = floor($olddevice['age'] / 604800);
		$olddevice['age'] = $olddevice['age'] % 604800;
		
		$olddevice['agedays'] = floor($olddevice['age'] / 86400);

		$newagetext = ($newdevice['ageyears']?"{$newdevice['ageyears']} years":"") . ($newdevice['ageweeks']?"{$newdevice['ageweeks']} weeks":"") . ($newdevice['agedays']?"{$newdevice['agedays']} days":"") ;

		$newoldlist[] = array
		(
			"<a href='{$CONFIG['webroot']}/?view=device&amp;deviceID={$newdevice['deviceID']}'>{$newdevice['deviceName']}</a>  ($newagetext)",
			"&nbsp;",
			"<a href='{$CONFIG['webroot']}/?view=device&amp;deviceID={$olddevice['deviceID']}'>{$olddevice['deviceName']}</a> ({$olddevice['ageyears']} years, {$olddevice['ageweeks']} weeks, {$olddevice['agedays']} days)"
		);
	}	
								
	print mainContentBox("Newest and Oldest Devices", NULL, Table::quick($newoldlist, true));	
	
	$total = dbEnumerateRows(db_query("SELECT COUNT(deviceID) from `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL;"));
	$total[1] = $total[0]/2;   //median position
	$total[2] = $total[0]/4;   //lower quartile position
	$total[3] = $total[0]/4*3; //upper quartile position
	
	$median = dbEnumerateRows(db_query("SELECT datePurchased FROM `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL ORDER BY `datePurchased` LIMIT 1 OFFSET {$total[1]} ;"));
	$min = dbEnumerateRows(db_query("SELECT MIN(datePurchased) FROM `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL ORDER BY `datePurchased` LIMIT 1 OFFSET {$total[1]} ;"));
	$max = dbEnumerateRows(db_query("SELECT MAX(datePurchased) FROM `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL ORDER BY `datePurchased` LIMIT 1 OFFSET {$total[1]} ;"));
	$uq = dbEnumerateRows(db_query("SELECT datePurchased FROM `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL ORDER BY `datePurchased` LIMIT 1 OFFSET {$total[3]} ;"));
	$lq = dbEnumerateRows(db_query("SELECT datePurchased FROM `device` WHERE `datePurchased` IS NOT NULL AND `dateRemoved` IS NULL ORDER BY `datePurchased` LIMIT 1 OFFSET {$total[2]} ;"));
	
	print mainContentBox("Age Plot", NULL, "<img src='{$CONFIG['webroot']}/lib/boxplot.php' alt='boxplot' title='Quantity by Age'>");				
?>
