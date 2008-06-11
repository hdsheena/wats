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
	header("Content-type: text/css");
	
	require_once("../../lib/page.php");
	require_once("../../lib/db.php");
	require_once("../../config.php");
	
	
	$path = "{$CONFIG['themedir']}/{$CONFIG['theme']}/";
?>

body
{
	margin: 0px;
	
	color: white;
	background-color: #888;
}

a
{
	padding: .1em;
}

a img
{
	border: none;
}

a:visited
{
	color: #0000ff;
}



#header
{	
	top: 0px;
	left: 0;
	right: 0;
	
	height: 9em;
}

#header img
{
	margin: 0;
	padding: 0;
}

#header a
{
	
	width: 100%;
	color: blue;
}

#header a:visited
{
	color: #ff00ff;
}


h1
{
	/*position: relative;*/
	top: 0px;
	
	margin: 0px;
	padding: .5em;
	
	color: white;
	
	background-color: #4b6983;
	
	border: 1px solid #006;
}

#welcome, .location
{
	color: white;
	
	background-color: #24333F;
	
	margin: 0;
	padding-left: 1em;
} 

#welcome a:visited, #welcome a, .location a, .location a:visited
{
	color: #aaf;
	background-color: inherit;
}

#welcome
{
	float: right;
	
	width: 11em;
	padding-right: .5em;
	
	background-image: none;
	
	text-align: right;
}

#nav h2
{
	display: none;	
}

#nav
{
	position: absolute;

	left: 0px;
	top: 5.7em;
	right: 0px;
	
	margin: 0px;
	
	border: 1px dashed silver;
	
	color: black;
	background-color: white;
}

#nav ul
{
	position: relative;
	float: left;
	display: inline;
	
	margin: 0;
	
	background-color: inherit;
	
	/*whitespace: nowrap;*/

}

#nav ul li
{
	display: inline;
	position: relative;
	list-style-type: none;
	
	padding-right: .4em;
	
	margin: 0;
	
	border-left: 2px solid white;
}

#nav ul li span
{
	padding-left: .5em;
	padding-right: .5em;
}

#nav ul li ul
{
	display: none;
}

#nav ul#menuroot > li:hover
{
	border-left: 2px solid silver;
	border-bottom: 2px solid silver;
}

#nav li.l0:hover
{
	color: white;
	background-color: #005;
}

#nav li:hover ul
{
	display: block;
	position: absolute;

	
	background-color: white;
	
	padding: .5em;
	margin:0;
	
	top: 1.2em;
	left: -2px;
	
	width: 20em;
	
	border-left: 2px solid black;
	border-bottom: 1px solid black;
	border-right: 1px solid black;
}

#nav li:hover ul li
{
	display: block;
	
	width: 100%;
	
	border:  none;
	
	padding-bottom: .2em;
}

#nav li li a /* forces the menu highlighting to the width of the dropdown */
{
	display: block;
}

#nav li li a:hover
{
	color: #fff;
	background-color: #004;

	padding: .1em;
}

#main
{
	top: 4em;
	bottom: 2.5em;
	left: 0;
	width: 95%;
	
	margin-top: -3em;
	
	/*margin: 0;*/
	padding: 1em;
	
	overflow: auto;
}


div.contentbox
{
	margin-top: .5em;
	margin-bottom: .4em;

	border: 1px solid black;
	color: black;
	background-color: #eee;
}

div.contentbox div
{
	padding-left: .5em;
	padding-right: .5em;
}

.contentbox h2
{
	position: relative;
	font-size: 115%;
	
	margin-top: 0px;
	padding-top: 0px;
	padding-left: .5em;
	
	border-bottom: 1px solid silver;
	
	background-color: white;
}

.contentbox h2 .headeraction
{
	position: absolute;
	right: .5em;
	
	font-size: 80%;
	font-weight: normal;
}

div.info
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;
	padding-top: 0em;
	padding-bottom: .5em;

	border: 1px solid black;
	color: black;
	background-color: #eee;
	
	background-image: url(<?php print $path; ?>/info.png);
	background-repeat: no-repeat; 
	background-position: 1em .7em;
}

div.query
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;
	padding-top: 0em;
	padding-bottom: .5em;

	border: 1px solid black;
	color: white;
	background-color: #444;
	
	background-image: url(<?php print $path; ?>/query.png);
	background-repeat: no-repeat; 
	background-position: 1em .7em;
}

div.caution
{
	margin-top: .1em;
	margin-bottom: .3em;
	
	padding-left: 60px;
	padding-top: 0em;
	padding-bottom: .5em;
	
	border: 1px solid yellow;
	color: black;
	background-color: #cc0;
	
	background-image: url(<?php print $path; ?>/caution.png);
	background-repeat: no-repeat; 
	background-position: 1em .7em;
}

div.warning
{
	margin-top: .3em;
	margin-bottom: .5em;
	
	padding-left: 60px;
	padding-top: 0em;
	padding-bottom: .5em;

	border: 1px solid red;
	color: white;
	background-color: #400;

	background-image: url(<?php print $path; ?>/warning.png);
	background-repeat: no-repeat; 
	background-position: 1em 1em;
}

div.warning h3, div.info h3, div.caution h3, div.success h3, div.query h3
{
	margin-top: 0em;
}

div#footer
{
	clear: both;
	
	left: 0px;	
	bottom: 0px;
	right: 0px;
	
	width: 100%;
	
	
	height: 2em;
	
	border-top: 1px solid black;
	color: black;
	background-color: #eee;
}

div#footer ul, div#footer ol
{
	margin:0px;
	padding-top: .5em;
}

div#footer li
{
	display: inline;
	
	border-left: 1px solid #777;
	
	margin: 0px;
	padding-left: 1em;
	padding-right: 1em;
	
}

div#footer li:first-child
{
	border-left: none;
}

/*** STOLEN FROM http://moronicbajebus.com/wordpress/wp-content/cssplay/pop-up-menus/simple.css***/
