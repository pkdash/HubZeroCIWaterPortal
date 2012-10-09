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

/**
 * Table class for custom plugin parameters
 */
class Hubzero_Plugin_Params extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id        = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $object_id = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $folder    = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $element   = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__plugin_params', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->object_id = intval($this->object_id);
		if (!$this->object_id) 
		{
			$this->setError(JText::_('Entry must have an object ID'));
			return false;
		}

		$this->folder = trim($this->folder);
		if (!$this->folder) 
		{
			$this->setError(JText::_('Entry must have a folder'));
			return false;
		}

		$this->element = trim($this->element);
		if (!$this->element) 
		{
			$this->setError(JText::_('Entry must have an element'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record and binf to $this
	 * 
	 * @param      integer $oid     Object ID (eg, group ID)
	 * @param      string  $folder  Plugin folder
	 * @param      string  $element Plugin name
	 * @return     boolean True on success
	 */
	public function loadPlugin($oid=null, $folder=null, $element=NULL)
	{
		if (!$oid) 
		{
			$oid = $this->object_id;
		}
		if (!$folder) 
		{
			$folder = $this->folder;
		}
		if (!$element) 
		{
			$element = $this->element;
		}
		if (!$oid || !$element || !$folder) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE object_id='$oid' AND folder='$folder' AND element='$element' LIMIT 1");
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get the custom parameters for a plugin
	 * 
	 * @param      integer $oid     Object ID (eg, group ID)
	 * @param      string  $folder  Plugin folder
	 * @param      string  $element Plugin name
	 * @return     object
	 */
	public function getCustomParams($oid=null, $folder=null, $element=null)
	{
		if (!$oid) 
		{
			$oid = $this->object_id;
		}
		if (!$folder) 
		{
			$folder = $this->folder;
		}
		if (!$element) 
		{
			$element = $this->element;
		}
		if (!$oid || !$folder || !$element) 
		{
			return null;
		}

		$this->_db->setQuery("SELECT params FROM $this->_tbl WHERE object_id=$oid AND folder='$folder' AND element='$element' LIMIT 1");
		$result = $this->_db->loadResult();

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$params = new $paramsClass($result);
		return $params;
	}

	/**
	 * Get the default parameters for a plugin
	 * 
	 * @param      string  $folder  Plugin folder
	 * @param      string  $element Plugin name
	 * @return     object
	 */
	public function getDefaultParams($folder=null, $element=null)
	{
		if (!$folder) 
		{
			$folder = $this->folder;
		}
		if (!$element) 
		{
			$element = $this->element;
		}
		if (!$folder || !$element) 
		{
			return null;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$plugin = JPluginHelper::getPlugin($folder, $element);
		$params = new $paramsClass($plugin->params);
		return $params;
	}

	/**
	 * Get the parameters for a plugin
	 * Merges default params and custom params (take precedence)
	 * 
	 * @param      integer $oid     Object ID (eg, group ID)
	 * @param      string  $folder  Plugin folder
	 * @param      string  $element Plugin name
	 * @return     object
	 */
	public function getParams($oid=null, $folder=null, $element=null)
	{
		$rparams = $this->getCustomParams($oid, $folder, $element);
		$params = $this->getDefaultParams($folder, $element);
		$params->merge($rparams);
		return $params;
	}
}

