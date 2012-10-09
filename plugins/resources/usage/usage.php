<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
	
/**
 * Resources Plugin class for usage
 */
class plgResourcesUsage extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($resource) 
	{
		if ($resource->_type->_params->get('plg_usage')) 
		{
			$areas = array(
				'usage' => JText::_('PLG_RESOURCES_USAGE')
			);
		} 
		else 
		{
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($resource, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area' => 'usage',
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($resource)) 
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($resource)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$resource->_type->_params->get('plg_usage')) 
		{
			return $arr;
		}

		// Display only for tools
		if ($resource->type != 7) 
		{
			//return $arr;
			$rtrn == 'metadata';
		}

		// Check if we have a needed database table
		$database =& JFactory::getDBO();

		$tables = $database->getTableList();
		$table = $database->_table_prefix . 'resource_stats_tools';

		if ($resource->alias) 
		{
			$url = JRoute::_('index.php?option=' . $option . '&alias=' . $resource->alias . '&active=usage');
		} 
		else 
		{
			$url = JRoute::_('index.php?option=' . $option . '&id=' . $resource->id . '&active=usage');
		}

		if (!in_array($table, $tables)) 
		{
			$arr['html'] = '<p class="error">'. JText::_('PLG_RESOURCES_USAGE_MISSING_TABLE') . '</p>';
			$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . JText::_('PLG_RESOURCES_USAGE_DETAILED') . '</a></p>';
			return $arr;
		}

		// Get/set some variables
		$dthis = JRequest::getVar('dthis', date('Y') . '-' . date('m'));
		$period = JRequest::getInt('period', $this->params->get('period', 14));

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'stats.php');
		if ($resource->type == 7) 
		{
			$stats = new ResourcesStatsTools($database);
		} 
		else 
		{
			$stats = new ResourcesStats($database);
		}
		$stats->loadStats($resource->id, $period, $dthis);

		$clusters = new ResourcesStatsClusters($database);
		$clusters->loadStats($resource->id);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			$action = JRequest::getVar('action', '');
			if ($action == 'top')
			{
				$this->getTopValues($resource->id, JRequest::getVar('datetime', '0000-00-00 00:00:00'));
				return;
			}
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');
	
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'usage',
					'name'    => 'browse'
				)
			);

			// Pass the view some info
			$view->option     = $option;
			$view->resource   = $resource;
			$view->stats      = $stats;
			$view->chart_path = $this->params->get('chart_path','');
			$view->map_path   = $this->params->get('map_path','');
			$view->dthis      = $dthis;
			$view->period     = $period;
			$view->params     = $this->params;
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			if ($resource->type == 7) 
			{
				$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . JText::sprintf('PLG_RESOURCES_USAGE_NUM_USERS', $stats->users) . '</a></p>';
			} 
			else 
			{
				if (!$stats->users)
				{
					$stats->users = 0;
				}
				$arr['metadata'] = '<p class="usage">' . JText::sprintf('%s users', $stats->users) . '</p>';
			}
			if ($clusters->users && $clusters->classes) 
			{
				$arr['metadata'] .= '<p class="usage">' . JText::sprintf('%s users', $clusters->users) . ' in ' . JText::sprintf('%s class(es)', $clusters->classes) . '</p>';
			}
		}

		return $arr;
	}

	/**
	 * Round time into nearest second/minutes/hours/days
	 * 
	 * @param      integer $time Time
	 * @return     string
	 */
	public function timeUnits($time) 
	{
		if ($time < 60) 
		{
			$data = round($time, 2) . ' ' . JText::_('PLG_RESOURCES_USAGE_SECONDS');
		} 
		else if ($time > 60 && $time < 3600) 
		{
			$data = round(($time/60), 2) . ' ' . JText::_('PLG_RESOURCES_USAGE_MINUTES');
		} 
		else if ($time >= 3600 && $time < 86400) 
		{
			$data = round(($time/3600), 2) . ' ' . JText::_('PLG_RESOURCES_USAGE_HOURS');
		} 
		else if ($time >= 86400) 
		{
			$data = round(($time/86400), 2) . ' ' . JText::_('PLG_RESOURCES_USAGE_DAYS');
		}

		return $data;
	}

	/**
	 * Get overview data
	 * 
	 * @param      integer $id  Resource ID
	 * @return     array
	 */
	public static function getOverview($id)
	{
		$database =& JFactory::getDBO();

		$sql = "SELECT * 
				FROM #__resource_stats_tools 
				WHERE resid = '$id' 
				AND period = '1'
				ORDER BY `datetime` ASC";
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	/**
	 * Check for data for a given time period
	 * 
	 * @param      integer $id  Resource ID
	 * @param      integer $top Value type (1 = country, 2 = domain, 3 = org)
	 * @param      integer $tid Stats ID for that tool
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public static function getTopValue($id, $top, $tid, $datetime)
	{
		$database =& JFactory::getDBO();

		if (!$id || !$tid)
		{
			return array();
		}

		$sql = "SELECT v.*, t.`datetime` 
				FROM #__resource_stats_tools AS t
				LEFT JOIN #__resource_stats_tools_topvals AS v ON v.id=t.id
				WHERE t.resid = '$id' 
				AND t.period = '1'
				AND t.datetime = '" . $datetime . "-00 00:00:00'
				AND t.id = $tid
				AND v.top = '3'
				ORDER BY v.id, v.rank";
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	/**
	 * Get the stats ID for a specific resource
	 * Getting this now allows for faster data pulling later on
	 * 
	 * @param      integer $id       Resource ID
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public static function getTid($id, $datetime)
	{
		$database =& JFactory::getDBO();

		$sql = "SELECT t.id FROM #__resource_stats_tools AS t WHERE t.resid = '$id' AND t.period = '1' AND t.datetime = '" . $datetime . "-00 00:00:00' ORDER BY t.id LIMIT 1";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	/**
	 * Get data for orgs, countries, domains for a given time period
	 * (1 = country, 2 = domain, 3 = org)
	 * 
	 * @param      integer $id       Resource ID
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public function getTopValues($id, $datetime)
	{
		$colors = array(
			$this->params->get('pie_chart_color1', '#7c7c7c'),
			$this->params->get('pie_chart_color2', '#515151'),
			$this->params->get('pie_chart_color3', '#d9d9d9'),
			$this->params->get('pie_chart_color4', '#3d3d3d'),
			$this->params->get('pie_chart_color5', '#797979'),
			$this->params->get('pie_chart_color6', '#595959'),
			$this->params->get('pie_chart_color7', '#e5e5e5'),
			$this->params->get('pie_chart_color8', '#828282'),
			$this->params->get('pie_chart_color9', '#404040'),
			$this->params->get('pie_chart_color10', '#6a6a6a'),
			$this->params->get('pie_chart_color1', '#bcbcbc'),
			$this->params->get('pie_chart_color2', '#515151'),
			$this->params->get('pie_chart_color3', '#d9d9d9'),
			$this->params->get('pie_chart_color4', '#3d3d3d'),
			$this->params->get('pie_chart_color5', '#797979'),
			$this->params->get('pie_chart_color6', '#595959'),
			$this->params->get('pie_chart_color7', '#e5e5e5'),
			$this->params->get('pie_chart_color8', '#828282'),
			$this->params->get('pie_chart_color9', '#404040'),
			$this->params->get('pie_chart_color10', '#3a3a3a'),
		);

		$json = new stdClass;

		$database =& JFactory::getDBO();

		$tid = $this->getTid($id, $datetime);

		$orgs = $this->getTopValue($id, 3, $tid, $datetime);
		if ($orgs)
		{
			$i = 0;
			$r = array();
			foreach ($orgs as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) number_format($row->value);
				$obj->color = $colors[$i];

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->orgs = $r;

		$countries = $this->getTopValue($id, 1, $tid, $datetime);
		if ($countries)
		{
			$i = 0;
			$r = array();
			foreach ($countries as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) number_format($row->value);
				$obj->color = $colors[$i];

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->countries = $r;

		$domains = $this->getTopValue($id, 2, $tid, $datetime);
		if ($domains)
		{
			$i = 0;
			$r = array();
			foreach ($domains as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) number_format($row->value);
				$obj->color = $colors[$i];

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->domains = $r;

		ob_clean();

		echo json_encode($json);
		die();
	}
}

