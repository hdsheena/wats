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
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$device = dbEnumerateRows(getDevice($_REQUEST['deviceID']));

	if(!in_array("admin", $_SESSION['user']['roles']))
	{
		print warningBox("You do not have authorization to view this page.");
		return;
	}

	if ($_REQUEST['edit'])
	{
		print warningBox("Function not yet implemented");
	}
	
	if ($_REQUEST['add'])
	{
		if (strlen($_REQUEST['supportURL']) > 0 && strpos($_REQUEST['supportURL'], "://") === false)
			$_REQUEST['supportURL'] = "http://" . $_REQUEST['supportURL'];
		
		$existing = dbEnumerateRows(getVendorByName(trim($_REQUEST['vendorName'])));
		if ($existing['vendorName'])
			print cautionBox("The vendor '{$_REQUEST['vendorName']}' is already in the system.");
		else		
			addVendor($_REQUEST['vendorName'], $_REQUEST['vendorPhone'], $_REQUEST['supportPhone'], $_REQUEST['supportURL']);
			
		unset($_REQUEST);
		$_REQUEST['view'] = "edit-vendors";	
	}

	$TITLE = "Manage Vendors"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Manage Vendors"));
	

	if ($_REQUEST['action'] == 'edit' && $_REQUEST['editVendorName'])
	{
		$result = changeVendor($_REQUEST['vendor'], $_REQUEST['editVendorName'] , $_REQUEST['editVendorPhone'], $_REQUEST['editSupPhone'], $_REQUEST['editSupURL']);
		
		if ($result !== false && affectedRows($result))
		{
			unset ($_REQUEST['action']);
			print successBox("Vendor saved.");
		}
		else
			print warningBox("Error saving vendor.");
	}

	if ($_REQUEST['action'] == 'edit')
	{	
		$vendor = dbEnumerateRows(getVendor($_REQUEST['vendor']));
		
		$edit[] = array("<label for='editVendorName'>Vendor Name:</label>", "<input type='text' name='editVendorName' id='editVendorName' value='".htmlentities($vendor['vendorName'], ENT_QUOTES)."'>");
		$edit[] = array("<label for='editVendorPhone'>Phone:</label>", "<input type='text' name='editVendorPhone' id='editVendorPhone' value='".htmlentities($vendor['vendorPhone'], ENT_QUOTES)."'>");
		$edit[] = array("<label for='editSupPhone'>Support Phone:</label>", "<input type='text' name='editSupPhone' id='editSupPhone' value='".htmlentities($vendor['supportPhone'], ENT_QUOTES)."'>");
		$edit[] = array("<label for='editSupURL'>Support URL:</label>", "<input type='text' name='editSupURL' id='editSupURL' value='".htmlentities($vendor['supportURL'], ENT_QUOTES)."'>");
		$edit[] = array("<input type='hidden' name='vendor' value='{$_REQUEST['vendor']}'>", "<input type='submit' name='save' value='Save'>");
		
		print mainContentBox("Edit Vendor '{$vendor['vendorName']}'", NULL, form('edit', 'POST', '', Table::quick($edit)));
	}



	$vendors = getVendors();
	
	$vendorlist[] = array("ID", "Name", "Phone", "Support Phone", "Support Link", "Edit");
	while ($vendor = dbEnumerateRows($vendors))
	{
		$vendorlist[] = array
		(
			$vendor['vendorID'],
			$vendor['vendorName'],
			$vendor['vendorPhone'],
			$vendor['supportPhone'],
			($vendor['supportURL'] ? "<a href='{$vendor['supportURL']}' target='_new'>Support</a>" : ""),
			"<a href='{$CONFIG['webroot']}/?view=edit-vendors&amp;action=edit&amp;vendor={$vendor['vendorID']}'>
			  <img src='{$CONFIG['themedir']}/{$CONFIG['theme']}/edit.png' alt='edit'></a>"
		); 
	}
	
	print mainContentBox("Vendors", NULL, $vendorlist = Table::quick($vendorlist, true));
	
	
	$addform[] = array("Vendor Name:", "<input type='text' name='vendorName'>");
	$addform[] = array("Vendor Phone:", "<input type='phone' name='vendorPhone'>");
	$addform[] = array("Support Phone:", "<input type='phone' name='supportPhone'>");
	$addform[] = array("Support URL:", "<input type='url' name='supportURL'>");
	$addform[] = array("", "<input type='submit' name='add' value='Add'>");
	
	$addform = form('add', 'POST', '', Table::quick($addform));
	
	print mainContentBox("Add Vendor", NULL, $addform);
?>
