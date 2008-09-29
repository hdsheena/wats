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

	$TITLE = "Batch Add to Status"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Batch Add to Status"),
						 array('name'=>"Step 1 - Choose Person"));
	

	$ONLOAD="document.form.focusme.focus();";

	$_REQUEST['modelID'] = ($_REQUEST['modelIDtxt'])?$_REQUEST['modelIDtxt']:$_REQUEST['modelIDdrop'];

	if ($_REQUEST['statusID'])
	{
		if (preg_match('/^[A-Z0-9]{7}$/i', $_REQUEST['deviceID']) && ! $_REQUEST['modelID']) //dell
		{
			//skip modelID form, since we can pull it all from Dell	
			print infoBox("Found a Dell Device ID. Dell equipment does not require a model ID to be entered in batch mode.");
			addDeviceToFetchQueue($_REQUEST['deviceID']);
			$_REQUEST['modelID']="NULL";
		} 
		
		if ($_REQUEST['deviceID'] && $_REQUEST['modelID'] && $_REQUEST['deviceName'])
		{			
			$res1 = addDeviceFull($_REQUEST['deviceID'], "", $_REQUEST['deviceName'], $_REQUEST['modelID'], "" , "", "", $_REQUEST['statusID']);
			
			if($res1)
				print successBox("Device Added");
			else
			{
				print warningBox("An error occurred. Device not added.");
			}
			$_REQUEST['deviceID']="";
			$_REQUEST['modelID']="";
			$_REQUEST['deviceName']="";
		}
		
		if ($_REQUEST['deviceID'] && $_REQUEST['modelID'] && ! $_REQUEST['deviceName'])
		{
			$BREADCRUMBS[1]['link']="{$CONFIG['webroot']}/?view=batch-add-room";
			$BREADCRUMBS[2]['name']="Step 4 - Device Name";
			
			$nameform[] = array("Device Name:", "<input id='focusme' type='text' name='deviceName'>");
			$nameform[] = array("", "<input type='submit' name='submit' value='Next'>");
		
			$nameform = form('form', 'POST', '', Table::quick($nameform));
			print mainContentBox("Device Name", NULL, $nameform);
		}
			
		if ($_REQUEST['deviceID'] && ! $_REQUEST['modelID'] && ! $_REQUEST['deviceName'])
		{
			$BREADCRUMBS[1]['link']="{$CONFIG['webroot']}/?view=batch-add-room";
			$BREADCRUMBS[2]['name']="Step 3 - Model ID";
			
			$models = getModels();
			$modelopts = "<option value=''> </option>";
			while ($model = dbEnumerateRows($models))
				$modelopts .= "<option value='{$model['modelID']}'>{$model['vendorName']} {$model['typeName']} {$model['modelName']}</option>";
			
			$modelform[] = array
			(
				"Model ID:",
				"<input id='focusme' type='text' name='modelIDtxt'>",
				"OR",
				"<select name='modelIDdrop'>$modelopts</select>"
			);
			$modelform[] = array("", "<input type='submit' name='submit' value='Next'>");
		
			$modelform = form('form', 'POST', '', Table::quick($modelform));
			print mainContentBox("Model ID", NULL, $modelform);
		}
		
		if (! $_REQUEST['deviceID'] && ! $_REQUEST['modelID'] && ! $_REQUEST['deviceName'])
		{
			$BREADCRUMBS[1]['link']="{$CONFIG['webroot']}/?view=batch-add-room";
			$BREADCRUMBS[2]['name']="Step 2 - Device ID";
			
			$deviceform[] = array("Device ID:", "<input id='focusme' type='text' name='deviceID'>");
			$deviceform[] = array("", "<input type='submit' name='submit' value='Next'>");
		
			$deviceform = form('form', 'POST', '', Table::quick($deviceform));
			print mainContentBox("Device ID", NULL, $deviceform);
		}
	}
	else
	{
		$statuses = getStatuses();
		while ($status = dbEnumerateRows($statuses))
			$statusopts .= "<option value='{$status['statusID']}'>{$status['statusName']}</option>";

		
		$statusform[] = array("Status:", "<select name='statusID'  id='focusme'>$statusopts</select>");
		$statusform[] = array("", "<input type='submit' name='submit' value='Select'>");
		
		$statusform = form('form', 'POST', '', Table::quick($statusform));
		
		print mainContentBox("Select Status", NULL, $statusform);
	}
?>
