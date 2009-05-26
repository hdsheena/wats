<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created May 26, 2009
	 * 
	 * @copyright: (C)2009 The Evergreen School
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

	if (! in_array("admin", $_SESSION['user']['roles']))
	{
		print warningBox("You are not authorized to view this page.");
		return;
	}
	
	if ($_REQUEST['newStatus'])
	{
		$result = addStatus($_REQUEST['newStatus']);
		
		if (affectedRows($result) > 0 && $result !== false)
			print successBox("Status Added.");
		else
			print warningBox("Could not add new status.");
	}
	
	if ($_REQUEST['action'] == "delete")
	{
		$result = deleteStatus($_REQUEST['status']);
		
		if (affectedRows($result) > 0 && $result !== false)
			print successBox("Status deleted.");
		else
			print warningBox("Could not add delete the status. The most likely cause is that devices are associated with it.");
		unset($_REQUEST['action'], $_GET['action']);
	}

	$TITLE = "Manage Device Statuses"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Manage Device Statuses"));
						
	
	
	$current[] = array("Status", "List Devices", "Delete");
	
	$statuses = getStatuses();
	while ($status = dbEnumerateRows($statuses))
	{
		$current[] = array
		(
			$status['statusName'],
			"<a href='{$CONFIG['webroot']}/?view=search-device&amp;statusID={$status['statusID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/details.png' alt='details'></a>",
			"<a href='{$CONFIG['webroot']}/?view=edit-statuses&amp;action=delete&amp;status={$status['statusID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/delete.png' alt='delete'></a>"
		);
	}
		
	$current = Table::quick($current, true);
	print mainContentBox("Current Statuses", NULL, $current);	
	
	
	
	$new[] = array("<label for='newStatus'>Status Name:</label>", "<input type='text' id='newStatus' name='newStatus'>");
	$new[] = array("", "<input type='submit' name='add' value='Add Status'>");
	
	print mainContentBox("Add Status", NULL, form('new', 'POST', '', Table::quick($new)));
?>
