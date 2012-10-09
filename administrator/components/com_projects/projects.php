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
		$app->redirect('index.php', JText::_('ALERTNOTAUTH'));
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

jimport('joomla.application.component.helper');

// Include scripts
require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'html.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.activity.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.microblog.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.comment.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.owner.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.type.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'project.todo.php' );
include_once( JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'helper.php' );

// Check to make sure component is installed
$database =& JFactory::getDBO();
$tables = $database->getTableList();
if (!in_array($database->_table_prefix . 'projects', $tables)) 
{
	include_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'install.php' );
	$installHelper = new ProjectsInstall($database, $tables);
	$installHelper->runInstall();
}

// Check for new db fields
$fields = $database->getTableFields($database->_table_prefix . 'project_owners');
if (!array_key_exists('prev_visit', $fields[$database->_table_prefix . 'project_owners'] )) 
{
	$database->setQuery( "ALTER TABLE `jos_project_owners` 
		ADD `prev_visit` datetime DEFAULT '0000-00-00 00:00:00'" );
	if (!$database->query()) 
	{
		echo $database->getErrorMsg();
		return false;
	}
}

ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Filter');
ximport('Hubzero_Plugin_View');
ximport('Hubzero_Group');

$controllerName = JRequest::getCmd('controller', 'projects');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'projects';
}

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ProjectsController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
