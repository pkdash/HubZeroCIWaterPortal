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

include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'reportabuse.php');

/**
 * Short description for 'SupportControllerAbusereports'
 * 
 * Long description (if any) ...
 */
class SupportControllerAbusereports extends Hubzero_Controller
{
	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.reports.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.reports.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['state']  = JRequest::getInt('state', 0);
		$this->view->filters['sortby'] = JRequest::getVar('sortby', 'a.created DESC');

		$model = new ReportAbuse($this->database);

		// Get record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->rows  = $model->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a record
	 *
	 * @return	void
	 */
	public function viewTask()
	{
		JRequest::setVar('hidemainmenu', 1);
		
		// Incoming
		$id = JRequest::getInt('id', 0);
		$cat = JRequest::getVar('cat', '');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);

		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Get the parent ID
		$results = $dispatcher->trigger('getParentId', array(
			$report->referenceid,
			$report->category
		));

		// Check the results returned for a parent ID
		$parentid = 0;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$parentid = $result;
				}
			}
		}

		// Get the reported item
		$results = $dispatcher->trigger('getReportedItem', array(
			$report->referenceid,
			$cat,
			$parentid
		));

		// Check the results returned for a reported item
		$reported = null;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$reported = $result[0];
				}
			}
		}

		// Get the title
		$titles = $dispatcher->trigger('getTitle', array(
			$report->category,
			$parentid
		));

		// Check the results returned for a title
		$title = null;
		if ($titles)
		{
			foreach ($titles as $t)
			{
				if ($t)
				{
					$title = $t;
				}
			}
		}

		$this->view->report = $report;
		$this->view->reported = $reported;
		$this->view->parentid = $parentid;
		$this->view->title = $title;

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Release a record from being marked as abusive
	 *
	 * @return	void
	 */
	public function releaseTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);
		$report->state = 1;
		if (!$report->store())
		{
			JError::raiseError(500, $report->getError());
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('ITEM_RELEASED_SUCCESSFULLY')
		);
	}

	/**
	 * Delete a record
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);
		$parentid = JRequest::getInt('parentid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$email     = 1; // Turn off/on
		$gratitude = 1; // Turn off/on
		$message   = '';

		$note = JRequest::getVar('note', '');

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);

		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Get the reported item
		$results = $dispatcher->trigger('getReportedItem', array(
			$report->referenceid,
			$report->category,
			$parentid
		));

		// Check the results returned for a reported item
		$reported = null;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$reported = $result[0];
				}
			}
		}

		// Remove the reported item and any other related processes that need be performed
		$results = $dispatcher->trigger('deleteReportedItem', array(
			$report->referenceid,
			$parentid,
			$report->category,
			$message
		));

		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$message .= $result;
				}
			}
		}

		// Mark abuse report as deleted	
		$report->state = 2;
		if (!$report->store())
		{
			JError::raiseError(500, $report->getError());
			return;
		}

		$jconfig =& JFactory::getConfig();

		// Notify item owner
		if ($email)
		{
			$juser =& JUser::getInstance($reported->author);

			// Email "from" info
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('SUPPORT');
			$from['email'] = $jconfig->getValue('config.mailfrom');

			// Email subject
			$subject = JText::sprintf('REPORT_ABUSE_EMAIL_SUBJECT',$jconfig->getValue('config.sitename'));

			// Build the email message
			if ($note)
			{
				$message .= "\r\n" . '---------------------------' . "\r\n";
				$message .= $note;
				$message .= "\r\n" . '---------------------------' . "\r\n";
			}
			$message .= "\r\n";
			$message .= JText::_('YOUR_POSTING') . ': ' . "\r\n";
			$message .= $reported->text . "\r\n";
			$message .= '---------------------------' . "\r\n";
			$message .= JText::_('PLEASE_CONTACT_SUPPORT');

			// Send the email
			if (SupportUtilities::checkValidEmail($juser->get('email')))
			{
				SupportUtilities::sendEmail($juser->get('email'), $subject, $message, $from);
			}
		}

		// Check the HUB configuration to see if banking is turned on
		$upconfig =& JComponentHelper::getParams('com_members');
		$banking = $upconfig->get('bankAccounts');

		// Give some points to whoever reported abuse
		if ($banking && $gratitude)
		{
			ximport('Hubzero_Bank');

			$BC = new Hubzero_Bank_Config($this->database);
			$ar = $BC->get('abusereport');  // How many points?
			if ($ar)
			{
				$ruser =& JUser::getInstance($report->created_by);
				if (is_object($ruser) && $ruser->get('id'))
				{
					$BTL = new Hubzero_Bank_Teller($this->database, $ruser->get('id'));
					$BTL->deposit($ar, JText::_('ACKNOWLEDGMENT_FOR_VALID_REPORT'), 'abusereport', $id);
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JTexT::_('ITEM_TAKEN_DOWN')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
