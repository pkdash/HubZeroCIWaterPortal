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
 * Controller class for citations
 */
class CitationsControllerCitations extends Hubzero_Controller
{
	/**
	 * List citations
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		$this->view->filters = array();

		// Get paging variables
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		// Get filters
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'sort', 
			'created DESC'
		));
		$this->view->filters['search']     = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		)));

		$obj = new CitationsCitation($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		//get the dynamic citation types
		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new citation
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a citation
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$this->view->config = $this->config;

		if (is_object($row))
		{
			$this->view->type = $row;
		}
		else 
		{
			// Incoming - expecting an array id[]=4232
			$id = JRequest::getVar('id', array());
			$id = (is_array($id) && !empty($id)) ? $id[0] : 0;

			// Load the object
			$this->view->row = new CitationsCitation($this->database);
			$this->view->row->load($id);
		}

		//get all citations sponsors
		$cs = new CitationsSponsor($this->database);
		$this->view->sponsors = $cs->getSponsor();

		// Load the associations object
		$assoc = new CitationsAssociation($this->database);

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$this->view->row->id) 
		{
			$this->view->row->uid = $this->juser->get('id');

			// It's new - no associations to get
			$this->view->assocs = array();
			
			//array of sponsors - empty
			$this->view->row_sponsors = array();
		} 
		else 
		{
			// Get the associations
			$this->view->assocs = $assoc->getRecords(array('cid' => $id));
			
			//get sponsors for citation
			$this->view->row_sponsors = $cs->getCitationSponsor($this->view->row->id);
		}

		//get the citations tags
		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$id}
				AND to1.label=''";
		$this->database->setQuery($sql);
		$this->view->tags = $this->database->loadAssocList();

		//get the badges
		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$id}
				AND to1.label='badge'";
		$this->database->setQuery($sql);
		$this->view->badges = $this->database->loadAssocList();

		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$this->view->params = new $paramsClass($this->view->row->params);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display stats for citations
	 * 
	 * @return     void
	 */
	public function statsTask()
	{
		// Load the object
		$row = new CitationsCitation($this->database);
		$this->view->stats = $row->getStats();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a citation
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$citation = JRequest::getVar('citation', array(), 'post');
		$citation = array_map('trim', $citation);
		$exclude = JRequest::getVar('exclude', '', 'post');

		// Bind incoming data to object
		$row = new CitationsCitation($this->database);
		if (!$row->bind($citation)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		//set params
		$cparams = new $paramsClass($this->_getParams($row->id));
		$cparams->set('exclude', $exclude);
		$row->params = $cparams->toString();

		// New entry so set the created date
		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());
		}

		// Check content for missing required data
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Incoming associations
		$arr = JRequest::getVar('assocs', array(), 'post');

		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation($this->database);

			if (!$this->_isempty($a, $ignored)) 
			{
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind($a)) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}

				// Check content
				if (!$assoc->check()) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}

				// Store new content
				if (!$assoc->store()) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}
			} 
			elseif ($this->_isEmpty($a, $ignored) && !empty($a['id'])) 
			{
				// Delete the row
				if (!$assoc->delete($a['id'])) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}
			}
		}

		//save sponsors on citation
		$sponsors = JRequest::getVar('sponsors', array(), 'post');
		$cs = new CitationsSponsor($this->database);
		$cs->addSponsors($row->id, $sponsors);

		//citation tags object
		$ct = new CitationTags($this->database);

		//get the tags
		$tags = trim(JRequest::getVar('tags', ''));

		//get the badges
		$badges = trim(JRequest::getVar('badges', ''));

		//add tags
		$ct->tag_object($this->juser->get("id"), $row->id, $tags, 1, false, "");

		//add badges
		$ct->tag_object($this->juser->get("id"), $row->id, $badges, 1, false, "badge");

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('CITATION_SAVED')
		);
	}

	/**
	 * Check if an array has any values set other than $ignored values
	 * 
	 * @param      array $b       Array to check
	 * @param      array $ignored Values to ignore
	 * @return     boolean True if empty
	 */
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore, $b)) 
			{
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b)) 
		{
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v) 
			{
				$e = false;
			}
		}
		return $e;
	}

	/**
	 * Remove one or more citations
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) 
		{
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation($this->database);
			$assoc = new CitationsAssociation($this->database);
			$author = new CitationsAuthor($this->database);
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords(array('cid'=>$id));
				foreach ($assocs as $a)
				{
					$assoc->delete($a->id);
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords(array('cid'=>$id));
				foreach ($authors as $a)
				{
					$author->delete($a->id);
				}

				// Delete the citation
				$citation->delete($id);

				//citation tags
				$ct = new CitationTags($this->database);
				$ct->remove_all_tags($id);
			}

			$message = JText::_('CITATION_REMOVED');
		} 
		else 
		{
			$message = JText::_('NO_SELECTION');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}

	/**
	 * Get the current citations format
	 * 
	 * @return     void
	 */
	public function getformatTask()
	{
		//get the format being sent via json
		$format = JRequest::getVar('format', 'apa');

		//include citations format class
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

		//new citations format object
		$cf = new CitationFormat();

		//get the default template for the format being passed in
		$format_template = $cf->getDefaultFormat($format);

		//return the template
		echo $format_template;
	}

	/**
	 * Get citation template keys
	 * 
	 * @return     void
	 */
	public function gettemplatekeysTask()
	{
		// include citations format class
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

		// new citations format object
		$cf = new CitationFormat();

		// get the keys
		$keys = $cf->getTemplateKeys();

		// var to hold html data
		$html = '';

		// create row for each key pair
		foreach ($keys as $k => $v) 
		{
			$html .= "<tr><td>{$v}</td><td>{$k}</td></tr>";
		}

		//return html
		echo $html;
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Get the params for a citation
	 *
	 * @param      integer $citation Citation ID
	 * @return     integer
	 */
	private function _getParams($citation)
	{
		$this->database->setQuery("SELECT c.params from #__citations c WHERE id=" . $citation);
		return $this->database->loadResult();
	}
}

