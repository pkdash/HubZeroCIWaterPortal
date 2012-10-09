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

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for supporting documentss
 */
class plgResourcesSupportingDocs extends JPlugin
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
	public function &onResourcesAreas($resource, $archive = 0)
	{
		if ($archive) 
		{
			$areas = array();
		} 
		else if ($resource->_type->_params->get('plg_supportingdocs')) 
		{
			$areas = array(
				'supportingdocs' => JText::_('PLG_RESOURCES_SUPPORTINGDOCS')
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
			'area' => 'supportingdocs',
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($resource))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($resource)))) 
			{
				// do nothing
				return;
			}
		}

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('resources', 'supportingdocs');

		$database =& JFactory::getDBO();

		// Initiate a resource helper class
		$helper = new ResourcesHelper($resource->id, $database);
		$helper->getChildren($resource->id, 0, 'all', 0);

		$config =& JComponentHelper::getParams($option);

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => 'supportingdocs',
				'name'    => 'browse'
			)
		);
		
		$jconfig =& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		
		// Pass the view some info
		$view->option    = $option;
		$view->resource  = $resource;
		$view->helper    = $helper;
		$view->config    = $config;
		$view->live_site = $live_site;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}

