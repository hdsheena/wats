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
	
	if (! function_exists("warningBox"))
		require_once($CONFIG['unixroot']. "lib/page.php");
	 
    /**
      * Establishes a link with the database specified in the
      * global configuration file.
      *
      * @return (resource) Link resource to the mysql database
      */
	function sql_connect()
	{
		global $CONFIG;
		
		$CONFIG['db-type'] = "mysqli";
		
		switch ($CONFIG['db-type'])
		{
			case 'mysql'; 
				$CONFIG['dbresource'] = mysql_connect($CONFIG['db-hostname'], $CONFIG['db-username'], $CONFIG['db-password']);
				if (! mysql_select_db($CONFIG['db-database']) && DEBUG)
					print warningBox(mysql_error());
				break;
			case 'mysqli'; 
				$CONFIG['dbresource'] = new mysqli($CONFIG['db-hostname'], $CONFIG['db-username'], $CONFIG['db-password'], $CONFIG['db-database']);
				if (!$CONFIG['dbresource'] instanceof mysqli)
					print warningBox(mysqli_error());
				break;	
		}

		

		return $CONFIG['dbresource'];
	}
	
	
	/**
	 * Wrapper for database query functions that traps errors
	 * as well as ensures that a connection has been established
	 *
	 * currently supports:
	 *   * mysql
	 *   * mysqli
	 *
	 * possible support:
	 *   * postgres
	 *   * odbc
	 *   * oracle 
	 *
	 * @param $query        (string) The mysql query
	 * @return      (resource) database resource
	 */
	function db_query($query)
	{
		global $CONFIG;

		//ensure that sql_connect has been called
		//to prevent errors
		if (! isset($CONFIG['dbresource']))
			sql_connect();

		//see if we're connected to a db. if not, there's nothing to do
		//we can't even log the error =P
		if (isset($CONFIG['dbresource']))
		{
			switch ($CONFIG['db-type'])
			{
				case 'mysql':
					if (! ($result = mysql_query($query)))
					{
						$err = debug_backtrace();						
						if (DEBUG) print warningBox ($err[1]['function'] . ": " .mysql_error());
						
						if ($err[1]['function'] != "db_query") //catch recursive errors
							log_event(date("Y-m-d"), "error", $_SESSION['user']['username'], "database", $err[1]['function'] . ": " .mysql_error());
					} 
					return $result;
				case 'mysqli':
					if (!($result = $CONFIG['dbresource']->query($query))) 
					{
						$err = debug_backtrace();
						if (DEBUG) print warningBox ($err[1]['function'] . ": " .$CONFIG['dbresource']->error ."<br>$query");
						
						if ($err[1]['function'] != "db_query") //catch recursive errors
							log_event(date("Y-m-d"), "error", $_SESSION['user']['username'], "database", $err[1]['function'] . ": " .mysql_error());
					} 
					return $result;
			}
		}
		else
			return false;
	}
	
	
	/** 
	 * Wrapper for mysql_num_rows and similar functions
	 * for other data types, so caller doesn't need to 
	 * switch on $CONFIG['db-type']
	 *
	 * @param $result db result
	 * @return integer
	**/
	function numRows($result)
	{
		global $CONFIG;
		
		switch ($CONFIG['db-type'])
		{	
			case 'mysql':
				return mysql_num_rows($result); 
			case 'mysqli':
				return $result->num_rows;
		}
	}
	
	/** 
	 * Wrapper for mysql_affected_rows and similar functions
	 * for other data types, so caller doesn't need to 
	 * switch on $CONFIG['db-type']
	 *
	 * @param $result db result
	 * @return integer
	**/
	function affectedRows()
	{
		global $CONFIG;
		
		switch ($CONFIG['db-type'])
		{	
			case 'mysql':
				return  @ mysql_affected_rows($CONFIG['dbresource']); 
			case 'mysqli':
				return @ $CONFIG['dbresource']->affected_rows;
		}
	}
	
	/**
	 * Returns the newest-generated value for an AUTO_INCREMENT column
	 */
	function insertID()
	{
		global $CONFIG;
		
		$val = dbEnumerateRows(db_query("SELECT LAST_INSERT_ID() as `val`;"));

		if (is_numeric($val['val']))
			return $val['val'];
		else
			return false;
	}
	
	/**
	 * Returns the next row in a database set as an 
	 * associative array.
	 *
	 * 
	**/
	function dbEnumerateRows($result, $bothTypes=true)
	{
		global $CONFIG;
	
		switch ($CONFIG['db-type'])
		{	
			case 'mysql':
				if ($bothTypes)
					return mysql_fetch_array($result, MYSQL_BOTH);
				else
					return mysql_fetch_array($result, MYSQL_ASSOC);
			case 'mysqli':
				if (! ($result instanceof mysqli_result || $result instanceof mysqli)) 
				{
					print warningBox(gettype($result) . " is not a valid mysqli_result object");
					return null;
				}
				if ($bothTypes)
					return $result->fetch_array(MYSQLI_BOTH);
				else
					return $result->fetch_array(MYSQLI_ASSOC);
		}
	}
	
	/**
	* Modifies variables before being put into an SQL query to prevent
	* injection attacks.  It will escape any special character, such as
	* ticks and quotes.  While the parameter should be of type string,
	* makeSafe will detect other types and not modify them.  Arrays and such
	* shouldnt be put in to a database without serialization, anyway.
	*
	* @param $string       (string) The data to be cleaned
	* @return (string)     sql-injection-proof string
	*/
	function makeSafe($string)
	{
		global $CONFIG;

		//this seems stupid, but rebooting kosumi broke mysql_real_escape_string's connection
		//to the local sql server (whatever it needed that for)
		if (! isset($CONFIG['dbresource']))
			sql_connect();


		if (get_magic_quotes_gpc())
		{
			//we don't want to escape our existing escapes!
			$string = stripslashes($string);
		}

		if (is_numeric($string))
			//nothing more to do, numbers won't break sql
			return $string;
		else
		{
			//replace smartquotes with unicode entries, fixes w3c validation
			//@see http://www.cs.tut.fi/~jkorpela/www/windows-chars.html#list 
			$string = str_replace(chr(145), "&#8216;", $string);
			$string = str_replace(chr(146), "&#8217;", $string);
			$string = str_replace(chr(147), "&#8220;", $string);
			$string = str_replace(chr(148), "&#8221;", $string);
			
			//some non iso-8859-1 chars are still getting thru.
			//control characters are fine, but bit 8 and higher are not
			$string = preg_replace('/[^\x01-\x7F]/e', '"&#".ord("$0").chr(59)', $string);
			
			$string = htmlentities($string, ENT_QUOTES, "ISO-8859-1", false);
			
			switch ($CONFIG['db-type'])
			{
				case 'mysql':
					return @ mysql_real_escape_string($string, $CONFIG['dbresource']);
				case 'mysqli':
					return @ $CONFIG['dbresource']->escape_string($string); 
				default:
					return @ addslashes($string); 
			}
		}
	}
	
	
	function log_event($date, $level, $username, $subsystem, $message)
	{
		$date = makeSafe($date);
		$level = makeSafe($level);
		$username= makeSafe($username);
		$subsystem = makeSafe($subsystem);
		$message = makeSafe($message);
		$address = makeSafe($_SERVER['REMOTE_ADDR']);
		
		$query = "INSERT INTO `log` (`incidentDate`, `level`, `user`, `subsystem`, `remoteAddress`, `error`) VALUES
				('$date', '$level', '$username', '$subsystem', '$address', '$message');";
		
		return db_query($query);
	} 
	
	
	function getPersonRoles($personID)
	{
		$personID=makeSafe($personID);
		
		$query = "SELECT * FROM `person_role` WHERE `personID`='$personID';";
		
		return db_query($query);
	}
	
	function getPersonRole($personID, $roleID)
	{
	}
	
	function getRooms()
	{
		$query = "SELECT * FROM `room`
					JOIN `building` USING(`buildingID`) 
				ORDER BY `buildingName`, `floor`, `roomName`";
		return db_query($query);
	}
	
	function getRoom($roomID)
	{
		$roomID =  makeSafe($roomID);
		
		$query = "SELECT * FROM `room`
					JOIN `building` USING(`buildingID`) 
				WHERE `roomID`='$roomID'
				ORDER BY `buildingName`, `floor`, `roomName`";
		return db_query($query);
	}
	
	function getAllDevices()
	{
		$deviceID=makeSafe($deviceID);
		
		$query = "SELECT * FROM `device` 
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`)
					JOIN `person` ON (`inventoriedBy` = `personID`)
				ORDER BY `dateInventoried`;";
		
		return db_query($query);
	}
	
	function getDevice($deviceID)
	{
		$deviceID=makeSafe($deviceID);
		
		$query = "SELECT * FROM `device` 
				JOIN `model` USING(`modelID`)
				JOIN `vendor` USING(`vendorID`)
				JOIN `type` USING (`typeID`)
				JOIN `status` USING (`statusID`)
				JOIN `person` ON (`inventoriedBy` = `personID`)
			WHERE `deviceID`='$deviceID';";
		
		return db_query($query);
	}
	
	function getDevicesOfModel($modelID)
	{
		$typeID = makeSafe($typeID);
		
		$query = "SELECT * FROM `device` 
				JOIN `model` USING(`modelID`)
				JOIN `vendor` USING(`vendorID`)
				JOIN `type` USING (`typeID`)
				JOIN `status` USING (`statusID`)
				JOIN `person` ON (`inventoriedBy` = `personID`)
			WHERE `modelID`='$modelID';";
		
		return db_query($query);
	}
	
	function getDevicesLikeName($name)
	{
		$name = makeSafe($name);
		
		$query = "SELECT * FROM `device` 
				JOIN `model` USING(`modelID`)
				JOIN `vendor` USING(`vendorID`)
				JOIN `type` USING (`typeID`)
				JOIN `status` USING (`statusID`)
			WHERE `deviceName` LIKE '%$name%';";
print queryBox($query);		
		return db_query($query);
	}
	
	function getCurrentDeviceAssignment($deviceID)
	{
		$deviceID=makeSafe($deviceID);
		
		$query = "SELECT `assignment`.*, `person`.`nameFirst`, `person`.`nameLast`, `room`.`roomName` FROM `assignment` 
					LEFT OUTER JOIN `room` USING (`roomID`)
					LEFT OUTER JOIN `person` USING (`personID`)
			WHERE `deviceID`='$deviceID' AND `dateRemoved` IS NULL;";
		
		return db_query($query);
	}
	
	function getDeviceAssignments($deviceID)
	{
		$deviceID=makeSafe($deviceID);
		
		$query = "SELECT `assignment`.*, `person`.`nameFirst`, `person`.`nameLast`, `room`.`roomName` FROM `assignment` 
					LEFT OUTER JOIN `room` USING (`roomID`)
					LEFT OUTER JOIN `person` USING (`personID`)
			WHERE `deviceID`='$deviceID' ORDER BY  `dateAssigned` DESC;";
		
		return db_query($query);
	}
	
	function getDeviceAssignment($assignmentID)
	{
		$assignmentID=makeSafe($assignmentID);
		
		$query = "SELECT `assignment`.*, `person`.`nameFirst`, `person`.`nameLast`, `room`.`roomName` FROM `assignment` 
					LEFT OUTER JOIN `room` USING (`roomID`)
					LEFT OUTER JOIN `person` USING (`personID`)
			WHERE `assignmentID`='$assignmentID' ORDER BY  `dateAssigned` DESC;";
		
		return db_query($query);
	}
	
	function getQueuedDevices()
	{
		$query = "SELECT * FROM `device_fetch_queue`
					JOIN `device` USING(`deviceID`)
				  ORDER BY `dateAdded`;";
					
		return db_query($query);
	}
	
	function getQueuedDevice($deviceID)
	{
		$deviceID=makeSafe($deviceID);
		
		$query = "SELECT * FROM `device` 
			WHERE `deviceID`='$deviceID';";
		
		return db_query($query);
	}
	
	function addDeviceBatch($id, $model, $name)
	{
		$id = makeSafe($id);
		$model = is_numeric($model)?makeSafe($model):"NULL";
		$name = makeSafe($name);
		$by = makeSafe($_SESSION['user']['personID']);
		
		$query = "INSERT INTO `device` (`deviceID`, `modelID`, `deviceName`, dateInventoried, inventoriedBy) 
			VALUES ('$id', $model, '$name', NOW(), '$by');";
		
		return db_query($query);
	}
	
	function addDeviceFull($id, $asset, $name, $model, $value, $purchased, $removed, $statusID)
	{
		$id = makeSafe($id);
		$asset = ($asset)?"'".makeSafe($asset)."'":"NULL";
		$name = makeSafe($name);
		$model = is_numeric($model)?makeSafe($model):"1";
		$value = is_numeric($value)?makeSafe($value):"NULL";
		$purchased = isValidDate($purchased)?"'".makeSafe(isValidDate($purchased))."'":"NOW()";
		$removed = isValidDate($removed)?"'".makeSafe(isValidDate($removed))."'":"NULL";
		$status = is_numeric($status)?makeSafe($status):"1";
		$by = makeSafe($_SESSION['user']['personID']);
		
		$query = "INSERT INTO `device` VALUES 
					('$id', $asset, '$name', $model, $value, NOW(), '$by', $purchased, $removed, $status);";
print queryBox($query);		
		return db_query($query);
	}
	
	function updateDevice($id, $asset, $name, $model, $value, $purchased, $removed, $status)
	{
		$id = makeSafe($id);
		$asset = ($asset)?"'" . makeSafe($asset )."'":"NULL";
		$name = makeSafe($name);
		$model = is_numeric($model)?makeSafe($model):"1";
		$value = is_numeric($value)?makeSafe($value):"NULL";
		$purchased = isValidDate($purchased)?"'".makeSafe(isValidDate($purchased))."'":"NULL";
		$removed = isValidDate($removed)?"'".makeSafe(isValidDate($removed))."'":"NULL";
		$status = is_numeric($status)?makeSafe($status):"1";
		
		$query = "UPDATE `device` SET `assetTag`=$asset, `deviceName`='$name', `modelID`='$model', 
					`value`=$value, `datePurchased`=$purchased, `dateRemoved`=$removed, `statusID`='$status'
				  WHERE `deviceID`='$id';";
		
		return db_query($query);
	}
	
	function updateDeviceModel($deviceID, $modelID)
	{
		$deviceID = makeSafe($deviceID);
		$modelID = makeSafe($modelID);
		
		$query = "UPDATE `device` SET `modelID`='$modelID' WHERE `deviceID`='$deviceID';";
		
		return db_query($query);
	}
	
	function addDeviceToFetchQueue($deviceID)
	{
		$deviceID = makeSafe($deviceID);
		
		$query = "INSERT INTO `device_fetch_queue` VALUES ('$deviceID', NOW());";
		
		return db_query($query);
	}
	
	function deleteDeviceFromFetchQueue($deviceID)
	{
		$deviceID = makeSafe($deviceID);
		
		$query = "DELETE FROM `device_fetch_queue` WHERE `deviceID`= '$deviceID';";
		
		return db_query($query);
	}
	
	function assignDeviceToRoom($deviceID, $roomID)
	{
		$device = makeSafe($deviceID);
		$room = makeSafe($roomID);
		
		$query = "INSERT INTO `assignment` (`deviceID`, `roomID`, `dateAssigned`) VALUES ('$device', '$room', NOW());";
		
		return db_query($query);
	}
	
	function assignDeviceToPerson($deviceID, $personID)
	{
		$device = makeSafe($deviceID);
		$person = makeSafe($personID);
		
		$query = "INSERT INTO `assignment` (`deviceID`, `personID`, `dateAssigned`) VALUES ('$device', '$person',  NOW());";
		
		return db_query($query);
	}
	
	
	function getModels()
	{
		$query = "SELECT * FROM `model`
					JOIN `vendor` USING (`vendorID`)
					JOIN `type` USING (`typeID`)
				ORDER BY `vendorName`, `typeName`, `modelName`;";
		
		return db_query($query);
	}
	
	function getModel($modelID)
	{
		$modelID = makeSafe($modelID);
		
		$query = "SELECT * FROM `model`
					JOIN `vendor` USING (`vendorID`)
					JOIN `type` USING (`typeID`)
				  WHERE `modelID`='$modelID'
				ORDER BY `vendorName`, `typeName`, `modelName`;";
		
		return db_query($query);
	}
	
	function addModel($name, $typeID, $vendorID, $value)
	{
		$name = makeSafe($name);
		$typeID = makeSafe($typeID);
		$vendorID = makeSafe($vendorID);
		$value = (int)makeSafe($value);
		
		$query = "INSERT INTO `model` (modelName, typeID, vendorID, defaultValue)  
			VALUES ('$name', '$typeID', '$vendorID', $value);";
		
		return db_query($query);
	}
	
	function updateModel($id, $name, $typeID, $vendorID, $value)
	{
		$id = makeSafe($id);
		$name = makeSafe($name);
		$typeID = makeSafe($typeID);
		$vendorID = makeSafe($vendorID);
		$value = (int)makeSafe($value);
		
		$query = "UPDATE `model` SET modelName='$name', typeID='$typeID', vendorID='$vendorID', defaultValue=$value
			WHERE `modelID`='$id';";
		
		return db_query($query);
	}
	
	
	function getModelByName($name)
	{
		$name = makeSafe($name);
		
		$query = "SELECT * FROM `model`
					JOIN `vendor` USING (`vendorID`)
					JOIN `type` USING (`typeID`) 
				WHERE `modelName`='$name';";
		
		return db_query($query);
	}
	
	function getVendorByName($name)
	{
		$name = makeSafe($name);
		
		$query = "SELECT * FROM `vendor` WHERE `vendorName`='$name';";
		
		return db_query($query);
	}
	
	
	function getPeople()
	{
		$query = "SELECT * FROM `person` ORDER BY `nameFirst`, `nameLast`;";
		
		return db_query($query);
	}
	
	function getCurrentPeople()
	{
		$query = "SELECT * FROM `person` WHERE `isCurrent`=1 ORDER BY `nameFirst`, `nameLast`;";
		
		return db_query($query);
	}
	
	function getOldPeople()
	{
		$query = "SELECT * FROM `person` WHERE `isCurrent`=0 ORDER BY `nameFirst`, `nameLast`;";
		
		return db_query($query);
	}
	
	function getPeopleInRoom($roomID)
	{
		$roomID = makeSafe($roomID);
		
		$query = "SELECT * FROM `person` WHERE `roomID`='$roomID' ORDER BY `nameFirst`, `nameLast`;";
		
		return db_query($query);
	}
	
	function getPerson($personID)
	{
		$personID = makeSafe($personID);
		
		$query = "SELECT * FROM `person` WHERE `personID`='$personID';";
		
		return db_query($query);
	}
	
	function updatePerson($personID, $nameFirst, $nameLast, $email, $username, $password, $roomID, $isCurrent)
	{
		$personID = makeSafe($personID);
		
		$nameFirst = makeSafe($nameFirst);
		$nameLast = makeSafe($nameLast);
		$email = makeSafe($email);
		$username = makeSafe($username);
		$pwdupdate = ($password)?"`password`='" . pw_encode($password) ."',": "";
		$roomID = ($roomID)?makeSafe($roomID):"NULL";
		$isCurrent = $isCurrent?1:0; //true/false, y/n, etc won't do
		
		$query = "UPDATE `person` SET `nameFirst`='$nameFirst', `nameLast`='$nameLast', `email`='$email', 
					`username`='$username', $pwdupdate `roomID`=$roomID, `isCurrent`='$isCurrent'
				WHERE `personID`='$personID';";
		
		return db_query($query);
	}
	
	function getPreferences($personID)
	{
		$personID = makeSafe($personID);
		
		return db_query("SELECT * FROM `preference` WHERE `personID`='$personID';");
	}
	
	function setPreference($personID, $preference, $value)
	{
		$personID = makeSafe($personID);
		$preference = makeSafe($preference);
		$value = makeSafe($value);
		
		if ($value != "")
			$query = "REPLACE INTO  `preference` VALUES('$personID', '$preference', '$value');";
		else
			$query = "DELETE FROM `preference` WHERE `personID`='$personID' AND `preference`='$preference';";
		
		return db_query($query);
	}
	
	function getDeviceTypes()
	{
		$query = "SELECT * FROM `type` ORDER BY `typeName`;";
		
		return db_query($query);
	}
	
	function getDeviceTypeByName($name)
	{
		$name = makeSafe($name);
		
		$query = "SELECT * FROM `type` WHERE `typeName`='$name' ORDER BY `typeName`;";
		
		return db_query($query);
	}
	
	function getDeviceType($typeID)
	{
		$typeID = makeSafe($typeID);
		
		$query = "SELECT * FROM `type` WHERE `typeID`='$typeID';";
		
		return db_query($query);
	}
	
	function addDeviceType($name)
	{
		$name = makeSafe($name);
		
		$query = "INSERT INTO `type` (`typeName`) VALUES('$name');";
		
		return db_query($query);
	}
	
	function deleteDeviceType($typeID)
	{
		$typeID = makeSafe($typeID);
		
		$query = "DELETE FROM `type` WHERE `typeID`='$typeID';";
		
		return db_query($query);
	}
	
	function getModelsOfType($typeID)
	{
		$typeID = makeSafe($typeID);
		
		$query = "SELECT * FROM `model` JOIN `type` USING(`typeID`) WHERE `typeID`='$typeID';";
		
		return db_query($query);
	}
	
	
	function getStatuses()
	{
		$query = "SELECT * FROM `status` ORDER BY `statusName`;";
		
		return db_query($query);
	}
	
	
	function getDevicesAssignedToRoom($roomID)
	{
		$roomID = makeSafe($roomID);
		
		$query = "SELECT * FROM `assignment` 
					JOIN `device` USING(`deviceID`) 
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`)
				WHERE `roomID`='$roomID' and `assignment`.`dateRemoved` IS NULL;";
		
		return db_query($query);
	}
	
	function getPastDevicesAssignedToRoom($roomID)
	{
		$roomID = makeSafe($roomID);
		
		$query = "SELECT * FROM `assignment` 
					JOIN `device` USING(`deviceID`) 
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`)
				WHERE `roomID`='$roomID' and `assignment`.`dateRemoved` IS NOT NULL;";
		
		return db_query($query);
	}
	
	function getDevicesAssignedToperson($personID)
	{
		$personID = makeSafe($personID);
		
		$query = "SELECT * FROM `assignment` 
					JOIN `device` USING(`deviceID`) 
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`)
				WHERE `personID`='$personID' and `assignment`.`dateRemoved` IS NULL;";
		
		return db_query($query);
	}
	
	function getBuildings()
	{
		$query = "SELECT * FROM `building` ORDER BY `buildingName`;";
		return db_query($query);
	}
	
	function updateRoom($roomID, $roomName, $floor, $buildingID)
	{
		$roomID = makeSafe($roomID);
		$roomName = makeSafe($roomName);
		$floor = makeSafe($floor);
		$buildingID = makeSafe($buildingID);
		
		$query = "UPDATE `room` SET `roomName`='$roomName', `floor`='$floor', `buildingID`='$buildingID'
					WHERE `roomID`='$roomID';";
				
		return db_query($query);
	}
	
	function deleteRoom($roomID)
	{
		$roomID = makeSafe($roomID);
		
		$query = "DELETE FROM `room` WHERE `roomID`='$roomID';";
				
		return db_query($query);
	}
	
	function getVendors()
	{
		$query = "SELECT * FROM `vendor` ORDER BY `vendorName`;";
		
		return db_query($query);
	}
	
	function getVendor($vendorID)
	{
		$vendorID = makeSafe($vendorID);
		
		$query = "SELECT * FROM `vendor` WHERE `vendorID`='$vendorID' ORDER BY `vendorName`;";
		
		return db_query($query);
	}
	

	
	function addVendor($name, $phone, $supportPhone, $supportURL)
	{
		$name = makeSafe($name);
		$phone = makeSafe($phone);
		$supportPhone = makeSafe($supportPhone);
		$supportURL = makeSafe($supportURL);
		
		$query = "INSERT INTO `vendor` VALUES (NULL, '$name', '$phone', '$supportPhone', '$supportURL');";
			
		return db_query($query);
	}
	
	
	function closeOutstandingAssignmentsForDevice($deviceID)
	{
		$deviceID = makeSafe($deviceID);
		
		$query = "UPDATE `assignment` SET `dateRemoved`=NOW() WHERE `deviceID`='$deviceID' AND `dateRemoved` IS NULL;";
		
		return db_query($query);
	}
	
	function closeOutstandingAssignmentForDevice($deviceID, $assignmentID)
	{
		$deviceID = makeSafe($deviceID);
		$assignmentID = makeSafe($assignmentID);
		
		$query = "UPDATE `assignment` SET `dateRemoved`=NOW() WHERE `deviceID`='$deviceID' 
			AND `assignmentID`='$assignmentID' AND `dateRemoved` IS NULL;";
		
		return db_query($query);
	}
	
	function setDeviceStatus($deviceID, $statusID)
	{
		$deviceID = makeSafe($deviceID);
		$statusID = makeSafe($statusID);
		
		$query = "UPDATE `device` SET `statusID`='$statusID' WHERE `deviceID`='$device';";
		
		return db_query($query);
	}
	
	function getUnassignedActiveDevices()
	{
		$status = dbEnumerateRows(getStatusByName("Active"));
		
		$query = "SELECT * FROM device WHERE `statusID`='{$status['statusID']}' 
					AND 0=(SELECT COUNT(assignmentID) from assignment WHERE deviceID=device.deviceID and dateRemoved IS NULL)";
				
		return db_query($query);			
	} 
	
	function getDevicesWithStatus($statusID)
	{
		$statusID = makeSafe($statusID);
		
		$query = "SELECT * FROM `device`J
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`) 
				WHERE `statusID`='$statusID';";
		
		return db_query($query);
	}
	
	function getStatusByName($statusName)
	{
		$statusName = makeSafe($statusName);
		
		$query = "SELECT * FROM `status` WHERE `statusName`='$statusName';";
		
		return db_query($query);
	}
	
	function getStatus($statusID)
	{
		$statusID = makeSafe($statusID);
		
		$query = "SELECT * FROM `status` WHERE `statusID`='$statusID';";
		
		return db_query($query);
	}
	
	function getDevicesBySearch($deviceID, $deviceName, $statusID)
	{
		$deviceID = makeSafe($deviceID);
		$deviceName = makeSafe($deviceName);
		$statusID = makeSafe($statusID);
		
		$query = "SELECT * FROM `device`
					JOIN `model` USING(`modelID`)
					JOIN `vendor` USING(`vendorID`)
					JOIN `type` USING (`typeID`)
					JOIN `status` USING (`statusID`)
				WHERE (`deviceID`='$deviceID' AND `statusID`='$statusID') 
					OR ((NOT '$deviceName'='') AND `deviceName` LIKE '%$deviceName%' AND `statusID`='$statusID')"; 
		
		return db_query($query);
	}
	
	function getPeopleBySearch($personID, $personName)
	{
		$personID = makeSafe($personID);
		$personName = makeSafe(str_replace(" ", "%", $personName));
		
		$query = "SELECT * FROM `person` WHERE `personID` LIKE '%$personID%' AND 
					(CONCAT(`nameFirst`, ' ', `nameLast`) LIKE '%$personName%' OR `nameFirst` LIKE '%$personName%' OR `nameLast` LIKE '%$personName%') ;";

		return db_query($query);
	}
?>
