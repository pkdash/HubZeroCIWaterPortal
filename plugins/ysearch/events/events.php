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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Search events
 */
class plgYSearchEvents extends YSearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(e.title, e.content) against(\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(e.title LIKE '%$mand%' OR e.content LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(e.title NOT LIKE '%$forb%' AND e.content NOT LIKE '%$forb%')";
		}

		$user =& JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$addtl_where[] = '(e.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . '))';
		}
		else 
		{
			if ($user->guest)
			{
				$addtl_where[] = '(e.access = 0)';
			}
			elseif ($user->usertype != 'Super Administrator')
			{
				$addtl_where[] = '(e.access = 0 OR e.access = 1)';
			}
		}

		$rows = new YSearchResultSQL(
			"SELECT 
				e.title,
				e.content AS description,
				concat('/events/details/', e.id) AS link,
				$weight AS weight,
				publish_up AS date,
				'Events' AS section
			FROM jos_events e
			WHERE 
				state = 1 AND 
				approved AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) 
			{
				continue;
			}
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}

	/**
	 * Short description for 'onBeforeYSearchRenderEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $res Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
 	public static function onBeforeYSearchRenderEvents($res)
	{
		$date = $res->get('date');
		return
			'<p class="event-date">
			<span class="month">' . date('M', $date) . '</span>
			<span class="day">' . date('d', $date) . '</span>
			<span class="year">' . date('Y', $date) . '</span>
			</p>';
	}
}

