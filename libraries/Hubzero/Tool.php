<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author	Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
ximport('Hubzero_Tool_Version');

/**
 * Short description for 'Hubzero_ToolHelper'
 * 
 * Long description (if any) ...
 */
class Hubzero_ToolHelper
{

	/**
	 * Short description for 'iterate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $func Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	static public function iterate($func)
	{
		$db = &JFactory::getDBO();

		if (true)
		{
			$query = "SELECT toolname FROM #__tool;";

			$db->setQuery($query);

			$result = $db->loadResultArray();

			if ($result === false)
			{
				return false;
			}

			foreach($result as $row)
			{
				call_user_func($func, $row);
			}
		}

		return true;
	}
}

/**
 * Hubzero class for tools
 */
class Hubzero_Tool
{
	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	private $id = null;

	/**
	 * Description for 'toolname'
	 * 
	 * @var string
	 */
	private $toolname = null;

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	private $title = null;

	/**
	 * Description for 'version'
	 * 
	 * @var array
	 */
	private $version = array();

	/**
	 * Description for 'registered'
	 * 
	 * @var unknown
	 */
	private $registered = null;

	/**
	 * Description for 'registered_by'
	 * 
	 * @var unknown
	 */
	private $registered_by = null;

	/**
	 * Description for 'state_changed'
	 * 
	 * @var unknown
	 */
	private $state_changed = null;

	/**
	 * Description for 'ticketid'
	 * 
	 * @var unknown
	 */
	private $ticketid = null;

	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	private $published = null;

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	private $state = null;

	/**
	 * Description for 'priority'
	 * 
	 * @var unknown
	 */
	private $priority = null;

	/**
	 * Description for '_list_keys'
	 * 
	 * @var array
	 */
	private $_list_keys = array('version');

	/**
	 * Description for '_updateAll'
	 * 
	 * @var boolean
	 */
	private $_updateAll = false;

	/**
	 * Description for '_propertyattrmap'
	 * 
	 * @var array
	 */
	static $_propertyattrmap = array('toolname' => 'toolName', 'title' => 'cn', 'version' => 'member');

	/**
	 * Description for '_updatedkeys'
	 * 
	 * @var array
	 */
	private $_updatedkeys = array();

	/**
	 * Constructor
	 * 
	 * @return    void
	 */
	private function __construct()
	{
	}

	/**
	 * Short description for 'clear'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 void
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key=>$value)
		{
			if ($key{0} != '_')
			{
				unset($this->$key);

				if (!in_array($key, $this->_list_keys))
				{
					$this->$key = null;
				}
				else
				{
					$this->$key = array();
				}
			}
		}

		$this->_updateAll = false;
		$this->_updatedkeys = array();
	}

	/**
	 * Log a debug message
	 * 
	 * @param     string $msg Message to log
	 * @return    void
	 */
	private function logDebug($msg)
	{
		$xlog = &Hubzero_Factory::getLogger();
		$xlog->logDebug($msg);
	}

	/**
	 * Get the names of all tools
	 * 
	 * @return    array
	 */
	public function getToolNames()
	{
		$db = &JFactory::getDBO();

		$db->setQuery("SELECT toolname FROM #__tool;");
		return $db->loadResultArray();
	}

	/**
	 * Short description for 'toArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $format Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public function toArray()
	{
		$result = array();

		if (true)
		{
			foreach (self::$_propertyattrmap as $key=>$value)
			{
				$current = $this->__get($key);

				$result[$key] = $current;
			}

			return $result;
		}

		return false;
	}

	/**
	 * Short description for 'getInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $instance Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public function getInstance($instance)
	{
		$hztv = new Hubzero_Tool();

		if ($hztv->read($instance) === false)
		{
			return false;
		}

		return $hztv;
	}

	/**
	 * Short description for 'createInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $name Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public function createInstance($name)
	{
		if (empty($name))
		{
			return false;
		}

		$instance = new Hubzero_Tool();

		$instance->toolname = $name;

		if ($instance->create())
		{
			return $instance;
		}

		return false;
	}

	/**
	 * Short description for '_mysql_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function _mysql_create()
	{
		$db = &JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}
		if (is_numeric($this->id))
		{
			$query = "INSERT INTO #__tool (id,toolname,title) VALUES (" . $db->Quote($this->id) .
				"," . $db->Quote($this->toolname) . "," . $db->Quote($this->title) . ");";

			$db->setQuery();

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO #__tool (toolname,title) VALUES (" .
				$db->Quote($this->toolname) . "," . $db->Quote($this->title) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM #__tool WHERE toolname=" .
					$db->Quote($this->toolname) . ";";

				$db->setQuery($query);

				$result = $db->loadResult();

				if ($result == null)
				{
					return false;
				}

				$this->id = $result;
				return true;
			}
			else if ($result !== false)
			{
				$this->id = $db->insertid();
				return true;
			}
		}

		return false;
	}

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function create()
	{
		$result = true;

		if (true)
		{
			$result = $this->_mysql_create();

			if ($result === false)
			{
				$this->_error(__FUNCTION__ . ": MySQL create failed", E_USER_WARNING);
			}
		}

		return $result;
	}

	/**
	 * Short description for '_mysql_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	private function _mysql_read()
	{
		$db = JFactory::getDBO();
		$lazyloading = false;

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->toolname))
		{
			$query = "SELECT id,toolname,title,registered,registered_by,state_changed,ticketid,published,state,priority FROM #__tool WHERE id=" .
				$db->Quote($this->toolname) . ";";
		}
		else
		{
			$query = "SELECT id,toolname,title,registered,registered_by,state_changed,ticketid,published,state,priority FROM #__tool WHERE " .
				" toolname=" . $db->Quote($this->toolname) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
		{
			return false;
		}

		$this->clear();

		foreach ($result as $key=>$value)
		{
			$this->__set($key, $value);
		}

		$this->__unset('version');

		if (!$lazyloading)
		{
			$this->__get('version');
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $toolname Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public function read($toolname = null)
	{
		if (is_null($toolname))
		{
			$toolname = $this->toolname;

			if (!empty($toolname) && !is_string($toolname) && !is_numeric($toolname))
			{
				$this->_error(__FUNCTION__ . ": invalid tool version instance defined",
					E_USER_ERROR);
				die();
			}
		}

		if (!empty($toolname) && !is_string($toolname) && !is_numeric($toolname))
		{
			$this->_error(__FUNCTION__ . ": Argument #1 is not a valid string and not numeric",
				E_USER_ERROR);
			die();
		}

		$result = true;

		if (true)
		{
			$this->clear();
			$this->toolname = $toolname;

			$result = $this->_mysql_read();

			if ($result === false)
			{
				$this->clear();
			}
		}

		return $result;
	}

	/**
	 * Short description for '_mysql_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  boolean $all Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	function _mysql_update($all = false)
	{
		$db = &JFactory::getDBO();
		$xlog = &Hubzero_Factory::getLogger();

		$query = "UPDATE #__tool SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value)
		{
			if (($property{0} == '_') || in_array($property, $this->_list_keys))
			{
				continue;
			}

			if (!$all && !in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			if (!$first)
			{
				$query .= ',';
			}
			else
			{
				$first = false;
			}

			$value = $this->__get($property);

			if ($value === null)
			{
				$query .= "`$property`=NULL";
			}
			else
			{
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

		if ($first == true)
		{
			$query = '';
		}

		$db->setQuery($query);

		if (!empty($query))
		{
			$result = $db->query();

			if ($result === false)
			{
				return false;
			}
		}

		foreach ($this->_list_keys as $property)
		{
			if (!$all && !in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			$aux_table = "#__tool_" . $property;

			$list = $this->__get($property);

			if (!is_null($list) && !is_array($list))
			{
				$list = array($list);
			}

			if (is_array($list) && count($list) > 0)
			{
				$first = true;

				if ($property == 'version')
				{
					$query = "INSERT IGNORE INTO $aux_table (toolid,toolname,instance) VALUES ";
				}
				else
				{
					$query = "REPLACE INTO $aux_table (tool_id, " . $property . ") VALUES ";
				}
				$order = 1;

				foreach ($list as $value)
				{
					if (!$first)
					{
						$query .= ',';
					}

					$first = false;

					$query .= '(' . $db->Quote($this->id) . ',' . $db->Quote($this->toolname) .
						"," . $db->Quote($value) . ')';

					$order ++;
				}
				$xlog->logDebug($query);
				$db->setQuery($query);

				if (!$db->query())
				{
					return false;
				}

			}

			if (!is_array($list) || count($list) == 0)
			{
				if ($property == 'version')
				{
					$query = "DELETE FROM $aux_table WHERE toolid=" . $db->Quote($this->id) . ";";
				}
				else
				{
					$query = "DELETE FROM $aux_table WHERE tool_id=" . $db->Quote($this->id) . ";";
				}
			}
			else
			{
				foreach ($list as $key=>$value)
				{
					$list[$key] = $db->Quote($value);
				}

				$valuelist = implode($list, ",");

				if (empty($valuelist))
				{
					$valuelist = "''";
				}

				if ($property == 'version')
				{
					$query = "DELETE FROM $aux_table WHERE toolid=" . $db->Quote($this->id) .
						" AND instance NOT IN ($valuelist);";
				}
				else
				{
					$query = "DELETE FROM $aux_table WHERE tool_id=" . $db->Quote($this->id) .
						" AND $property NOT IN ($valuelist);";
				}
			}

			$db->setQuery($query);

			if (!$db->query())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Short description for 'sync'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 unknown Return description (if any) ...
	 */
	public function sync()
	{
		$this->_updateAll = true;
		return $this->update();
	}

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function update()
	{
		$result = true;

		if (true)
		{
			$result = $this->_mysql_update($this->_updateAll);

			if ($result === false)
			{
				$this->_error(__FUNCTION__ . ': MySQL update failed', E_USER_WARNING);
			}
		}

		$this->_updateAll = false;
		return $result;
	}

	/**
	 * Short description for '_mysql_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function _mysql_delete()
	{
		if (!isset($this->toolname) && !isset($this->id))
		{
			return false;
		}

		$db = JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->id))
		{
			$db->setQuery("SELECT id FROM #__tool WHERE toolname" . $db->Quote($this->toolname) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM #__tool WHERE id= " . $db->Quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		$db->setQuery("UPDATE #__tool_version SET toolid=NULL WHERE toolid=" . $db->Quote($this->id) . ";");

		$db->query();

		return true;
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function delete()
	{
		$result = true;

		if (true)
		{
			$result = $this->_mysql_delete();

			if ($result === false)
			{
				$this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
			}
		}

		return $result;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, $this->_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this)))
			{
				$db = &JFactory::getDBO();

				if (is_object($db))
				{
					if (in_array($property, array('version')))
					{
						$aux_table = "#__tool_" . $property;

						$query = "SELECT instance FROM $aux_table AS aux WHERE aux.toolid=" .
							$db->Quote($this->id) . " ORDER BY $property" . " ASC;";
					}
					else
					{
						$query = null;
					}

					$db->setQuery($query);

					$result = $db->loadResultArray();

					if ($result !== false)
					{
						$this->$property = (isset($result[0])) ? $result : array();
						$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));
					}
				}
			}
		}

		if (isset($this->$property))
		{
			return $this->$property;
		}

		if (array_key_exists($property, get_object_vars($this)))
		{
			return null;
		}

		$this->_error('Undefined property ' . __CLASS__ . '::$' . $property, E_USER_NOTICE);

		return null;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $property Parameter description (if any) ...
	 * @param	  unknown $value Parameter description (if any) ...
	 * @return	 void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error('Cannot access property ' . __CLASS__ . '::$' . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, $this->_list_keys))
		{
			$this->$property = array_values(array_unique(array_diff((array) $value, array(''))));
		}
		else
		{
			$this->$property = $value;
		}

		if (!in_array($property, $this->_updatedkeys))
		{
			$this->_updatedkeys[] = $property;
		}
	}

	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error('Cannot access property ' . __CLASS__ . '::$' . $property, E_USER_ERROR);
			die();
		}

		return isset($this->$property);
	}

	/**
	 * Short description for '__unset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error('Cannot access property ' . __CLASS__ . '::$' . $property, E_USER_ERROR);
			die();
		}

		$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

		unset($this->$property);
	}

	/**
	 * Short description for '_error'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $message Parameter description (if any) ...
	 * @param	  integer $level Parameter description (if any) ...
	 * @return	 void
	 */
	private function _error($message, $level = E_USER_NOTICE)
	{
		$caller = next(debug_backtrace());

		switch ($level)
		{
			case E_USER_NOTICE:
				echo 'Notice: ';
			break;
			case E_USER_ERROR:
				echo 'Fatal error: ';
			break;
			default:
				echo 'Unknown error: ';
			break;
		}

		echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
	}

	/**
	 * Short description for 'get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $key Parameter description (if any) ...
	 * @return	 unknown Return description (if any) ...
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Short description for 'set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $key Parameter description (if any) ...
	 * @param	  unknown $value Parameter description (if any) ...
	 * @return	 unknown Return description (if any) ...
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Short description for 'add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $key Parameter description (if any) ...
	 * @param	  array $value Parameter description (if any) ...
	 * @return	 void
	 */
	public function add($key = null, $value = array())
	{
		$this->__set($key, array_merge($this->__get($key), (array) $value));
	}

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $key Parameter description (if any) ...
	 * @param	  array $value Parameter description (if any) ...
	 * @return	 void
	 */
	public function remove($key = null, $value = array())
	{
		$this->__set($key, array_diff($this->__get($key), (array) $value));
	}

	/**
	 * Short description for 'getCurrentVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 unknown Return description (if any) ...
	 */
	public function getCurrentVersion()
	{
		return Hubzero_Tool_VersionHelper::getCurrentToolVersion($this->id);
	}

	/**
	 * Short description for 'getDevelopmentVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 unknown Return description (if any) ...
	 */
	public function getDevelopmentVersion()
	{
		return Hubzero_Tool_VersionHelper::getDevelopmentToolVersion($this->id);
	}

	/**
	 * Short description for 'getRevision'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $revision Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	public function getRevision($revision = 'dev')
	{
		return Hubzero_Tool_VersionHelper::getToolRevision($this->id, $revision);
	}

	/**
	 * Short description for 'getDevelopmentGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function getDevelopmentGroup()
	{
		$db = &JFactory::getDBO();

		$query = "SELECT cn FROM #__tool_groups WHERE toolid=" .
			$db->Quote($this->id) . " AND role='1';";

		$db->setQuery($query);

		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Short description for 'unpublishVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $instance Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public function unpublishVersion($instance)
	{
		$db = &JFactory::getDBO();

		if (empty($this->toolname))
		{
			return false;
		}

		if (empty($instance))
		{
			return false;
		}

		$query = "SELECT id FROM #__tool_version AS v WHERE v.toolname=" .
			$db->Quote($this->toolname) . " AND v.instance=" . $db->Quote($instance) .
			" ORDER BY v.revision DESC LIMIT 1";

		$db->setQuery($query);

		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		$hzvt = Hubzero_Tool_Version::getInstance($result);

		if (empty($hzvt))
		{
			return false;
		}

		$hzvt->state = 0;
		$hzvt->update();
		return true;
	}

	/**
	 * Short description for 'unpublishAllVersions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 boolean Return description (if any) ...
	 */
	public function unpublishAllVersions()
	{
		$db = &JFactory::getDBO();

		if (empty($this->toolname))
		{
			return false;
		}

		$query = "SELECT id FROM #__tool_version AS v WHERE v.toolname=" .
			$db->Quote($this->toolname) . " v.state=1 ORDER BY v.revision DESC LIMIT 1";

		$db->setQuery($query);

		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		foreach ((array) $result as $v)
		{
			$hzvt = Hubzero_Tool_Version::getInstance($v);

			if (empty($hzvt))
			{
				continue;
			}

			$hzvt->state = 0;
			$hzvt->update();
		}

		return true;
	}

	/**
	 * Short description for 'buildQueryLimit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	protected static function buildQueryLimit($filters = array(), $admin = false)
	{
		if (!isset($filters['start']) && !isset($filters['limit']))
		{
			return '';
		}

		$start = '0';
		if (isset($filters['start']) && intval($filters['start']) == $filters['start'] &&
			$filters['start'] > 0)
		{
			$start = $filters['start'];
		}

		$limit = '0';
		if (isset($filters['limit']))
		{
			if ($filters['limit'] == 'all')
			{
				$limit = '18446744073709551615';
			}
			else if (intval($filters['limit']) == $filters['limit'] && $filters['limit'] > 0)
			{
				$limit = $filters['limit'];
			}
		}

		return " LIMIT $start,$limit ";
	}

	/**
	 * Short description for 'buildQuerySort'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	protected static function buildQuerySort($filters = array(), $admin = false)
	{
		if (!isset($filters['sortby']))
		{
			return ' ORDER BY toolname ';
		}

		if (in_array($filters['sortby'], array('id ASC', 'id DESC', 'toolname ASC',
			'toolname DESC', 'title ASC', 'title DESC', 'versions ASC', 'versions DESC',
			'state_changed ASC', 'state_changed DESC', 'registered ASC', 'registered DESC')))
		{
			return " ORDER BY " . $filters['sortby'] . " ";
		}

		return '';
	}

	/**
	 * Short description for 'buildQuerySearch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	protected static function buildQuerySearch($filters = array(), $admin = false)
	{
		$db = &JFactory::getDBO();

		if (empty($filters['search']))
		{
			return '';
		}

		if (empty($filters['search_field']))
		{
			$filters['search_field'] = '';
		}

		$sqlsearch = ' AND ';
		$words = explode(' ', $filters['search']);

		switch ($filters['search_field'])
		{
			case 'toolname':
				$sqlsearch .= " t.toolname=" . $db->Quote($filters['search']) . " ";
			break;

			case 'title':
				$sqlsearch .= " t.title=" . $db->Quote($filters['search']) . " ";
			break;

			case 'id':
				$sqlsearch .= " t.id=" . $db->Quote($filters['search']) . " ";
			break;

			default:
				$sqlsearch .= " (";
				foreach ($words as $word)
				{
					$sqlsearch .= " (t.id LIKE '$word') OR (t.title LIKE '%$word%') OR " .
						" (t.toolname LIKE '%$word%') OR";
				}
				$sqlsearch = substr($sqlsearch, 0, - 3);
				$sqlsearch .= ") ";
			break;
		}

		return $sqlsearch;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	protected static function buildQuery($filters = array(), $admin = false)
	{
		return ' FROM #__tool AS t ';
	}

	/**
	 * Short description for 'getToolCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return	 object Return description (if any) ...
	 */
	static function getToolCount($filters = array(), $admin = false)
	{
		$db = &JFactory::getDBO();

		$query = "SELECT count(DISTINCT t.toolname) FROM #__tool AS t ";
		$query .= ";";
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Short description for 'getToolSummaries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return    array
	 */
	static function getToolSummaries($filters = array(), $admin = false)
	{
		$db = &JFactory::getDBO();

		$query = "SELECT t.id,t.toolname,t.title,count(v.revision) as versions,t.registered," .
			" t.state_changed,t.state FROM #__tool as t, " . "#__tool_version as v " .
			" where t.id=v.toolid ";
		$query .= self::buildQuerySearch($filters, $admin);
		$query .= " GROUP BY t.toolname ";
		$query .= self::buildQuerySort($filters, $admin);
		$query .= self::buildQueryLimit($filters, $admin);
		$query .= ";";
		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Short description for 'getToolVersionSummaries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  boolean $admin Parameter description (if any) ...
	 * @return    array
	 */
	public function getToolVersionSummaries($filters = array(), $admin = false)
	{
		// id  instance  version revision state

		$db = &JFactory::getDBO();

		$query = "SELECT v.id,v.instance,v.version,v.revision,v.state FROM #__tool_version AS v " .
			" WHERE v.toolid=" . $db->Quote($this->id);
		$query .= self::buildQuerySort($filters, $admin) . self::buildQueryLimit($filters, $admin);
		$query .= ";";
		$db->setQuery($query);
		return $db->loadAssocList();
	}

	/**
	 * Short description for 'getToolContributions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $userid Parameter description (if any) ...
	 * @return    mixed False on error, array on success
	 */
	static public function getToolContributions($userid = null)
	{
		if (empty($userid))
		{
			return false;
		}

		$db = &JFactory::getDBO();

		$sql = "SELECT f.toolname FROM #__tool as f " . "JOIN #__tool_groups AS g ON " .
			" f.id=g.toolid AND g.role=1 " . "JOIN #__xgroups AS xg ON g.cn=xg.cn " .
			"JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber AND uidNumber='$uid' ";

		$this->_db->setQuery($sql);

		return $this->_db->loadResultArray();
	}

	/**
	 * Short description for 'getResourceId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $toolname Parameter description (if any) ...
	 * @param	  unknown $id Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public function getResourceId($toolname = null, $id = null)
	{
		$db = &JFactory::getDBO();

		if (isset($this) && is_a($this,'Hubzero_Tool'))
		{
			$toolname = $this->toolname;
			$id = $this->id;
		}
		else
		{
			if (is_numeric($toolname) && empty($id))
			{
				$id = $toolname;
				$toolname = null;
			}
		}

		if (!is_null($toolname))
		{
			$clause1 = " t.toolname=" . $db->Quote($toolname) . " ";
		}
		else
		{
			$clause1 = '';
		}

		if (!is_null($id))
		{
			$clause2 = " t.id=" . $db->Quote($id) . " ";
		}
		else
		{
			$clause2 = '';
		}

		if (empty($clause1) && empty($clause2))
		{
			return false;
		}
		else if (empty($clause1))
		{
			$clause = " $clause2 ";
		}
		else if (empty($clause2))
		{
			$clause = " $clause1 ";
		}
		else
		{
			$clause = " $clause1 AND $clause2 ";
		}

		$query = 'SELECT r.id FROM #__tool as t LEFT JOIN #__resources as r ON ' .
			' r.alias = t.toolname WHERE ' . "$clause ;";

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Short description for 'validate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array &$tool Parameter description (if any) ...
	 * @param	  array &$err Parameter description (if any) ...
	 * @param	  unknown $id Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	static public function validate(&$tool, &$err, $id)
	{
		$db = &JFactory::getDBO();
		$xlog = &Hubzero_Factory::getLogger();

		$query = "SELECT t.id FROM #__tool AS t WHERE LOWER(t.toolname)=LOWER(" .  $db->Quote($tool['toolname']) . ") ";

		if ($id)
		{
			$query .= " AND id != " . $db->Quote($id);
		}

		$query .= ";";

		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result || (in_array($tool['toolname'], array('test','shortname','hub','tool')) && !$id))
		{
			$err['toolname'] = JText::_('ERR_TOOLNAME_EXISTS');
		}
		else if (preg_match('#^[a-zA-Z0-9]{3,15}$#',$tool['toolname']) == '' && !$id)
		{
			$err['toolname'] = JText::_('ERR_TOOLNAME');
		}

		// Check if repository exists under /apps - added to allow for auto-AddRepo
		jimport('joomla.filesystem.folder');
		if (!$id && (is_dir('/apps/'.strtolower($tool['toolname'])) OR is_dir('/apps/'.$tool['toolname']))) 
		{
			$err['toolname'] = JText::_('ERR_TOOLNAME_EXISTS');
		}

		$query = "SELECT t.id FROM #__tool AS t WHERE LOWER(t.title)=LOWER(" . $db->Quote($tool['title']) . ") ";

		if ($id)
		{
			$query .= " AND id != " . $db->Quote($id);
		}

		$query .= ";";

		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result)
		{
			$err['title'] = JText::_('ERR_TITLE_EXISTS');
		}

		if (empty($tool['title']))
		{
			$err['title'] = JText::_('ERR_TITLE');
		}

		if (empty($tool['description']))
		{
			$err['description'] = JText::_('ERR_DESC');
		}

		if (empty($tool['version']))
		{
			$err['version'] = JText::_('ERR_VERSION_BLANK');
		}
		else if (!preg_match("#^[_0-9a-zA-Z.:-]+$#i", $tool['version']))
		{
			$err['version'] = JText::_('ERR_VERSION_ILLEGAL');
		}

		if (empty($tool['exec']))
		{
			$err['exec'] = JText::_('ERR_EXEC');
		}

		if ($tool['exec']=='@GROUP' && empty($tool['membergroups']))
		{
			$err['membergroups'] = JText::_('ERR_GROUPS_EMPTY');
		}
		else if (empty($tool['membergroups']) or $tool['exec']!='@GROUP')
		{
		}
		else if ($tool['exec']=='@GROUP')
		{
		}

		if (empty($tool['code']))
		{
			$err['code'] = JText::_('ERR_CODE');
		}

		if (empty($tool['wiki']))
		{
			$err['wiki'] = JText::_('ERR_WIKI');
		}

		if (empty($tool['developers']))
		{
			$err['developers'] =  JText::_('ERR_TEAM_EMPTY');
		}
		else
		{
		}

		if (empty($tool['vncGeometryX']) || empty($tool['vncGeometryY']) || preg_match('#[^0-9]#' , $tool['vncGeometryX']) || preg_match('#[^0-9]#' , $tool['vncGeometryY']))
		{
			$err['vncGeometry'] = JText::_('ERR_VNCGEOMETRY');
		}

		if (count($err) > 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'validateVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $newversion Parameter description (if any) ...
	 * @param	  string &$err Parameter description (if any) ...
	 * @param	  unknown $id Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	static public function validateVersion($newversion, &$err, $id)
	{
		$db = &JFactory::getDBO();
		$xlog = &Hubzero_Factory::getLogger();

		$err = '';

		if (empty($newversion))
		{
			$err = JText::_('ERR_VERSION_BLANK');
		}
		else if (preg_match('#^[a-zA-Z0-9]{3,15}$#',$newversion) == '' && !$id)
		{
			$err = JText::_('ERR_VERSION_ILLEGAL');
		}
		else
		{
			$query = "SELECT v.id FROM #__tool AS t, #__tool_version AS v WHERE v.toolid=t.id AND t.id=" . $db->Quote($id) . " AND LOWER(v.version)=LOWER(" . $db->Quote($newversion) . ") AND v.state!='3' LIMIT 1;";

			$db->setQuery($query);

			$result = $db->loadResult();
			$xlog->logDebug("validateVersion($newversion,$id) = $result");
			if (!empty($result))
			{
				$err = JText::_('ERR_VERSION_EXISTS');
			}
		}
		return empty($err);
	}

	/**
	 * Short description for 'validateLicense'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $license Parameter description (if any) ...
	 * @param	  string $code Parameter description (if any) ...
	 * @param	  unknown &$err Parameter description (if any) ...
	 * @return	 integer Return description (if any) ...
	 */
	static public function validateLicense($license, $code, &$err)
	{
		preg_replace('/\[([^]]+)\]/', ' ', $license['text'], -1, $bingo);

		$result = 0;

		if (!$license['text']) 
		{
			$err = JText::_('ERR_LICENSE_EMPTY') ;
		}
		else if ($bingo) 
		{
			$err = JText::_('ERR_LICENSE_DEFAULTS') ;
		}
		else if (!$license['authorize'] && $code=='@OPEN') 
		{
			$err = JText::_('ERR_LICENSE_AUTH_MISSING') ;
		}
		else 
		{
			$result = 1;
		}

		return $result;
	}

	/**
	 * Short description for 'getMyTools'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	 object Return description (if any) ...
	 */
	static public function getMyTools()
	{
		$db = &JFactory::getDBO();
		$sql = "SELECT r.alias, v.toolname, v.title, v.description, v.toolaccess AS access, v.mw, v.instance, v.revision
				FROM #__resources AS r, #__tool_version AS v	
				WHERE r.published=1 
				AND r.type=7 
				AND r.standalone=1 
				AND r.access!=4
				AND r.alias=v.toolname 
				AND v.state=1
				ORDER BY v.title, v.toolname, v.revision DESC";

		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	/**
	 * Short description for 'getToolId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  unknown $toolname Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	static public function getToolId($toolname=NULL)
	{
		$db = &JFactory::getDBO();
		if ($toolname=== NULL) 
		{
			return false;
		}
		$db->setQuery('SELECT id FROM #__tool WHERE toolname="' . $db->Quote($toolname) . '" LIMIT 1');
		return $db->loadResult();
	}

	/**
	 * Short description for 'getToolDevelopers'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $toolid Parameter description (if any) ...
	 * @return	 unknown Return description (if any) ...
	 */
	static public function getToolDevelopers($toolid)
	{
		$db = &JFactory::getDBO();

		$query  = "SELECT m.uidNumber FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		$query .= "WHERE g.toolid = '" . $toolid . "' AND g.role=1 ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Short description for 'getToolGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $toolid Parameter description (if any) ...
	 * @param	  array $groups Parameter description (if any) ...
	 * @return	 array Return description (if any) ...
	 */
	static public function getToolGroups($toolid, $groups = array())
	{
		$db = &JFactory::getDBO();

		$query  = "SELECT DISTINCT g.cn FROM #__tool_groups AS g "; // @FIXME cn should be unique, this was a workaround for a nanohub data bug
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "WHERE g.toolid = '" . $toolid . "' AND g.role=0 ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Returns the list of groups to which the launching of a tool is restricted
	 * 
	 * @FIXME: This should really be a part of the above function, getToolGroups.  The problem seems to be related to the jos_tool_groups table.
	 *		 If there is a tool that was at one point restricted to a group, but it isn't anymore, there remains a jos_tool_groups entry.  
	 *		 The above function will grab that restriction and undoly say that the tool is restricted to a group.  The solution seems to be 
	 *		 removing the jos_tool_groups entry if the tool is changed to no longer be restricted to a group.  At the moment, this function also
	 *		 checks to ensure that toolaccess='@GROUP' from the jos_tool_version table
	 * 
	 * @param	  string $toolid is the tool id
	 * @param	  string $instance is the tool version instance
	 * @return	 array Return list of groups
	 */
	static public function getToolGroupsRestriction($toolid, $instance)
	{
		$db = &JFactory::getDBO();

		$query  = "SELECT tv.toolname, tg.cn ";
		$query .= "FROM #__tool_groups AS tg, #__tool_version AS tv ";
		$query .= "WHERE tg.toolid = tv.toolid AND tg.toolid=" . $toolid . " AND role=0 AND tv.instance='" . $instance ."' AND tv.toolaccess='@GROUP'";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Short description for 'saveTicketId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $toolid Parameter description (if any) ...
	 * @param	  string $ticketid Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	static public function saveTicketId($toolid=NULL, $ticketid=NULL)
	{
		$db = &JFactory::getDBO();
		if ($toolid=== NULL or $ticketid=== NULL) 
		{
			return false;
		}
		$query = "UPDATE #__tool SET ticketid='" . $ticketid . "' WHERE id=" . $toolid;
		$db->setQuery($query);
		if ($db->query()) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Short description for 'getTicketId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  string $toolid Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
 	static public function getTicketId($toolid=NULL)
	{
		$db = &JFactory::getDBO();
		if ($toolid=== NULL) 
		{
			return false;
		}
		$db->setQuery('SELECT ticketid FROM #__tool WHERE id="' . $toolid . '"');
		return $db->loadResult();
	}

	/**
	 * Short description for 'xbuildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	  array $filters Parameter description (if any) ...
	 * @param	  unknown $admin Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	static public function xbuildQuery($filters, $admin)
	{
		$juser =& JFactory::getUser();

		// get and set record filter
		$filter = ($admin) ? " WHERE f.id!=0": " WHERE f.state!=9";

		switch ($filters['filterby'])
		{
			case 'mine':      $filter .= " AND f.registered_by='" . $juser->get('username') . "' "; break;
			case 'published': $filter .= " AND f.published='1' AND f.state!='9' ";					break;
			case 'dev':       $filter .= " AND f.published='0' AND f.state!='9' AND f.state!='8' "; break;
			case 'all':       $filter .= " ";														break;
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$search = $filters['search'];
			if (intval($search)) 
			{
				$filter .= " AND f.id='%$search%' ";
			}
			else 
			{
				$filter .= " AND LOWER(f.toolname) LIKE '%$search%' ";
			}
		}
		if (!$admin) 
		{
			$filter .= " AND m.uidNumber='" . $juser->get('id') . "' ";
			$sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state, f.registered';
		}
		else 
		{ 
			$sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state_changed DESC'; 
		}

		$query = "#__tool as f "
				."JOIN #__tool_version AS v ON f.id=v.toolid AND v.state=3 "
				."JOIN #__tool_groups AS g ON f.id=g.toolid AND g.cn=CONCAT('app-',f.toolname) AND g.role=1 "
				."JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		if (!$admin) 
		{
			$query .="JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		}
		$query .= "$filter"
				. "\n ORDER BY $sortby";

		return $query;
	}

	/**
	 * Get a list of tools
	 * 
	 * @param     array   $filters Filters to build query from
	 * @param     boolean $admin   Admin access?
	 * @return    array
	 */
	public function getTools($filters=array(), $admin=false)
	{
		$db = &JFactory::getDBO();
		$filter = self::xbuildQuery($filters, $admin);

		$sql = "SELECT f.id, f.toolname, f.registered, f.published, f.state_changed, f.priority, f.ticketid, f.state as state, v.title, v.version, g.cn as devgroup"
				. " FROM $filter";
		if (isset($filters['start']) && isset($filters['limit'])) 
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$db->setQuery($sql);
		return $db->loadObjectList();
	}
}

