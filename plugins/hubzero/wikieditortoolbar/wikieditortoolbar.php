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
 * HUBzero plugin class for displaying a wiki editor toolbar
 */
class plgHubzeroWikiEditorToolbar extends JPlugin
{
	/**
	 * Flag for if scripts need to be pushed to the document or not
	 * 
	 * @var boolean
	 */
	private $_pushscripts = true;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Initiate the editor. Push scripts to document if needed
	 * 
	 * @return     string
	 */
	public function onInitEditor()
	{
		if ($this->_pushscripts) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('hubzero', 'wikieditortoolbar');
			Hubzero_Document::addPluginScript('hubzero', 'wikieditortoolbar');

			$this->_pushscripts = false;
		}

		return '';
	}

	/**
	 * Display a wiki editor
	 * 
	 * @param      string  $name    Name of field
	 * @param      string  $id      ID for field
	 * @param      string  $content Field content
	 * @param      string  $cls     Field class
	 * @param      integer $col     Number of columns
	 * @param      integer $row     Number of rows
	 * @return     string HTML
	 */
	public function onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)
	{
		$cls = ($cls) ? 'wiki-toolbar-content ' . $cls : 'wiki-toolbar-content';
		$editor  = '<ul id="wiki-toolbar-' . $id . '" class="wiki-toolbar hidden"></ul>' . "\n";
		$editor .= '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $content . '</textarea>' . "\n";

		return $editor;
	}
}
