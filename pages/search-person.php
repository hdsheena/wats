<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created may 16, 2008
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

	$TITLE = "Search People"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Search People"));

	$searchform[] = array("ID:", "<input type='text' name='personID' value='{$_REQUEST['personID']}'>");
	$searchform[] = array("Name:", "<input type='text' name='personName' value='{$_REQUEST['personName']}'>");
	$searchform[] = array("", "<input type='submit' name='search' value='Search'>");
	
	print mainContentBox("Search", 	NULL, form('search', 'GET', '', Table::quick($searchform)));
	
	if ($_REQUEST['personID'] || $_REQUEST['personName'])
	{
		$results = getPeopleBySearch($_REQUEST['personID'], $_REQUEST['personName']);
		
		while ($person = dbEnumerateRows($results))
		{
			
			$link = "<a href='{$CONFIG['webroot']}/index.php?view=person&amp;personID={$person['personID']}'><img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/details.png' alt='details' title='Details'></a>";
			
			$resultlist[] = array($person['nameFirst'], $person['nameLast'], $link);
		}
		
		$resultlist = Table::quick($resultlist);
		print mainContentBox("Results", NULL, $resultlist);
	}
?>
