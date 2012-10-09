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
defined('_JEXEC') or die('Restricted access');

/**
 * Time - hubs database class
 */
Class TimeHubs extends JTable
{
	/**
	 * id, primary key
	 * 
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * hub name
	 * 
	 * @var varchar(255)
	 */
	var $name = null;

	/**
	 * normalized hub name
	 * 
	 * @var varchar(255)
	 */
	var $name_normalized = null;

	/**
	 * hub liaison
	 * 
	 * @var varchar(255)
	 */
	var $liaison = null;

	/**
	 * anniversary date
	 * 
	 * @var date
	 */
	var $anniversary_date = null;

	/**
	 * support level
	 * 
	 * @var varchar(255)
	 */
	var $support_level = null;

	/**
	 * active
	 * 
	 * @var int(1)
	 */
	var $active = null;

	/**
	 * notes
	 * 
	 * @var blob
	 */
	var $notes = null;

	/**
	 * Constructor
	 * 
	 * @param   database object
	 * @return  void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_hubs', 'id', $db );
	}

	/**
	 * Override check function to perform validation
	 * 
	 * @return boolean true if all checks pass, else false
	 */
	public function check()
	{
		// Trim whitespace from variables
		$this->name    = trim($this->name);
		$this->liaison = trim($this->liaison);

		// If name or liaison is empty, return an error
		if(empty($this->name) || empty($this->liaison))
		{
			if(empty($this->name))
			{
				$this->setError(JText::_('PLG_TIME_HUBS_NO_NAME'));
				return false;
			}
			if(empty($this->liaison))
			{
				$this->setError(JText::_('PLG_TIME_HUBS_NO_LIAISON'));
				return false;
			}
		}

		// Create the normalized version of the hub name
		$this->name_normalized = strtolower(str_replace(" ", "", $this->name));

		// Everything passed, return true
		return true;
	}

	/**
	 * Build query
	 * 
	 * @param  $filters (not needed yet...)
	 * @return $query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS h";

		return $query;
	}

	/**
	 * Get count of hubs, mainly used for pagination
	 * 
	 * @return query result number of hubs
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(h.id)";
		$query .= $this->buildquery();

		// If we only want active hubs
		if(!empty($filters['active']))
		{
			$query .= " WHERE h.active = ".$filters['active'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get list of hubs
	 * 
	 * @param  $filters (examples: active, orderby, orderdir, start, limit)
	 * @return object list of hubs
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT h.*";
		$query .= $this->buildquery($filters);

		// Only active hubs
		if (!empty($filters['active']))
		{
			$query .= " WHERE h.active = 1";
		}

		// If orderby and orderdir are set, use them
		if(!empty($filters['orderby']) && !empty($filters['orderdir']))
		{
			$query .= " ORDER BY ".$filters['orderby']." ".$filters['orderdir'];
		}
		// If orderby and orderdir are not set, use some defaults
		else
		{
			$query .= " ORDER BY name ASC";
		}
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get hubname by report id
	 * 
	 * @param  $id of current report
	 * @return query result hubname
	 */
	public function getHubNameByReportId($id)
	{
		$query  = "SELECT DISTINCT(h.name)";
		$query .= " FROM $this->_tbl AS h";
		$query .= " LEFT JOIN #__time_tasks as p ON p.hub_id = h.id";
		$query .= " LEFT JOIN #__time_records as rec ON rec.task_id = p.id";
		$query .= " LEFT JOIN #__time_reports_records_assoc as assoc ON assoc.record_id = rec.id";
		$query .= " LEFT JOIN #__time_reports as rep ON rep.id = assoc.report_id";

		$query .= " WHERE rep.id = ".$id;

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}