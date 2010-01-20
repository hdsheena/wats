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
	session_name("inventory");
	session_start();
	
	define("DEBUG", true);
	
	$CONFIG['db-hostname'] = "localhost";
	$CONFIG['db-username'] = "root";
	$CONFIG['db-password'] = "";
	$CONFIG['db-database'] = "inventory";
	$CONFIG['db-type'] = "mysqli";
	
	if (! function_exists("db_query"))
		require_once ("lib/db.php");
	
	$entities = db_query("SELECT * FROM `config`");
	while ($entry = dbEnumerateRows($entities))
		$CONFIG[$entry['key']] = $entry['value'];
		
	if ($_SESSION['user']['personID'])
	{
		$user = makeSafe($_SESSION['user']['personID']);
		$prefs = db_query("SELECT * FROM `preference` WHERE `personID`='$user';");
		while ($pref = dbEnumerateRows($prefs))
			$CONFIG[$pref['preference']] = $pref['value'];
	}		

?>
