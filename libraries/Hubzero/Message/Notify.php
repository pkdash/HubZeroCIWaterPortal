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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for message notification
 */
class Hubzero_Message_Notify extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $uid      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $method   = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $type     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $priority = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_notify', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->uid = intval($this->uid);
		if (!$this->uid) 
		{
			$this->setError(JText::_('Please provide a user ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Get records for a user
	 * 
	 * @param      integer $uid  User ID
	 * @param      string  $type Record type
	 * @return     mixed False if errors, array on success
	 */
	public function getRecords($uid=null, $type=null)
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		if (!$type) 
		{
			$type = $this->type;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE `uid`='$uid'";
		$query .= ($type) ? " AND `type`='$type'" : "";
		$query .= " ORDER BY `priority` ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Clear all entries for a user
	 * 
	 * @param      integer $uid  User ID
	 * @return     boolean True on success
	 */
	public function clearAll($uid=null)
	{
		if (!$uid) 
		{
			$uid = $this->uid;
		}
		if (!$uid) 
		{
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE `uid`='$uid'";

		$this->_db->setQuery($query);
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;
	}
}

