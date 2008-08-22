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


	function startPage($pagetitle,$pageheader, $breadcrubms,  $COMMONCSS, $THEMECSS, $cssprint, $jsfile1, $jsfile2, $jsbodyload)
	{
		print startHTML($pagetitle, $jsfile1, $jsfile2, $COMMONCSS, $THEMECSS, $cssprint);
		
		print startBody($jsbodyload);
		
		print pageHeader($breadcrubms, $pageheader);
		
		print startContents($breadcrubms, $pageheader);
		
		
		
		print startMain();
	}
	
	function endPage($footer, $menu, $sideboxes)
	{
		print endMain();
		
		print navSection($menu, $sideboxes);
		
		print endContents();
		
		print footer($footer);
		
		print endBody();
		
		print endHTML();
	}


	function startHTML($title, $javascript, $js2,  $css1, $css2 , $cssprint)
	{
		global $CONFIG;
// 		$buffer = ""; 
		$buffer .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		//$buffer .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
		
		$buffer .= "\n<html>\n";
		$buffer .= "	<head>\n";
		$buffer .= "		<title>$title</title>\n";
		$buffer .= "		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>\n";
		
		if ($css1 != "")
			$buffer .= "		<link rel='stylesheet' type='text/css' media='screen' href='$css1'>\n";
		if ($css2 != "")
			$buffer .= "		<link rel='stylesheet' type='text/css' media='screen' href='$css2'>\n";
		if ($cssprint != "")
			$buffer .= "		<link rel='stylesheet' type='text/css' media='print' href='$cssprint'>\n";
		
		//$buffer .= "		<script type='text/javascript' src='{$CONFIG['webroot']}/lib/global.js.php' ></script>\n";
		
		if ($javascript != "")
			$buffer .= "		<script type='text/javascript' src='$javascript' ></script>\n";
		if ($js2 != "")
			$buffer .= "		<script type='text/javascript' src='$js2' ></script>\n";
		$buffer .= "	</head>\n";
		
		return $buffer;

	}
	
	function endHtml()
	{
		return "</html>\n";
	}
	
	function startBody($onload)
	{
		//return "	<body onLoad='$onload' onUnload='pageUnloading()'>\n";
		return "	<body onLoad='$onload; ' >\n";
	}
	
	function endBody()
	{
		return "	</body>\n";
	}
	
	/**
	 *
	 *
	 * $tabs should be an array of associative 2-element arrays.
	 *	
	 */
	function pageHeader($location, $header)
	{
		global $CONFIG;
	
		$buffer .= "		<div id='header'>\n";
		//$buffer .= "			<p id='logos'><img src='{$CONFIG['webroot']}/common/themes/{$CONFIG['theme']}/inventorylogo.png' alt='Inventory'></p>\n";
			
		$buffer .= "			<h1>{$header}</h1>\n";
		if ($_SESSION['user']['username']=="Anonymous" || ! isset($_SESSION['user']['username']))
			$buffer .= "			<p id='welcome'>Hello, Anonymous </p>\n";
		else
			$buffer .= "			<p id='welcome'>Hello, ".($_SESSION['user']['nameFirst'])." (<a href='{$CONFIG['webroot']}/index.php?action=signout'>Logout</a>)</p>\n";		


		
		$buffer .= locationBar($location);
		
		$buffer .= "		</div>\n";
		
		return $buffer;
	}
	
	/**
	 * Starts the 'contents' section of the webpage
	 * 
	 * @param $location array containing the current location. each element
	 *  has two sub-elements, 'name' and 'link'. 
	 */
	function startContents()
	{
		global $CONFIG;
	
		$buffer .= "		<div id='contents'>\n";
		

		
		return $buffer;
	}
	
	function endContents()
	{
		return "		</div>\n";
	}
	
	function navSection($menuitems, $sideboxes = "")
	{
		$buffer = "";
		$buffer .= "			<div id='nav'>\n";
			
		if ($menuitems != "")
		{
			
			$buffer .= "				<div class='linkbox'>\n";
			$buffer .= "					<h2>Menu</h2><div class='linkbox-real'>\n";
			$buffer .= "<ul id='menuroot'>\n";
			
			$firsttime=true;
		
			//parseMenu() prints, and isn't going to get fixed anytime soon
			ob_start();	
			foreach ($menuitems as $list)
			{
				//print "<li>";
				MenuItem::parseMenu($list,0);
				//print "</li>";
				
				$firsttime=false;
			}
			
			$buffer .= ob_get_contents();		
			ob_end_clean();

			$buffer .= "</ul>\n\n";
			
			$buffer .= "				</div></div>\n";
		}

		if ($sideboxes != "")
		{
			foreach ($sideboxes as $box)
			{	

				$buffer .= "				<div class='linkbox'>\n";
				$buffer .= "					<h2>".$box->getName()."</h2><div class='linkbox-real'>\n";
				$buffer .= $box->getContents();
				$buffer .= "				</div></div>\n";
			}
		}
		
		$buffer .= "			</div>\n"; //end of nav
		
		return $buffer;
	}


	function startMain()
	{
		return "			<div id='main'>\n";
	}
	
	function endMain()
	{
		return "			</div>\n";
	}
	
	/**
	 * TODO : Should output OL, not SINGLE PARAGRAPH!
	 *
	 * Returns the "location bar" using $location.
	 *
	 * The location bar is the "Home > Section > Page" under the H1
	 *
	 * @param $location array containing the current location. each element
	 *  has two sub-elements, 'name' and 'link'.
	 *
	 */
	function locationBar($location)
	{
		global $CONFIG;
		
		if ($location == "")
			return;
		
		$buffer = "<p class='location'>";
		
		$first = true;
		
		foreach ($location as $link)
		{
			if (! $first) $buffer .= "&rarr; ";
			if ($link['link'] == "")
				$buffer .= "<strong>{$link['name']}</strong> ";
			else
				$buffer .= "<a href='{$link['link']}'>{$link['name']}</a> ";
// 			$buffer .="</li>";
			
			$first = false;
		}
		
		$buffer .= "</p>\n";
		
		return $buffer;		
	}
	
	
	function paragraph($text)
	{
		return "<p>$text</p> \n";
	}
	
	function queryBox($text)
	{
		$err = debug_backtrace();
		$caller =  $err[1]['function'];
		
		//text that contains block-level html shouldn't be in <P> tags,
		//but text w/o block-level elements should.
		if (! preg_match('/(\<p|\<div|\<table|\<ul|\<ol)/', $text) )
			$text = paragraph($text);
	
		return "<div class='query'><h3>$caller asks:</h3>$text</div>\n";
	}
	
	
	function successBox($text)
	{
		//text that contains block-level html shouldn't be in <P> tags,
		//but text w/o block-level elements should.
		if (! preg_match('/(\<p|\<div|\<table|\<ul|\<ol)/', $text) )
			$text = paragraph($text);
	
		return "<div class='success'>$text</div>\n";
	}
	
	function infoBox($text)
	{
		//text that contains block-level html shouldn't be in <P> tags,
		//but text w/o block-level elements should.
		if (! preg_match('/(\<p|\<div|\<table|\<ul|\<ol)/', $text) )
			$text = paragraph($text);
	
		return "<div class='info'>$text</div>\n";
	}
	
	function cautionBox($text)
	{
		//text that contains block-level html shouldn't be in <P> tags,
		//but text w/o block-level elements should.
		if (! preg_match('/(\<p|\<div|\<table|\<ul|\<ol)/', $text) )
			$text = paragraph($text);
	
		return "<div class='caution'>$text</div>\n";
	}
	
	function warningBox($text)
	{
		//text that contains block-level html shouldn't be in <P> tags,
		//but text w/o block-level elements should.
		if (! preg_match('/(\<p|\<div|\<table|\<ul|\<ol)/', $text) )
			$text = paragraph($text);
	
		return "<div class='warning'>$text</div>\n";
	}

	
	function mainContentBox($title, $actionurl, $contents)
	{
		if (! preg_match("/(\\<p|\\<table|\\<form|\\<ul)/", $contents))
			$contents = "<p>$contents</p>";
		
		if ($actionurl instanceof HTMLLink) $url = $actionurl->__toString();
		
		$buffer = "";
		$buffer .= "				<div class='contentbox'>\n";
		$buffer .= "					<h2>$title<span class='headeraction'>$url</span></h2>";
		$buffer .= "					<div>$contents</div>\n";
		$buffer .= "					<div style='clear: both'> </div>\n";
		$buffer .= "				</div>\n";
		
		return $buffer;
	}
	
	
	function form($name, $method, $location, $contents)
	{
		global $CONFIG;
		
		if ($location == "" && strtoupper($method)=='POST')
		{	
			$location = $CONFIG['webroot'] .'/?';
			foreach ($_REQUEST as $var=>$value)
			{
				//if ($_REQUEST[$var] == $_GET[$var])
				$location .= "{$var}={$value}&amp;";
			}
		}
		else if ($location =="" && strtoupper($method) == 'GET')
		{
			//preserve mode and view. others will need a hidden form element
			$location = $CONFIG['webroot'];
			$contents .= "<input type='hidden' name='view' value='{$_REQUEST['view']}'>";
		}
		
		return "<form name='$name' method='$method' action='$location' enctype='multipart/form-data' onsubmit='document.unloadingBySubmit=true;'>$contents</form>";
	}
	
	
	
	function footer($items)
	{

		
		$buffer = "";
		$buffer .= "				<div id='footer'>\n";		
		if  (is_array($items))
		{
			$buffer .= "					<ul>\n";
			foreach ($items as $item)
				$buffer .= "						<li>$item</li>\n";
			
			$buffer .= "					</ul>\n";	
		}	
		$buffer .= "				</div>\n";
		
		return $buffer;
	}



class HTMLLink
{
	private $text;
	private $url;
	private $target;
	private $onclick;
	
	function __construct($text, $url, $target, $onclick)
	{
		$this->text = $text; 
		$this->url=$url;
		$this->target=$target;
		$this->onclick=$onclick;
	}
	
	function __toString()
	{
		$target = ($this->target)?"target='{$this->target}'":"";
		return "<a href='{$this->url}' $target onclick='{$this->onclick}'>{$this->text}</a>";
	}
}	

class Table
{
	private $rows;
	private $cols; 
	
	private $cells = array(array());
	
	
	function __construct($rows, $cols)
	{
		if ($rows < 1)
			throw new Exception ("Too Few Rows");
		if ($cols < 1)
			throw new Exception ("Too Few Columns");
		
		$this->rows = $rows;
		$this->cols = $cols;
	}
	
	function getCell($row, $col)
	{
		return $this->cells[$row][$col];
	}
	
	function setCell($row, $col, $cell)
	{
		if ($row > ($this->rows -1) || $row < 1)
			throw new Exception("Invalid Row");
		if ($col > ($this->cols -1) || $col < 1)
			throw new Exception("Invalid Column");
		if (! $cell instanceof TableCell)
			throw new Exception("Incorrect Cell"); 	
			
		$this->cells[$row][$col] = $cell;	
	}
	
	
	/**
	 * Renders a two-dimensional array as an HTML table.
	 *
	 * If $isheader is TRUE, the first row is rendered with TH
	 * tags rather than TD tags.
	 *
	 * @param $data[][]  a two-dimensional array (rows, then cols) of table data
	 * @param isheader   specifies if the first row is data or header
	 */
	static function quick($data, $isheader = false)
	{
		if ($data === null || strtolower(getType($data)) != "array")
			return false;
		
		$buffer = "";
		$buffer .= "<table>\n";
	
		foreach ($data as $key=>$row)
		{
			$buffer .= "<tr>";
			
			foreach($row as $col)
			{
				if ($isheader && $key == 0)
					$buffer .= "<th>$col</th>";
				else
					$buffer .= "<td>$col</td>";
			}
			
			$buffer .= "</tr>\n";
		}
		
		$buffer .= "</table>\n";
		
		return $buffer;

	}
}

class TableCell
{
	const DATACELL = 0;
	const HEADERCELL = 1;
	
	private $type = TableCell::DATACELL;
	private $contents;
	
	function __construct($type, $contents)
	{
		if ($type != TableCell::DATACELL && $type != TableCell::HEADERCELL)
			throw new Exception("Invalid Cell Type");
			
		$this->type = $type;
		$this->contents = $contents;	
	}
	
	function __toString()
	{
		$tag = ($this->type==TableCell::DATACELL)?"td":"th";
		
		return "<{$tag}>{$this->contents}</$tag>";
	}
}
	

?>