<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created Jan 22, 2010
	 * 
	 * @copyright: (C)2010 The Evergreen School
	 * 
	 * This program is free software: you can redistribute it and/or modify 
	 * it under the terms of the GNU Affero General Public License version 3 as published by
	 * the Free Software Foundation.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU Affero General Public License for more details.
	 * 
	 * You should have received a copy of the GNU Affero General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/agpl-3.0.html>.
	 */
	 
	 //assume we've been included in an index.php. if not, bail
	if (!isset($_SESSION['user']) && ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$ONLOAD="document.unassign.deviceID.focus();";
	$TITLE = "Batch Unassign"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Batch Unassign"));
						
						
	
	if ($_REQUEST['deviceID'])
	{
		$result = closeOutstandingAssignmentsForDevice($_REQUEST['deviceID']);
		
		if (affectedRows($result))
			print successBox("Device unassigned.");
		unset ($_REQUEST['deviceID']);
	}	
	
	
	$form[] = array("Device ID:", "<input type='text' id=deviceID name='deviceID'>");
	$form[] = array("", "<input type='submit' name='unassign' value='Unassign'>");
	
	print mainContentBox("Unassign Device", NULL, form('unassign', 'POST', '', Table::quick($form)));
?>
