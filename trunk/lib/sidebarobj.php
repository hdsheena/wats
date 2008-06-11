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


	class SideBox
	{
		//The name of the item as displayed in a menu
		var $boxname;
		
		//content of the box
		var $content; 
			
		/**
		 * Creates a SideBox with the given Name and  Content.  
		 *
		 * @param name		(string) The text that should be displayed in the menu
		 * @param content	(string) The contents
		 *
		 * @return SideBox	
		 */
		function SideBox($name, $content)
		{
			if (is_string($name))
				$this->boxname = $name; 
				
			if (is_string($content))
				$this->content = $content;
				
			$this->index=0;

		}
		
		/**
		 * Returns the name of the sidebox set by setName() or 
		 * during instantiation
		 *
		 * @return (string) The name of the box
		 */
		function getName()
		{
			return $this->boxname;
		}
		
		/**
		 * Returns the location of the sidebox set by setLocation() or 
		 * during instantiation
		 *
		 * @return (string) The contents of the box
		 */
		function getContents()
		{
			return $this->content; 
		}
		
		function setName($newname)
		{
			if (is_string($newname))
				$this->boxname = $newname;
		}

		function setContents($contents)
		{
			if (is_string($contents))
				$this->content = $contents;
		}

	}
?>