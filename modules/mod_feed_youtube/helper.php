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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Module class for displaying a YouTube feed
 */
class modFeedYoutubeHelper extends JObject
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
	 * @param      object $this->params JParameter
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
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		// module params
		$limit = (int) $this->params->get('rssitems', 5);

		//  get RSS parsed object
		$options = array(
			'rssUrl'     => $this->params->get('rssurl', ''),
			'cache_time' => null
		);

		if (!$options['rssUrl'])
		{
			echo '<p class="warning">' . JText::_('No feed URL specified.') . '</p>';
			return;
		}

		$rssDoc =& JFactory::getXMLparser('RSS', $options);

		$this->feed = new stdclass();

		if ($rssDoc != false)
		{
			// channel header and link
			$this->this->feed->title        = $rssDoc->get_title();
			$this->this->feed->link         = $rssDoc->get_link();
			$this->this->feed->description  = $rssDoc->get_description();

			// channel image if exists
			$this->feed->image->url   = $rssDoc->get_image_url();
			$this->feed->image->title = $rssDoc->get_image_title();

			// items
			$items = $rssDoc->get_items();

			// feed elements
			if ($this->params->get('pick_random', 0)) 
			{
				// randomize items
				shuffle($items);
			}
			$this->feed->items = array_slice($items, 0, $limit);
		} 
		else 
		{
			$this->feed = false;
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
