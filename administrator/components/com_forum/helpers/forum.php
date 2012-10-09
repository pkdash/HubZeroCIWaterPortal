<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class ForumHelper
{
	public static $extension = 'com_forum';
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_FORUM_SUBMENU_CATEGORIES'),
			'index.php?option=com_kb&extension=com_content',
			$vName == 'categories');
		JSubMenuHelper::addEntry(
			JText::_('COM_FORUM_SUBMENU_FEATURED'),
			'index.php?option=com_kb&view=featured',
			$vName == 'featured'
		);
	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	string	$extension	The extension.
	 * @param	int		$categoryId	The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($assetType='component', $assetId = 0)
	{
		$assetName = 'com_forum';
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$actions = array(
				'admin', 'manage', 'create', 'edit', 'edit.state', 'delete'
			);

			foreach ($actions as $action) 
			{
				$result->set('core.' . $action, $user->authorize($assetName, 'manage'));
			}
		}
		else 
		{
			$assetName .= '.' . $assetType;
			if ($assetId) {
				$assetName .= '.' . (int) $assetId;
			}

			$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
			);

			foreach ($actions as $action) 
			{
				$result->set($action, $user->authorise($action, $assetName));
			}
		}

		return $result;
	}
}

