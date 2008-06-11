<?php header("Content-type: text/javascript; charset=iso-8859-1"); ?>
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

	var mouseoldX;
	var mouseoldY;
	var moveobj; 
	var isMoving;
	
	 
	 
	function initialize()
	{
		var mainblock = document.getElementById('main');
		var thisblock;
		var h2;
		var attrib;
		
		for (x = 0; x < mainblock.childNodes.length; x++) // >
		{	
			thisblock = mainblock.childNodes[x]
		
			if (thisblock.className == "contentbox")
			{	
				h2 = thisblock.getElementsByTagName('h2');
				h2 = h2[0];
				
				h2.onmousedown=moveStart;
				h2.onmousemove=move;
				h2.onmouseup=moveEnd;
				h2.onmouseout=moveEnd;
				
				if (h2.captureEvents) h2.captureEvents(Event.MOUSEDOWN);
				if (h2.captureEvents) h2.captureEvents(Event.MOUSEMOVE);
				if (h2.captureEvents) h2.captureEvents(Event.MOUSEUP);
				if (h2.captureEvents) h2.captureEvents(Event.MOUSEOUT);
				
			} 
		}
	} 

	
	function moveStart(e)
	{		
		mouseoldX = getMouseX(e);
		mouseoldY = getMouseY(e);
		isMoving = true;
	}
	
	function move(e)
	{
		if (isMoving)
		{
			var parent = this.parentNode;
			
			if (isNaN(parseInt(parent.style.left))) { parent.style.left = (mouseoldX-20)+'px'; }
			if (isNaN(parseInt(parent.style.top))) { parent.style.top = (mouseoldY-10)+'px'; }
			
			var diffX = getMouseX(e) - mouseoldX;
			var diffY = getMouseY(e) - mouseoldY;
			
			mouseoldX = getMouseX(e);
			mouseoldY = getMouseY(e);

			parent.style.zindex = 10000;			
			parent.style.position = "absolute";
			parent.style.left = (parseInt(parent.style.left) + diffX) + "px;";
			parent.style.top = (parseInt(parent.style.top) + diffY) + "px;";
		}
	}
	
	function moveEnd(e)
	{
		isMoving = false;
	}
	
	function getMouseX(e)
	{
		var posx = 0;
		var posy = 0;
	
		var isOpera = (navigator.userAgent.indexOf('Opera') != -1);
		var isIE = (!isOpera && navigator.userAgent.indexOf('MSIE') != -1)
	
		if (!e) var e = window.event;
		if (e.pageX)
			posx = e.pageX;
		else if (e.clientX)
			posx = e.clientX;
		else if (e.clientX)
		{
			posx = e.clientX;
			if (isIE)
				posx += document.body.scrollLeft;
		}
		
		return posx;
	}
	
	function getMouseY(e)
	{
		var posy = 0;
	
		var isOpera = (navigator.userAgent.indexOf('Opera') != -1);
		var isIE = (!isOpera && navigator.userAgent.indexOf('MSIE') != -1)
	
		if (!e) var e = window.event;
		if (e.pageY)
		{
			posy = e.pageY;
		}
		else if (e.clientY)
		{
			posy = e.clientY;
		}
		else if (e.clientX || e.clientY)
		{
			posy = e.clientY;
			if (isIE)
			{
				posy += document.body.scrollTop;
			}
		}
		
		return posy;
	}