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

	$TITLE = "Inventory Dashboard"; 
	$BREADCRUMBS = array(array('name' => "Home"));
	


	
	$unassignedActive = getUnassignedActiveDevices();
	while ($device = dbEnumerateRows($unassignedActive))
	{
		$list .= "<li><a href='{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}'>{$device['deviceName']} ({$device['deviceID']})</a></li>";
	}
	if ($list)
		print mainContentBox("Active but Homeless Devices", NULL, "<p>Devices in this list are marked 'Active' but have not been assigned to a person
			or room. Please assign them or change their status as appropriate.<ul>$list</ul>");



	$status = dbEnumerateRows(getStatusByName("Lost"));
	$lost = getDevicesWithStatus($status['statusID']);
	$list = "";
	while ($device = dbEnumerateRows($lost))
	{
		$list .= "<li><a href='{$CONFIG['webroot']}/?view=device&amp;deviceID={$device['deviceID']}'>{$device['deviceName']} ({$device['deviceID']})</a></li>";
	}
	if ($list)
		print mainContentBox("Missing Devices", NULL, "<ul>$list</ul>");
	
?>