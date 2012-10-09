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
 * Support Utilities class
 */
class SupportUtilities
{
	/**
	 * Send an email
	 * 
	 * @param      string $email             Address to send to
	 * @param      string $subject           Message subject
	 * @param      string $message           Message to send
	 * @param      array  $from              Who the message is from
	 * @param      array  $replyto           Reply to information
	 * @param      array  $additionalHeaders More headers to apply
	 * @return     integer 1 = success, 0 = failure
	 */
	public function sendEmail($email, $subject, $message, $from, $replyto = '', $additionalHeaders = null)
	{
		if ($from) 
		{
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";

			if ($replyto)
			{
				$headers .= 'Reply-To: ' . $from['name'] .' <'. $replyto . ">\n";
			}
			else
			{
				$headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
			}

			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $from['name'] ."\n";

			if (!is_null($additionalHeaders))
			{
				foreach ($additionalHeaders as $header)
				{
					$headers .= $header['name'] . ': ' . $header['value'] . "\n";
				}
			}

			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return(1);
			}
		}
		return(0);
	}

	/**
	 * Check if a username is valid
	 * 
	 * @param      string $login Username to check
	 * @return     boolean True if valid
	 */
	public function checkValidLogin($login)
	{
		if (preg_match("#^[_0-9a-zA-Z]+$#i", $login)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if an email address is valid
	 * 
	 * @param      string $email Address to check
	 * @return     boolean True if valid
	 */
	public function checkValidEmail($email)
	{
		if (preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $email)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate an array of severities
	 * 
	 * @param      string $severities Comma-separated list
	 * @return     array 
	 */
	public function getSeverities($severities)
	{
		if ($severities) 
		{
			$s = array();
			$svs = explode(',', $severities);
			foreach ($svs as $sv)
			{
				$s[] = trim($sv);
			}
		} 
		else 
		{
			$s = array('critical', 'major', 'normal', 'minor', 'trivial');
		}
		return $s;
	}

	/**
	 * Retrieve and parse incoming filters
	 * 
	 * @return     array
	 */
	public function getFilters()
	{
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Query filters defaults
		$filters = array();
		$filters['search']     = '';
		$filters['status']     = 'open';
		$filters['type']       = 0;
		$filters['owner']      = '';
		$filters['reportedby'] = '';
		$filters['severity']   = 'normal';
		$filters['severity']   = '';

		$filters['sort']       = trim($app->getUserStateFromRequest($this->_option . '.tickets.sort', 'filter_order', 'created'));
		$filters['sortdir']    = trim($app->getUserStateFromRequest($this->_option . '.tickets.sortdir', 'filter_order_Dir', 'DESC'));

		// Paging vars
		$filters['limit']      = $app->getUserStateFromRequest($this->_option . '.tickets.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']      = $app->getUserStateFromRequest($this->_option . '.tickets.limitstart', 'limitstart', 0, 'int');

		// Incoming
		$filters['_find']      = urldecode(trim($app->getUserStateFromRequest($this->_option . '.tickets.find', 'find', '')));
		$filters['_show']      = urldecode(trim($app->getUserStateFromRequest($this->_option . '.tickets.show', 'show', '')));

		// Break it apart so we can get our filters
		// Starting string hsould look like "filter:option filter:option"
		if ($filters['_find'] != '') 
		{
			$chunks = explode(' ', $filters['_find']);
			$filters['_show'] = '';
		} 
		else 
		{
			$chunks = explode(' ', $filters['_show']);
		}

		// Loop through each chunk (filter:option)
		foreach ($chunks as $chunk)
		{
			if (!strstr($chunk, ':')) 
			{
				$chunk = trim($chunk, '"');
				$chunk = trim($chunk, "'");

				$filters['search'] = $chunk;
				continue;
			}

			// Break each chunk into its pieces (filter, option)
			$pieces = explode(':', $chunk);

			// Find matching filters and ensure the vaule provided is valid
			switch ($pieces[0])
			{
				case 'q':
					$pieces[0] = 'search';
					if (isset($pieces[1])) 
					{
						$pieces[1] = trim($pieces[1], '"');  // Remove any surrounding quotes
						$pieces[1] = trim($pieces[1], "'");  // Remove any surrounding quotes
					} 
					else 
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'status':
					$allowed = array('open', 'closed', 'all', 'waiting', 'new');
					if (!in_array($pieces[1], $allowed)) 
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array(
						'submitted' => 0,
						'automatic' => 1,
						'none'      => 2,
						'tool'      => 3
					);
					if (in_array($pieces[1],$allowed)) 
					{
						$pieces[1] = $allowed[$pieces[1]];
					} 
					else 
					{
						$pieces[1] = 0;
					}
				break;
				case 'owner':
				case 'reportedby':
					if (isset($pieces[1])) 
					{
						if ($pieces[1] == 'me') 
						{
							$juser =& JFactory::getUser();
							$pieces[1] = $juser->get('username');
						} 
						else if ($pieces[1] == 'none') 
						{
							$pieces[1] = 'none';
						}
					}
				break;
				case 'severity':
					$allowed = array('critical', 'major', 'normal', 'minor', 'trivial');
					if (!in_array($pieces[1], $allowed)) 
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
			}

			$filters[$pieces[0]] = (isset($pieces[1])) ? $pieces[1] : '';
		}

		// Return the array
		return $filters;
	}
}

