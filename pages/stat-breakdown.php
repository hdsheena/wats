<?php
	/**
	 * wats - Web-based Asset Tracking System
	 * 
	 * @author Ryan Illman (rillman@evergreenschool.org)
	 * @created may 27, 2008
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

	$TITLE = "Deployed Devices"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link' => "{$CONFIG['webroot']}"),
						array('name' => "Deployed Devices"));
						
						
	$active = dbEnumerateRows(getStatusByName("active"));
	
	$totalcount = dbEnumerateRows(db_query("SELECT COUNT(deviceID) as `total` from `device` WHERE `statusID`='{$active['statusID']}';"));

	$typetotals[] = array("Device Type", "Count", "Percentage of Total");

	$num = 0;
	$types = getDeviceTypes();
	while ($type = dbEnumerateRows($types))
	{		
		$typecount = dbEnumerateRows(db_query("SELECT COUNT(deviceID) as `total` FROM `device` JOIN `model` USING (`modelID`) WHERE `typeID`='{$type['typeID']}' AND `statusID`='{$active['statusID']}';"));
		$percent = round(100*$typecount['total']/$totalcount['total'], 1);
		$typetotals[] = array($type['typeName'], $typecount['total'], $percent . "%");
	
		if ($percent > 0)
		{
			$pieurl .= "&slice[$num][size]=$percent&slice[$num][name]={$type['typeName']}";
			$num ++;
		}
		
		$subpieurl = "";
		$subnum = 0;
		$modeltotals=array();
		$models = db_query("SELECT DISTINCT modelID, modelName FROM `device` JOIN `model` USING (`modelID`) WHERE `typeID`='{$type['typeID']}' AND `statusID`='{$active['statusID']}';");
		while ($model = dbEnumerateRows($models))
		{
			$modelcount = dbEnumerateRows(db_query("SELECT COUNT(deviceID) as `total` FROM device WHERE `modelID`='{$model['modelID']}' AND `statusID`='{$active['statusID']}';"));
			$subpercent = round(100*$modelcount['total'] / $typecount['total'], 1);
			
			$modeltotals[] = array($model['modelName'], $modelcount['total'], $subpercent . '%');

			$subpieurl .= "&slice[$subnum][size]=$subpercent&slice[$subnum][name]={$model['modelName']}";
			
			$subnum ++;
		}
		
		$subpie = "<img style='float: right; clear:both' src='{$CONFIG['webroot']}/lib/piechart.php?q$subpieurl' alt='piechart'>";
		
		$buf .= mainContentBox($type['typeName'], NULL, $subpie . Table::quick($modeltotals, false));
		
	}
	
	$typecount = dbEnumerateRows(db_query("SELECT COUNT(deviceID) as `total` FROM `device` JOIN `model` USING (`modelID`) WHERE `typeID` IS NULL AND `statusID`='{$active['statusID']}';"));
	$typetotals[] = array("(unknown type)", $typecount['total'], floor(100*$typecount['total']/$totalcount['total']) . "%");
	
	$typetotals[] = array("<strong>Total:</strong>", $totalcount['total'], "100%");

	$chart = "<img style='float: right' src='{$CONFIG['webroot']}/lib/piechart.php?q$pieurl' alt='piechart'>";
	
	print mainContentBox("Overall Breakdown", NULL, $chart .  Table::quick($typetotals, true));
	
	
	print $buf;
					
?>
