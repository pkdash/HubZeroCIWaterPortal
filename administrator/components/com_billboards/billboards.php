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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Set access levels
$jacl =& JFactory::getACL();
$jacl->addACL($option, 'manage', 'users', 'super administrator');
$jacl->addACL($option, 'manage', 'users', 'administrator');
$jacl->addACL($option, 'manage', 'users', 'manager');

// Authorization check
$user = & JFactory::getUser();
if (!$user->authorize($option, 'manage')) 
{
	$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

// Include needed tables and controller
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'billboard.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'collection.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'helpers'.DS.'html.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'controller.php');

// Initiate controller
$controller = new BillboardsController();
$controller->execute();
$controller->redirect();