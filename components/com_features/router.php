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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'FeaturesBuildRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function FeaturesBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task'])) 
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['id'])) 
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['format'])) 
	{
		$segments[] = $query['format'];
		unset($query['format']);
	}
	if (!empty($query['controller'])) 
	{
		unset($query['controller']);
	}

	return $segments;
}

/**
 * Short description for 'FeaturesParseRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function FeaturesParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if (isset($segments[0])) 
	{
		$vars['task'] = $segments[0];
	}
	if (isset($segments[1])) 
	{
		$vars['id'] = $segments[1];
	}
	if (isset($segments[2])) 
	{
		$vars['format'] = $segments[2];
	}

	return $vars;
}
