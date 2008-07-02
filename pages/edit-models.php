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
	if (!isset($_SESSION['user']) && ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	if (! in_arraY("admin", $_SESSION['user']['roles']))
	{
		print warningBox("You are not authorized to view this page.");
		return;
	}

	if ($_REQUEST['add'] || $_REQUEST['newModelName'])
	{
		$existing  = dbEnumerateRows(getModelByName($_REQUEST['newModelName']));
		if ($existing['modelName'])
			print cautionBox("The model '{$_REQUSET['newModelName']}' is already in the system.");
		else
			addModel($_REQUEST['newModelName'], $_REQUEST['newType'], $_REQUEST['newVendor'], $_REQUEST['newModelValue']);
	}
	
	if ($_REQUEST['edit'] || $_REQUEST['emodelName'])
	{
		updateModel($_REQUEST['model'], $_REQUEST['emodelName'], $_REQUEST['etype'], $_REQUEST['evendor'], $_REQUEST['edefaultValue']);
		
		unset($_REQUEST['action']);
		unset($_REQUEST['emodelName']);
		unset($_REQUEST['evendor']);
		unset($_REQUEST['etype']);
		unset($_REQUEST['edefaultValue']);
	}


	$TITLE = "Manage Models"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Manage Models"));
						
	if ($_REQUEST['action']== "edit")
	{
		$model = dbEnumerateRows(getModel($_REQUEST['model']));
		
		$vendors = getVendors();
		while ($vendor = dbEnumerateRows($vendors))
		{
			$sel = ($vendor['vendorID']==$model['vendorID'])?" SELECTED ":"";
			$vendoropts .= "<option value='{$vendor['vendorID']}' $sel>{$vendor['vendorName']}</option>";
		}
		
		$types = getDeviceTypes();
		while ($type = dbEnumerateRows($types))
		{
			$sel = ($type['typeID']==$model['typeID'])?" SELECTED ":"";
			$typeopts .= "<option value='{$type['typeID']}' $sel>{$type['typeName']}</option>";
		}
		
		$editform[] = array("Model Name:", "<input type='text' name='emodelName' value='{$model['modelName']}'>");
		$editform[] = array("Default Device Value:", "<input type='text' name='edefaultValue' value='{$model['defaultValue']}'>");
		$editform[] = array("Vendor:", "<select name='evendor'>$vendoropts</select>");
		$editform[] = array("Type:", "<select name='etype'>$typeopts</select>");		
		$editform[] = array("", "<input type='submit' name='edit' value='Save'>");
		
		$editform = form("edit", "POST", "", Table::quick($editform));
		
		print mainContentBox("Edit Model '{$model['modelName']}'", NULL, $editform);
	}
	
	
	
	
	$models = getModels();
	
	$modellist[] = array("Model ID", "Type", "Vendor Name", "Model Name", "Value", "Details", "Edit", "Delete");
	
	while ($model = dbEnumerateRows($models))
	{
		$modellist[] = array($model['modelID'], $model['typeName'], $model['vendorName'], $model['modelName'], '$'.$model['defaultValue'],
			"<a href='{$CONFIG['webroot']}/?view=model&amp;model={$model['modelID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/details.png' alt='edit'></a>",
			"<a href='{$CONFIG['webroot']}/?view=edit-models&amp;action=edit&amp;model={$model['modelID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/edit.png' alt='edit'></a>",
			"<a href='{$CONFIG['webroot']}/?view=edit-models&amp;action=delete&amp;model={$model['modelID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/delete.png' alt='delete'></a>");
	}
	
	print mainContentBox("Existing Models", NULL, Table::quick($modellist, true));
	
	
	
	$types = getDeviceTypes();
	$typeopts = "";
	while ($type = dbEnumerateRows($types))
		$typeopts .= "<option value='{$type['typeID']}'>{$type['typeName']}</option>";
		
	$vendors = getVendors();
	$vendoropts = "";
	while ($vendor = dbEnumerateRows($vendors))
		$vendoropts .= "<option value='{$vendor['vendorID']}'>{$vendor['vendorName']}</option>";	
	
	$add[] = array("Model Name:", "<input type='text' name='newModelName'>");
	$add[] = array("Default Value (if no per-device value is specified):", "<input type='text' name='newModelValue'>");
	$add[] = array("Type:", "<select name='newType'>$typeopts</select>");
	$add[] = array("Vendor:", "<select name='newVendor'>$vendoropts</select>");
	$add[] = array("", "<input type='submit' name='add' value='Add'>");
	
	$add = form("add", "POST", "", Table::quick($add));
	
	print mainContentBox("Add New Model", NULL, $add);						
?>
