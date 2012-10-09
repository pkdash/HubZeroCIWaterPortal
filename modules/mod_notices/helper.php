<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * Module class for displaying site wide notices
 */
class modNotices extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Calculate the time left from a date time
	 * 
	 * @param      integer $year   Year
	 * @param      integer $month  Month
	 * @param      integer $day    Day
	 * @param      integer $hour   Hour
	 * @param      integer $minute Minute
	 * @return     array 
	 */
	private function _countdown($year, $month, $day, $hour, $minute)
	{
		$config = JFactory::getConfig();

		// make a unix timestamp for the given date
		$the_countdown_date = mktime($hour, $minute, 0, $month, $day, $year, -1);

		// get current unix timestamp
		$now = time() + ($config->getValue('config.offset') * 60 * 60);

		$difference = $the_countdown_date - $now;
		if ($difference < 0) 
		{
			$difference = 0;
		}

		$days_left = floor($difference/60/60/24);
		$hours_left = floor(($difference - $days_left*60*60*24)/60/60);
		$minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);

		$left = array($days_left, $hours_left, $minutes_left);
		return $left;
	}

	/**
	 * Turn datetime 0000-00-00 00:00:00 to time
	 * 
	 * @param      string $stime Datetime to convert
	 * @return     integer
	 */
	private function _mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs)) 
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Break a timestamp into its parts
	 * 
	 * @param      integer $stime Timestamp
	 * @return     array
	 */
	private function _convert($stime)
	{
		$t = array();
		$t['year']   = date('Y', $stime);
		$t['month']  = date('M', $stime);
		$t['day']    = date('jS', $stime);
		$t['hour']   = date('g', $stime);
		$t['minute'] = date('i', $stime);
		$t['ampm']   = date('A', $stime);
		return $t;
	}

	/**
	 * Show the amoutn of time left
	 * 
	 * @param      array $stime Timestamp
	 * @return     string 
	 */
	private function _timeto($stime)
	{
		if ($stime[0] == 0 && $stime[1] == 0 && $stime[2] == 0) 
		{
			$o  = JText::_('IMMEDIATELY');
		} 
		else 
		{
			$o  = JText::_('IN') . ' ';
			$o .= ($stime[0] > 0) ? $stime[0] . ' ' . JText::_('DAYS') . ', '  : '';
			$o .= ($stime[1] > 0) ? $stime[1] . ' ' . JText::_('HOURS') . ', ' : '';
			$o .= ($stime[2] > 0) ? $stime[2] . ' ' . JText::_('MINUTES')      : '';
		}
		return $o;
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		$database =& JFactory::getDBO();

		// Set today's time and date
		$now = date('Y-m-d H:i:s', time());

		$this->dateFormat = '%Y-%m-%d %H:%M:%S';
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'Y-m-d H:i:s';
			$this->tz = true;
		}

		// Get some initial parameters
		$start = $this->params->get('start_publishing');
		$start = JHTML::_('date', $start, $this->dateFormat, $this->tz);
		$stop  = $this->params->get('stop_publishing');
		$stop  = JHTML::_('date', $stop, $this->dateFormat, $this->tz);

		$this->publish = false;
		if (!$start || $start == '0000-00-00 00:00:00') 
		{
			$this->publish = true;
		} 
		else 
		{
			if ($start <= $now) 
			{
				$this->publish = true;
			} 
			else 
			{
				$this->publish = false;
			}
		}
		if (!$stop || $stop == '0000-00-00 00:00:00') 
		{
			$this->publish = true;
		} 
		else 
		{
			if ($stop >= $now && $this->publish) 
			{
				$this->publish = true;
			} 
			else 
			{
				$this->publish = false;
			}
		}

		$hide = '';
		if ($this->publish && $this->params->get('allowClose', 1))
		{
			// Figure out days left

			// make a unix timestamp for the given date
			$the_countdown_date = $this->_mkt($stop);

			// get current unix timestamp
			$now = time() + (JFactory::getConfig()->getValue('config.offset') * 60 * 60);

			$difference = $the_countdown_date - $now;
			if ($difference < 0) 
			{
				$difference = 0;
			}

			$this->days_left = floor($difference/60/60/24);

			$expires = $now + 60*60*24*$this->days_left;

			if ($hide = JRequest::getVar($this->params->get('moduleid', 'sitenotice'), '', 'get'))
			{
				setcookie($this->params->get('moduleid', 'sitenotice'), 'closed', $expires);
			}
			$hide = JRequest::getVar($this->params->get('moduleid', 'sitenotice'), '', 'cookie');
		}

		// Only do something if the module's time frame hasn't expired
		if ($this->publish && !$hide) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addModuleStylesheet($this->module->module);

			// Get some parameters
			$this->moduleid   = $this->params->get('moduleid');
			$this->alertlevel = $this->params->get('alertlevel');
			$timezone         = $this->params->get('timezone');
			$message          = $this->params->get('message');

			// Convert start time
			$start = $this->_mkt($start);
			$d = $this->_convert($start);
			$time_start = $d['hour'] . ':' . $d['minute'] . ' ' . $d['ampm'] . ', ' . $d['month'] . ' ' . $d['day'] . ', ' . $d['year'];

			// Convert end time
			$stop = $this->_mkt($stop);
			$u = $this->_convert($stop);
			$time_end  = $u['hour'] . ':' . $u['minute'] . ' ' . $u['ampm'] . ', ' . $u['month'] . ' ' . $u['day'] . ', ' . $u['year'];

			// Convert countdown-to-start time
			$d_month   = date('m', $start);
			$d_day     = date('d', $start);
			$d_hour    = date('H', $start);
			$time_left = $this->_countdown($d['year'], $d_month, $d_day, $d_hour, $d['minute']);
			$time_cd_tostart = $this->_timeto($time_left);

			// Convert countdown-to-return time
			$u_month   = date('m', $stop);
			$u_day     = date('d', $stop);
			$u_hour    = date('H', $stop);
			$time_left = $this->_countdown($u['year'], $u_month, $u_day, $u_hour, $u['minute']);
			$time_cd_toreturn = $this->_timeto($time_left);

			// Parse message for tags
			$message = str_replace('<notice:start>', $time_start, $message);
			$message = str_replace('<notice:end>', $time_end, $message);
			$message = str_replace('<notice:countdowntostart>', $time_cd_tostart, $message);
			$message = str_replace('<notice:countdowntoreturn>', $time_cd_toreturn, $message);
			$message = str_replace('<notice:timezone>', $timezone, $message);

			$this->message = $message;

			require(JModuleHelper::getLayoutPath($this->module->module));
		}
	}
}
