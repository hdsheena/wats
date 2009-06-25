<?php
	/**
	 * wats - Web-based Asset Tracking System
	 *
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created June 25, 2009
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
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");

	if ($_REQUEST['NbuildingName'])
	{
		addBuilding($_REQUEST['NbuildingName']);
		if (insertID())
			print successBox("Building {$_REQUEST['NbuildingName']} added.");
	}


	if ($_REQUEST['EbuildingName'] && $_REQUEST['action'])
	{
		$result = updateBuilding($_REQUEST['buildingID'], $_REQUEST['EbuildingName']);
		if (affectedRows($result) && $result !== false)
			print successBox("Building updated.");
		else
			print warningBox("An error occurrred while updating the building.");
		unset($_REQUEST['action']);
	}


	if ($_REQUEST['action'] == 'delete')
	{
		if ($_REQUEST['reallyOK'])
		{
			$result = deleteBuilding($_REQUEST['buildingID']);
			if (affectedRows($result) && $result !== false)
				print successBox("Building deleted.");
			else
				print warningBox("An error occurrred while deleting the building. Make sure it has no rooms.");


			unset ($_REQUEST['action']);
		}
		else
		{
			$building = dbEnumerateRows(getBuilding($_REQUEST['buildingID']));

			$message = "Are you sure you want to delete the building '{$building['buildingName']}'?<br>
				<input type='submit' name='reallyOK' value='Delete'> <input type='submit' name='action' value='Cancel'>";
			print cautionBox(form('really', 'POST', '', $message));
		}

	}

	$TITLE = "Buildings";
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Buildings"));


	if ($_REQUEST['action'] == "edit")
	{
		$building = dbEnumerateRows(getBuilding($_REQUEST['buildingID']));

		$editfrm[] = array("<label for='EbuildingName'>Building Name:</label>",
			"<input type='text' name='EbuildingName' id='EbuildingName' value='".htmlentities($building['buildingName'], ENT_QUOTES)."'>");
		$editfrm[] = array("", "<input type='submit' name='save' value='Save'>");

		$editfrm = form('edit', 'POST', '', Table::quick($editfrm));
		print mainContentBox("Edit Building", NULL, $editfrm);
	}



	$buildinglist[] = array("Building Name", "Edit","Delete");

	$buildings = getBuildings();
	while ($building = dbEnumerateRows($buildings))
	{
		$edurl = "{$CONFIG['webroot']}/?view=edit-buildings&amp;action=edit&amp;buildingID={$building['buildingID']}";
		$delurl = "{$CONFIG['webroot']}/?view=edit-buildings&amp;action=delete&amp;buildingID={$building['buildingID']}";

		$buildinglist[] = array
		(
			$building['buildingName'],
			new HTMLLink ("<img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/edit.png' alt='edit'>", $edurl, "", ""),
			new HTMLLink ("<img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/delete.png' alt='delete'>", $delurl, "", "")
		);
	}

	$buildinglist = Table::quick($buildinglist, true);
	print mainContentBox("Existing Buildings", NULL, $buildinglist);


	$addform[] = array("<label for='NbuildingName'>Building Name:</label>", "<input type='text' id='NbuildingName' name='NbuildingName'>");
	$addform[] = array("", "<input type='submit' name='add' value='Add'>");
	$addform = form('new', 'POST', '', Table::quick($addform));
	print mainContentBox("Add Building", NULL, $addform);
?>
