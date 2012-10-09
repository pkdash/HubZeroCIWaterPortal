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
 * Short description for 'SupportControllerCategories'
 * 
 * Long description (if any) ...
 */
class SupportControllerCategories extends Hubzero_Controller
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

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.categories.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.categories.limitstart',
			'limitstart',
			0,
			'int'
		);

		$model = new SupportCategory($this->database);

		// Record count
		$this->view->total = $model->getCount($this->view->filters);

		// Fetch results
		$this->view->rows  = $model->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) {
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		if (is_object($edit))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getInt('id', 0);

			// Initiate database class and load info
			$this->view->row = new SupportCategory($this->database);
			$this->view->row->load($id);

			// Set action
			if (!$this->view->row->id)
			{
				$this->view->row->category = '';
				$this->view->row->section = 1;
			}
		}

		// Get support sections
		$ss = new SupportSection($this->database);
		$this->view->sections = $ss->getSections();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim and addslashes all posted items
		$cat = JRequest::getVar('cat', array(), 'post');
		$cat = array_map('trim', $cat);

		// Initiate class and bind posted items to database fields
		$row = new SupportCategory($this->database);
		if (!$row->bind($cat)) {
			JError::raiseError(500, $row->getError());
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->category = trim($row->category);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::_('CATEGORY_SUCCESSFULLY_SAVED');
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids))
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
			$this->_message = JText::_('SUPPORT_ERROR_SELECT_CATEGORY_TO_DELETE');
			return;
		}

		foreach ($ids as $id)
		{
			// Delete message
			$cat = new SupportCategory($this->database);
			$cat->delete(intval($id));
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::sprintf('CATEGORY_SUCCESSFULLY_DELETED', count($ids));
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
}
