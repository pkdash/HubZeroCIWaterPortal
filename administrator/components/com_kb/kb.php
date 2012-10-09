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

error_reporting(E_ALL);
@ini_set('display_errors','1');

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
	
	// Authorization check
	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}

	require_once(JPATH_COMPONENT . DS . 'models' . DS . 'category.php');
	require_once(JPATH_COMPONENT . DS . 'models' . DS . 'article.php');
}
// Include scripts
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'comment.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'article.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'category.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'vote.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'tags.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'kb.php');

$controllerName = JRequest::getCmd('controller', 'categories');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'categories';
}

JSubMenuHelper::addEntry(
	JText::_('Categories'),
	'index.php?option=com_kb&id=0',
	$controllerName == 'categories'
);
JSubMenuHelper::addEntry(
	JText::_('Articles'),
	'index.php?option=com_kb&controller=articles&id=0',
	$controllerName == 'articles'
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'KbController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

