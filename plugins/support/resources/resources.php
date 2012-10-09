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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Support plugin class for com_resources entries
 */
class plgSupportResources extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Short description for 'getReportedItem'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $parent Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if ($category != 'review' && $category != 'reviewcomment') 
		{
			return null;
		}

		if ($category == 'review') 
		{
			$query  = "SELECT rr.id, rr.comment as text, rr.user_id as author, 
						NULL as subject, 'review' as parent_category, rr.anonymous as anon 
						FROM #__resource_ratings AS rr 
						WHERE rr.id=" . $refid;
		} 
		else if ($category == 'reviewcomment') 
		{
			$query  = "SELECT rr.id, rr.comment as text, rr.added_by as author, 
						NULL as subject, rr.category as parent_category, rr.anonymous as anon 
						FROM #__comments AS rr 
						WHERE rr.id=" . $refid;
		}

		$database =& JFactory::getDBO();
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows) 
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_resources&id=' . $parent . '&active=reviews') : '';
			}
		}
		return $rows;
	}

	/**
	 * Looks up ancestors to find root element
	 * 
	 * @param      integer $parentid ID to check for parents of
	 * @param      string  $category Element type (determines table to look in)
	 * @return     integer
	 */
	public function getParentId($parentid, $category)
	{
		ximport('Hubzero_Comment');

		$database =& JFactory::getDBO();
		$refid = $parentid;

		if ($category == 'reviewcomment') 
		{
			$pdata = $this->parent($parentid);
			$category = $pdata->category;
			$refid = $pdata->referenceid;

			if ($pdata->category == 'reviewcomment') 
			{
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->category;
				$refid = $pdata->referenceid;

				if ($pdata->category == 'reviewcomment') 
				{
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->category;
					$refid = $pdata->referenceid;
				}
			}
		}

		if ($category == 'review') 
		{
			$database->setQuery("SELECT resource_id FROM #__resource_ratings WHERE id=" . $refid);
		 	return $database->loadResult();
		}
	}

	/**
	 * Retrieve parent element
	 * 
	 * @param      integer $parentid ID of element to retrieve
	 * @return     object
	 */
	public function parent($parentid)
	{
		$database =& JFactory::getDBO();

		$parent = new Hubzero_Comment($database);
		$parent->load($parentid);

		return $parent;
	}

	/**
	 * Returns the appropriate text for category
	 * 
	 * @param      string  $category Element type (determines text)
	 * @param      integer $parentid ID of element to retrieve
	 * @return     string
	 */
	public function getTitle($category, $parentid)
	{
		if ($category != 'review' && $category != 'reviewcomment') 
		{
			return null;
		}

		switch ($category)
		{
			case 'review':
				return JText::sprintf('Review of resource #%s', $parentid);
         	break;

			case 'reviewcomment':
				return JText::sprintf('Comment to review of resource #%s', $parentid);
         	break;
		}
	}

	/**
	 * Removes an item reported as abusive
	 * 
	 * @param      integer $referenceid ID of the database table row
	 * @param      integer $parentid    If the element has a parent element
	 * @param      string  $category    Element type (determines table to look in)
	 * @param      string  $message     Message to user to append to
	 * @return     string
	 */
	public function deleteReportedItem($referenceid, $parentid, $category, $message)
	{
		if ($category != 'review' && $category != 'reviewcomment') 
		{
			return null;
		}

		$database =& JFactory::getDBO();

		switch ($category)
		{
			case 'review':
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'review.php');

				// Delete the review
				$review = new ResourcesReview($database);
				$review->delete($referenceid);

				// Recalculate the average rating for the parent resource
				$resource = new ResourcesResource($database);
				$resource->load($parentid);
				$resource->calculateRating();
				if (!$resource->store()) 
				{
					$this->setError($resource->getError());
					return false;
				}

				$message .= JText::sprintf('This is to notify you that your review to resource #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;

			case 'reviewcomment':
				ximport('Hubzero_Comment');

				$comment = new Hubzero_Comment($database);
				$comment->load($referenceid);
				$comment->state = 2;
				if (!$comment->store()) 
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= JText::sprintf('This is to notify you that your comment on review for resource #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
		}

		return $message;
	}
}
