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

	$TITLE = "Device Types"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Device Types"));
						 
	if (! in_arraY("admin", $_SESSION['user']['roles']))
	{
		print warningBox("You are not authorized to view this page.");
		return;
	}
	
	if ($_REQUEST['newTypeName'])
	{
		$type = dbEnumerateRows(getDeviceTypeByName(trim($_REQUEST['newTypeName'])));
		if ($type['typeName'])
			print cautionBox("The type you have entered already exists.");
		else
		{
			addDeviceType($_REQUEST['newTypeName']);
		}
		
		unset($_REQUEST['newTypeName']);
	}
	
	if ($_REQUEST['action']=="delete")
	{
		$type = dbEnumerateRows(getDeviceType($_REQUEST['deviceType']));
		$models = getModelsOfType($_REQUEST['deviceType']);
		
		if (! numRows($models))
		{
			deleteDeviceType($_REQUEST['deviceType']);
			print successBox("'{$type['typeName']}' deleted.");
		}
		else
			print warningBox("Cannot delete device type '{$type['typeName']}': At least one Model is in this category."); 
			
		unset($_REQUEST['action']);
		unset($_REQUEST['deviceType']);
	}
	
	$types = getDeviceTypes();
	
	while ($type = dbEnumerateRows($types))
	{
		$typelist[] = array($type['typeID'], $type['typeName'], 
			"<a href='{$CONFIG['webroot']}/?view=edit-device-types&amp;action=delete&amp;deviceType={$type['typeID']}'>
			<img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/delete.png' alt='delete'></a>");
	}
	
	print mainContentBox("Existing Types", NULL, Table::quick($typelist));
	
	
	$add[] = array("Type Name:", "<input type='text' name='newTypeName'>");
	$add[] = array("", "<input type='submit' name='submit' value='Add'>");
	
	$add = form("add", "POST", "", Table::quick($add));
	
	print mainContentBox("Add New Type", NULL, $add);
?>
