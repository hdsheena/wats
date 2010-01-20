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


	if ($_REQUEST['add'] || $_REQUEST['ID'])
	{
		$result = addPerson($_REQUEST['ID'], $_REQUEST['nameFirst'], $_REQUEST['nameLast'], $_REQUEST['usenrame'], $_REQUEST['password']);
		
		if ($result !== false || affectedRows())
			print successBox("{$_REQUEST['nameFirst']} added.");
	}






	$TITLE = "Manage People"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Manage People"));
				
	
	$oldsel = ($_REQUEST['whom']=="old") ? "CHECKED" : "";
	$allsel = ($_REQUEST['whom']=="all") ? "CHECKED" : "";
	$cursel = ($oldsel || $allsel) ? "" : "CHECKED";
	$whoform[] = array("<input type='radio' name='whom' value='current' id='current' $cursel>", "<label for='current'>Current People</label>");
	$whoform[] = array("<input type='radio' name='whom' value='old' id='old' $oldsel>", "<label for='old'>Removed People</label>");
	$whoform[] = array("<input type='radio' name='whom' value='all' id='all' $allsel>", "<label for='all'>All People</label>");
	$whoform[] = array("", "<input type='submit' name='select' value='Select'>");
	$whoform = form("who", "GET", "", Table::quick($whoform));
	
	print mainContentBox("Select Who to Manage", NULL, $whoform);
	
	
	
	if ($_REQUEST['whom'] == "all")					
		$people = getPeople();
	else if ($_REQUEST['whom'] == "current" || $_REQUEST['whom'] == "")
		$people = getCurrentPeople();
	else if ($_REQUEST['whom'] == "old")
		$people = getOldPeople();
		
	$peoplelist[] = array("ID", "First Name", "Last Name", "Email Address", "Current", "Edit");
	while ($person = dbEnumerateRows($people))
	{
		$link = "<a href='{$CONFIG['webroot']}/index.php?view=person&amp;personID={$person['personID']}'><img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/edit.png' alt='edit' title='Edit'></a>";
		
		$peoplelist[] = array
		(
			$person['personID'],
			$person['nameFirst'],
			$person['nameLast'],
			$person['email'],
			($person['isCurrent'])?"Y":"N",
			$link
		);
	}	
	
	$peoplelist = Table::quick($peoplelist, true);
	
	
	print mainContentBox("People", NULL, $peoplelist);
	
	
	
	
	$new[] = array("ID:", "<input type='text' name='ID'>");
	$new[] = array("First Name:", "<input type='text' name='nameFirst'>");
	$new[] = array("Last Name:", "<input type='text' name='nameLast'>");
	$new[] = array("");
	$new[] = array("Username:", "<input type='text' name='username'>", "leave blank to not allow login");
	$new[] = array("Password:", "<input type='text' name='password'>", "leave blank to not allow login");
	$new[] = array("");
	$new[] = array("", "<input type='submit' name='add' value='Add'>");
	
	print mainContentBox("Add Person", NULL, form('add', 'POST', '', Table::quick($new)));
	
?>
