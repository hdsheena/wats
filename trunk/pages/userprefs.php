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
	 
	 //assume we've been included in an index.php. if not, bail
	if (!isset($_SESSION['user']) || ! isset ($CONFIG))
		die("Please don't access this file directly. Use index.php");	

	$TITLE = "Preferences"; 
	$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']),
						 array('name'=>"Preferences"));

	if ($_REQUEST['vissave'])
	{
		setPreference($_SESSION['user']['personID'], "theme", $_REQUEST['theme']);
		
		include "config.php"; //keeps $CONFIG up-to-date
			
		$THEMECSS = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme.css.php";
		$cssprint = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme-print.css.php";	
		
		unset($_REQUEST);
		$_REQUEST['view'] = "userprefs";
	}
	
	
	if ($_REQUEST['setpass'] || $_REQUEST['newpass1'] || $_REQUEST['oldpass'])
	{
		if (pw_encode($_REQUEST['oldpass']) == $_SESSION['user']['password'])
		{
			if ($_REQUEST['newpass1'] == $_REQUEST['newpass2'])
			{
				$result = setPassword($_SESSION['user']['personID'], pw_encode($_REQUEST['newpass1']));
				
				if ($result !== false)
					print successBox("Your password has been changed.");
				else
					print warningBox("Something went wront while changing your password.");
			}
			else
				print warningBox("The new passwords you entered do not match.");
		}
		else
			print warningBox("The old password you entered is incorrect.");
			
		unset($_REQUEST);
		$_REQUEST['view'] = "userprefs";	
	}


	$person = dbEnumerateRows(getPerson($_SESSION['user']['personID']));
	$preferences = getPreferences($_SESSION['user']['personID']);		
	
	while ($pref = dbEnumerateRows($preferences))
		$PREFS[$pref['preference']]=$pref['value'];

	$entities = db_query("SELECT * FROM `config`");
	while ($entry = dbEnumerateRows($entities))
		$CFGS[$entry['key']] = $entry['value'];

	
	
	$passform[] = array("Old Password:", "<input type='password' name='oldpass'>");
	$passform[] = array("", "");
	$passform[] = array("New Password:", "<input type='password' name='newpass1'>");
	$passform[] = array("Repeat New Password:", "<input type='password' name='newpass2'>");
	$passform[] = array("", "<input type='submit' name='setpass' value='Set Password'>");
	
	$passform = form("pas", "POST", "", Table::quick($passform));
	
	print mainContentBox("Set Password", NULL, $passform);
	
	
	
	$themes = getAvailableThemes();
	$themeopts = "<option value=''>(default)</option>";
	foreach ($themes as $themeinfo)
	{
		$sel=($themeinfo['dir']==$CONFIG['theme'])?"SELECTED":"";
		$themeopts.= "<option value='{$themeinfo['dir']}' $sel>{$themeinfo['title']} by {$themeinfo['author']}</option>";
	}
	
	
	$visprefs[] = array("Theme:", "<select name='theme'>$themeopts</select>");
	$visprefs[] = array("", "<input type='submit' name='vissave' value='Save'>");
	
	$visprefs = form("vis", "POST", "", Table::quick($visprefs));
	print mainContentBox("Visual Preferences", NULL, $visprefs);
	
?>
