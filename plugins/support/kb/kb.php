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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Plugin for abuse reports on KB comments
 */
class plgSupportKb extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Get items reported as abusive
	 * 
	 * @param      integer $refid    Comment ID
	 * @param      string  $category Item type (kb)
	 * @param      integer $parent   Parent ID
	 * @return     array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if ($category != 'kb') 
		{
			return null;
		}

		$query  = "SELECT rc.id, rc.content as text, rc.created_by as author, NULL as subject, rc.anonymous as anon, 'kb' AS parent_category, 
					s.alias AS section, c.alias AS category, f.alias AS article 
					FROM #__faq_comments AS rc
					LEFT JOIN #__faq AS f 
						ON f.id = rc.entry_id
					LEFT JOIN #__faq_categories AS s 
						ON s.id = f.section
					LEFT JOIN #__faq_categories AS c
						ON c.id = f.category
					WHERE rc.id=" . $refid;

		$database =& JFactory::getDBO();
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows) 
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = JRoute::_('index.php?option=com_kb&section=' . $row->section . '&category=' . $row->category . '&alias=' . $row->article);
			}
		}
		return $rows;
	}
}
