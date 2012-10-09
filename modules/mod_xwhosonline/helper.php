<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying who's online
 */
class modXWhosonline
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		$database =& JFactory::getDBO();

		$this->online = $this->params->get('online');
		$this->users  = $this->params->get('users');
		$moduleclass_sfx = $this->params->get('moduleclass_sfx');

		$juser =& JFactory::getUser();
		$this->admin = $juser->authorize('mod_xwhosonline', 'manage');

		if ($this->online) 
		{
			$query1 = "SELECT COUNT(DISTINCT ip) AS guest_online FROM #__session WHERE guest=1 AND (usertype is NULL OR usertype='')";
			$database->setQuery($query1);
			$this->guest_array = $database->loadResult();

			$query2 = "SELECT COUNT(DISTINCT username) AS user_online FROM #__session WHERE guest=0 AND usertype <> 'administrator' AND usertype <> 'superadministrator'";
			$database->setQuery($query2);
			$this->user_array = $database->loadResult();
		} 
		else 
		{
			$this->guest_array = null;
			$this->user_array = null;
		}

		if ($this->users) 
		{
			$query = "SELECT DISTINCT a.username"
					. "\n FROM #__session AS a"
					. "\n WHERE (a.guest=0)";
			$database->setQuery($query);
			$this->rows = $database->loadObjectList();
		} 
		else 
		{
			$this->rows = null;
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
