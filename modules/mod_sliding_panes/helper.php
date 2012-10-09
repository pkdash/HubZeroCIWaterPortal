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
 * Module class for displaying sliding panes of content
 */
class modSlidingPanes extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Get a list of content articles
	 * 
	 * @return     array
	 */
	private function _getList()
	{
		$db =& JFactory::getDBO();

		$catid 	 = (int) $this->params->get('catid', 0);
		$random  = $this->params->get('random', 0);
		$orderby = $random ? 'RAND()' : 'a.ordering';
		$limit   = (int) $this->params->get('limitslides', 0);
		$limitby = $limit ? ' LIMIT 0,' . $limit : '';

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			// query to determine article count
			$query = 'SELECT a.*,' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
				' FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' WHERE a.state = 1 ' .
				//($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
				' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
				' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
				' AND cc.id = '. (int) $catid .
				' AND cc.section = s.id' .
				' AND cc.published = 1' .
				' AND s.published = 1' .
				' ORDER BY ' . $orderby . ' ' . $limitby;
		}
		else 
		{
			// query to determine article count
			$query = 'SELECT a.* FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' WHERE a.state = 1 ' .
				' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
				' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
				' AND cc.id = ' . (int) $catid .
				' AND cc.published = 1' .
				' ORDER BY ' . $orderby . ' ' . $limitby;
		}
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		$jdocument =& JFactory::getDocument();

		$type = $this->params->get('animation', 'slide');

		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if (!$this->multiple_instances) 
		{
			// Push some CSS to the template
			ximport('Hubzero_Document');
			Hubzero_Document::addModuleStylesheet($this->module->module, $type . '.css');
			Hubzero_Document::addModuleScript($this->module->module);
		}

		$id = rand();

		$this->content = $this->_getList();

		$this->container = $this->params->get('container', 'pane-sliders');

		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$js = "jQuery(document).ready(function($){ $('#" . $this->container . " .panes-content').jSlidingPanes(); });";
		}
		else 
		{
			$js = "window.addEvent('domready', function(){
				if ($('" . $this->container . "')) {
					myTabs" . $id . " = new ModSlidingPanes('" . $this->container . "', " . $this->params->get('rotate', 1) . ");

					// this sets it up to work even if it's width isn't a set amount of pixels
					window.addEvent('resize', myTabs" . $id . ".recalcWidths.bind(myTabs" . $id . "));
				}
			});";
		}

		$jdocument->addScriptDeclaration($js);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}