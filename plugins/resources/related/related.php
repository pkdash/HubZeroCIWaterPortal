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

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for related resources
 */
class plgResourcesRelated extends JPlugin
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
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'related' => JText::_('PLG_RESOURCES_RELATED')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area' => 'related',
			'html' => '',
			'metadata' => ''
		);

		$database =& JFactory::getDBO();

		// Build the query that checks topic pages
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias, v.pagetext AS introtext, NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.rating, w.times_rated, w.ranking, 'Topic' AS section, w.`group_cn`  
				FROM #__wiki_page AS w, #__wiki_version AS v
				WHERE w.id=v.pageid AND v.approved=1 AND (v.pagetext LIKE '%[[Resource(".$resource->id . ")]]%' OR v.pagetext LIKE '%[[Resource(" . $resource->id . ",%' OR v.pagetext LIKE '%[/resources/" . $resource->id . " %'";
		$sql1 .= ($resource->alias) ? " OR v.pagetext LIKE '%[[Resource(" . $resource->alias . "%') " : ") ";
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			if ($juser->authorize('com_resources', 'manage') 
			 || $juser->authorize('com_groups', 'manage')) 
			{
				$sql1 .= '';
			} 
			else 
			{
				ximport('Hubzero_User_Helper');

				$ugs = Hubzero_User_Helper::getGroups($juser->get('id'), 'members');
				$groups = array();
				if ($ugs && count($ugs) > 0) 
				{
					foreach ($ugs as $ug)
					{
						$groups[] = $ug->cn;
					}
				}
				$g = "'" . implode("','", $groups) . "'";

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group_cn IN ($g) OR w.created_by='" . $juser->get('id') . "'))) ";
			}
		} 
		else 
		{
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, NULL AS pageid, NULL AS version, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up, "
			 . "\n NULL AS scope, r.rating, r.times_rated, r.ranking, rt.type AS section, NULL AS `group` "
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.parent_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.child_id=" . $resource->id . " AND r.type=rt.id AND r.type!=8 ";
		if (!$juser->get('guest')) 
		{
			if ($juser->authorize('com_resources', 'manage') 
			 || $juser->authorize('com_groups', 'manage')) 
			{
				$sql2 .= '';
			} 
			else 
			{
				$sql2 .= "AND (r.access!=1 OR (r.access=1 AND (r.group_owner IN ($g) OR r.created_by='" . $juser->get('id') . "'))) ";
			}
		} 
		else 
		{
			$sql2 .= "AND r.access=0 ";
		}
		//echo '<!-- '.$sql2.' -->';
		$sql2 .= "ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery($query);
		$related = $database->loadObjectList();

		ximport('Hubzero_View_Helper_Html');

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		if ($miniview) 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'related',
					'name'    => 'browse',
					'layout'  => 'mini'
				)
			);
		} 
		else 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'related',
					'name'    => 'browse'
				)
			);
		}

		// Pass the view some info
		$view->option   = $option;
		$view->resource = $resource;
		$view->related  = $related;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}

