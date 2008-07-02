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
	color: white;
	background: black url(<?php print $path;?>background.jpg) no-repeat fixed top left;
}

a img
{
	border: none;
}

a:visited
{
	color: #aa00aa;
}
a:hover
{
	color: #0000ff;
	background-color: #ccc;
}

#header
{
	position: fixed;
	
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
	color: blue;
}

#header a:visited
{
	color: #ff00ff;
}

#contents
{
	position: absolute;
	
	top: 7em;
	bottom: 3em;
	width: 95%;
	
	/*height:*/ 
	
	margin: 0;
	padding: 0;
	
	overflow: hidden;
}

h1
{
	/*position: relative;*/
	top: 0px;
	
	margin: 0px;
	padding: .5em;
	
	color: yellow;
	
	background-image: url(<?php print $path;?>50blue.png);
	
	border: 1px solid #006;
}

#welcome, .location
{
	color: white;
	
	background-image: url(<?php print $path;?>50black.png);
	
	margin: 0;
	padding-left: 1em;
} 

#nav h2
{
	text-align: center;
	
	padding:0px;
	margin: 0px;
	
}

#nav
{
	float: right;

	right: 5px;
	top: 0px;

	width: 24%;
	
	margin: 0px;
	
	border: 1px dashed silver;
	
	color: black;
	background-image: url(<?php print $path;?>50grey.png);
}


#main
{
	position: fixed;
	
	top: 7em;
	bottom: 2.5em;
	left: 1em;
	width: 70%;
	
	margin: 0;
	
	overflow: auto;
}


div.contentbox
{
	margin-top: .5em;
	margin-bottom: .4em;

	border: 1px solid black;
	color: black;
	background-image: url(<?php print $path;?>50grey.png);
}

.contentbox h2
{
	margin-top: 0px;
	padding-top: 0px;
}

div.info
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;

	border: 1px solid black;
	color: black;
	background-image: url(<?php print $path;?>50grey.png);
	
	content: url(example1/info.png);
}

div.caution
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;

	border: 1px solid yellow;
	color: black;
	background-image: url(<?php print $path;?>50yellow.png);
}

div.warning
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;

	border: 1px solid red;
	color: white;
	background-image: url(<?php print $path;?>50red.png);
}

div.success
{
	margin-top: .4em;
	margin-bottom: .3em;
	
	padding-left: 60px;

	border: 1px solid green;
	color: white;
	background-image: url(<?php print $path;?>50green.png);
}



div#footer
{
	position: fixed;
	clear: both;
	
	left: 0px;	
	bottom: 0px;
	
	width: 100%;
	
	
	height: 2em;
	
	border-top: 1px solid black;
	color: black;
	background-image: url(<?php print $path;?>50grey.png);
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
