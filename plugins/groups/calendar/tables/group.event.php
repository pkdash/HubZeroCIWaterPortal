<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'GroupEvent'
 * 
 * Long description (if any) ...
 */
Class GroupEvent extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id = null;

	/**
	 * Description for 'gidNumber'
	 * 
	 * @var unknown
	 */
	var $gidNumber = null;

	/**
	 * Description for 'actorid'
	 * 
	 * @var unknown
	 */
	var $actorid = null;

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title = null;

	/**
	 * Description for 'details'
	 * 
	 * @var unknown
	 */
	var $details = null;

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type = null;

	/**
	 * Description for 'start'
	 * 
	 * @var unknown
	 */
	var $start = null;

	/**
	 * Description for 'end'
	 * 
	 * @var unknown
	 */
	var $end = null;

	/**
	 * Description for 'active'
	 * 
	 * @var unknown
	 */
	var $active = null;

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__xgroups_events', 'id', $db );
	}

	//-----
}

?>