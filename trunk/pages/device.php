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

	
	if ( ! (in_array("view", $_SESSION['user']['roles']) || in_array("inventory", $_SESSION['user']['roles'])))
	{
		print cautionBox("You do not have sufficient permission to view this page.");
		return;
	}
	else if (in_array("inventory", $_SESSION['user']['roles']))
		$ed = true;
	else
		$ed=false;

	$device = dbEnumerateRows(getDevice($_REQUEST['deviceID']));


	if (($_REQUEST['reassign'] || $_REUQEST['to']) && $ed)
	{
		closeOutstandingAssignmentsForDevice($_REQUEST['deviceID']);
		
		if ($_REQUEST['to'] == "room")
		{
			if ($_REQUEST['roomID'])
				$_REQUEST['room'] = $_REQUEST['roomID'];
			
			assignDeviceToRoom($_REQUEST['deviceID'], $_REQUEST['room']);
		}
		else if ($_REQUEST['to'] == "person")
		{
			if ($_REQUEST['personID'])
				$_REQUEST['person'] = $_REQUEST['personID'];
				
			assignDeviceToPerson($_REQUEST['deviceID'], $_REQUEST['person']);
		}
			
		setDeviceStatus($_REQUEST['deviceID'], 1);	
		
		unset($_REQUEST['reassign']);
		unset($_REQUEST['to']);
		unset($_REQUEST['room']);
		unset($_REQUEST['person']);
	}
	
	if ($_REQUEST['action'] == "unassign")
	{
		$assignment = dbEnumerateRows(getDeviceAssignment($_REQUEST['assignment']));
		
		if ($_REQUEST['reallyUnassign'])
		{
			closeOutstandingAssignmentsForDevice($_REQUEST['deviceID']);
			
			if ($assignment['roomID'])
				print successBox("Device '' removed from room '{$room['roomName']}'");
			else
				print successBox("Device '' unassigned from '{$room['nameFirst']}'");
			
			unset($_REQUEST['action']);
			unset($_REQUEST['assignment']);
		}
		else
		{
			$form  = "<h3>Your permission is needed to continue</h3> Do you really want to unassign the device '{$device['deviceName']}' from ";
			if ($assignment['roomID'])
				$form .= "the room '{$assignment['roomName']}'? ";
			else
				$form .= "'{$assignment['nameFirst']}'? ";
			
			$form .= " If you unassign a device and subsequently reassign it, it will still show up as two separate assignments.
				<br><input type='submit' name='reallyUnassign' value='Unassign'> <input type='submit' name='action' value='Cancel'>";	
				
			print warningBox(form('unassign', 'POST', '', $form));	
		}
	}
	
	if ($_REQUEST['action'] == "Cancel")
	{
		unset($_REQUEST['action']);
		unset($_REQUEST['assignment']);
	}
	
	if (($_REQUEST['editDeviceID'] || $_REQUEST['save']) && $ed)
	{
		//($id, $asset, $name, $model, $value, $purchased, $removed, $status)
		updateDevice
		(
			$_REQUEST['deviceID'],
			$_REQUEST['assetTag'],
			$_REQUEST['deviceName'],
			$_REQUEST['modelID'],
			$_REQUEST['value'],
			$_REQUEST['datePurchased'],
			$_REQUEST['dateRemoved'],
			$_REQUEST['statusID']
		);
		
		$device = dbEnumerateRows(getDevice($_REQUEST['deviceID']));
		
		unset($_REQUEST);
		$_REQUEST['view']= "device";
		$_REQUEST['deviceID'] = $device['deviceID'];
	}

	

	$TITLE = "Viewing Device '{$device['deviceName']}'"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Search", 'link' => "{$CONFIG['webroot']}/?view=search-device&amp;deviceID={$device['deviceID']}"),
						array('name' => "Device '{$device['deviceName']}'"));
						

	$statuses = getStatuses();
	while ($status = dbEnumerateRows($statuses))
	{
		$sel = ($device['statusID'] == $status['statusID'])?"SELECTED":"";
		$statusopts .= "<option value='{$status['statusID']}' $sel>{$status['statusName']}</option>";
	}
	
	$models = getModels();
	while ($model = dbEnumerateRows($models))
	{
		$sel = ($device['modelID'] == $model['modelID'])?"SELECTED":"";
		$modelopts .= "<option value='{$model['modelID']}' $sel>{$model['vendorName']} {$model['typeName']} {$model['modelName']}</option>";
	}		

	$deviceform[] = array("ID:", $device['deviceID'],($ed)?"<input type='hidden' name='editDeviceID' value='{$device['deviceID']}'>":"" );
	$deviceform[] = array("Asset Tag:", $device['assetTag'], ($ed)?"<input type='text' name='assetTag' value='{$device['assetTag']}'>":"");
	$deviceform[] = array("Status:", $device['statusName'], ($ed)?"<select name='statusID'>$statusopts</select>":"");
	$deviceform[] = array("Name:", $device['deviceName'], ($ed)?"<input type='text' name='deviceName' value='{$device['deviceName']}'>":"");
	$deviceform[] = array("Device Type:", $device['typeName']);
	$deviceform[] = array("Model:", $device['vendorName'] .' '. $device['modelName'], ($ed)?"<select name='modelID'>$modelopts</select>":"");
	$deviceform[] = array("Value:", '$'.$device['value'], ($ed)?"$<input type='text' name='value' value='{$device['value']}'>":"");
	$deviceform[] = array("Inventoried By:", "{$device['nameFirst']} {$device['nameLast']}");
	$deviceform[] = array("Inventoried On:", $device['dateInventoried']);
	$deviceform[] = array("Purchased On:", $device['datePurchased'], ($ed)?"<input type='date' name='datePurchased' value='{$device['datePurchased']}'>":"");
	$deviceform[] = array("Removed On:", $device['dateRemoved'], ($ed)?"<input type='text' name='dateRemoved' value='{$device['dateRemoved']}'>":"");
	if ($ed)
		$deviceform[] = array("", "", "<input type='submit' name='save' value='Save'>");

	$deviceform = form("edit", 'POST', '', Table::quick($deviceform));
	print mainContentBox("Device Information", NULL, $deviceform);	
	
	
	
	$assignments  = getDeviceAssignments($device['deviceID']);
	
	if ($ed)
		$history[] = array("Assignment Type", "Assignee", "From", "Until", "Unassign");
	else
		$history[] = array("Assignment Type", "Assignee", "From", "Until");
	
	while ($assignment = dbEnumerateRows($assignments))
	{
		if ($assignment['roomID'])
		{
			$type="Room";
			$assignee = "<a href='{$CONFIG['webroot']}/?view=room&amp;roomID={$assignment['roomID']}'>{$assignment['roomName']}</a>";
		}
		else if ($assignment['personID'])
		{
			$type="Person";
			$assignee = "<a href='{$CONFIG['webroot']}/?view=person&amp;personID={$assignment['personID']}'>{$assignment['nameFirst']} {$assignment['nameLast']}</a>";
		}
		else
		{
			$type = "{$assignment['roomID']} {$assignment['personID']}"; 
		}
		
		if ($ed && !$assignment['dateRemoved'])
		{
			$unassign = "<a href='{$CONFIG['webroot']}/index.php?view=device&amp;deviceID={$device['deviceID']}&amp;action=unassign&amp;assignment={$assignment['assignmentID']}'>";
			$unassign .= "<img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/unassign.png' title='Unassign' alt='unassign'>";
			$unassign .= "</a>";
			$history[] = array($type, $assignee, $assignment['dateAssigned'], $assignment['dateRemoved'], $unassign);
		}
		else
			$history[] = array($type, $assignee, $assignment['dateAssigned'], $assignment['dateRemoved']);
	}
	
	$history = Table::quick($history, true);
	print mainContentBox("Device Assignment History", NULL, $history);		
	
	if ($ed && $device['statusID'] == 1)
	{	
		
		$rooms = getRooms();
		$roomopts = '<option value=""></option>';
		while ($room = dbEnumerateRows($rooms))
			$roomopts .= "<option value='{$room['roomID']}'>{$room['roomName']} ({$room['roomID']})</option>";
		
		$people = getPeople();
		$peopleopts = '<option value=""></option>';
		while ($person = dbEnumerateRows($people))
			$peopleopts .= "<option value='{$person['personID']}'>{$person['nameFirst']} {$person['nameLast']}</option>";
		
		
		
		$reassign[] = array("To Room:", "<input type='radio' id='toroom' name='to' value='room'><select name='room' onchange='document.getElementById(\"toroom\").checked=true;'>$roomopts</select>", "or", "<input type='text' name='roomID' onchange='document.getElementById(\"toroom\").checked=true;'>");
		$reassign[] = array("To Person:", "<input type='radio' id='toperson' name='to' value='person'><select name='person' onchange='document.getElementById(\"toperson\").checked=true;'>$peopleopts</select>", "or", "<input type='text' name='personID' onchange='document.getElementById(\"toperson\").checked=true;'>");
		$reassign[] = array("","<input type='submit' name='reassign' value='Reassign'>"); 
		
		$reassign = form('reassign', 'POST', '', Table::quick($reassign));
		print mainContentBox("Reassign Device", NULL, $reassign);
	}		
?>
