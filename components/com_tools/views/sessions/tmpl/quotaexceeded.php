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

$juser =& JFactory::getUser();
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('COM_TOOLS_QUOTAEXCEEDED'); ?></h2>
</div><!-- / #content-header -->

<?php if ($this->config->get('access-manage-session')) { ?>
<div id="sub-menu">
	<ul>
		<li id="sm-1"<?php if ($this->active == '') { echo ' class="active"'; } ?>><a class="tab" rel="mysessions" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=quotaexceeded'); ?>"><span>My Sessions</span></a></li>
		<li id="sm-2"<?php if ($this->active == 'all') { echo ' class="active"'; } ?>><a class="tab" rel="allsessions" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=quotaexceeded&active=all'); ?>"><span>All Sessions</span></a></li>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->
<?php } ?>

<div class="main section<?php if ($this->config->get('access-manage-session') && $this->active == 'all') { echo ' hide'; } else { echo ''; }?>" id="mysessions-section">
	<p class="warning"><?php echo JText::_('COM_TOOLS_ERROR_QUOTAEXCEEDED'); ?></p>
	<table class="sessions" summary="<?php echo JText::_('COM_TOOLS_SESSIONS_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th>Session</th>
				<th>Started</th>
				<th>Last accessed</th>
				<th>Option</th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->sessions) {
	$cls = 'even';
	foreach ($this->sessions as $session)
	{
		$cls = ($cls == 'odd') ? 'even' : 'odd';
?>	
			<tr class="<?php echo $cls; ?>">
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=session&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_RESUME_TITLE'); ?>"><?php echo $session->sessname; ?></a></td>
				<td><?php echo $session->start; ?></td>
				<td><?php echo $session->accesstime; ?></td>
<?php if ($juser->get('username') == $session->username) { ?>
				<td><a class="closetool" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=stop&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_TERMINATE_TITLE'); ?>"><?php echo JText::_('COM_TOOLS_TERMINATE'); ?></a></td>
<?php } else { ?>
				<td><a class="disconnect" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=unshare&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_DISCONNECT_TITLE'); ?>"><?php echo JText::_('COM_TOOLS_DISCONNECT'); ?></a> <span class="owner"><?php echo JText::_('COM_TOOLS_MY_SESSIONS_OWNER').': '.$session->username; ?></span></td>
<?php } ?>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
</div><!-- / .section -->
<?php if ($this->config->get('access-manage-session')) { ?>
<div class="main section<?php if ($this->active == 'all') { echo ''; } else { echo ' hide'; }?>" id="allsessions-section">
	<table class="sessions" summary="<?php echo JText::_('COM_TOOLS_SESSIONS_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th>Session</th>
				<th>Owner</th>
				<th>Started</th>
				<th>Last accessed</th>
				<th>Option</th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->allsessions) {
	$cls = 'even';
	foreach ($this->allsessions as $session)
	{
		$cls = ($cls == 'odd') ? 'even' : 'odd';
?>	
			<tr class="<?php echo $cls; ?>">
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=session&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_RESUME_TITLE'); ?>"><?php echo $session->sessname; ?></a></td>
				<td><?php echo $session->username; ?></td>
				<td><?php echo $session->start; ?></td>
				<td><?php echo $session->accesstime; ?></td>
				<td><a class="closetool" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=stop&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_TERMINATE_TITLE'); ?>"><?php echo JText::_('COM_TOOLS_TERMINATE'); ?></a></td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
</div><!-- / .section -->
<?php } ?>