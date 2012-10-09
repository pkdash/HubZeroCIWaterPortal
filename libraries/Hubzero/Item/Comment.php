<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for comments
 */
class Hubzero_Item_Comment extends JTable 
{
	/**
	 * Primary key
	 * 
	 * @var integer int(11) 
	 */
	var $id            = NULL;

	/**
	 * Object this is a comment for
	 * 
	 * @var integer int(11) 
	 */
	var $item_id      = NULL;

	/**
	 * Object type (resource, kb, etc)
	 * 
	 * @var string varchar(100)
	 */
	var $item_type    = NULL;

	/**
	 * Comment
	 * 
	 * @var string text
	 */
	var $content       = NULL;

	/**
	 * When the entry was created
	 * 
	 * @var string datetime (0000-00-00 00:00:00)
	 */
	var $created       = NULL;

	/**
	 * Who created this entry
	 * 
	 * @var integer int(11)
	 */
	var $created_by    = NULL;

	/**
	 * When the entry was modifed
	 * 
	 * @var string datetime (0000-00-00 00:00:00)
	 */
	var $modified      = NULL;

	/**
	 * Who modified this entry
	 * 
	 * @var integer int(11)
	 */
	var $modified_by   = NULL;

	/**
	 * Display comment as anonymous
	 * 
	 * @var integer tinyint(3)
	 */
	var $anonymous     = NULL;

	/**
	 * Parent comment
	 * 
	 * @var integer int(11) 
	 */
	var $parent        = NULL;

	/**
	 * Notify the user of replies
	 * 
	 * @var integer tinyint(2)
	 */
	var $notify        = NULL;

	/**
	 * Access level (0=public, 1=registered, 2=special, 3=protected, 4=private)
	 * 
	 * @var integer tinyint(2)
	 */
	var $access        = NULL;

	/**
	 * Pushed state (0=unpublished, 1=published, 2=trashed)
	 * 
	 * @var integer int(2)
	 */
	var $state         = NULL;

	/**
	 * Positive votes (people liked this comment)
	 * 
	 * @var integer int(11) 
	 */
	var $positive      = NULL;

	/**
	 * Negative votes (people disliked this comment)
	 * 
	 * @var integer int(11) 
	 */
	var $negative      = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_comments', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check() 
	{
		$this->content = trim($this->content);
		if (!$this->content || $this->content == JText::_('Enter your comments...')) 
		{
			$this->setError(JText::_('Please provide a comment'));
			return false;
		}

		$this->item_id = intval($this->item_id);
		if (!$this->item_id) 
		{
			$this->setError(JText::_('Missing entry ID.'));
			return false;
		}

		$this->item_type = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($this->item_type)));
		if (!$this->item_type) 
		{
			$this->setError(JText::_('Missing entry type.'));
			return false;
		}

		if (!$this->created_by) 
		{
			$juser = JFactory::getUser();
			$this->created_by = $juser->get('id');
		}

		if (!$this->id)
		{
			$this->created = date('Y-m-d H:i:s', time());
		}
		else 
		{
			$juser = JFactory::getUser();
			$this->modified_by = $juser->get('id');
			$this->modified = date('Y-m-d H:i:s', time());
		}

		return true;
	}

	/**
	 * Get all the comments on an entry
	 * 
	 * @param      string  $item_type Type of entry these comments are attached to
	 * @param      integer $item_id   ID of entry these comments are attached to
	 * @param      integer $parent     ID of parent comment
	 * @return     mixed False if error otherwise array of records
	 */
	public function getComments($item_type=NULL, $item_id=0, $parent=0, $limit=25, $start=0)
	{
		if (!$item_type) 
		{
			$item_type = $this->item_type;
		}
		if (!$item_id) 
		{
			$item_id = $this->item_id;
		}
		if (!$parent) 
		{
			$parent = 0;
		}

		if (!$item_type || !$item_id) 
		{
			$this->setError(JText::_('Missing parameter(s). item_type:' . $item_type . ', item_id:' . $item_id));
			return false;
		}

		$juser =& JFactory::getUser();

		if (!$juser->get('guest')) 
		{
			$sql  = "SELECT c.*, u.name, v.vote, (c.positive - c.negative) AS votes FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__users AS u ON u.id=c.created_by ";
			$sql .= "LEFT JOIN #__item_votes AS v ON v.item_id=c.id AND v.created_by=" . $juser->get('id') . " AND v.item_type='comment' ";
		} 
		else 
		{
			$sql  = "SELECT c.*, u.name, NULL as vote, (c.positive - c.negative) AS votes FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__users AS u ON u.id=c.created_by ";
		}
		$sql .= "WHERE c.item_type='$item_type' AND c.item_id=$item_id AND c.parent=$parent AND c.state IN (1, 3) ORDER BY created ASC LIMIT $start,$limit";

		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();
		if ($rows && count($rows) > 0)
		{
			foreach ($rows as $k => $row)
			{
				$rows[$k]->replies = $this->getComments($item_type, $item_id, $row->id, $limit, $start);
			}
		}
		return $rows;
	}

	/**
	 * Delete a comment and any chldren
	 *
	 * @param      integer $id     ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}

		if (!$this->deleteDescendants($oid, 2))
		{
			return false;
		}

		return parent::delete($oid);
	}

	/**
	 * Delete descendants of a comment
	 *
	 * @param      integer $id     ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function deleteDescendants($id=NULL)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else 
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadResultArray();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$ids = implode(',', $rows);

			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE id IN ($ids)");
			if (!$this->_db->query()) 
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->deleteDescendants($rows, $state);
		}
		return true;
	}

	/**
	 * Set the state of a comment and all descendants
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setState($oid=null, $state=0)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$oid = intval($oid);

		if (!$this->setDescendantState($oid, $state))
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE id=$oid");
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Set the state of descendants of a comment
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setDescendantState($id=NULL, $state=0)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else 
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadResultArray();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$id = implode(',', $rows);

			$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE parent IN ($id)");
			if (!$this->_db->query()) 
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->setDescendantState($rows, $state);
		}
		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= " LEFT JOIN #__groups AS a ON c.access=a.id";
		}
		else 
		{
			$query .= " LEFT JOIN #__viewlevels AS a ON c.access=a.id";
		}

		$where = array();

		if (isset($filters['state'])) 
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}

		if (isset($filters['item_type']) && $filters['item_type'] >= 0) 
		{
			$where[] = "c.item_type=" . $this->_db->Quote($filters['item_type']);
		}

		if (isset($filters['item_id']) && $filters['item_id'] >= 0) 
		{
			$where[] = "c.item_id=" . $this->_db->Quote($filters['item_id']);
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "LOWER(c.content) LIKE '%" . strtolower($filters['search']) . "%'";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to build query off of
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an array of records
	 * 
	 * @param      array $filters Filters to build query off of
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= ", a.name AS access_level";
		}
		else 
		{
			$query .= ", a.title AS access_level";
		}
		$query .= " " . $this->buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
