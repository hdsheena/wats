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


	$room = dbEnumerateRows(getRoom($_REQUEST['roomID'])); 

	$TITLE = "Room '{$room['roomName']}'"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Room '{$room['roomName']}'"));
						 
	if (in_array("view", $_SESSION['user']['roles']) || in_array("admin", $_SESSION['user']['roles']))
	{					 
		$details .= "<ul>
			<li>Room ID: {$room['roomID']}</li>
			<li>Building: {$room['buildingName']}</li>
			<li>Floor: {$room['floor']}</li>
			</ul>";
		print mainContentBox("Room Details", NULL, $details);
	
	
		$devices = getDevicesAssignedToRoom($room['roomID']);
		$details = "<ul>";
		while ($device = dbEnumerateRows($devices))
			$details .= "<li><a href='{$CONFIG['webroot']}/?view=device&deviceID={$device['deviceID']}'>{$device['deviceName']} ({$device['vendorName']} {$device['modelName']})</a></li>";
		$details .= "</ul>";
		print mainContentBox("Assigned Devices", NULL, $details);	
	
	
		$details = "<ul>";
		$occupants = getPeopleInRoom($room['roomID']);
		while ($occupant = dbEnumerateRows($occupants))
		{
			$details .= "<li><a href='{$CONFIG['webroot']}/?view=person&amp;personID={$occupant['personID']}'>{$occupant['nameFirst']} {$occupant['nameLast']}</a></li>";
		}
		$details .= "</ul>";
		print mainContentBox("Occupants", NULL, $details);					 
	}
	else
		print cautionBox("You have not been granted the appropriate role required to view this page.");
						 
?>
