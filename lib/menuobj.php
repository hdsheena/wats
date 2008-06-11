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

	/**
	 * MenuItem defines an item in a navigational menu.  Each item 
	 * can have children, allowing the user to create a multi-level,
	 * tree-like menuing system.
	 */
	class MenuItem
	{
		//The name of the item as displayed in a menu
		var $itemname;
		
		//Location on activation
		var $url; 
		
		//Can be used to disable inappropriate menuitems
		var $module;
		var $page; 
		
		//part of the tree structure
		//this would be better handled by a native Collection object, 
		//but there doesn't seem to be one...
		var $child = array();
		
		//used in the child iterator 
		var $index; 
	
		/**
		 * Creates a MenuItem with the given Name and Location.  
		 *
		 * @param name		(string) The text that should be displayed in the menu
		 * @param location	(string) URL the menu item represents
		 *
		 * @return MenuItem	
		 */
		function MenuItem($name, $location)
		{
			if (is_string($name))
				$this->itemname = $name; 
				
			if (is_string($location))
				$this->url = $location;
				
			$this->index=0;

		}
		
		/**
		 * Returns the name of the menu item set by setName() or 
		 * during instantiation
		 *
		 * @return (string) The name of the menu item
		 */
		function getName()
		{
			return $this->itemname;
		}
		
		/**
		 * Returns the location of the menu item set by setLocation() or 
		 * during instantiation
		 *
		 * @return (string) The URL of the menu item
		 */
		function getLocation()
		{
			return $this->url; 
		}
		
		/**
		 * Returns the module the menu item is associated with
		 *
		 * @return (string) The URL of the menu item
		 */
		function getModule()
		{
			return $this->module; 
		}
		
		/**
		 * Returns the page the menu item is associated with
		 *
		 * @return (string) The URL of the menu item
		 */
		function getPage()
		{
			return $this->page; 
		}
		
		/**
		 * Acts as an iterator over the collection of children of this
		 * menuitem.  On success, it returns an instance of MenuItem.
		 * When all children have been iterated, returns null.
		 *
		 * By returning NULL, nextChid() can be used in a while() loop
		 *
		 * Iteration can be reset either by:
		 * 		1) using the reset() method.
		 *		2) adding a child to the menu item.
		 *
		 * @return (object) MenuItem of the next child, or false if there are no more children.
		 */
		function nextChild()
		{
			//see if we're out of children
			if ($this->index >= count($this->child)) 
			{
				return false; 
			}

			return $this->child[$this->index++];
		}
		
		/**
		 * Resets the iterator of child menuitems
		 */
		function reset()
		{
			//start at the beginning of the array
			$this->index = 0; 
		}
		
		
		/**
		 * Adds the MenuItem as a child of this item, optionally at a specified index. If 
		 * index is not specified, it defauts to the end.
		 *
		 * @param newchild		(MenuItem) the child MenuItem that should be added to the collection
		 * @param addindex		(optional integer) the position in the collection for the new item
		 *
		 * @return (boolean) true on success, null on failure
		 */
		function addChild($newchild , $addindex = -1)
		{
			//sanity checks- must be in the array, or last element.
			//and $child needs to be a MenuItem
			if (($addindex < 0) || ($addindex > count($this->child)))
				$addindex = count($this->child); 
				
			//can't add non-MenuItems to our collection
			if (! is_a($newchild, 'MenuItem'))
				return false; 
			
			//there are three main cases: front, back, somewhere in the middle.  
			//back is the easiest, middle the hardest. front is basically like middle 
			if ($addindex == count($this->child))
			{
				$this->child[count($this->child)] = &$newchild; 
			}
			else
			{
				/* Steps: 
					1. Shift the array above the $addindex up one. we've already guarenteed
					   that $addindex is a resonable value
					2. insert the new object
				*/
				
				for ($i = count($this->child); $i > $addindex; $i--)
				{
					$this->child[$i] = &$this->child[$i-1];
				}
				
				
				$this->child[$addindex] = &$newchild;
			}

			
			//reset the iteration pointer, as this operation will upset the iteration process
			$this->reset();
		}
		
		/**
		 * Changes the name initially assigned to the menu item.
		 * 
		 * Behavior is undefined when $newname is not a string
		 *
		 * @param newname	(string) The new name for the menuitem
		 */
		function setName($newname)
		{
			//check to see that we got a string. if we didn't, oh well.
			if (is_string($newname))
				$this->name = $newname;
		}
		
		/**
		 * Changes the location initially assigned to the menu item.
		 * 
		 * Behavior is undefined when $newurl is not a string
		 *
		 * @param newurl	(string) The new location for the menuitem
		 */
		function setLocation($newurl)
		{
			//check to see that we got a string. if we didn't, oh well.
			if (is_string($newurl))
				$this->url = $newurl;
		}
		
		/**
		 * Changes the module the menu item is assigned.
		 * 
		 * Behavior is undefined when $newmod is not a string
		 *
		 * @param newurl	(string) The new module assignment
		 */
		function setModule($newmod)
		{
			//check to see that we got a string. if we didn't, oh well.
			if (is_string($newmod))
				$this->module = $newmod;
		}
		
		/**
		 * Changes the page the menuitem is assigned.
		 * 
		 * Behavior is undefined when $newurl is not a string
		 *
		 * @param newpage	(string) The new location for the menuitem
		 */
		function setPage($newpage)
		{
			//check to see that we got a string. if we didn't, oh well.
			if (is_string($newpage))
				$this->page = $newpage;
		}
		
		/**
		 * Returns true if this MenuItem has subitems.
		 *
		 * @return (boolean) True if subitems exist
		 */
		function hasChildren()
		{
			return count($this->child) >0 ? true : false;
		}
		
	/**
	 * parseMenu() iterates over the subtree formed by $item and outputs
	 * the contents of a list.  If subitems are found, it starts a sub-&lt;ul&gt; element
	 * to hold the children.
	 *
	 * It assumes that an ol or ul has already been started.
	 *
	 * @param item		(object) MenuItem that is the root of the subtree
	 * @param level		(integer) depth
	 */
	static function parseMenu($item, $level)
	{
		//output us as a link if there's a location, or a span otherwise
		//so that they still get indented properly.
		if ($item->getLocation() == "")
			print "<li class='l$level'><span>" . $item->getName() ."</span>";
		else
			print "<li class='l$level'><a href='".$item->getLocation()."' >" . $item->getName() ."</a>";

		//if there's children, they're still a part of this list item
		if ($item->hasChildren())
			print "<ul>\n";
		else
		{
			print "</li>\n";
			return;
		}

		//deal with children
		$item->reset();

		while($child = $item->nextChild())
		{
				MenuItem::parseMenu($child, $level + 1);
		}

		print "</ul></li>\n";
	}
	}


?>