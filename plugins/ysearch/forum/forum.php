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
 * Search forum entries
 */
class plgYSearchForum extends YSearchPlugin
{
	/**
	 * Get the name of the area being searched
	 * 
	 * @return     string
	 */
	public static function getName() 
	{ 
		return JText::_('Forum'); 
	}

	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = "match(f.title, f.comment) against ('" . join(' ', $terms['stemmed']) . "')";

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.comment LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.comment NOT LIKE '%$forb%')";
		}
		
		$juser =& JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$gids = $authz->get_group_ids();
			if (!$juser->authorise('core.view', 'com_groups'))
			{
				$addtl_where[] = 'f.group_id IN (0' . ($gids ? ',' . join(',', $gids) : '') . ')';
			}
			else 
			{
				$viewlevels = implode(',', $juser->getAuthorisedViewLevels());

				if ($gids)
				{
					$addtl_where[] = '(f.access IN (' . $viewlevels . ') OR ((f.access = 4 OR f.access = 5) AND f.group_id IN (0,' . join(',', $gids) . ')))';
				}
				else 
				{
					$addtl_where[] = '(f.access IN (' . $viewlevels . '))';
				}
			}
		}
		else 
		{
			if ($juser->get('guest'))
			{
				$addtl_where[] = '(f.access = 0)';
			}
			elseif ($juser->get('usertype') != 'Super Administrator')
			{
				$groups = array_map('intval', $authz->get_group_ids());
				if ($groups)
				{
					$addtl_where[] = '(f.access = 0 OR f.access = 1 OR ((f.access = 3 OR f.access = 4) AND f.group_id IN (0,' . join(',', $groups) . ')))';
				}
				else
				{
					$addtl_where[] = '(f.access = 0 OR f.access = 1)';
				}
			}
		}

		$rows = new YSearchResultSQL(
			"SELECT 
				f.title,
				coalesce(f.comment, '') AS description, f.group_id, s.alias as sect, c.alias as cat, CASE WHEN f.parent > 0 THEN f.parent ELSE f.id END as thread,
				(CASE WHEN f.group_id > 0 THEN
					concat('/groups/', g.cn, '/forum/')
				ELSE
					concat('/forum/', coalesce(concat(s.alias, '/', coalesce(concat(c.alias, '/'), ''))), CASE WHEN f.parent > 0 THEN f.parent ELSE f.id END)
				END) AS link,
				$weight AS weight,
				f.created AS date,
				concat(s.alias, ', ', c.alias) AS section
			FROM jos_forum_posts f
			LEFT JOIN jos_forum_categories c 
				ON c.id = f.category_id
			LEFT JOIN jos_forum_sections s
				ON s.id = c.section_id
			LEFT JOIN jos_xgroups g
				ON g.gidNumber = f.group_id
			WHERE 
				f.state = 1 AND 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) 
			{
				continue;
			}
			if ($row->group_id)
			{
				$row->link .= ($row->sect ? $row->sect : 'defaultsection') . '/';
				$row->link .= ($row->cat ? $row->cat : 'discussion') . '/';
				$row->link .= $row->thread;
			}
			$results->add($row);
		}
	}
}

