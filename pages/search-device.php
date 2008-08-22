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

	$TITLE = "Device Search"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Device Search"));
						
						
	$statuses = getStatuses();
	$statusopts .= "<option value=''></option>";
	while ($status = dbEnumerateRows($statuses))
	{
		$sel = ($_REQUEST['statusID'] == $status['statusID'])?"SELECTED":"";
		$statusopts .= "<option value='{$status['statusID']}' $sel>{$status['statusName']}</option>";
	}				
						
	$searchform[] = array("Device ID:", "<input type='text' name='deviceID' value='{$_REQUEST['deviceID']}'>");
	$searchform[] = array("Device Name:", "<input type='text' name='deviceName' value='{$_REQUEST['deviceName']}'>");
	$searchform[] = array("Device Status:", "<select name='statusID'>$statusopts</select>");
	$searchform[] = array("", "<input type='submit' name='search' value='Search'>");
	
	print mainContentBox("Search", 	NULL, form('search', 'GET', '', Table::quick($searchform)));
	

	if (($_REQUEST['deviceID'] && $_REQUEST['statusID']) || ($_REQUEST['deviceName'] && $_REQUEST['statusID']))
	{
		$devform = array();
		
		$devices = getDevicesBySearch($_REQUEST['deviceID'], $_REQUEST['deviceName'], $_REQUEST['statusID']);
		while ($device = dbEnumerateRows($devices))
		{
			$devassign = dbEnumerateRows(getCurrentDeviceAssignment($device['deviceID']));
		
			$url = "{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}";
		
			$devform[] = array("<a href='$url'>{$device['deviceName']}</a>", "{$device['vendorName']} {$device['modelName']}");
		
			$devform[] = array("", "Status: {$device['statusName']}");
		
			if ($devassign['personID'])
				$devform[] = array("", "Assigned to person: <a href='{$CONFIG['webroot']}/index.php?view=person&amp;personID={$devassign['personID']}'>{$devassign['nameFirst']} {$defassign['nameLast']}</a> since {$devassign['dateAssigned']}");
			else if ($devassign['roomID'])
				$devform[] = array("", "Assigned to room: <a href='{$CONFIG['webroot']}/index.php?view=room&amp;roomID={$devassign['roomID']}'>{$devassign['roomName']}</a> since {$devassign['dateAssigned']}");
			else	
				$devform[] = array("", "Unassigned");
		}
			
		$devform = Table::quick($devform);
		
		print mainContentBox("Combined Results", NULL, $devform);
	}


	if ($_REQUEST['deviceID'])					
	{
		$devform = array();
		
		$device = dbEnumerateRows(getDevice($_REQUEST['deviceID']));
		$devassign = dbEnumerateRows(getCurrentDeviceAssignment($_REQUEST['deviceID']));
		
		if ($device['deviceID'])
		{
			$url = "{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}";
		
			$devform[] = array("<a href='$url'>{$device['deviceName']}</a>", "{$device['vendorName']} {$device['modelName']}");
		
			$devform[] = array("", "Status: {$device['statusName']}");
		
			if ($devassign['personID'])
				$devform[] = array("", "Assigned to person: {$devassign['nameFirst']} {$defassign['nameLast']} since {$devassign['dateAssigned']}");
			else if ($devassign['roomID'])
				$devform[] = array("", "Assigned to room: {$devassign['roomName']} since {$devassign['dateAssigned']}");
			else	
				$devform[] = array("", "Unassigned");
		}	
		
		$devform = Table::quick($devform);
		
		print mainContentBox("Devices matching ID '{$_REQUEST['deviceID']}'", NULL, $devform);
	}
	
	if ($_REQUEST['deviceName'])					
	{
		$devform = array();
		
		$devices = getDevicesLikeName($_REQUEST['deviceName']);
		while ($device = dbEnumerateRows($devices))
		{
			$devassign = dbEnumerateRows(getCurrentDeviceAssignment($device['deviceID']));
		
			$url = "{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}";
		
			$devform[] = array("<a href='$url'>{$device['deviceName']}</a>", "{$device['vendorName']} {$device['modelName']}");
		
			$devform[] = array("", "Status: {$device['statusName']}");
		
			if ($devassign['personID'])
				$devform[] = array("", "Assigned to person: <a href='{$CONFIG['webroot']}/index.php?view=person&amp;personID={$devassign['personID']}'>{$devassign['nameFirst']} {$devassign['nameLast']}</a> since {$devassign['dateAssigned']}");
			else if ($devassign['roomID'])
				$devform[] = array("", "Assigned to room: <a href='{$CONFIG['webroot']}/index.php?view=room&amp;roomID={$devassign['roomID']}'>{$devassign['roomName']}</a> since {$devassign['dateAssigned']}");
			else	
				$devform[] = array("", "Unassigned");
		}
			
		$devform = Table::quick($devform);
		
		print mainContentBox("Devices matching Name '{$_REQUEST['deviceName']}'", NULL, $devform);
	}
	
	if ($_REQUEST['statusID'])					
	{
		$devform = array();
		$status = dbEnumerateRows(getStatus($_REQUEST['statusID']));
		
		$devices = getDevicesWithStatus($_REQUEST['statusID']);
		while ($device = dbEnumerateRows($devices))
		{
			$devassign = dbEnumerateRows(getCurrentDeviceAssignment($device['deviceID']));
		
			$url = "{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}";
		
			$devform[] = array("<a href='$url'>{$device['deviceName']}</a>", "{$device['vendorName']} {$device['modelName']}");
		
			$devform[] = array("", "Status: {$device['statusName']}");
		
			if ($devassign['personID'])
				$devform[] = array("", "Assigned to person: <a href='{$CONFIG['webroot']}/index.php?view=person&amp;personID={$devassign['personID']}'>{$devassign['nameFirst']} {$devassign['nameLast']}</a> since {$devassign['dateAssigned']}");
			else if ($devassign['roomID'])
				$devform[] = array("", "Assigned to room: <a href='{$CONFIG['webroot']}/index.php?view=room&amp;roomID={$devassign['roomID']}'>{$devassign['roomName']}</a> since {$devassign['dateAssigned']}");
			else	
				$devform[] = array("", "Unassigned");
		}
			
		$devform = Table::quick($devform);
		
		print mainContentBox("Devices With Status '{$status['statusName']}'", NULL, $devform);
	}

?>
