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


	if ($_REQUEST['action'] == "delete")
	{
		$devicesPresent = getDevicesAssignedToRoom($_REQUEST['roomID']);
		$devicesPast = getPastDevicesAssignedToRoom($_REQUEST['roomID']);
		$room = dbEnumerateRows(getRoom($_REQUEST['roomID']));
		
		if (numRows($devicesPresent))
		{
			print warningBox("The room '{$room['roomName']}' in the '{$room['buildingName']}' building
				cannot be deteted, becase it has one or more devices assigned to it.");
			unset($_REQUEST);
			$_REQUEST['view'] = "edit-rooms";	
		}
		else if (numRows($devicesPast))
		{
			print warningBox("The room '{$room['roomName']}' in the '{$room['buildingName']}' building
				cannot be deteted, becase it has had one or more devices assigned to it in the past.");
			unset($_REQUEST);
			$_REQUEST['view'] = "edit-rooms";
		}
		else if ($_REQUEST['reallyOK'])
		{
			deleteRoom($_REQUEST['roomID']);
			unset($_REQUEST);
			$_REQUEST['view'] = "edit-rooms";
		}
		else 
		{
			print warningBox(form('delete', 'POST', '', "Are you sure you wish to delete the room 
				'{$room['roomName']}' in the '{$room['buildingName']}' building?<br>
				<input type='submit' name='reallyOK' value='Delete'>"));
		}
	}

	if ($_REQUEST['action'] == "edit" && $_REQUEST['roomName'])
	{
		updateRoom($_REQUEST['roomID'], $_REQUEST['roomName'], $_REQUEST['floor'], $_REQUEST['buildingID']);
		
		print successBox("Updated room.");
		
		unset($_REQUEST);
		$_REQUEST['view'] = "edit-rooms";
	}
	
	if ($_REQUEST['action'] == "Add Room" || $_REQUEST['roomID'])
	{	
		addRoom($_REQUEST['roomID'], $_REQUEST['roomName'], $_REQUEST['floor'], $_REQUEST['buildingID']);
		
		unset($_REQUEST);
		$_REQUEST['view'] = "edit-rooms";
	}

	$TITLE = "Rooms"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Rooms"));
						 

	if ($_REQUEST['action'] == "edit")
	{
		$room = dbEnumerateRows(getRoom($_REQUEST['roomID']));
		
		$buildings = getBuildings();
		while ($building = dbEnumerateRows($buildings))
		{
			$sel = ($room['buildingID']==$building['buildingID'])?"SELECTED":"";
			$buildingopts .= "<option value='{$building['buildingID']}' $sel>{$building['buildingName']}</option>";
		}
		
		$editform[] = array("Room ID:", $room['roomID']);
		$editform[] = array("Room Name:", "<input type='text' name='roomName' value='{$room['roomName']}'>");
		$editform[] = array("Floor:", "<input type='text' name='floor' value='{$room['floor']}'>");
		$editform[] = array("Building", "<select name='buildingID'>$buildingopts</select>");
		$editform[] = array("","<input type='submit' name='save' value='Save'>");
		
		$editform = form('edit', 'POST', '', Table::quick($editform));
		
		print mainContentBox("Edit Room '{$room['roomName']}'", NULL, $editform);
	}


	$rooms = getRooms();
	
	while ($room = dbEnumerateRows($rooms))
	{
		$edurl = "{$CONFIG['webroot']}/?view=edit-rooms&amp;action=edit&amp;roomID={$room['roomID']}";
		$delurl = "{$CONFIG['webroot']}/?view=edit-rooms&amp;action=delete&amp;roomID={$room['roomID']}";
		
		$list[] = array(
			"<a href='{$CONFIG['webroot']}/?view=building&amp;buildingID={$room['buildingID']}'>{$room['buildingName']}</a>", 
			$room['floor'], 
			"<a href='{$CONFIG['webroot']}/?view=room&amp;roomID={$room['roomID']}'>{$room['roomName']}</a>", 
			"<a href='$edurl'><img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/edit.png' alt='edit' title='Edit Room'></a>",
			"<a href='$delurl'><img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/delete.png' alt='delete' title='Delete Room'></a>");
	}
	print mainContentBox("Rooms", NULL, Table::quick($list));
	
	
	$buildings = getBuildings();
	while ($building = dbEnumerateRows($buildings))
		$buildingopts .= "<option value='{$building['buildingID']}'>{$building['buildingName']}</option>";
	
	$addform[] = array("Room ID:", "<input type='text' name='roomID'>");
	$addform[] = array("Room Name:", "<input type='text' name='roomName'>");
	$addform[] = array("&nbsp;");
	$addform[] = array("Building:", "<select name='buildingID'>$buildingopts</select>");
	$addform[] = array("Floor #:", "<input type='text' name='floor'>");
	$addform[] = array("", "<input type='submit' name='action' value='Add Room'>");
	
	$addform = form("add", "POST", "", Table::quick($addform));
	
	print mainContentBox("Add Room", NULL, $addform);
?>
