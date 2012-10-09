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

/**
 * Short description for 'modDashboard'
 * 
 * Long description (if any) ...
 */
class modDashboard
{

	/**
	 * Description for '_attributes'
	 * 
	 * @var array
	 */
	private $_attributes = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @param      unknown $module Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_attributes[$property]))
		{
			return $this->_attributes[$property];
		}
	}

	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$mosConfig_bankAccounts = 0;
		$database =& JFactory::getDBO();

		$jconfig = JFactory::getConfig();
		$upconfig =& JComponentHelper::getParams('com_members');
		$banking =  $upconfig->get('bankAccounts');
		$sitename = $jconfig->getValue('config.sitename');

		$threemonths 	= date( 'Y-m-d H:i:s', time() - (92 * 24 * 60 * 60));
		$onemonth 		= date( 'Y-m-d H:i:s', time() - (30 * 24 * 60 * 60) );

		if($banking) {
			// get new store orders
			$database->setQuery( "SELECT count(*) FROM #__orders WHERE status=0");
			$orders = $database->loadResult();
		}

		// get open support tickets over 3 months old
		/*$sql = "SELECT count(*) FROM #__support_tickets WHERE status=1 AND created < '".$threemonths."' AND section!=2 AND type=0";
		$database->setQuery($sql);
		$oldtickets = $database->loadResult();
		
		// get unassigned support tickets
		$sql = "SELECT count(*) FROM #__support_tickets WHERE status=0 AND section!=2 AND type=0 AND (owner is NULL OR owner='') AND report != ''";
		$database->setQuery($sql);
		$newtickets = $database->loadResult();*/

		// get abuse reports
		$sql = "SELECT count(*) FROM #__abuse_reports WHERE state=0";
		$database->setQuery($sql);
		$reports = $database->loadResult();

		// get pending resources
		$sql = "SELECT count(*) FROM #__resources WHERE published=3";
		$database->setQuery($sql);
		$pending = $database->loadResult();

		// get contribtool entries requiring admin attention
		$sql = "SELECT count(*) FROM #__tool AS t JOIN jos_tool_version as v ON v.toolid=t.id AND v.mw='narwhal' AND v.state=3  WHERE t.state=1 OR t.state=3 OR t.state=5 OR t.state=6";
		$database->setQuery($sql);
		$contribtool = $database->loadResult();

		// get recent quotes
		$sql = "SELECT count(*) FROM #__feedback WHERE date > '".$onemonth."'";
		$database->setQuery($sql);
		$quotes = $database->loadResult();

		// get wishes from main wishlist - to come
		$wishes = 0;

		// Check if component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_wishlist' AND enabled=1" );
		$found = $database->loadResult();

		if($found) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
			$obj = new Wishlist( $database );
			$objWish = new Wish( $database );
			$juser 	  =& JFactory::getUser();

			// Check if main wishlist exists, create one if missing
			$mainlist = $obj->get_wishlistID(1, 'general');
			if(!$mainlist) {
				$mainlist = $obj->createlist('general', 1);
			}
			$filters = array('filterby'=>'pending', 'sortby'=>'date');
			$wishes = $objWish->get_wishes($mainlist, $filters, 1, $juser);
			$wishes = count($wishes);
		}

		// Get styles
		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/' . $this->module->module . '/' . substr($this->module->module, 4). '.css');

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
