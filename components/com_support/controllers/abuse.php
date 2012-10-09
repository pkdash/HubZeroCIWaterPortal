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
 * Report items as abusive
 */
class SupportControllerAbuse extends Hubzero_Controller
{
	/**
	 * Method to set the document path
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=index'
			);
		}
		$pathway->addItem(
			JText::_(strtoupper('REPORT_ABUSE')),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=reportabuse'
		);
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		$this->_title .= ': ' . JText::_(strtoupper('REPORT_ABUSE'));

		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Reports an item as abusive
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Login required
		if ($this->juser->get('guest')) 
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller), 'server'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return)
			);
			return;
		}

		$this->view->setLayout('display');
		$this->view->juser = $this->juser;

		// Incoming
		$this->view->refid = JRequest::getInt('id', 0);
		$this->view->parentid = JRequest::getInt('parent', 0);
		$this->view->cat = JRequest::getVar('category', '');

		// Check for a reference ID
		if (!$this->view->refid) 
		{
			JError::raiseError(404, JText::_('REFERENCE_ID_NOT_FOUND'));
			return;
		}

		// Check for a category
		if (!$this->view->cat) 
		{
			JError::raiseError(404, JText::_('CATEGORY_NOT_FOUND'));
			return;
		}

		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Get the search result totals
		$results = $dispatcher->trigger('getReportedItem', array(
			$this->view->refid,
			$this->view->cat,
			$this->view->parentid
		));

		// Check the results returned for a reported item
		$report = null;
		if ($results) 
		{
			foreach ($results as $result)
			{
				if ($result) 
				{
					$this->view->report = $result[0];
				}
			}
		}

		// Ensure we found a reported item
		if (!$this->view->report) 
		{
			$this->setError(JText::_('ERROR_REPORTED_ITEM_NOT_FOUND'));
		}

		// Set the page title
		$this->_buildTitle();
		
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Add the CSS to the template and set the page title
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Save an abuse report and displays a "Thank you" message
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		$email = 0; // turn off

		// Incoming
		$this->view->cat = JRequest::getVar('category', '');
		$this->view->refid = JRequest::getInt('referenceid', 0);
		$this->view->returnlink = JRequest::getVar('link', '');

		// Trim and addslashes all posted items
		$incoming = array_map('trim', $_POST);

		// Initiate class and bind posted items to database fields
		$row = new ReportAbuse($this->database);
		if (!$row->bind($incoming)) 
		{
			JRequest::setVar('referenceid', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		ximport('Hubzero_Filter');
		$row->report     = Hubzero_Filter::cleanXss($row->report);
		$row->report     = nl2br($row->report);
		$row->created_by = $this->juser->get('id');
		$row->created    = date('Y-m-d H:i:s', time());
		$row->state      = 0;

		// Check content
		if (!$row->check()) 
		{
			JRequest::setVar('referenceid', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			JRequest::setVar('referenceid', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Get the search result totals
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();
		$results = $dispatcher->trigger('onReportItem', array(
			$this->view->refid,
			$this->view->cat
		));

		// Send notification email 
		if ($email) 
		{
			$jconfig =& JFactory::getConfig();

			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('REPORTABUSE');
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('REPORTABUSE');

			$message = '';

			$tos = array();

			// Get administration e-mail
			$tos[] = $jconfig->getValue('config.mailfrom');

			// Get the user's e-mail
			$tos[] = $juser->get('email');

			foreach ($tos as $to)
			{
				if (SupportUtilities::checkValidEmail($to)) 
				{
					if (!SupportUtilities::sendEmail($from, $to, $subject, $message)) 
					{
						$this->setError(JText::sprintf('ERROR_FAILED_TO_SEND_EMAIL', $to));
					}
				}
			}
		}

		// Set the page title
		$this->_buildTitle();
		
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}
}
