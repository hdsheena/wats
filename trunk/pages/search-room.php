<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created July 2, 2008
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

	$TITLE = "Search Rooms"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Search Rooms"));
						
	
	$searchform[] = array("ID:", "<input type='text' name='roomID' value='{$_REQUEST['roomID']}'>");
	$searchform[] = array("Name:", "<input type='text' name='roomName' value='{$_REQUEST['roomName']}'>");
	$searchform[] = array("", "<input type='submit' name='search' value='Search'>");
	
	
	print mainContentBox("Search", 	NULL, form('search', 'GET', '', Table::quick($searchform)));
	
	if ($_REQUEST['roomID'] || $_REQUEST['roomName'])
	{
		$results = getRoomsBySearch($_REQUEST['roomID'], $_REQUEST['roomName']);
		
		if ($results === false)
		{
			print cautionBox("No results match your search.");
			return;
		} 
		
		
		$resultlist[] = array("Room ID", "Building Name", "Floor", "Room Name", "Details");
		while ($room = dbEnumerateRows($results))
		{
			
			$link = "<a href='{$CONFIG['webroot']}/index.php?view=room&amp;roomID={$room['roomID']}'><img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/details.png' alt='details' title='Details'></a>";
			
			$resultlist[] = array($room['roomID'], $room['buildingName'], $room['floor'], $room['roomName'],  $link);
		}
		
		$resultlist = Table::quick($resultlist, true);
		print mainContentBox("Results", NULL, $resultlist);
	}					
?>
