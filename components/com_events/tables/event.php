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
 * Events table class for an event
 */
class EventsEvent extends JTable
{
	/**
	 * int(12) Primary key
	 * 
	 * @var integer
	 */
	var $id               = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $sid              = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $catid            = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $title            = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content          = NULL;

	/**
	 * varchar(120)
	 * 
	 * @var string
	 */
	var $contact_info     = NULL;

	/**
	 * varchar(120)
	 * 
	 * @var string
	 */
	var $adresse_info     = NULL;

	/**
	 * varchar(240)
	 * 
	 * @var string
	 */
	var $extra_info       = NULL;

	/**
	 * varchar(8)
	 * 
	 * @var string
	 */
	var $color_bar        = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $useCatColor      = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $state            = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $mask             = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created          = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by       = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $created_by_alias = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modified         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $checked_out      = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $checked_out_time = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_up       = NULL;

	/**
	 * varchar(5)
	 * 
	 * @var string
	 */
	var $time_zone        = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_down     = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $images           = NULL;

	/**
	 * varchar(1)
	 * 
	 * @var integer
	 */
	var $reccurtype       = NULL;

	/**
	 * varchar(4)
	 * 
	 * @var string
	 */
	var $reccurday        = NULL;

	/**
	 * varchar(20)
	 * 
	 * @var string
	 */
	var $reccurweekdays   = NULL;

	/**
	 * varchar(10)
	 * 
	 * @var string
	 */
	var $reccurweeks      = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $approved         = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $announcement     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering         = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $archived         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $access           = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $hits             = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $registerby       = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params           = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $restricted       = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $email            = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '') 
		{
			$this->setError(JText::_('EVENTS_MUST_HAVE_TITLE'));
			return false;
		}
		if (trim($this->catid) == '' || trim($this->catid) == 0) 
		{
			$this->setError(JText::_('EVENTS_MUST_HAVE_CATEGORY'));
			return false;
		}
		return true;
	}

	/**
	 * Increase event hit count
	 * 
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function hit($oid=NULL)
	{
		$k = $this->_tbl_key;
		if ($oid !== NULL) 
		{
			$this->$k = intval($oid);
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET hits=(hits+1) WHERE id=$this->id");
		$this->_db->query();
	}

	/**
	 * Set an event to published
	 * 
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function publish($oid=NULL)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=1 WHERE id=$oid");
		$this->_db->query();
	}

	/**
	 * Set an event to unpublished
	 * 
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function unpublish($oid=NULL)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=0 WHERE id=$oid");
		$this->_db->query();
	}

	/**
	 * Get the newest event date
	 * 
	 * @return     string
	 */
	public function getFirst()
	{
		$this->_db->setQuery("SELECT publish_up FROM $this->_tbl ORDER BY publish_up ASC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the oldest event date
	 * 
	 * @return     string
	 */
	public function getLast()
	{
		$this->_db->setQuery("SELECT publish_down FROM $this->_tbl ORDER BY publish_down DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get events for a date range
	 * 
	 * @param      string $period  Date range [month, year, week, day]
	 * @param      array  $filters Extra filters to apply to query
	 * @return     array
	 */
	public function getEvents($period='month', $filters=array())
	{
		$gid = (isset($filters['gid'])) ? $filters['gid'] : 0;

		// Build the query
		switch ($period)
		{
			case 'month':
				$select_date = $filters['select_date'];
				$select_date_fin = $filters['select_date_fin'];

				$sql = "SELECT $this->_tbl.* 
						FROM #__categories AS b, $this->_tbl
						WHERE $this->_tbl.catid = b.id 
						AND b.access <= $gid 
						AND $this->_tbl.access <= $gid 
						AND (((publish_up >= '$select_date%' AND publish_up <= '$select_date_fin%') 
							OR (publish_down >= '$select_date%' AND publish_down <= '$select_date_fin%') 
							OR (publish_up >= '$select_date%' AND publish_down <= '$select_date_fin%') 
							OR (publish_up <= '$select_date%' AND publish_down >= '$select_date_fin%')) 
							AND $this->_tbl.state = '1'";
				$sql .= ($filters['category'] != 0) ? " AND b.id=" . $filters['category'] : "";
				$sql .= ") ORDER BY publish_up ASC";
			break;

			case 'year':
				$year = $filters['year'];

				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl
						WHERE $this->_tbl.catid = b.id AND b.access <= $gid AND $this->_tbl.access <= $gid
						AND publish_up LIKE '$year%' AND (publish_down >= '$year%' OR publish_down = '0000-00-00 00:00:00')
						AND $this->_tbl.state = '1'";
				$sql .= ($filters['category'] != 0) ? " AND b.id=" . $filters['category'] : "";
				$sql .= " ORDER BY publish_up ASC";
				//$sql .= " LIMIT ".$filters['start'].", ".$filters['limit'];
			break;

			case 'week':
				$startdate = $filters['startdate'];
				$enddate = $filters['enddate'];

				$sql = "SELECT * FROM $this->_tbl 
					WHERE ((publish_up >= '$startdate%' AND publish_up <= '$enddate%') 
					OR (publish_down >= '$startdate%' AND publish_down <= '$enddate%') 
					OR (publish_up >= '$startdate%' AND publish_down <= '$enddate%') 
					OR (publish_down >= '$enddate%' AND publish_up <= '$startdate%')) 
					AND state = '1' ORDER BY publish_up ASC";
			break;

			case 'day':
				$select_date = $filters['select_date'];

				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl 
						WHERE $this->_tbl.catid = b.id AND b.access <= $gid AND $this->_tbl.access <= $gid AND 
							((publish_up >= '$select_date 00:00:00' AND publish_up <= '$select_date 23:59:59') 
							OR (publish_down >= '$select_date 00:00:00' AND publish_down <= '$select_date 23:59:59') 
							OR (publish_up <= '$select_date 00:00:00' AND publish_down >= '$select_date 23:59:59') 
							OR (publish_up >= '$select_date 00:00:00' AND publish_down <= '$select_date 23:59:59')";
				$sql .= ($filters['category'] != 0) ? " AND b.id=" . $filters['category'] : "";
				$sql .= ") AND $this->_tbl.state = '1' ORDER BY publish_up ASC";
			break;
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT count(*) FROM $this->_tbl AS a";
		$where = array();
		if ($filters['catid'] > 0) 
		{
			$where[] = "a.catid='" . $filters['catid'] . "'";
		}
		if ($filters['search']) 
		{
			$where[] = "LOWER(a.title) LIKE '%".$filters['search']."%'";
		}
		$query .= (count($where)) ? " WHERE " . implode(' AND ', $where) : "";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT a.*, cc.name AS category, u.name AS editor, g.name AS groupname 
				FROM $this->_tbl AS a 
				LEFT JOIN #__users AS u ON u.id = a.checked_out 
				LEFT JOIN #__groups AS g ON g.id = a.access, 
				#__categories AS cc";

		$where = array();
		if ($filters['catid'] > 0) 
		{
			$where[] = "a.catid='" . $filters['catid'] . "'";
		}
		if ($filters['search']) 
		{
			$where[] = "LOWER(a.title) LIKE '%" . $filters['search'] . "%'";
		}
		$where[] = "a.catid=cc.id";

		$query .= (count($where)) ? " WHERE " . implode(' AND ', $where) : "";
		$query .= " ORDER BY a.publish_up DESC LIMIT " . $filters['start'] . "," . $filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

