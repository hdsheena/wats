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

	$person = dbEnumerateRows(getPerson($_REQUEST['personID'])); 
	$room = dbEnumerateRows(getRoom($person['roomID']));

	$TITLE = "Person '{$person['nameFirst']} {$person['nameLast']}'"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Person '{$person['nameFirst']} {$person['nameLast']}'"));
						 
	if (in_array("admin", $_SESSION['user']['roles']))
		$ed = true;
	else if (in_array("view", $_SESSION['user']['roles']))
		$ed = false;
	else
	{
		print warningBox("You do not have authorization to view this page."); 
		return;
	}
	
	if (($_REQUEST['nameFirst'] || $_REQUEST['update']) && $ed)
	{
		
		updatePerson
		(
			$_REQUEST['personID'],
			$_REQUEST['nameFirst'],
			$_REQUEST['nameLast'],
			$_REQUEST['email'],
			$_REQUEST['username'],
			$_REQUEST['password'],
			$_REQUEST['roomID'],
			$_REQUEST['isCurrent']
		);
		
		$id = $_REQUEST['personID'];
		unset($_REQUEST);
		$_REQUEST['view'] = "person";
		$_REQUEST['personID'] = $id;
		
		$person = dbEnumerateRows(getPerson($_REQUEST['personID'])); 
		$room = dbEnumerateRows(getRoom($person['roomID']));
	}
	
	
	
	
	$rooms = getRooms();
	$roomopts = "<option value=''>(none)</option>";
	while ($aroom = dbEnumerateRows($rooms))
	{
		$sel = ($person['roomID']==$aroom['roomID'])?"SELECTED":"";
		$roomopts .= "<option value='{$aroom['roomID']}' $sel>{$aroom['buildingName']}/{$aroom['floor']}: {$aroom['roomName']}</option>";
	}
						 
	$details[] = array("Person ID:", $person['personID']);
	$details[] = array("First Name:", $person['nameFirst'], $ed?"<input type='text' name='nameFirst' value='{$person['nameFirst']}'>":"");
	$details[] = array("Last Name:", $person['nameLast'], $ed?"<input type='text' name='nameLast' value='{$person['nameLast']}'>":"");
	$details[] = array("");
	$details[] = array("Username:", $person['username'], $ed?"<input type='text' name='username' value='{$person['username']}'>":"");
	$details[] = array("Password:", "*********", $ed?"<input type='text' name='password' value=''>":"");
	$details[] = array("");
	$details[] = array("Email:", $person['email'], $ed?"<input type='text' name='email' value='{$person['email']}'>":"");
	$details[] = array("");
	$details[] = array("Room:", ($person['roomID'])?"{$room['buildingName']}/{$room['floor']}: {$room['roomName']}":"(none)", $ed?"<select name='roomID'>$roomopts</select>":"");
	$details[] = array("");
	$details[] = array("Current:", ($person['isCurrent'])?"Yes":"No", $ed?"<input type='checkbox' name='isCurrent' value=1 ".(($person['isCurrent'])?"CHECKED":"").">":"");
	$details[] = array("");
	$details[] = array("", "", $ed?"<input type='submit' name='update' value='Update'>":"");

	$details = form("edit", "POST", "", Table::quick($details));
	print mainContentBox("Personal Details", NULL, $details);
	
	
	$devices = getDevicesAssignedToPerson($person['personID']);
	$details = "<ul>";
	while ($device = dbEnumerateRows($devices))
		$details .= "<li><a href='{$CONFIG['webroot']}/?view=device&deviceID={$device['deviceID']}'>{$device['deviceName']}</a> ({$device['vendorName']} {$device['modelName']})</li>";
	$details .= "</ul>";
	print mainContentBox("Assigned Devices", NULL, $details);	
						 
			
?>
