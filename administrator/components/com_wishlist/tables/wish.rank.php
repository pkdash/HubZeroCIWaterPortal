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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for wish ranking
 */
class WishRank extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $wishid      	= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $userid 		= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $voted    	    = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $importance     = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $effort		    = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $due    	    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_vote', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->wishid) == '') 
		{
			$this->setError(JText::_('WISHLIST_ERROR_NO_WISHID'));
			return false;
		}

		return true;
	}

	/**
	 * Get a record and bind to $this
	 * 
	 * @param      integer $oid    User ID
	 * @param      integer $wishid Wish ID
	 * @return     boolean False if error, True on success
	 */
	public function load_vote($oid=NULL, $wishid=NULL)
	{
		if ($oid === NULL) 
		{
			$oid = $this->userid;
		}
		if ($wishid === NULL) 
		{
			$wishid = $this->wishid;
		}

		if ($oid === NULL or $wishid === NULL) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE userid='$oid' AND wishid='$wishid'");
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get votes on a wish
	 * 
	 * @param      integer $wishid Wish ID
	 * @return     mixed False if error, array on success
	 */
	public function get_votes($wishid=NULL)
	{
		if ($wishid === NULL) 
		{
			$wishid = $this->wishid;
		}

		if ($wishid === NULL) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE wishid='$wishid'");
		return $this->_db->loadObjectList();
	}

	/**
	 * Remove a vote
	 * 
	 * @param      integer $wishid Wish ID
	 * @param      integer $oid    User ID
	 * @return     boolean False if error, True on success
	 */
	public function remove_vote($wishid=NULL, $oid=NULL)
	{
		if ($oid === NULL) 
		{
			$oid = $this->userid;
		}
		if ($wishid === NULL) 
		{
			$wishid = $this->wishid;
		}

		if ($wishid === NULL) {
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE wishid='$wishid'";
		if ($oid) 
		{
			$query .= " AND userid=" . $oid;
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

