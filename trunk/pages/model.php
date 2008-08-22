<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created May 20, 2008
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

	$model = dbEnumerateRows(getModel($_REQUEST['model']));



	$TITLE = "{$model['modelName']} from {$model['vendorName']}"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "TEMPLATE TITLE"));
						
						
	
	$devices = getDevicesOfModel($model['modelID']);
	while($device = dbEnumerateRows($devices))
	{
		$url = "{$CONFIG['webroot']}/index.php?view=device&amp;deviceID={$device['deviceID']}";
		
		$devicelist[] = array
		(
			"<a href='$url'>{$device['deviceName']}</a>",
			$device['statusName']
		);
	}
	
	print mainContentBox("Devices", NULL, Table::quick($devicelist));	
?>
