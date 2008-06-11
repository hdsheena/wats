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
	 
	 //assume we've been included in an index.php. if not, bail
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$TITLE = "Manage Device Queue"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Manage Device Queue"));
						 
						 
	
	if ($_REQUEST['deviceID'])
	{
		$device = dbEnumerateRows(getQueuedDevice($_REQUEST['deviceID']));
		
		print infoBox("Fetching information for device {$_REQUEST['deviceID']} ");
		
		if (!$_REQUEST['confirm'] && preg_match('/^[A-Z0-9]{7}$/i', trim($device['deviceID']))) //dell!
		{
			$url = "http://support.dell.com/support/topics/global.aspx/support/my_systems_info/details?~ck=ln&~tab=2&c=us&cs=rc956904&l=en&lnki=0&s=hied&ServiceTag={$device['deviceID']}&ServiceTagNumber={$device['deviceID']}";
			
			$page = http_get($url, null,  $info);
			
			//System Type:</td><td class="gridCellAlt">Latitude D600</td>
			
			$systemtype = 'System Type:';
			$td = '(\<[\/]?td[\sA-Z0-9="-]*\>)';
			$actualtype = '([^\<\>]*)';
			preg_match("/{$systemtype}{$td}{2}{$actualtype}/i", $page, $matches);
			$type = $matches[2];
			
			$model = dbEnumerateRows(getModelByName($type));
			
			if ($model['modelID'])
			{
				print successBox("Found model {$model['modelName']} {$model['typeName']} from {$model['vendorName']}. Updating device record.");
				updateDeviceModel($device['deviceID'], $model['modelID']);
				deleteDeviceFromFetchQueue($device['deviceID']);
			}
			else
			{
				print infoBox("Found new model '$type'. Creating initial model record and updating device.");
				
				$vendor = dbEnumerateRows(getVendorByName("Dell"));
				
				addModel($type, 1, $vendor['vendorID'], 0);
				
				$modelID = insertID();
				
				updateDeviceModel($device['deviceID'], $modelID);
				deleteDeviceFromFetchQueue($device['deviceID']);
			}
			
				
		}
		else
			print infoBox("Could not locate information based on '{$device['deviceID']}'.");
	}
	
	
	$devices = getQueuedDevices();
	
	$table[] = array("Device ID", "Device Name", "Vendor (guessed)", "Fetch");
	
	while ($device = dbEnumerateRows($devices))
	{
		if (preg_match('/^[A-Z0-9]{7}$/i', $device['deviceID']))
		{
			$vendorLike="Dell";
		}
		
		$url="{$CONFIG['webroot']}/?view=device-queue&deviceID={$device['deviceID']}";
		$img ="<img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/fetch.png'>";
		
		$table[] = array($device['deviceID'], $device['deviceName'], $vendorLike, (string)(new HTMLLink($img, $url, "", "")));
	}
	
	print mainContentBox("Queued Devices", NULL, Table::quick($table, true));					 
?>
