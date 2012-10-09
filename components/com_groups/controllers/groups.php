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

ximport('Hubzero_Controller');

/**
 * Groups controller class
 */
class GroupsController extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = JRequest::getVar('task', '');
		$this->gid    = JRequest::getVar('gid', '');
		$this->active = JRequest::getVar('active', '');

		if ($this->gid && !$this->_task) 
		{
			$this->_task = 'view';
		}
		if ($this->active && $this->_task) 
		{
			$this->action = ($this->_task == 'view') ? '' : $this->_task;
			$this->_task = 'view';
		}
		if ($this->_task == '') 
		{
			$this->_task = 'intro';
		}

		//are we serving up a file
		$uri = $_SERVER['REQUEST_URI'];
		if (strstr($uri, 'Image:')) 
		{
			$file = strstr($uri, 'Image:');
			$this->_task = 'download';
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = strstr($uri, 'File:');
			$this->_task = 'download';
		}

		// Execute the task
		switch ($this->_task)
		{
			// File manager for uploading images/files to be used in group descriptions
			case 'media':             $this->media();             break;
			case 'listfiles':         $this->listfiles();         break;
			case 'upload':            $this->upload();            break;
			case 'deletefolder':      $this->deletefolder();      break;
			case 'deletefile':        $this->deletefile();        break;
			case 'ajaxupload':        $this->ajaxUpload();        break;

			// Autocompleter - called via AJAX
			case 'autocomplete':      $this->autocomplete();      break;
			case 'memberslist':       $this->memberslist();       break;
			
			//
			case 'groupavailability': $this->groupAvailability(); break;

			// Group management
			case 'new':               $this->edit();              break;
			case 'edit':              $this->edit();              break;
			case 'save':              $this->save();              break;
			case 'delete':            $this->delete();            break;
			case 'invite':            $this->invite();            break;
			case 'accept':            $this->accept();            break;

			//Group Customization
			case 'customize':         $this->customize();         break;
			case 'savecustomization': $this->saveCustomization(); break;
			case 'managepages':       $this->managePages();       break;

			// Admin option
			case 'approve':           $this->approve();           break;

			// User options
			case 'join':              $this->join();              break;
			case 'cancel':            $this->cancel();            break;
			case 'confirm':           $this->confirm();           break;

			// General views
			case 'view':              $this->view();              break;
			case 'browse':            $this->browse();            break;
			case 'login':             $this->login();             break;
			case 'intro':             $this->intro();             break;
			case 'features':          $this->features();          break;

			//serve group files via content server
			case 'download':          $this->download($file);     break;

			default:                  $this->intro();             break;
		}
	}

	/**
	 * Set a notification
	 * 
	 * @param      string $message Message to set
	 * @param      string $type    Type [error, passed, warning]
	 * @return     void
	 */
	public function setNotification($message, $type)
	{
		//if type is not set, set to error message
		$type = ($type == '') ? 'error' : $type;

		//if message is set push to notifications
		if ($message != '') 
		{
			$this->addComponentMessage($message, $type);
		}
	}

	/**
	 * Get norifications
	 * 
	 * @return     array
	 */
	public function getNotifications()
	{
		//getmessages in quene 
		$messages = $this->getComponentMessage();

		//if we have any messages return them
		if ($messages) 
		{
			return $messages;
		}
	}

	/**
	 * Method to add stylesheets to the document.
	 * 
	 * @param      integer $group_type Parameter description (if any) ...
	 * @return     void
	 */
	protected function _getGroupStyles($group_type = null)
	{
		$task = $this->_task;
		$doc =& JFactory::getDocument();

		$mainframe =& JFactory::getApplication();
		$template = $mainframe->getTemplate();

		$template_css = '/templates' . DS . $template . DS . 'html' . DS . 'com_groups' . DS . $task . '.css';
		$view_css = '/components' . DS . 'com_groups' . DS . 'assets' . DS . 'css' . DS . $task . '.css';
		$component_css = '/components' . DS . 'com_groups' . DS . 'assets' . DS . 'css' . DS . 'groups.css';

		if (file_exists(JPATH_ROOT . $template_css)) 
		{
			$doc->addStyleSheet($template_css);
		} 
		elseif (file_exists(JPATH_ROOT . $view_css)) 
		{
			$doc->addStyleSheet($view_css);
		} 
		elseif (file_exists(JPATH_ROOT . $component_css)) 
		{
			$doc->addStyleSheet($component_css);
		} 
		else 
		{
			$this->_getStyles();
		}

		if ($group_type == 3) 
		{
			$template_special_css = DS . 'templates' . DS . $template . DS . 'html' . DS .'com_groups' . DS . 'special.css';
			$special_css = DS . 'components' . DS . 'com_groups' . DS . 'assets' . DS . 'css' . DS . 'special.css';

			if (file_exists(JPATH_ROOT . $template_special_css))
			{
				$doc->addStyleSheet($template_special_css);
			}
			else
			{
				$doc->addStyleSheet($special_css);
			}
		}
	}

	/**
	 * Push scripts to document head
	 * 
	 * @return     void
	 */
	protected function _getGroupScripts()
	{
		$this->_getScripts('assets/js/' . $this->_name);
	}

	/**
	 * Method to set the document path
	 * 
	 * @param      array $group_pages List of roup pages
	 * @return     void
	 */
	protected function _buildPathway($group_pages = array())
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);

			if ($this->gid) 
			{
				$group = new Hubzero_Group();
				$group->read($this->gid);

				$pathway->addItem(
					stripslashes($group->get('description')),
					'index.php?option=' . $this->_option . '&gid=' . $this->gid
				);
			}

			if ($this->_task == 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}

			if ($this->_task && $this->_task != 'view' && $this->_task != 'intro' && $this->_task != 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&gid=' . $this->gid . '&task=' . $this->_task
				);
			}

			if ($this->active && $this->active != 'overview') 
			{
				if (in_array($this->active, array_keys($group_pages))) 
				{
					$text = JText::_($group_pages[$this->active]['title']);
				} 
				else 
				{
					$text = JText::_('GROUP_' . strtoupper($this->active));
				}

				$pathway->addItem(
					$text, 
					'index.php?option=' . $this->_option . '&gid=' . $this->gid . '&active=' . $this->active
				);
			}
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		//$option = substr($this->_option,4);

		//set title used in view
		$this->_title = JText::_(strtoupper($this->_name));

		if ($this->_task && $this->_task != 'intro') 
		{
			$this->_title = JText::_(strtoupper($this->_name . '_' . $this->_task));
		}

		if ($this->gid) 
		{
			$group = new Hubzero_Group();
			$group->read($this->gid);

			$this->_title = JText::_('GROUP') . ': ' . stripslashes($group->get('description'));
		}

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Look up username in profiles
	 * 
	 * @param      string $user Username or user ID
	 * @return     array
	 */
	public function getMemberProfile($user)
	{
		if (is_numeric($user)) 
		{
			$sql = "SELECT * FROM #__xprofiles WHERE uidNumber='" . $user . "'";
		} 
		else 
		{
			$sql = "SELECT * FROM #__xprofiles WHERE username='" . $user . "'";
		}

		$this->_db->setQuery($sql);
		$profile = $this->_db->loadAssoc();

		return $profile;
	}

	/**
	 * Redirect to login page
	 * 
	 * @return     void
	 */
	protected function login($message = '')
	{
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->gid . '&task=' . $this->_task));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}

	/**
	 * Display component main page
	 * 
	 * @return     void
	 */
	protected function intro()
	{
		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getGroupStyles();

		// Push some needed scripts to the template
		$this->_getGroupScripts();

		//vars
		$mytags = '';
		$mygroups = array();
		$populargroups = array();
		$interestinggroups = array();

		//get the users profile
		$profile = Hubzero_User_Profile::getInstance($this->juser->get("id"));

		if (is_object($profile))
		{
			//get users tags
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');
			$mt = new MembersTags($this->database);
			$mytags = $mt->get_tag_string($profile->get("uidNumber"));

			//get users groups
			$mygroups['members'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'members', 1);
			$mygroups['invitees'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'invitees', 1);
			$mygroups['applicants'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'applicants', 1);
			$mygroups = array_filter($mygroups);

			//get groups user may be interested in
			$interestinggroups = Hubzero_Group_Helper::getGroupsMatchingTagString($mytags, Hubzero_User_Helper::getGroups($profile->get("uidNumber")));
		}

		//get the popular groups
		$populargroups = Hubzero_Group_Helper::getPopularGroups(3);

		// Output HTML
		$view = new JView(array('name' => 'intro'));
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->database = $this->database;
		$view->user = $this->juser;
		$view->title = $this->_title;
		$view->mygroups = $mygroups;
		$view->populargroups = $populargroups;
		$view->interestinggroups = $interestinggroups;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Display a list of groups on the site and options for filtering/browsing them
	 * 
	 * @return     void
	 */
	protected function browse()
	{
		$view = new JView(array('name' => 'browse'));
		$view->option = $this->_option;

		// Push some styles to the template
		$this->_getGroupStyles();

		// Incoming
		$view->filters = array();
		$view->filters['type']   = array(1,3);
		$view->filters['authorized'] = "";

		// Filters for getting a result count
		$view->filters['limit']  = 'all';
		$view->filters['fields'] = array('COUNT(*)');
		$view->filters['search'] = JRequest::getVar('search', '');
		$view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'title'));
		if (!in_array($view->filters['sortby'], array('alias', 'title')))
		{
			$view->filters['sortby'] = 'title';
		}
		$view->filters['policy'] = strtolower(JRequest::getWord('policy', ''));
		if (!in_array($view->filters['policy'], array('open', 'restricted', 'invite', 'closed')))
		{
			$view->filters['policy'] = '';
		}
		$view->filters['index']  = htmlentities(JRequest::getVar('index', ''));

		// Get a record count
		$view->total = Hubzero_Group::find($view->filters);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters['limit']  = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['limit']  = ($view->filters['limit']) ? $view->filters['limit'] : 'all';
		$view->filters['start']  = JRequest::getInt('limitstart', 0);
		$view->filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy');

		// Get a list of all groups
		$view->groups = Hubzero_Group::find($view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Run through the master list of groups and mark the user's status in that group
		$view->authorized = $this->_authorize();
		if ($view->authorized && $view->groups) 
		{
			$view->groups = $this->getGroups($view->groups);
		}

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * View a group
	 * 
	 * @return     void
	 */
	protected function view()
	{
		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!is_object($group) || (!$group->get('gidNumber') && !$group->get('cn'))) 
		{
			/*JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));*/
			//$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=new' . (is_numeric($this->gid) ? '' : '&cn=' . $this->gid));
			$view = new JView(array('name' => 'error'));
			$view->title = JText::_('GROUPS_NO_GROUP_FOUND');
			$view->notifications = array(array(
				'type' => 'warning',
				'message' => JText::_('This group does not seem to exist. Would you like to <a href="' . JRoute::_('index.php?option=' . $this->_option . '&task=new' . (is_numeric($this->gid) ? '' : '&cn=' . $this->gid)) . '">create it</a>?')
			));
			$view->gid = $this->gid;
			$view->option = $this->_option;
			$view->display();
			return;
		}

		// Set gid for other functions
		$this->gid = $group->get('cn');

		// Ensure it's an allowable group type to display
		if ($group->get('type') != 1 && $group->get('type') != 3) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Ensure the group has been published or has been approved
		if ($group->get('published') != 1) 
		{
			JError::raiseError(404, JText::_('GROUPS_NOT_PUBLISHED'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// Check authorization
		$authorized = $this->_authorize();

		// Get the active tab (section)
		$tab = JRequest::getVar('active', 'overview');

		if ($tab == 'wiki') 
		{
			$path = '';
		} 
		else 
		{
			$path = $this->config->get('uploadpath');
		}

		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $group->get('cn'),
			'pageid'   => $group->get('gidNumber'),
			'filepath' => $path,
			'domain'   => $group->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Get the group pages if any
		$GPages = new GroupPages($this->database);
		$pages = $GPages->getPages($group->get('gidNumber'), true);
		
		if (in_array($tab, array_keys($pages)))
		{
			$wikiconfig['pagename'] .= DS . $tab;
		}
		
		// Push some vars to the group pages
		$GPages->parser = $p;
		$GPages->config = $wikiconfig;
		$GPages->group = $group;
		$GPages->authorized = $authorized;
		$GPages->tab = $tab;
		$GPages->pages = $pages;

		// Get the content to display group pages
		$group_overview = $GPages->displayPage();

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$start = JRequest::getInt('limitstart', 0);

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$hub_group_plugins = $dispatcher->trigger('onGroupAreas', array());
		array_unshift($hub_group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get plugin access
		$group_plugin_access = Hubzero_Group_Helper::getPluginAccess($group);

		// If active tab not overview and an not one of available tabs
		if ($tab != 'overview' && !in_array($tab, array_keys($group_plugin_access))) 
		{
			$tab = 'overview';
		}

		// Limit the records if we're on the overview page
		if ($tab == 'overview') 
		{
			$limit = 5;
		}

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the sections
		$sections = $dispatcher->trigger('onGroup', array(
				$group,
				$this->_option,
				$authorized,
				$limit,
				$start,
				$this->action,
				$group_plugin_access,
				array($tab)
			)
		);

		// Push some needed styles to the template
		// Pass in group type to include special css for paying groups
		$this->_getGroupStyles($group->get('type'));

		// Push some needed scripts to the template
		$this->_getGroupScripts();

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway($pages);

		// Add the default "About" section to the beginning of the lists
		if ($tab == 'overview') 
		{
			$view = new JView(array('name' => 'view', 'layout' => 'overview'));
			$view->group_overview = $group_overview;
			$body = $view->loadTemplate();
		} 
		else 
		{
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		array_unshift($sections, array('html' => $body, 'metadata' => ''));

		// If we are a special group load special template
		if ($group->get('type') == 3) 
		{
			if (is_file(JPATH_SITE . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS . 'group.php'))
			{
				JRequest::setVar('tmpl', 'group');
			}
			$view = new JView(array('name' => 'view', 'layout' => 'special'));
		} 
		else 
		{
			$view = new JView(array('name' => 'view'));
		}

		$view->option = $this->_option;
		$view->group = $group;
		$view->user = $this->juser;
		$view->gparams = $gparams;

		$view->hub_group_plugins = $hub_group_plugins;
		$view->group_plugin_access = $group_plugin_access;
		$view->pages = $pages;

		$view->sections = $sections;
		$view->tab = $tab;

		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Perform necessary actions for when a member clicks 'join'
	 *   Closed membership - show the group page
	 *   Invite only - show the group page
	 *   Open membership - Go ahead and make them a member
	 *   Restricted membership - show a form for requesting membership
	 * 
	 * @return     void
	 */
	protected function join()
	{
		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to join the ' . $group->get('description') . ' group.');
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		// Check if the user is already a member, applicant, invitee, or manager
		if ($group->is_member_of('applicants', $this->juser->get('id')) ||
			$group->is_member_of('members', $this->juser->get('id')) ||
			$group->is_member_of('managers', $this->juser->get('id')) ||
			$group->is_member_of('invitees', $this->juser->get('id'))) 
		{
			// Already a member - show the group page
			$this->_task = 'view';
			$this->view();
			return;
		}

		// Based on join policy is what happens
		switch ($group->get('join_policy'))
		{
			case 3:
				// Closed membership - show the group page
				$this->_task = 'view';
				$this->view();
			break;
			case 2:
				// Invite only - show the group page
				$this->_task = 'view';
				$this->view();
			break;
			case 1:
				// Output HTML
				$view = new JView(array('name' => 'join'));
				$view->option = $this->_option;
				$view->title = $this->_title;
				$view->group = $group;
				$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
				$view->display();
			break;
			case 0:
			default:
				// Open membership - Go ahead and make them a member
				$this->confirm();
			break;
		}
	}

	/**
	 * Cancel membership in a group
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		$app = JFactory::getApplication();

		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to cancel your group membership.');
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		// Remove the user from the group
		$group->remove('managers', $this->juser->get('id'));
		$group->remove('members', $this->juser->get('id'));
		$group->remove('applicants', $this->juser->get('id'));
		$group->remove('invitees', $this->juser->get('id'));
		if ($group->update() === false) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_CANCEL_MEMBERSHIP_FAILED'), 'error');
		}

		// Log the membership cancellation
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_cancelled';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->setNotification($log->getError(), 'error');
		}

		// Remove record of reason wanting to join group
		$reason = new GroupsReason($this->database);
		$reason->deleteReason($this->juser->get('id'), $group->get('gidNumber'));

		$jconfig =& JFactory::getConfig();

		// Email subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP_CANCELLED', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'cancelled'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_cancelled_me', $subject, $message, $from, $group->get('managers'), $this->_option)))
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option), '', 'message', true);
		} 
		else 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn')), '', 'message', true);
		}
	}

	/**
	 * Confirm membership requests
	 * 
	 * @return     void
	 */
	protected function confirm()
	{
		$app = JFactory::getApplication();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to confirm your group status.');
			return;
		}
		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');

		// Auto-approve member for group without any managers
		if (count($emailmanagers) < 1) 
		{
			$group->add('managers', array($this->juser->get('id')));
		} 
		else 
		{
			if ($group->get('join_policy') == 0) 
			{
				$group->add('members', array($this->juser->get('id')));
			}
			else 
			{
				$group->add('applicants', array($this->juser->get('id')));
			}
		}
		
		if ($group->update() === false) 
		{
			$this->setError(JText::_('GROUPS_ERROR_REGISTER_MEMBERSHIP_FAILED'));
		}

		if ($group->get('join_policy') == 1) 
		{
			// Instantiate the reason object and bind the incoming data
			$row = new GroupsReason($this->database);
			$row->uidNumber = $this->juser->get('id');
			$row->gidNumber = $group->get('gidNumber');
			$row->reason    = JRequest::getVar('reason', JText::_('GROUPS_NO_REASON_GIVEN'), 'post');
			$row->reason    = Hubzero_View_Helper_Html::purifyText($row->reason);
			$row->date      = date('Y-m-d H:i:s', time());

			// Check and store the reason
			if (!$row->check()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// Log the membership request
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_requested';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->setError($log->getError());
		}
		// Log the membership approval if the join policy is open
		if ($group->get('join_policy') == 0) 
		{
			$log2 = new XGroupLog($this->database);
			$log2->gid = $group->get('gidNumber');
			$log2->uid = $this->juser->get('id');
			$log2->timestamp = date('Y-m-d H:i:s', time());
			$log2->action = 'membership_approved';
			$log2->actorid = $this->juser->get('id');
			if (!$log2->store()) 
			{
				$this->setError($log2->getError());
			}
		}

		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'request'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		if ($group->get('join_policy') == 1) 
		{
			$url = 'index.php?option=' . $this->_option . '&gid=' . $group->get('cn') . '&active=members';
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('groups_requests_membership', $subject, $message, $from, $group->get('managers'), $this->_option, $group->get('gidNumber'), $url))) 
			{
				$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
			}
		}

		// Push through to the groups listing
		$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn')), '', 'message', true);
	}

	/**
	 * Accept membership invitation
	 * 
	 * @return     void
	 */
	protected function accept()
	{
		$app = JFactory::getApplication();

		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));

		// Check if they're logged in	
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to accept your group invitation.');
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		//do we have permission to join group
		if ($group->get('type') == 2) 
		{
			JError::raiseError(404, JText::_('You do not have permission to join this group.'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		//get invite token
		$token = JRequest::getVar('token','','get');

		//get current members and invitees
		$invitees = $group->get('invitees');
		$members = $group->get("members");

		//are we already a member
		if (in_array($this->juser->get('id'), $members)) 
		{
			$add->redirect(JRoute::_('index.php?option=com_groups&gid=' . $group->get("cn")));
			exit();
		}

		// Get invite emails
		$group_inviteemails = new Hubzero_Group_Invite_Email($this->database);
		$inviteemails = $group_inviteemails->getInviteEmails($group->get('gidNumber'), true);
		$inviteemails_with_token = $group_inviteemails->getInviteEmails($group->get('gidNumber'), false);

		//group log comment
		$log_comments = '';

		//check to make sure weve been invited
		if ($token)
		{
			$sql = "SELECT * FROM #__xgroups_inviteemails WHERE token=" . $this->database->quote($token);
			$this->database->setQuery($sql);
			$invite = $this->database->loadAssoc();

			if ($invite) 
			{
				$group->add('members',array($this->juser->get('id')));
				$group->update(); 

				$log_comments = $invite['email'];
				$update = "UPDATE `jos_course_enrollments` SET `uid`=" . $this->juser->get('id') . " WHERE MD5(`enrollment_id`)='" . $token . "'";
				$this->database->setQuery($update);
				$this->database->query();

				$sql = "DELETE FROM #__xgroups_inviteemails WHERE id=" . $this->database->quote($invite['id']);
				$this->database->setQuery($sql);
				$this->database->query();
			}
		}
		elseif (in_array($this->juser->get('email'), $inviteemails))
		{
			$group->add('members',array($this->juser->get('id')));
			$group->update();
			$sql = "DELETE FROM #__xgroups_inviteemails WHERE email='" . $this->juser->get('email') . "' AND gidNumber='" . $group->get('gidNumber') . "'";
			$this->database->setQuery($sql);
			$this->database->query();
		}
		elseif (in_array($this->juser->get('id'), $invitees))
		{
			$group->add('members',array($this->juser->get('id')));
			$group->remove('invitees',array($this->juser->get('id')));
			$group->update();
		}
		else
		{
			JError::raiseError(404, JText::_('You do not have permission to join this group.'));
			return;
		}

		// Log the invite acceptance
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->comments = $log_comments;
		$log->action = 'membership_invite_accepted';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'accepted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_accepts_membership', $subject, $message, $from, $group->get('managers'), $this->_option))) 
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option), '', 'message', true);
		} 
		else 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid='. $group->get('cn')), '', 'message', true);
		}
	}

	/**
	 * Show a form for editing a group
	 * 
	 * @return     void
	 */
	protected function edit()
	{
		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getGroupStyles();

		// Push some needed scripts to the template
		$this->_getGroupScripts();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to edit or create groups.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager' && $this->_task != 'new') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Instantiate an Hubzero_Group object
		$group = new Hubzero_Group();

		if ($this->_task != 'new') 
		{
			// Ensure we have a group to work with
			if (!$this->gid) 
			{
				JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
				return;
			}

			// Load the group
			$group->read($this->gid);

			// Ensure we found the group info
			if (!$group) 
			{
				JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
				return;
			}

			$title = "Edit Group: ".$group->get('description');
		} 
		else 
		{
			$group->set('cn', JRequest::getVar('cn', ''));
			$group->set('join_policy', $this->config->get('join_policy'));
			$group->set('privacy', $this->config->get('privacy'));
			$group->set('access', $this->config->get('access'));
			$group->set('published', $this->config->get('auto_approve'));

			$title = 'Create New Group';
		}

		//get directory for group file uploads
		if ($this->lid != '')
		{
			$lid = $this->lid;
		}
		elseif ($group->get('gidNumber'))
		{
			$lid = $group->get('gidNumber');
		}
		else
		{
			$lid = time().rand(0,1000);
		}

		// Get the group's interests (tags)
		$gt = new GroupsTags($this->database);
		$tags = $gt->get_tag_string($group->get('gidNumber'));

		if ($this->group) 
		{
			$group = $this->group;
			$tags  = $this->tags;
		}

		// Output HTML
		$view = new JView(array('name' => 'edit'));
		$view->option = $this->_option;
		$view->title  = $title;
		$view->group  = $group;
		$view->tags   = $tags;
		$view->juser  = $this->juser;
		$view->task   = $this->_task;
		$view->lid    = $lid;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Save a group
	 * 
	 * @return     void
	 */
	protected function save()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to save group settings.');
			return;
		}

		// Incoming
		$g_cn           = strtolower(trim(JRequest::getVar('cn', '', 'post')));
		$g_description  = preg_replace('/\s+/', ' ',trim(JRequest::getVar('description', JText::_('NONE'), 'post')));
		$g_privacy      = JRequest::getInt('privacy', 0, 'post');
		$g_gidNumber    = JRequest::getInt('gidNumber', 0, 'post');
		$g_published    = JRequest::getInt('published', 0, 'post');
		$g_public_desc  = trim(JRequest::getVar('public_desc',  '', 'post', 'none', 2));
		$g_private_desc = trim(JRequest::getVar('private_desc', '', 'post', 'none', 2));
		$g_restrict_msg = trim(JRequest::getVar('restrict_msg', '', 'post', 'none', 2));
		$g_join_policy  = JRequest::getInt('join_policy', 0, 'post');
		$tags = trim(JRequest::getVar('tags', ''));
		$g_discussion_email_autosubscribe = JRequest::getInt('discussion_email_autosubscribe', 0, 'post');
		$lid = JRequest::getInt('lid', 0, 'post');

		//Check authorization
		if ($this->_authorize() != 'manager' && $g_gidNumber != 0) 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Instantiate an Hubzero_Group object
		$group = new Hubzero_Group();

		// Is this a new entry or updating?
		$isNew = false;
		if (!$g_gidNumber) 
		{
			$isNew = true;

			// Set the task - if anything fails and we re-enter edit mode 
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
		} 
		else 
		{
			$this->_task = 'edit';

			// Load the group
			$group->read($g_gidNumber);
		}

		// Check for any missing info
		if (!$g_cn) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('GROUPS_ID'), 'error');
		}
		if (!$g_description) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('GROUPS_TITLE'), 'error');
		}

		// Push back into edit mode if any errors
		if ($this->getNotifications()) 
		{
			$group->set('published', $g_published);
			$group->set('description', $g_description);
			//$group->set('access', $g_access);
			$group->set('privacy', $g_privacy);
			$group->set('public_desc', $g_public_desc);
			$group->set('private_desc', $g_private_desc);
			$group->set('restrict_msg', $g_restrict_msg);
			$group->set('join_policy', $g_join_policy);
			$group->set('cn', $g_cn);
			$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
            
			$this->lid = $lid;
			$this->group = $group;
			$this->tags = $tags;
			$this->edit();
			return;
		}

		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse') 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_INVALID_ID'), 'error');
		}
		if (!$this->_validCn($g_cn)) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_INVALID_ID'), 'error');
		}
		if ($isNew && Hubzero_Group::exists($g_cn,true)) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_GROUP_ALREADY_EXIST'), 'error');
		}

		// Push back into edit mode if any errors
		if ($this->getNotifications()) 
		{
			$group->set('published', $g_published);
			$group->set('description', $g_description);
			//$group->set('access', $g_access);
			$group->set('privacy', $g_privacy);
			$group->set('public_desc', $g_public_desc);
			$group->set('private_desc', $g_private_desc);
			$group->set('restrict_msg', $g_restrict_msg);
			$group->set('join_policy', $g_join_policy);
			$group->set('cn', $g_cn);
			$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);

			$this->lid = $lid;
			$this->group = $group;
			$this->tags = $tags;
			$this->edit();
			return;
		}

		// Get some needed objects
		$jconfig =& JFactory::getConfig();

		// Build the e-mail message
		if ($isNew) 
		{
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_REQUESTED', $g_cn);
		} 
		else 
		{
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_UPDATED', $g_cn);
		}

		if ($isNew) 
		{
			$type = 'groups_created';
		} 
		else 
		{
			$type = 'groups_changed';
		}

		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$eview = new JView(array('name' => 'emails', 'layout' => 'saved'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->isNew = $isNew;
		$eview->g_description = $g_description;
		//$eview->g_access = $g_access;
		$eview->g_privacy = $g_privacy;
		$eview->g_public_desc = $g_public_desc;
		$eview->g_private_desc = $g_private_desc;
		$eview->g_restrict_msg = $g_restrict_msg;
		$eview->g_join_policy = $g_join_policy;
		$eview->g_cn = $g_cn;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Set the group changes and save
		$group->set('cn', $g_cn);
		if ($isNew) 
		{
			$group->create();
			$group->set('type', 1);
			$group->set('published', $g_published);
			$group->set('created', date("Y-m-d H:i:s"));
			$group->set('created_by', $this->juser->get('id'));

			$group->add('managers', array($this->juser->get('id')));
			$group->add('members', array($this->juser->get('id')));
		}

		$group->set('description', $g_description);
		//$group->set('access', $g_access);
		$group->set('privacy', $g_privacy);
		$group->set('public_desc', $g_public_desc);
		$group->set('private_desc', $g_private_desc);
		$group->set('restrict_msg',$g_restrict_msg);
		$group->set('join_policy',$g_join_policy);
		$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$group->update();

		// Process tags
		$gt = new GroupsTags($this->database);
		$gt->tag_object($this->juser->get('id'), $group->get('gidNumber'), $tags, 1, 1);

		// Log the group save
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');

		// Rename the temporary upload directory if it exist
		if ($isNew) 
		{
			if ($lid != $group->get('gidNumber')) 
			{
				$config = $this->config;
				$bp = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS);
				if (is_dir($bp . DS . $lid)) 
				{
					rename($bp . DS . $lid, $bp . DS . $group->get('gidNumber'));
				}
			}

			$log->action = 'group_created';

			// Get plugins
			JPluginHelper::importPlugin('groups');
			$dispatcher =& JDispatcher::getInstance();

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onGroupNew', array($group));
			if (count($logs) > 0) 
			{
				$log->comments .= implode('', $logs);
			}
		} 
		else 
		{
			$log->action = 'group_edited';
		}

		if (!$log->store()) 
		{
			$this->setNotification($log->getError(), 'error');
		}

		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Get the "from" info
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get plugins
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, $group->get('managers'), $this->_option))) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
		}

		if ($this->getNotifications()) 
		{
			$view = new JView(array('name' => 'error'));
			$view->title = $title;
			$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
			$view->display();
			return;
		}

		// Show success message to user
		if ($isNew) 
		{
			$this->setNotification("You have successfully created the \"{$group->get('description')}\" group" , 'passed');
		} 
		else 
		{
			$this->setNotification("You have successfully updated the \"{$group->get('description')}\" group" , 'passed');
		}

		// Redirect back to the group page
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $g_cn);
	}

	/**
	 * Delete a group
	 * This method initially displays a form for confirming deletion
	 * then deletes group and associated information upon POST
	 * 
	 * @return     void
	 */
	protected function delete()
	{
		// Build title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to delete a group.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		// Push some needed styles to the template
		$this->_getStyles();

		// Push some needed scripts to the template
		$this->_getScripts();

		// Get number of group members
		$members  = $group->get('members');
		$managers = $group->get('managers');

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Incoming
		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');
		$msg = trim(JRequest::getVar('msg', '', 'post'));

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setNotification(JText::_('GROUPS_ERROR_CONFIRM_DELETION'), 'error');
			}

			$log = JText::sprintf('GROUPS_MEMBERS_LOG',count($members));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onGroupDeleteCount', array($group));
			if (count($logs) > 0) 
			{
				$log .= '<br />' . implode('<br />', $logs);
			}

			// Output HTML
			$view = new JView(array('name' => 'delete'));
			$view->option = $this->_option;
			$view->title  = 'Delete Group: ' . $group->get('description');
			$view->group  = $group;
			$view->log    = $log;
			$view->msg    = $msg;
			$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
			$view->display();
			return;
		}

		// Start log
		$log  = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $group->get('cn'));
		$log .= JText::_('GROUPS_TITLE') . ': ' . $group->get('description') . "\n";
		$log .= JText::_('GROUPS_ID') . ': ' . $group->get('cn') . "\n";
		$log .= JText::_('GROUPS_PRIVACY') . ': ' . $group->get('access') . "\n";
		$log .= JText::_('GROUPS_PUBLIC_TEXT') . ': ' . stripslashes($group->get('public_desc'))  . "\n";
		$log .= JText::_('GROUPS_PRIVATE_TEXT') . ': ' . stripslashes($group->get('private_desc'))  . "\n";
		$log .= JText::_('GROUPS_RESTRICTED_MESSAGE') . ': ' . stripslashes($group->get('restrict_msg')) . "\n";

		// Log ids of group members
		if ($members) 
		{
			$log .= JText::_('GROUPS_MEMBERS') . ': ';
			foreach ($members as $gu)
			{
				$log .= $gu . ' ';
			}
			$log .= '' . "\n";
		}
		$log .= JText::_('GROUPS_MANAGERS') . ': ';
		foreach ($managers as $gm)
		{
			$log .= $gm . ' ';
		}
		$log .= '' . "\n";

		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = $dispatcher->trigger('onGroupDelete', array($group));
		if (count($logs) > 0) 
		{
			$log .= implode('',$logs);
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
		$config = $this->config;

		if (is_dir($path)) 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_DELETE_DIRECTORY'), 'error');
			}
		}

		$gidNumber = $group->get('gidNumber');
		$gcn = $group->get('cn');

		$deletedgroup = clone($group);

		// Delete group
		if (!$group->delete()) 
		{
			$view = new JView(array('name' => 'error'));
			$view->title = $title;
			if ($group->error) 
			{
				$this->setNotification($group->error, 'error');
			}
			$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
			$view->display();
			return;
		}

		// Get and set some vars
		$date = date('Y-m-d H:i:s', time());

		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $gcn);

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails','layout' => 'deleted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->gcn = $gcn;
		$eview->msg = $msg;
		$eview->group = $deletedgroup;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_deleted', $subject, $message, $from, $members, $this->_option))) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED'), 'error');
		}

		// Log the deletion
		$xlog = new XGroupLog($this->database);
		$xlog->gid = $gidNumber;
		$xlog->uid = $this->juser->get('id');
		$xlog->timestamp = date('Y-m-d H:i:s', time());
		$xlog->action = 'group_deleted';
		$xlog->comments = $log;
		$xlog->actorid = $this->juser->get('id');
		if (!$xlog->store()) 
		{
			$this->setNotification($xlog->getError(), 'error');
		}

		// Redirect back to the groups page
		$this->setNotification("You successfully deleted the \"{$deletedgroup->get('description')}\" group", 'passed');
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
	}

	/**
	 * Show a form for approving a new group
	 * 
	 * @return     void
	 */
	protected function approve()
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		// Check authorization ONLY ADMINS!!
		if ($this->_authorize(false) != 'admin') 
		{
			JError::raiseError(404, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Approve the group
		$group->set('published', 1);
		$group->update();

		if ($group->getError()) 
		{
			$this->setError(JText::_('GROUPS_ERROR_APPROVING_GROUP'));
			$this->view();
			return;
		}

		// Log the group approval
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'group_approved';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		$jconfig =& JFactory::getConfig();

		// Get the managers' e-mails
		$emails = $group->getEmails('managers');

		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_APPROVED', $group->get('cn'));

		// Build the e-mail message	
		$eview = new JView(array('name' => 'emails', 'layout' => 'approved'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Send e-mail to the group managers
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_approved', $subject, $message, $from, $group->get('managers'), $this->_option))) 
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Push through to the group page
		$this->view();
	}

	/**
	 * Invite users to a group
	 * This method initially displays a form for sending invites
	 * then processes those invites upon POST
	 * 
	 * @return     void
	 */
	protected function invite()
	{
		$app = JFactory::getApplication();
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to invite members to a group.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get group params
		$gparams = new $paramsClass($group->get('params'));
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_groups&gid=' . $group->get('cn'));
			return;
		}

		// Incoming
		$process = JRequest::getVar('process', '');
		$logins  = trim(JRequest::getVar('logins', '', 'post'));
		$msg     = trim(JRequest::getVar('msg', '', 'post'));
		$return  = trim(JRequest::getVar('return', '', 'get'));

		// Did they confirm delete?
		if (!$process || !$logins) 
		{
			if ($process && !$logins) 
			{
				$this->setNotification(JText::_('Please provide a list of names or emails to invite.'), 'error');
			}

			// Build the page title
			$this->_buildTitle();

			// Build the pathway
			$this->_buildPathway();

			// Push some needed styles to the template
			$this->_getStyles();

			// Push some needed scripts to the template
			$this->_getScripts();

			// Output HTML
			$view = new JView(array('name' => 'invite'));
			$view->option = $this->_option;
			$view->title = $this->_title;
			$view->group = $group;
			$view->return = $return;
			$view->msg = $msg;
			$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
			$view->display();
			return;
		}

		$return = trim(JRequest::getVar('return', '', 'post'));
		$invitees = array();
		$inviteemails = array();
		$badentries = array();
		$apps = array();
		$mems = array();

		// Get all the group's members
		$members = $group->get('members');
		$applicants = $group->get('applicants');
		$current_invitees = $group->get('invitees');

		// Get invite emails
		$group_inviteemails = new Hubzero_Group_Invite_Email($this->database);
		$current_inviteemails = $group_inviteemails->getInviteEmails($group->get('gidNumber'), true);

		// Explode the string of logins/e-mails into an array
		if (strstr($logins, ',')) 
		{
			$la = explode(',', $logins);
		}
		else 
		{
			$la = array($logins);
		}

		foreach($la as $l)
		{
			// Trim up content
			$l = trim($l);

			// If it was a user id
			if (is_numeric($l)) 
			{
				$user = JUser::getInstance($l);
				$uid = $user->get('id');

				// Ensure we found an account
				if ($uid != '') 
				{
					// If not a member
					if (!in_array($uid, $members) || !in_array($uid, $current_invitees)) 
					{
						// If an applicant
						// Make applicant a member
						if (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						$badentries[] = array($uid, 'User is already a member.');
					}
				}
			} 
			else 
			{
				// If not a userid check if proper email
				if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $l)) 
				{
					// Try to find an account that might match this e-mail
					$this->database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='" . $l . "' OR u.email LIKE '" . $l . "\'%' LIMIT 1;");
					$uid = $this->database->loadResult();
					if (!$this->database->query()) 
					{
						$this->setNotification($this->database->getErrorMsg(), 'error');
					}

					// If we found an ID, add it to the invitees list
					if ($uid) 
					{
						// Check if user is already member or invitee
						// Check if applicant remove from applicants and add as member
						// Check if in current email invitee if not add a new email invite
						if (in_array($uid, $members) || in_array($uid, $current_invitees)) 
						{
							$badentries[] = array($uid, 'User is already a member or invitee.');
						} 
						elseif (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						if (!in_array($l, $current_inviteemails)) 
						{
							$inviteemails[] = array('email' => $l, 'gidNumber' => $group->get('gidNumber'), 'token' => $this->randomString(32));
						} 
						else 
						{
							$badentries[] = array($l, 'Email address has already been invited.');
						}
					}
				} 
				else 
				{
					$badentries[] = array($l, 'Entry is not a valid email address or user.');
				}
			}
		}

		// Add the users to the invitee list and save
		$group->remove('applicants', $apps);
		$group->add('members', $mems);
		$group->add('invitees', $invitees);
		$group->update();

		// Add the inviteemails
		foreach ($inviteemails as $ie)
		{
			$group_inviteemails = new Hubzero_Group_Invite_Email($this->database);
			$group_inviteemails->save($ie);
		}

		// Log the sending of invites
		foreach ($invitees as $invite)
		{
			if (!in_array($invite,$current_invitees)) 
			{
				$log = new XGroupLog($this->database);
				$log->gid = $group->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_invites_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->setNotification($log->getError(), 'error');
				}
			}
		}

		// Sending of invites to emails
		foreach ($inviteemails as $invite)
		{
			if (!in_array($invite,$current_inviteemails)) 
			{
				$log = new XGroupLog($this->database);
				$log->gid = $group->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_email_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->setNotification($log->getError(), 'error');
				}
			}
		}

		// Get and set some vars
		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Message subject
		$subject = JText::sprintf('GROUPS_SUBJECT_INVITE', $group->get('cn'));

		// Message body for HUB user
		$eview = new JView(array('name' => 'emails', 'layout' => 'invite'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->msg = $msg;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		$juri = JURI::getInstance();

		foreach ($inviteemails as $mbr)
		{
			// Message body for HUB user
			$eview2 = new JView(array('name' => 'emails', 'layout' => 'inviteemail'));
			$eview2->option = $this->_option;
			$eview2->sitename = $jconfig->getValue('config.sitename');
			$eview2->juser = $this->juser;
			$eview2->group = $group;
			$eview2->msg = $msg;
			$eview2->token = $mbr['token'];
			$message2 = $eview2->loadTemplate();
			$message2 = str_replace("\n", "\r\n", $message2);

			// Send the e-mail
			if (!$this->email($mbr['email'], $jconfig->getValue('config.sitename') . ' ' . $subject, $message2, $from)) 
			{
				$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED') . ' ' . $mbr['email'], 'error');
			}
		}

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_invite', $subject, $message, $from, $invitees, $this->_option))) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED'), 'error');
		}

		// Do we need to redirect?
		if ($return == 'members') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid='. $group->get('cn') . '&active=members'), '', 'message', true);
		}

		// Push all invitees together
		$all_invites = array_merge($invitees,$inviteemails);

		// Declare success/error message vars
		$success_message = '';
		$error_message = '';

		if (count($all_invites) > 0) 
		{
			$success_message = 'Group invites were successfully sent to the following users/email addresses: <br />';
			foreach ($all_invites as $invite)
			{
				if (is_numeric($invite)) 
				{
					$user = JUser::getInstance($invite);
					$success_message .= ' - ' . $user->get('name') . '<br />';
				} 
				else 
				{
					$success_message .= ' - ' . $invite['email'] . '<br />';
				}
			}
		}

		if (count($badentries) > 0) 
		{
			$error_message = 'We were unable to send invites to the following entries: <br />';
			foreach ($badentries as $entry)
			{
				if (is_numeric($entry[0])) 
				{
					$user = JUser::getInstance($entry[0]);
					if ($user->get('name') != '') 
					{
						$error_message .= ' - ' . $user->get('name') . ' &rarr; ' . $entry[1] . '<br />';
					} 
					else 
					{
						$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
					}
				} 
				else 
				{
					$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
				}
			}
		}

		// Push some notifications to the view
		$this->setNotification($success_message, 'passed');
		$this->setNotification($error_message, 'error');

		// Redirect back to view group
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn'));
	}

	/**
	 * Show a form for setting group customizations
	 * 
	 * @return     void
	 */
	protected function customize()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to customize a group.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid)
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber'))
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Path to group assets
		$asset_path = JPATH_ROOT . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');

		// Declare an empty array to hold logo paths
		$logo_fullpaths = array();

		// If path is a directory then load images
		$logos = array();
		if (is_dir($asset_path)) 
		{
			// Get all images that are in group asset folder and could be a possible group logo
			$logos = JFolder::files($asset_path, '.jpg|.jpeg|.png|.gif|.PNG|.JPG|.JPEG|.GIF', false, true);
		}

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$hub_group_plugins = $dispatcher->trigger('onGroupAreas', array());
		array_unshift($hub_group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone', 
			'display_menu_tab' => true
		));

		// Get plugin access
		$group_plugin_access = Hubzero_Group_Helper::getPluginAccess($group);

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getGroupStyles();

		// Push some needed scripts to the template
		$this->_getGroupScripts();

		// Output HTML
		$view = new JView(array('name' => 'customize'));
		$view->option = $this->_option;
		$view->task   = $this->_task;
		$view->title  = $this->_title;
		$view->group  = $group;

		if (is_dir($asset_path)) 
		{
			$view->logos = $logos;
		}
		
		$view->hub_group_plugins   = $hub_group_plugins;
		$view->group_plugin_access = $group_plugin_access;

		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Save group customizations
	 * Redirects to group view
	 * 
	 * @return     void
	 */
	protected function saveCustomization()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to customize the group.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Get the group 
		$gid = JRequest::getVar('gidNumber', '', 'POST');

		// Ensure we have a group to work with
		if (!$gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		$customization = JRequest::getVar('group', '', 'POST', 'none', 2);
		$plugins = JRequest::getVar('group_plugin', '', 'POST');

		// Get the logo
		$logo_parts = explode("/",$customization['logo']);
		$logo = array_pop($logo_parts);

		// Overview type and content
		$overview_type = (!is_numeric($customization['overview_type'])) ? 0 : $customization['overview_type'];
		$overview_content = $customization['overview_content'];

		// Plugin settings
		$plugin_access = '';
		foreach ($plugins as $plugin)
		{
			$plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
		}

		$group->set('logo', $logo);
		$group->set('overview_type', $overview_type);
		$group->set('overview_content', $overview_content);
		$group->set('plugins', $plugin_access);

		if (!$group->update()) 
		{
			$this->setNotification(JText::_('An error occurred when trying to save changes to this group.'), 'error');
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn'));
			return;
		}

		// Log the group save
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');
		$log->action = 'group_customized';

		if (!$log->store()) 
		{
			$this->setNotification($log->getError(), 'error');
		}

		// Push a success message
		$this->setNotification(JText::sprintf('You have successfully customized the %s group.', $group->get('description')), 'passed');

		// Redirect back to the group page
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn'));
	}

	/**
	 * Show an interface for managing group pages
	 * 
	 * @return     void
	 */
	protected function managePages()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->login('You must be logged in to manage a group\'s pages.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('GROUPS_NOT_AUTH'));
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getGroupStyles();

		// Push some needed scripts to the template
		$this->_getGroupScripts();

		// Import the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $group->get('cn'),
			'pageid'   => $group->get('gidNumber'),
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => $group->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Instantiate group page and module object
		$GPage = new GroupPages($this->database);

		// Get the highest page order
		$high_order_pages = $GPage->getHighestPageOrder($group->get('gidNumber'));

		// Get a subtask if there is one
		$sub_task = JRequest::getVar('sub_task');
		$page_id  = JRequest::getVar('page');

		// Set the grou for changing state
		$this->_group = $group;

		// Perform task based on sub task
		switch ($sub_task)
		{
			case 'add_page':        $this->editPage($group); return;
			case 'edit_page':       $this->editPage($group); return;
			case 'save_page':       $this->savePage();       return;

			case 'deactivate_page': $this->change_state('page', 'deactivate', $page_id);         break;
			case 'activate_page':   $this->change_state('page', 'activate', $page_id);           break;
			case 'down_page':       $this->reorder('page', 'down', $page_id, $high_order_pages); break;
			case 'up_page':         $this->reorder('page', 'up', $page_id, $high_order_pages);   break;
		}

		// Get the group pages
		$pages = $GPage->getPages($group->get('gidNumber'));

		// Seperate active/inactive pages
		$active_pages = array();
		$inactive_pages = array();

		foreach ($pages as $page)
		{
			if ($page['active'] == 1) 
			{
				array_push($active_pages, $page);
			} 
			else 
			{
				array_push($inactive_pages, $page);
			}
		}

		// Get the highest page order
		$high_order_pages = $GPage->getHighestPageOrder($group->get('gidNumber'));

		// Output HTML
		$view = new JView(array('name' => 'customize', 'layout' => 'managepages'));
		$view->option = $this->_option;
		$view->task   = $this->_task;
		$view->title  = 'Manage Custom Content: ' . $group->get('description');
		$view->group  = $group;

		$view->active_pages     = $active_pages;
		$view->inactive_pages   = $inactive_pages;
		$view->high_order_pages = $high_order_pages;

		$view->parser = $p;
		$view->wikiconfig = $wikiconfig;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Show a form for editing a group page
	 * 
	 * @param      object $group Hubzero_Group
	 * @return     void
	 */
	protected function editPage($group)
	{
		$view = new JView(array('name' => 'customize', 'layout' => 'page'));
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->title = $this->_title;
		$view->group = $group;

		$page = JRequest::getVar('page','','get');

		if ($page) 
		{
			$db =& JFactory::getDBO();
			$GPage = new GroupPages($db);
			$GPage->load($page);

			$page = array();
			$page['id']      = $GPage->id;
			$page['gid']     = $GPage->gid;
			$page['url']     = $GPage->url;
			$page['title']   = $GPage->title;
			$page['content'] = $GPage->content;
			$page['porder']  = $GPage->porder;
			$page['active']  = $GPage->active;
			$page['privacy'] = $GPage->privacy;
		}

		if ($this->page) 
		{
			$page = $this->page;
		}

		$view->page = $page;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Save a group page
	 * 
	 * @return     void
	 */
	protected function savePage()
	{
		// Ensure we have a group to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_ID'));
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance($this->gid);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('GROUPS_NO_GROUP_FOUND'));
			return;
		}

		// Get the page vars being posted
		$page = JRequest::getVar('page',array(),'post','none',2);

		// Check if the page title is set
		if ($page['title'] == '') 
		{
			$this->setNotification('You must enter a page title.','error');
			$this->page = $page;
			$this->editPage($group);
			return;
		}

		// Default task
		$task = 'update';

		// Instantiate db and group page objects
		$db =& JFactory::getDBO();
		$GPage = new GroupPages($db);

		// If new page we must create extra vars
		if ($page['new']) 
		{
			$high = $GPage->getHighestPageOrder($group->get('gidNumber'));

			$page['gid'] = $group->get('gidNumber');
			$page['active'] = 1;
			$page['porder'] = ($high + 1);

			$task = 'create';
		}

		// Get the group pages
		$pages = $GPage->getPages($group->get('gidNumber'));

		//check to see if user supplied url
		if (isset($page['url']) && $page['url'] != '')
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['url'])));
		}
		else
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['title'])));
		}

		//remove unwanted chars
		$invalid_chrs = array("?","!",">","<",",",".",";",":","`","~","@","#","$","%","^","&","*","(",")","-","=","+","/","\/","|","{","}","[","]");
		$page['url'] = str_replace("'", '', $page['url']);
		$page['url'] = str_replace('"', '', $page['url']);
		$page['url'] = str_replace($invalid_chrs, '', $page['url']);

		// Get unique page name
		$page['url'] = $this->uniquePageURL($page['url'],$pages, $group, $page['id']);

		// Save the page
		if (!$GPage->save($page)) 
		{
			$this->setNotification("An error occurred while trying to {$task} the page.", 'error');
		}

		// Push success message and redirect
		$this->setNotification("You have successfully {$task}d the page.", 'passed');
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $group->get('cn') . '&task=managepages');
	}

	/**
	 * Change the status of an item
	 * 
	 * @param      string $type   Item being changed
	 * @param      string $status Status to set
	 * @param      string $id     Item ID
	 * @return     void
	 */
	protected function change_state($type, $status, $id)
	{
		// Based on passed in status either activate or deactivate
		if ($status == 'deactivate') 
		{
			$active = 0;
		} 
		else 
		{
			$active = 1;
		}

		// Create and set query
		$sql = "UPDATE #__xgroups_" . $type . "s SET active='" . $active . "' WHERE id='" . $id . "'";
		$this->database->setQuery($sql);

		// Run query and set message
		if (!$this->database->Query()) 
		{
			$this->setNotification('An error occurred while trying to ' . $status . ' the ' . $type . '. Please try again', 'error');
		} 
		else 
		{
			$this->setNotification('The ' . $type . ' was successfully ' . $status . 'd.', 'passed');
		}

		// Redirect back to manage pages area
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->_group->get('cn') . '&task=managepages');
	}

	/**
	 * Reorder items in a list
	 * 
	 * @param      string  $type       Items being reordered
	 * @param      string  $direction  Direction to move item
	 * @param      string  $id         Item ID
	 * @param      integer $high_order Highest order
	 * @return     void
	 */
	protected function reorder($type, $direction, $id, $high_order)
	{
		$order_field = substr($type, 0, 1) . 'order';

		// Get the current order of the object trying to reorder
		$sql = "SELECT $order_field FROM #__xgroups_" . $type . "s WHERE id='" . $id . "'";
		$this->database->setQuery($sql);
		$order = $this->database->loadAssoc();

		// Set the high and low that the order can be
		$lowest_order = 1;
		$highest_order = $high_order;

		// Set the old order
		$old_order = $order[$order_field];

		// Get the new order depending on the direction of reordering
		// Make sure we are with our high and low limits
		if ($direction == 'down') 
		{
			$new_order = $old_order + 1;
			if ($new_order > $highest_order) 
			{
				$new_order = $highest_order;
			}
		} 
		else 
		{
			$new_order = $old_order - 1;
			if ($new_order < $lowest_order) 
			{
				$new_order = $lowest_order;
			}
		}

		// Check to see if another object holds the order we are trying to move to
		$sql = "SELECT *  FROM #__xgroups_" . $type . "s WHERE $order_field='" . $new_order . "' AND gid='" . $this->_group->get('gidNumber') . "'";
		$this->database->setQuery($sql);
		$new = $this->database->loadAssoc();

		// If there isnt an object there then just update
		if ($new['id'] == '') 
		{
			$sql = "UPDATE #__xgroups_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->setNotification('An error occurred while trying to reorder the ' . $type . '. Please try again', 'error');
			} 
			else 
			{
				$this->setNotification('The ' . $type . ' was successfully reordered.', 'passed');
			}
		} 
		else 
		{
			// Otherwise basically switch the two objects orders
			$sql = "UPDATE #__xgroups_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);
			$this->database->Query();

			$sql = "UPDATE #__xgroups_" . $type . "s SET $order_field='" . $old_order . "' WHERE id='" . $new['id'] . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->setNotification('An error occurred while trying to reorder the ' . $type . '. Please try again','error');
			} 
			else 
			{
				$this->setNotification('The ' . $type . ' was successfully reordered.', 'passed');
			}
		}

		// Redirect back to manage pages area
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->_group->get('cn') . '&task=managepages');
	}

	/**
	 * Generate a unique page URL
	 * 
	 * @param      string $current_url Current URL
	 * @param      array  $group_pages List of group pages
	 * @param      object $group       Hubzero_Group
	 * @return     string
	 */
	private function uniquePageURL($current_url, $group_pages, $group, $current_id = null)
	{
		//remove the current page so we dont check it
		foreach ($group_pages as $k => $v)
		{
			if ($current_id != null && $current_id == $v['id'])
			{
				unset($group_pages[$k]);
			}
		}

		// Get the page urls
		$page_urls = array_keys($group_pages);

		// Get plugin names
		$plugin_names = array_keys(Hubzero_Group_Helper::getPluginAccess($group));

		if (in_array($current_url, $plugin_names)) 
		{
			$current_url = $current_url . '_page';
			return $this->uniquePageURL($current_url, $group_pages, $group);
		}

		// Check if current url is already taken
		// otherwise return current url
		if (in_array($current_url, $page_urls)) 
		{
			// Split up the current url
			$url_parts = explode('_', $current_url);

			// Get the last part of the split url
			$num = end($url_parts);

			// If last part is numeric we need to remove that part from array and increment number then append back on end of url
			// else append a number to the end of the url
			if (is_numeric($num)) 
			{
				$num++;
				$oldNum = array_pop($url_parts);
				$url  = implode('_', $url_parts);
				$url .= "_{$num}";
			} 
			else 
			{
				$count = 1;
				$url  = implode('_', $url_parts);
				$url .= "_{$count}";
			}

			// Run the function again to see if we now have a unique url
			return $this->uniquePageURL($url, $group_pages, $group);
		} 
		else 
		{
			return $current_url;
		}
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	protected function upload()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->media();
			return;
		}

		// Load the component config
		$config = $this->config;

		// Incoming
		$listdir = JRequest::getInt('listdir', 0, 'post');

		// Ensure we have an ID to work with
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
			$this->media();
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setNotification(JText::_('GROUPS_NO_FILE'), 'error');
			$this->media();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT . DS . trim($config->get('uploadpath', '/site/groups'), DS) . DS . trim($listdir, DS);

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'), 'error');
				$this->media();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = urldecode($file['name']);
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setNotification(JText::_('ERROR_UPLOADING'), 'error');
		}

		//push a success message
		$this->setNotification('You successfully uploaded the file.', 'passed');

		// Push through to the media view
		$this->media();
	}

	/**
	 * Streaking file upload
	 * This is used by AJAX
	 * 
	 * @return     void
	 */
	private function ajaxUpload()
	{
		//get config
		$config =& JComponentHelper::getParams('com_media');

		// Incoming
		$listdir = JRequest::getInt('listdir', 0);

		//allowed extensions for uplaod
		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));

		//max upload size
		$sizeLimit = $config->get('upload_maxsize');

		//get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			return;
		}

		// Build the upload path if it doesn't exist
		$uploadDirectory = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $listdir . DS;

		//make sure upload directory is writable
		if (!is_dir($uploadDirectory))
		{
			if (!JFolder::create($uploadDirectory))
			{
				echo json_encode(array('error' => "Server error. Unable to create upload directory."));
				return;
			}
		}
		if (!is_writable($uploadDirectory))
		{
			echo json_encode(array('error' => "Server error. Upload directory isn't writable."));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0) 
		{
			echo json_encode(array('error' => 'File is empty'));
			return;
		}
		if ($size > $sizeLimit) 
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			echo json_encode(array('error' => 'File is too large. Max file upload size is ' . $max));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => 'File has an invalid extension, it should be one of ' . $these . '.'));
			return;
		} 

		//final file
		$file = $uploadDirectory . $filename . '.' . $ext;

		//save file
		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);
		
			//move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		//return success
		echo json_encode(array('success'=>true));
		return;
	}

	/**
	 * Delete a folder
	 * 
	 * @return     void
	 */
	protected function deletefolder()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->media();
			return;
		}

		// Load the component config
		$config = $this->config;

		// Incoming group ID
		$listdir = JRequest::getInt('listdir', 0, 'get');
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
			$this->media();
			return;
		}

		// Incoming file
		$folder = trim(JRequest::getVar('folder', '', 'get'));
		if (!$folder) 
		{
			$this->setNotification(JText::_('GROUPS_NO_DIRECTORY'), 'error');
			$this->media();
			return;
		}

		$del_folder = DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . trim($listdir, DS) . DS . ltrim($folder, DS);

		// Delete the folder
		if (is_dir(JPATH_ROOT . $del_folder)) 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete(JPATH_ROOT . $del_folder)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_DELETE_DIRECTORY'), 'error');
			} 
			else 
			{
				//push a success message
				$this->setNotification('You successfully deleted the folder.', 'passed');
			}
		}

		// Push through to the media view
		$this->media();
	}

	/**
	 * Delete a file
	 * 
	 * @return     void
	 */
	protected function deletefile()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->media();
			return;
		}

		// Load the component config
		$config = $this->config;

		// Incoming group ID
		$listdir = JRequest::getInt('listdir', 0, 'get');
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
			$this->media();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file) 
		{
			$this->setNotification(JText::_('GROUPS_NO_FILE'), 'error');
			$this->media();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $listdir;

		if (!file_exists($path . DS . $file) or !$file) 
		{
			$this->setNotification(JText::_('FILE_NOT_FOUND'), 'error');
			$this->media();
			return;
		} 
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_DELETE_FILE'), 'error');
			}
		}

		//push a success message
		$this->setNotification('The file was successfully deleted.', 'passed');

		// Push through to the media view
		$this->media();
	}

	/**
	 * Show a form for uploading and managing files
	 * 
	 * @return     void
	 */
	protected function media()
	{
		// Load the component config
		$config = $this->config;

		// Incoming
		$listdir = JRequest::getInt('listdir', 0);
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
		}

		$group = Hubzero_Group::getInstance($listdir);

		// Push some needed styles to the template
		$this->_getGroupStyles();

		// Output HTML
		$view = new JView(array('name' => 'edit', 'layout' => 'filebrowser'));
		$view->option = $this->_option;
		$view->config = $this->config;
		if (is_object($group)) 
		{
			$view->group = $group;
		}
		$view->listdir = $listdir;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Method for recursively going through a file tree and listing contents
	 * 
	 * @param      string $base Path to start looking through
	 * @return     array 
	 */
	protected function recursive_listdir($base)
	{
		static $filelist = array();
		static $dirlist  = array();
		if (is_dir($base))
		{
			$dh = opendir($base);
			while (false !== ($dir = readdir($dh)))
			{
				if (is_dir($base  . DS .  $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') 
				{
					$subbase    = $base  . DS .  $dir;
					$dirlist[]  = $subbase;
					$subdirlist = $this->recursive_listdir($subbase);
				}
			}
			closedir($dh);
		}
		return $dirlist;
	}

	/**
	 * List files for a group
	 * 
	 * @return     void
	 */
	protected function listfiles()
	{
		// Incoming
		$listdir = JRequest::getInt('listdir', 0, 'get');

		// Check if coming from another function
		if ($listdir == '') 
		{
			$listdir = $this->listdir;
		}

		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
		}

		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$images  = array();
		$folders = array();
		$docs    = array();

		if ($d) 
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|gif|jpg|jpeg|jpe|tif|tiff|png#i", $img_file)) 
					{
						$images[$entry] = $img_file;
					} 
					else 
					{
						$docs[$entry] = $img_file;
					}
				} 
				else if (is_dir($path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'cvs' 
				 && strtolower($entry) !== 'template' 
				 && strtolower($entry) !== 'blog') 
				{
					$folders[$entry] = $img_file;
				}
			}
			
			$d->close();

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$view = new JView(array('name' => 'edit', 'layout' => 'filelist'));
		$view->option = $this->_option;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->listdir = $listdir;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Check user access
	 * 
	 * @param      boolean $checkOnlyMembership Flag for checking only membership (not admin access)
	 * @return     mixed False if no access, string if has access
	 */
	protected function _authorize($checkOnlyMembership = true)
	{
		//load the group
		$group = Hubzero_Group::getInstance($this->gid);
		if (!is_object($group))
		{
			return false;
		}

		//check to see if they are a site admin
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			if (!$checkOnlyMembership && $this->juser->authorise('core.admin', $this->_option))
			{
				return 'admin';
			}
		}
		else 
		{
			if (!$checkOnlyMembership && $this->juser->get('usertype') == 'Super Administrator')
			{
				return 'admin';
			}
		}

		//check to see if they are a group manager
		if (in_array($this->juser->get('id'), $group->get('managers')))
		{
			return 'manager';
		}

		//check to see if they are a group member
		if (in_array($this->juser->get('id'), $group->get('members')))
		{
			return 'member';
		}

		//return false if they are none of the above
		return false;
		
		/*
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}

		if (!$checkonlymembership) {
			// Check if they're a site admin (from Joomla)
			if ($this->juser->authorize($this->_option, 'manage')) {
				return 'admin';
			}
		}

		// Get the user's groups
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
		
		$invitees = $profile->getGroups('invitees');
		$members = $profile->getGroups('members');
		$managers = $profile->getGroups('managers');

		$groups = array();
		$managerids = array();
		if ($managers && count($managers) > 0) {
			foreach ($managers as $manager)
			{
				$groups[] = $manager;
				$managerids[] = $manager->cn;
			}
		}
		if ($members && count($members) > 0) {
			foreach ($members as $mem)
			{
				if (!in_array($mem->cn,$managerids)) {
					$groups[] = $mem;
				}
			}
		}

		// Check if they're a member of this group
		if ($groups && count($groups) > 0) {
			foreach ($groups as $ug)
			{
				if ($ug->cn == $this->gid) {
					// Check if they're a manager of this group
					if ($ug->manager) {
						return 'manager';
					}
					// Are they a confirmed member?
					if ($ug->regconfirmed) {
						return 'member';
					}
				}
			}
		}

		// Check if they're invited to this group
		if ($invitees && count($invitees) > 0) {
			foreach ($invitees as $ug)
			{
				if ($ug->cn == $this->gid) {
					return 'invitee';
				}
			}
		}

		return false;
		*/
	}

	/**
	 * Short description for 'getGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $groups Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function getGroups($groups)
	{
		if (!$this->juser->get('guest')) 
		{
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

			$ugs = $profile->getGroups('all');

			for ($i = 0; $i < count($groups); $i++)
			{
				if (!isset($groups[$i]->cn)) 
				{
					$groups[$i]->cn = '';
				}
				$groups[$i]->registered   = 0;
				$groups[$i]->regconfirmed = 0;
				$groups[$i]->manager      = 0;

				if ($ugs && count($ugs) > 0) 
				{
					foreach ($ugs as $ug)
					{
						if (is_object($ug) && $ug->cn == $groups[$i]->cn) 
						{
							$groups[$i]->registered   = $ug->registered;
							$groups[$i]->regconfirmed = $ug->regconfirmed;
							$groups[$i]->manager      = $ug->manager;
						}
					}
				}
			}
		}

		return $groups;
	}

	/**
	 * Send an email
	 * 
	 * @param      string $email   Address to send message to
	 * @param      string $subject Message subject
	 * @param      string $message Message to send
	 * @param      array  $from    Who the email is from (name and address)
	 * @return     boolean Return description (if any) ...
	 */
	public function email($email, $subject, $message, $from)
	{
		if ($from) 
		{
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] . ' <' . $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <' . $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: ' . $from['name'] . "\n";
			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Return data for the qurocompleter
	 * 
	 * @return     string JSON
	 */
	protected function autocomplete()
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString('value', ''));

		// Fetch results
		$rows = $this->_getAutocomplete($filters);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$name = str_replace("\n", '', stripslashes(trim($row->description)));
				$name = str_replace("\r", '', $name);
				
				//$json[] = '{"id":"'.$row->cn.'","name":"'.htmlentities($name,ENT_COMPAT,'UTF-8').'"}';
				$item = array(
					'id'   => $row->cn,
					'name' => $name
				);
				$json[] = $item;
			}
		}

		echo json_encode($json); //'[' . implode(',',$json) . ']';
	}

	/**
	 * Get data for the autocomplete() method
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	private function _getAutocomplete($filters=array())
	{
		$query = "SELECT t.gidNumber, t.cn, t.description 
					FROM #__xgroups AS t 
					WHERE (t.type=1 OR t.type=2) AND (LOWER(t.cn) LIKE '%" . $filters['search'] . "%' OR LOWER(t.description) LIKE '%" . $filters['search'] . "%')
					ORDER BY t.description ASC";

		$this->database->setQuery($query);
		return $this->database->loadObjectList();
	}

	/**
	 * Get a list of members
	 * 
	 * @return     void
	 */
	protected function memberslist()
	{
		// Fetch results
		$filters = array();
		$filters['cn'] = trim(JRequest::getString('group', ''));

		if ($filters['cn']) 
		{
			$query = "SELECT u.username, u.name 
						FROM #__users AS u, #__xgroups_members AS m, #__xgroups AS g
						WHERE g.cn='" . $filters['cn'] . "' AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id
						ORDER BY u.name ASC";
		} 
		else 
		{
			$query = "SELECT a.username, a.name"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if ($filters['cn'] == '') 
		{
			$json[] = '{"username":"","name":"No User"}';
		}
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$json[] = '{"username":"' . $row->username . '","name":"' . htmlentities(stripslashes($row->name), ENT_COMPAT, 'UTF-8') . '"}';
			}
		}

		echo '{"members":[' . implode(',', $json) . ']}';
	}

	/**
	 * Check if a group alias is valid
	 * 
	 * @param      integer $gid Group alias
	 * @return     boolean True if valid, false if not
	 */
    private function _validCn($gid)
	{
		if (preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $gid))
		{
			if (is_numeric($gid) && intval($gid) == $gid && $gid >= 0) 
			{
				return false;
			} 
			else 
			{
				return true;
			}
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * Generate a random string
	 * 
	 * @param      integer $length Length of string
	 * @return     string
	 */
	private function randomString($length)
	{
		$str = '';

		for ($i=0; $i<$length; $i++)
		{
		    $d = rand(1,30)%2;
		    $str .= $d ? chr(rand(65,90)) : chr(rand(48,57));
		}

		return strtoupper($str);
	}

	/**
	 * Get a group's availability
	 * 
	 * @param      object $group Hubzero_Group
	 * @return     string
	 */
	private function groupAvailability($group = NULL)
	{
		//get the group
		$group = (!is_null($group)) ? $group : JRequest::getVar('group', '');
		$group = strtolower(trim($group));

		if ($group == '')
		{
			return;
		}

		// Ensure the data passed is valid
		if (($group == 'new' || $group == 'browse') || (!$this->_validCn($group)) || (Hubzero_Group::exists($group, true))) 
		{
			$availability = false;
		}
		else
		{
			$availability = true;
		}

		if (JRequest::getVar('no_html', 0) == 1)
		{
			echo json_encode(array('available' => $availability));
            return;
		}
		else
		{
			return $availability;
		}
	}

	/**
	 * Download a file
	 * 
	 * @param      string $filename File name
	 * @return     void
	 */
	protected function download($filename)
	{
		//get the group
		$group = Hubzero_Group::getInstance($this->gid);

		//authorize
		$authorized = $this->_authorize();

		//get the file name
		if (substr(strtolower($filename), 0, 5) == 'image') 
		{
			$file = urldecode(substr($filename, 6));
		} 
		elseif (substr(strtolower($filename), 0, 4) == 'file') 
		{
			$file = urldecode(substr($filename, 5));
		}

		//if were on the wiki we need to output files a specific way
		if ($this->active == 'wiki') 
		{
			//get access level for wiki
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'wiki');

			//check to make sure user has access to wiki section
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				JError::raiseError(403, JText::_('COM_GROUPS_NOT_AUTH') . ' ' . $file);
				return;
			}

			//load wiki page from db
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
			$page = new WikiPage($this->database);
			$page->load(JRequest::getVar('pagename'), $group->get('cn') . DS . 'wiki');

			//check specific wiki page access
			if ($page->get('access') == 1 && !in_array($this->juser->get('id'), $group->get('members')) && $authorized != 'admin') 
			{
				JError::raiseError(403, JText::_('COM_GROUPS_NOT_AUTH') . ' ' . $file);
				return;
			}

			//get the config and build base path
			$wiki_config = JComponentHelper::getParams('com_wiki');
			$base_path = $wiki_config->get('filepath') . DS . $page->get('id');
		} 
		elseif ($this->active == 'blog')
		{
			//get access setting of group blog
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'blog');
	
			//make sure user has access to blog
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				JError::raiseError(403, JText::_('COM_GROUPS_NOT_AUTH') . ' ' . $file);
				return;
			}

			//make sure we have a group id of the proper length
			$groupID = Hubzero_Group_Helper::niceidformat($group->get('gidNumber'));

			//buld path to blog folder
			$base_path = $this->config->get('uploadpath') . DS . $groupID . DS . 'blog';
			if (!file_exists(JPATH_ROOT . DS . $base_path . DS . $file)) 
			{
				$base_path = $this->config->get('uploadpath') . DS . $group->get('gidNumber') . DS . 'blog';
			}
		}
		else 
		{
			//get access level for overview or other group pages
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'overview');

			//check to make sure we can access it
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				JError::raiseError(403, JText::_('COM_GROUPS_NOT_AUTH') . ' ' . $file);
				return;
			}

			// Build the path
			$base_path = $this->config->get('uploadpath');
			$base_path .= DS . $group->get('gidNumber');
		}

		// Final path of file
		$file_path = $base_path . DS . $file;

		// Ensure the file exist
		if (!file_exists(JPATH_ROOT . DS . $file_path)) 
		{
			JError::raiseError(404, JText::_('COM_GROUPS_FILE_NOT_FOUND') . ' ' . $file);
			return;
		}

		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename(JPATH_ROOT . DS . $file_path);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		if (!$xserver->serve()) 
		{
			JError::raiseError(404, JText::_('COM_GROUPS_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}
}

