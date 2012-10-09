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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Filters helper class for time plugins
 */
class TimeFilters
{
	/**
	 * Get filters from request
	 * 
	 * @return $filters - array of filters
	 */
	public function getFilters()
	{
		// Get request variables for filters (use values from state if new ones are defined)
		$filters['start']    = (JRequest::getInt('start')) ? JRequest::getInt('start') : JRequest::getInt('limitstart', 0);
		$filters['limit']    = JRequest::getInt('limit', $this->mainframe->getUserState("$this->option.$this->active.limit"));
		$filters['orderby']  = JRequest::getVar('orderby', $this->mainframe->getUserState("$this->option.$this->active.orderby"));
		$filters['orderdir'] = JRequest::getVar('orderdir', $this->mainframe->getUserState("$this->option.$this->active.orderdir"));
		$filters['search']   = JRequest::getVar('search', $this->mainframe->getUserState("$this->option.$this->active.search"));

		// Process query filters
		$q = $this->mainframe->getUserState("$this->option.$this->active.q");
		if(JRequest::getVar('q', NULL))
		{
			$incoming = JRequest::getVar('q', NULL);
			if(!empty($incoming['column']) && !empty($incoming['operator']) && !empty($incoming['value']))
			{
				$q[] = $incoming;
			}
			else
			{
				$this->addPluginMessage(JText::_('Looks like you may have forgotten to select an option'), 'warning');
			}
		}

		// Turn search into array of results, if not already
		if($filters['search'] !== NULL && !is_array($filters['search']))
		{
			// Explode multiple words into array
			$filters['search'] = explode(" ", $filters['search']);
			// Only allow alphabetical characters for search
			$filters['search'] = preg_replace("/[^a-zA-Z]/", "", $filters['search']);
		}

		// Set some defaults for the filters, if not set otherwise
		$filters['limit']  = (empty($filters['limit'])) ? 10 : $filters['limit'];
		$filters['search'] = ($filters['search'] === NULL) ? '' : $filters['search'];
		if(!is_array($q))
		{
			if($this->active == 'records')
			{
				$q[0]['column']   = 'user_id';
				$q[0]['operator'] = 'e';
				$q[0]['value']    = $this->juser->get('id');
			}
			elseif($this->active == 'tasks')
			{
				$q[0]['column']   = 'assignee';
				$q[0]['operator'] = 'e';
				$q[0]['value']    = $this->juser->get('id');
			}
		}

		// Translate operators and augment query filters with human-friendly text
		$filters['q'] = self::filtersMap($q);

		// Set some values in the session
		$this->mainframe->setUserState("$this->option.$this->active.start", $filters['start']);
		$this->mainframe->setUserState("$this->option.$this->active.limit", $filters['limit']);
		$this->mainframe->setUserState("$this->option.$this->active.orderby", $filters['orderby']);
		$this->mainframe->setUserState("$this->option.$this->active.orderdir", $filters['orderdir']);
		$this->mainframe->setUserState("$this->option.$this->active.search", $filters['search']);
		$this->mainframe->setUserState("$this->option.$this->active.q", $filters['q']);

		return $filters;
	}

	/**
	 * Get column names
	 * 
	 * @param  array $table   - table to get column names for
	 * @param  array $exclude - array of columns to exclude
	 * @return $columns       - array of column names
	 */
	public function getColumnNames($table, $exclude=array())
	{
		// Get the column names
		$prefix = JFactory::getApplication()->getCfg('dbprefix');
		$cols = $this->db->getTableFields($prefix.$table);

		$columns = array();

		// Loop through them and make a guess at the human readable equivalent
		$cols = array_keys($cols[$prefix.$table]);
		foreach($cols as $c)
		{
			if(!in_array($c, $exclude))
			{
				$human = str_replace('_', ' ', $c);
				$human = ucwords($human);
				$columns[] = array("raw" => $c, "human" => $human);
			}
		}

		return $columns;
	}

	/**
	 * Highlight search text
	 * 
	 * @param  object $obj    - object list in which to highlight text
	 * @param  array $filters - array of filters (search included)
	 * @return object $obj    - object to return
	 */
	public function highlight($obj, $filters=array())
	{
		// Highlight search words if set
		if(!empty($filters['search']) && is_array($obj))
		{
			foreach($obj as $o)
			{
				foreach($filters['search'] as $arg)
				{
					$o->name = str_ireplace($arg, "<span class=\"highlight\">{$arg}</span>", $o->name);
				}
			}
		}

		return $obj;
	}

	/**
	 * Build the operators html
	 * 
	 * @return string $html - html for operators select box
	 */
	public function buildSelectOperators()
	{
		$html  = '<select name="q[operator]" id="filter-operator">';
		$html .= '<option value="e">equals (&#61;)</option>';
		$html .= '<option value="de">doesn\'t equal (&#8800;)</option>';
		$html .= '<option value="gt">is greater than (&#62;)</option>';
		$html .= '<option value="lt">is less than (&#60;)</option>';
		$html .= '<option value="gte">is greater than or equal to (&#62;&#61;)</option>';
		$html .= '<option value="lte">is less than or equal to (&#60;&#61;)</option>';
		$html .= '<option value="like">is like (LIKE)</option>';
		$html .= '</select>';

		return $html;
	}

	/**
	 * Setup pagination
	 * 
	 * @param  int $total  - total number of records
	 * @param  int $start  - start of query
	 * @param  int $limit  - limit of query
	 * @return $pagination - Joomla list footer (string)
	 */
	public function getPagination($total, $start, $limit)
	{
		// Import pagination
		jimport('joomla.html.pagination');

		// Instantiate pagination
		$pageNav    = new JPagination($total, $start, $limit);
		$pagination = $pageNav->getListFooter();

		return $pagination;
	}

	/**
	 * Augment query filters
	 * 
	 * Here we're basically modifying what we get from the database,
	 * either by changing the display column (ex: 'user_id' => 'User'),
	 * or by changing the display value (ex: '1' to 'yes', or '1000' to Sam Wilson)
	 * 
	 * @param  $q - query arguments
	 * @return void
	 */
	public function filtersMap($q=array())
	{
		// Initialize variables
		$filters   = array();
		$return    = array();
		$dcolumn   = '';
		$doperator = '';
		$dvalue    = '';

		// First, make sure we have something to iterate over
		if(!empty($q[0]))
		{
			// Go through query filters
			foreach($q as $val)
			{
				// Make sure we're not deleting this filter
				if(!array_key_exists('delete', $val))
				{
					// Add an if statement here if you want to augment a fields 'human readable' value
					// Augment user_id information
					if($val['column'] == 'user_id')
					{
						$val['human_column']   = 'User';
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = JFactory::getUser($val['value'])->name;
						$filters[]  = $val;
					}
					// Augment name information for multiple fields
					elseif($val['column'] == 'assignee' || $val['column'] == 'liaison')
					{
						$val['human_column']   = ucwords($val['column']);
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = JFactory::getUser($val['value'])->name;
						$filters[]  = $val;
					}
					// Augment task_id information
					elseif($val['column'] == 'task_id')
					{
						$task = new TimeTasks($this->db);
						$task->load($val['value']);
						$val['human_column']   = 'Task';
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = $task->name;
						$filters[]  = $val;
					}
					// Augment 'active' column information
					elseif($val['column'] == 'active')
					{
						$val['human_column']   = ucwords($val['column']);
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = ($val['value'] == 1) ? 'yes' : 'no';
						$filters[]  = $val;
					}
					// All others
					else
					{
						$val['human_column']   = ucwords(str_replace("_", " ", $val['column']));
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = $val['value'];
						$filters[]  = $val;
					}
				}
				else // we're establishing the details of the query filter to delete (which we'll do below)
				{
					// Values to delete
					$dcolumn   = $val['column'];
					$doperator = $val['operator'];
					$dvalue    = $val['value'];
				}
			}
		}

		// Distil down the results to only unique filters
		$filters = array_map("unserialize", array_unique(array_map("serialize", $filters)));

		// Now go through them again and only keep ones not marked for deletion (there's probably a much better way to do this)
		foreach($filters as $filter)
		{
			if(!($filter['column'] == $dcolumn && $filter['operator'] == $doperator && $filter['value'] == $dvalue))
			{
				$return[] = $filter;
			}
		}

		return $return;
	}

	/**
	 * Override default filter values
	 * 
	 * ex: change hub_id to Hub
	 * 
	 * @param  $vals   - incoming values
	 * @param  $column - incoming column for which values pertain
	 * @return $return - outgoing values
	 */
	public function filtersOverrides($vals, $column)
	{
		$return = array();

		foreach($vals as $val)
		{
			// Just so I don't have to keep writing $val->val
			$value = $val->val;

			$x            = array();
			$x['value']   = $value;
			$x['display'] = $value;

			// Now override at will...
			if($column == 'assignee' || $column == 'liaison')
			{
				$x['value'] = $value;
				$x['display'] = JFactory::getUser($value)->get('name');
			}

			$return[] = $x;
		}

		return $return;
	}

	/**
	 * Translate operators from form value to database operator
	 * 
	 * @param  $o - operator of interest
	 * @return void
	 */
	private function translateOperator($o)
	{
		if($o == 'e')
		{
			return '=';
		}
		elseif($o == 'de')
		{
			return '!=';
		}
		elseif($o == 'gt')
		{
			return '>';
		}
		elseif($o == 'gte')
		{
			return '>=';
		}
		elseif($o == 'lt')
		{
			return '<';
		}
		elseif($o == 'lte')
		{
			return '<=';
		}
		elseif($o == 'like')
		{
			return 'LIKE';
		}
		return $o;
	}

	/**
	 * Map operator symbol to text equivalent (ex: '>' = 'is greater than')
	 * 
	 * @param  $o - operator of interest
	 * @return string - value of operator
	 */
	private function mapOperator($o)
	{
		if($o == '=')
		{
			return 'is';
		}
		elseif($o == '!=')
		{
			return 'is not';
		}
		elseif($o == '>')
		{
			return 'is greater than';
		}
		elseif($o == '>=')
		{
			return 'is greater than or equal to';
		}
		elseif($o == '<')
		{
			return 'is less than';
		}
		elseif($o == '<=')
		{
			return 'is less than or equal to';
		}
		elseif($o == 'LIKE')
		{
			return 'is like';
		}
		return $o;
	}
}