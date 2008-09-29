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

	if ($_REQUEST['deviceID'] && in_array("admin", $_SESSION['user']['roles']))
	{
		$device = dbEnumerateRows(getDevice($_REQUEST['deviceID']));
		if ($device['deviceID'])
		{
			print cautionBox("The device you have entered is already in the system. 
				Perhaps you want to <a href='{$CONFIG['webroot']}/?view=device&deviceID={$_REQUEST['deviceID']}'>edit it's record</a>.");
		}
		else
		{
			$_REQUEST['modelID'] = ($_REQUEST['modelIDtxt'])?$_REQUEST['modelIDtxt']:$_REQUEST['modelIDdrop'];
			
			if ($_REQUEST['value'] == "" || !is_numeric($_REQUEST['value']))
			{
				$model= dbEnumerateRows(getModel($_REQUEST['modelID']));
				$_REQUEST['value'] = $model['defaultValue'];
			}
			
			
			$result = addDeviceFull
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
			
			if (affectedRows())
			{
				unset($_REQUEST['deviceID']);
				unset($_REQUEST['assetTag']);
				unset($_REQUEST['deviceName']);
				unset($_REQUEST['modelID']);
				unset($_REQUEST['value']);
				unset($_REQUEST['datePurchased']);
				unset($_REQUEST['dateRecycled']);
				unset($_REQUEST['statusID']);
				print successBox("Device Added.");
			}
			else
				print warningBox("An error occurred while adding the device.");
		}
	}


	$TITLE = "Add Device"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Add Device"));
	
	$models = getModels();
	$modelopts = "<option value=''></option>";
	while ($model = dbEnumerateRows($models))
		$modelopts .= "<option value='{$model['modelID']}'>{$model['vendorName']} {$model['modelName']} ({$model['typeName']})</option>";

	$statuses = getStatuses();
	while ($status = dbEnumerateRows($statuses))
		$statusopts .= "<option value='{$status['statusID']}'>{$status['statusName']}</option>";

	$form[] = array("Device ID:", "<input type='text' name='deviceID'>");
	$form[] = array("Asset Tag:", "<input type='text' name='assetTag'>");
	$form[] = array("", "");
	$form[] = array("Device Name:", "<input type='text' name='deviceName'>");
	$form[] = array("", "");
	$form[] = array("Model", "<input type='text' name='modelIDtxt'>", "OR", "<select name='modelIDdrop'>$modelopts</select>");
	$form[] = array("", "");
	$form[] = array("Value", "<input type='text' name='value'>");
	$form[] = array("Date Purchased", "<input type='text' name='datePurchased'>");
	$form[] = array("Date Recycled <br>(leave blank if Active)", "<input type='text' name='dateRecycled'>");
	$form[] = array("Status", "<select name='statusID'>$statusopts</select>");
	$form[] = array("", "");
	$form[] = array("", "<input type='submit' name='add' value='Add'>");
	
	$form = form("add", "POST", "", Table::quick($form));
	print mainContentBox("Add Device", NULL, $form);
?>
