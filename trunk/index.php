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
	
	require_once("config.php"); 
	require_once("lib/utilities.php");
	require_once("lib/db.php");
	require_once("lib/page.php");
	
	
	session_name("inventory");
	session_start();
	ob_start();
	
	//keep track of browsing history
	//mostly used to replace the unreliable $_SERVER['HTTP_REFERER']
	$_SESSION['history'][] = $CONFIG['webroot'] ."/index.php?" . $_SERVER['QERRY_STRING'] ;
	
	if ($_REQUEST['action'] == "signout")
	{
		session_destroy();
		header ("Location: {$CONFIG['webroot']}/login.php");
		die();
	}
	
	//login check
	if ((! isset($_SESSION['user']['username'])))
	{
		header ("Location: {$CONFIG['webroot']}/login.php");
		die();
	}
	
	
	//deal with theme stuff
	$COMMONCSS = "{$CONFIG['themedir']}/common.css.php";
	$THEMECSS = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme.css.php";
	$cssprint = "{$CONFIG['themedir']}/{$CONFIG['theme']}/theme-print.css.php";

	require_once ("menu.php");
	
	if ($_REQUEST['view'] == "") $_REQUEST['view'] = "home";

	$computedFile = $CONFIG['unixroot'] .'/pages/'.stripPaths($_REQUEST['view']) .'.php';

	log_event(date("Y-m-d H:i:s"), "info", $_SESSION['user']['username'], "page access", 
		"{$_SERVER['QUERY_STRING']} $computedFile");


	if (file_exists($computedFile))
	{
		ob_start();
		
		
		include $computedFile;
		
		$body = ob_get_contents();
		ob_end_clean();
	}
	else
	{
		if (DEBUG)
			$body = cautionBox("Page '$computedFile' not found.");
		else
			$body = warningBox("The page you have tried to view has not yet been implemented. If you think that's wrong, contact Ryan.");
		
		$TITLE = "Error"; 
		$BREADCRUMBS = array(array('name' => "Home", 'link'=>$CONFIG['webroot']), array('name'=>"Error")); 
	}


	startPage("$TITLE: WATS",$TITLE, $BREADCRUMBS, $COMMONCSS, $THEMECSS, $cssprint, $JSSRC1,$JSSRC2, $ONLOAD);

	if (DEBUG)
		print cautionBox("Debug mode is on.");
			
	print $body;

	$links = array
	(
		new HTMLLink("Preferences", "{$CONFIG['webroot']}/?view=userprefs", "", ""),
		"<a href='https://sourceforge.net/projects/wats/'>wats</a> &copy;2008 The Evergreen School, released under the <a href='http://www.gnu.org/licenses/gpl-3.0.html'>GNU GPL v3</a>",
	);

	endPage($links, $MENU, $SIDEBOXES);		
	
	

?>
