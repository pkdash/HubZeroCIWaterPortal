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

/**
 * Table class for tool authors
 */
class ToolAuthor extends  JTable
{
	/**
	 * varchar (50)
	 * 
	 * @var string
	 */
	var $toolname     = NULL;

	/**
	 * int (11)
	 * 
	 * @var integer
	 */
	var $revision     = NULL;

	/**
	 * int (11)
	 * 
	 * @var integer
	 */
	var $uid          = NULL;

	/**
	 * int (11)
	 * 
	 * @var integer
	 */
	var $ordering     = NULL;

	/**
	 * int (11)
	 * 
	 * @var integer
	 */
	var $version_id   = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $name         = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $organization = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_authors', 'version_id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->version_id) 
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_VERSIONID'));
			return false;
		}

		if (!$this->uid) 
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_UID'));
			return false;
		}

		$this->toolname = trim($this->toolname);
		if (!$this->toolname) 
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_TOOLNAME'));
			return false;
		}
		if (!$this->revision) 
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_REVISION'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getToolContributions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolContributions($uid)
	{
		if (!$uid) 
		{
			return false;
		}
		$sql = " SELECT f.toolname FROM #__tool as f "
				. "JOIN #__tool_groups AS g ON f.id=g.toolid AND g.role=1 "
				. "JOIN #__xgroups AS xg ON g.cn=xg.cn "
				. "JOIN #__xgroups_managers AS m ON xg.gidNumber=m.gidNumber AND uidNumber='$uid'";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();

	}

	/**
	 * Get the first author's name on a resource
	 * 
	 * @param      integer $rid Resource ID
	 * @return     string
	 */
	public function getFirstAuthor($rid = 0)
	{
		$query  = "SELECT x.name FROM #__xprofiles x ";
		$query .= " JOIN #__author_assoc AS aa ON x.uidNumber=aa.authorid AND aa.subid= " . $rid . " AND aa.subtable='resources' ";
		$query .= " ORDER BY aa.ordering ASC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	/**
	 * Short description for 'getAuthorsDOI'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $rid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getAuthorsDOI($rid = 0)
	{
		$query  = "SELECT x.name FROM #__xprofiles x ";
		$query .= " JOIN #__author_assoc AS aa ON x.uidNumber=aa.authorid AND aa.subid= " . $rid . " AND aa.subtable='resources' ";
		$query .= " ORDER BY aa.ordering ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of the authors on a tool
	 * 
	 * @param      string  $version  Tool version
	 * @param      integer $rid      Resource ID
	 * @param      string  $toolname Tool name
	 * @param      string  $revision Tool revision
	 * @param      array   $authors  Author list
	 * @return     array 
	 */
	public function getToolAuthors($version='', $rid=0, $toolname='', $revision='', $authors=array())
	{
		$juser = &JFactory::getUser();

		if ($version == 'dev' && $rid) 
		{
			$query = "SELECT authorid as uidNumber FROM #__author_assoc WHERE subid= " . $rid . " AND subtable='resources' ORDER BY ordering";
			$this->_db->setQuery($query);
			$authors = $this->_db->loadObjectList();
		}
		else 
		{
			$query  = "SELECT DISTINCT a.uid as uidNumber ";
			$query .= "FROM #__tool_authors as a  ";
			if ($version == 'current' && $toolname) 
			{
				$objV = new ToolVersion($this->_db);
				$rev = $objV->getCurrentVersionProperty ($toolname, 'revision');
				if ($rev) 
				{
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname= '" . $toolname . "' AND a.revision='" . $rev . "'";
				} 
				else 
				{
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname= '" . $toolname . "' AND v.state=1 ORDER BY v.revision DESC";
				}
			}
			else if (is_numeric($version)) 
			{
				$query .= "WHERE a.version_id= '" . $version . "' ORDER BY a.ordering";
			}
			else if ($toolname && $revision) 
			{
				$query .= "WHERE a.toolname = '" . $toolname . "' AND a.revision = '" . $revision . "' ORDER BY a.ordering";
			}
			else if (is_object($version)) 
			{
				$query .= "WHERE a.version_id= '" . $version->id . "' ORDER BY a.ordering";
			}
			else if (is_object($version[0])) 
			{
				$query .= "WHERE a.version_id= '" . $version[0]->id . "' ORDER BY a.ordering";
			}
			else
			{
				return null;
			}

			$this->_db->setQuery($query);
			$authors = $this->_db->loadObjectList();
		}
		return $authors;
	}

	/**
	 * Save a list of authors
	 * 
	 * @param      array   $authors  List of authors to add
	 * @param      string  $version  Tool version
	 * @param      integer $rid      Resource ID
	 * @param      integer $revision Revision number
	 * @param      string  $toolname Tool name
	 * @return     boolean False if errors, True if not
	 */
	public function saveAuthors($authors, $version='dev', $rid=0, $revision=0, $toolname='')
	{
		if (!$rid) 
		{
			return false;
		}
		ximport('Hubzero_User_Profile');

		if ($authors) 
		{
			$authors = ToolsHelperUtils::transform($authors, 'uidNumber');
		}

		$dev_authors = $this->getToolAuthors('dev', $rid);
		$dev_authors = ToolsHelperUtils::transform($dev_authors, 'uidNumber');

		if ($dev_authors && $version == 'dev') 
		{
			// update 
			$to_delete = array_diff($current_authors, $authors);
			if ($to_delete) 
			{
				foreach ($to_delete as $del) 
				{
					$query = "DELETE FROM #__author_assoc  WHERE authorid='" . $del . "' AND subid= " . $rid . " AND subtable='resources'";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}

		// add new authors
		if ($version == 'dev') 
		{
			// development version is updated
			$to_delete = array_diff($dev_authors, $authors);

			$rc = new ResourcesContributor($this->_db);
			$rc->subtable = 'resources';
			$rc->subid = $rid;

			if ($to_delete) 
			{
				foreach ($to_delete as $del) 
				{
					$query = "DELETE FROM #__author_assoc  WHERE authorid='" . $del . "' AND subid= " . $rid . " AND subtable='resources'";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
			// Get the last child in the ordering
			$order = $rc->getLastOrder($rid, 'resources');
			$order = $order + 1; // new items are always last

			foreach ($authors as $authid) 
			{
				// Check if they're already linked to this resource
				$rc->loadAssociation($authid, $rid, 'resources');
				if (!$rc->authorid) 
				{
					$xprofile = new Hubzero_User_Profile();
					$xprofile->load($authid);

					// New record
					$rc->authorid = $authid;
					$rc->ordering = $order;
					$rc->name = addslashes($xprofile->get('name'));
					$rc->organization = addslashes($xprofile->get('organization'));
					$rc->createAssociation();

					$order++;
				}
			}
		}
		else if ($dev_authors) 
		{
			// new version is being published, transfer data from author_assoc
			$i=0;

			foreach ($dev_authors as $authid) 
			{
				// Do we have name/org info in previous version?
				$query  = "SELECT name, organization FROM #__tool_authors ";
				$query .= "WHERE toolname='" . $toolname . "' AND uid='" . $authid . "' AND revision < " . $revision;
				$query .= " AND name IS NOT NULL AND organization IS NOT NULL ";
				$query .= " ORDER BY revision DESC LIMIT 1";
				$this->_db->setQuery($query);
				$info = $this->_db->loadObjectList();
				if ($info) 
				{
					$name         = $info[0]->name;
					$organization = $info[0]->organization;
				}
				else 
				{
					$xprofile = new Hubzero_User_Profile();
					$xprofile->load($authid);

					$name         = $xprofile->get('name');
					$organization = $xprofile->get('organization');
				}

				$query = "INSERT INTO $this->_tbl (toolname, revision, uid, ordering, version_id, name, organization) VALUES ('" . $toolname . "','" . $revision . "','" . $authid . "','" . $i . "', '" . $version . "', '" . addslashes($name) . "', '" . addslashes($organization) . "')";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) 
				{
					return false;
				}
				$i++;
			}
		}

		return true;
	}

	/**
	 * Check the author's name
	 * Ensures the individual name fields are filled in
	 * 
	 * @param      integer $id User ID
	 * @return     void
	 */
	private function _author_check($id)
	{
		$xprofile = Hubzero_User_Profile::getInstance($id);
		if ($xprofile->get('givenName') == '' 
		 && $xprofile->get('middleName') == '' 
		 && $xprofile->get('surname') == '') 
		{
			$bits = explode(' ', $xprofile->get('name'));
			$xprofile->set('surname', array_pop($bits));
			if (count($bits) >= 1) 
			{
				$xprofile->set('givenName', array_shift($bits));
			}
			if (count($bits) >= 1) 
			{
				$xprofile->set('middleName', implode(' ', $bits));
			}
		}
	}
}
