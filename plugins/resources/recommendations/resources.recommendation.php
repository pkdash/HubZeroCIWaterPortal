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
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Comment database class
//----------------------------------------------------------

/**
 * Short description for 'ResourcesRecommendation'
 * 
 * Long description (if any) ...
 */
class ResourcesRecommendation extends JTable
{

	/**
	 * Description for 'fromID'
	 * 
	 * @var unknown
	 */
	var $fromID       = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'toID'
	 * 
	 * @var unknown
	 */
	var $toID         = NULL;  // @var int(11)

	/**
	 * Description for 'contentScore'
	 * 
	 * @var unknown
	 */
	var $contentScore = NULL;  // @var float

	/**
	 * Description for 'tagScore'
	 * 
	 * @var unknown
	 */
	var $tagScore     = NULL;  // @var float

	/**
	 * Description for 'titleScore'
	 * 
	 * @var unknown
	 */
	var $titleScore   = NULL;  // @var float

	/**
	 * Description for 'timestamp'
	 * 
	 * @var unknown
	 */
	var $timestamp    = NULL;  // @var datetime (0000-00-00 00:00:00)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__recommendation', 'fromID', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getResults'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getResults( $filters=array() )
	{
		$query = "SELECT *, (10*titleScore + 5*contentScore+2*tagScore)/(10+5+2) AS rec_score 
		FROM #__recommendation AS rec, #__resources AS r 
		WHERE (rec.fromID ='".$filters['id']."' AND r.id = rec.toID AND r.standalone=1) 
		OR (rec.toID ='".$filters['id']."' AND r.id = rec.fromID AND r.standalone=1) having rec_score > ".$filters['threshold']." 
		ORDER BY rec_score DESC LIMIT ".$filters['limit'];

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>