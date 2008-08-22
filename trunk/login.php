<?php
	/**
	 * Inventory System 
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
	 
	require_once("config.php"); 
	require_once("lib/utilities.php");
	require_once("lib/db.php");
	require_once("lib/page.php");

	
	session_name("inventory");
	session_start();
	ob_start();
	
	if ($_REQUEST['username'] && $_REQUEST['password'])
	{
		sql_connect();
		
		$user = makeSafe($_REQUEST['username']);
		$result = dbEnumerateRows($CONFIG['dbresource']->query("SELECT * FROM person WHERE username='{$user}'"));
		
		
		if ($result['personID'] && $result['isCurrent'])
		{
			if ($result['password'] == pw_encode($_REQUEST['password']) && $result['isCurrent'])
			{
				$_SESSION['user'] = $result;
				
				$roles = getPersonRoles($_SESSION['user']['personID']);
				$_SESSION['user']['roles'] = array();
				while ($role = dbEnumerateRows($roles))
					$_SESSION['user']['roles'][] =  $role['roleID'];
		
				
				header("Location: index.php");
				return;
			}
			else
				$error = "Invalid Username or Password";
		}			
		else
			$error = "Invalid Username or Password";
	}
	else if ($_REQUEST['username'])
		$error  = "Password is a required field";
	else if ($_REQUEST['password'])
		$error  = "Username is a required field";

	
	//login check
	if ((isset($_SESSION['user']['username'])))
	{
		header ("Location: {$CONFIG['webroot']}/login.php");
		die();
	}
	
	
	//deal with theme stuff
	$COMMONCSS = "{$CONFIG['themedir']}/common.css.php";
	$THEMECSS = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme.css.php";
	$cssprint = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme-print.css.php";

	
	
	if ($_REQUEST['view'] == "") $_REQUEST['view'] = "home";

	$computedFile = $CONFIG['localunixroot'] .'/pages/'.stripPaths($_REQUEST['view']) .'.php';

	log_event(date("Y-m-d H:i:s"), "info", $_SESSION['user']['username'], "page access", 
		"{$_SERVER['QUERY_STRING']} $computedFile");


	//keep track of browsing history
	//mostly used to replace the unreliable $_SERVER['HTTP_REFERER']
	$_SESSION['history'][] = $CONFIG['webroot'] ."/index.php?" . $_SERVER['QERRY_STRING'] ;

	


	startPage("wats Login","wats Login", $BREADCRUMBS,  $COMMONCSS, $THEMECSS, $cssprint, $JSSRC1,$JSSRC2, $ONLOAD);

	if ($error)
		print warningBox($error);

	$form[] = array("Username:", "<input type='text' name='username'>");
	$form[] = array("Password:", "<input type='password' name='password'>");
	$form[] = array("", "<input type='submit' name='login' value='Login'>");
	$form = form("login", "POST", "login.php",Table::quick($form, false));
	
	print mainContentBox("Login", null, $form);

	endPage(array (), $MENU, "");		
	
	

?>
