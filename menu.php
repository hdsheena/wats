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
	require_once("{$CONFIG['unixroot']}/lib/menuobj.php");
	
	

	if (in_array("view", $_SESSION['user']['roles']))
	{	
		$tmp = new MenuItem("Search", "");
		$tmp->addChild(new MenuItem("Search Devices", "{$CONFIG['webroot']}/?view=search-device"));
		$tmp->addChild(new MenuItem("Search People", "{$CONFIG['webroot']}/?view=search-person"));
		$tmp->addChild(new MenuItem("Search Rooms", "{$CONFIG['webroot']}/?view=search-room"));
		
		$MENU[] = $tmp;
		
		$tmp = new MenuItem("Statistics", "");
		$tmp->addChild(new MenuItem("Deployed Device Breakdown", "{$CONFIG['webroot']}/?view=stat-breakdown"));
		$tmp->addChild(new MenuItem("Devices by Bulding", "{$CONFIG['webroot']}/?view=stat-building"));
		$tmp->addChild(new MenuItem("Device Lifespan", "{$CONFIG['webroot']}/?view=stat-lifespan"));
		
		$MENU[] = $tmp;
	}
	
	if (in_array("request", $_SESSION['user']['roles']))
	{
		
	}
	
	if (in_array("inventory", $_SESSION['user']['roles']))
	{
		$tmp = new MenuItem("Inventory Items", "");
		
		$tmp->addChild(new MenuItem("Add", "{$CONFIG['webroot']}/?view=add-device"));
		$tmp->addChild(new MenuItem("Batch Add to Room", "{$CONFIG['webroot']}/?view=batch-add-room"));
		$tmp->addChild(new MenuItem("Batch Add to Person", "{$CONFIG['webroot']}/?view=batch-add-person"));
		$tmp->addChild(new MenuItem("Batch Add to Status", "{$CONFIG['webroot']}/?view=batch-add-status"));
		$tmp->addChild(new MenuItem("Batch Unassign", "{$CONFIG['webroot']}/?view=batch-unassign"));
	
		$MENU[] = $tmp;	
	}
	
	if (in_array("admin", $_SESSION['user']['roles']))
	{
		$tmp = new MenuItem("System Functions", "");
		
		$tmp->addChild(new MenuItem("Manage New Device Queue", "{$CONFIG['webroot']}/?view=device-queue"));
        $tmp->addChild(new MenuItem("Manage Buildings", "{$CONFIG['webroot']}/?view=edit-buildings"));
		$tmp->addChild(new MenuItem("Manage Rooms", "{$CONFIG['webroot']}/?view=edit-rooms"));
		$tmp->addChild(new MenuItem("Manage Pepole", "{$CONFIG['webroot']}/?view=edit-people"));
		$tmp->addChild(new MenuItem("Manage Device Types", "{$CONFIG['webroot']}/?view=edit-device-types"));
		$tmp->addChild(new MenuItem("Manage Vendors", "{$CONFIG['webroot']}/?view=edit-vendors"));
		$tmp->addChild(new MenuItem("Manage Models", "{$CONFIG['webroot']}/?view=edit-models"));
		$tmp->addChild(new MenuItem("Manage Statuses", "{$CONFIG['webroot']}/?view=edit-statuses"));
		$tmp->addChild(new MenuItem("Export Devices", "{$CONFIG['webroot']}/?view=export-devices"));
		
		$MENU[] = $tmp;
	}
	
	
?>
