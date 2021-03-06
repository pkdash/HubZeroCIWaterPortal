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
 * Wiki macro class for listing files
 */
class FileIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 * 
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(JURI::base(), '/');

		// What pages are we getting?
		if ($et) 
		{
			$et = strip_tags($et);
			// Get pages with a prefix
			$sql  = "SELECT * FROM #__wiki_attachments WHERE LOWER(filename) LIKE '" . strtolower($et) . "%' AND pageid='" . $this->pageid . "' ORDER BY filename ASC";
		} 
		else 
		{
			// Get all pages
			$sql  = "SELECT * FROM #__wiki_attachments WHERE pageid='" . $this->pageid . "' ORDER BY filename ASC";
		}

		// Perform query
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		// Did we get a result from the database?
		if ($rows) 
		{
			$config = JComponentHelper::getParams('com_wiki');
			if ($this->filepath != '') 
			{
				$config->set('filepath', $this->filepath);
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$link = $live_site . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->pageid . DS . $row->filename;

				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="' . JRoute::_($link) . '">' . $row->filename;
				$html .= ($row->description) ? '<br /><span>' . stripslashes($row->description) . '</span>' : '';
				$html .= '</a></li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		} 
		else 
		{
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No ' . $et . ' files to display)';
		}
	}
}

