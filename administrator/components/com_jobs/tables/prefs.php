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

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job prefs
 */
class Prefs extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $uid		= NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $filters	= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_prefs', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      integer $uid      User ID
	 * @param      string  $category Category
	 * @return     boolean True upon success
	 */
	public function loadPrefs($uid, $category = 'resume')
	{
		if ($uid === NULL) 
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid='$uid' AND category='$category' LIMIT 1");

		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		}
		return false;
	}
}

