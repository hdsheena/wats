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
	 
	 
	chdir("../");
	require_once("config.php");
	require_once("lib/db.php");
	require_once("lib/page.php");

	header('Content-type: text/csv; charset=iso-8859-1');
	header('Content-disposition: attachment; filename="inventory-devices.csv"');
	
	print '"deviceID","assetTag","deviceName","value","dateInventoried","inventoriedBy","datePurchased","dateRecycled","status","modelName","vendorName","deviceType","assignmentType","assignee","assignedSince"'."\r\n";
	
	$devices = getAllDevices();
	
	while ($device = dbEnumerateRows($devices))
	{
		$row = array();
		$assignment = dbEnumerateRows(getDeviceAssignments($device['deviceID']));
	
		$row[] = $device['deviceID'];
		$row[] = $device['assetTag'];
		$row[] = $device['deviceName'];
		$row[] = $device['value'];
		$row[] = $device['dateInventoried'];
		$row[] = $device['nameFirst'] . " " . $device['nameLast'];
		$row[] = $device['datePurchased'];
		$row[] = $device['dateRemoved'];
		$row[] = $device['statusName'];
		$row[] = $device['modelName'];
		$row[] = $device['vendorName'];
		$row[] = $device['typeName'];
		
		if ($assignment['roomID'])
		{
			$row[] = "room";
			$row[] = $assignment['roomName'];
			$row[] = $assignment['dateAssigned'];
		} 	
		else if ($assignment['personID'])
		{
			$row[] = "person";
			$row[] = $assignment['nameFirst'] . " " .$assignment['nameLast'];
			$row[] = $assignment['dateAssigned'];
		}
		else
		{
			$row[] = "";
			$row[] = "";
			$row[] = "";
		}	
		
		print arrayToCSVLine($row);
	}
	
	function arrayToCSVLine($array)
	{
		$line = "";
		$first = true;
		foreach ($array as $cell)
		{
			$cell = preg_replace('/([^"])["]([^"])/', '\1""\2', $cell);
			
			if (! $first)
				$line .= ",";
			
			$line .= "\"$cell\"";
			
			$first = false;
		}
		
		$line .= "\r\n";
		
		return $line;
	}
?>
