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

$juser =& JFactory::getUser();

if (!$this->no_html) { 
?>
	<form method="get" action="<?php echo JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=com_members&task=myaccount&active=dashboard'), 'server'); ?>" id="myresources-form" enctype="multipart/form-data">
		<h4>
			<?php echo JText::_('Sort by'); ?>
			<select name="params[sort]" id="myresources-sort">
				<option value="publish_up"<?php if ($this->sort == 'publish_up') { echo ' selected="selected"'; } ?>>Date</option>
				<option value="title"<?php if ($this->sort == 'title') { echo ' selected="selected"'; } ?>>Title</option>
				<option value="usage"<?php if ($this->sort == 'usage') { echo ' selected="selected"'; } ?>>Usage</option>
			</select>
			 &nbsp; <?php echo JText::_('Show'); ?>
			<select name="params[limit]" id="myresources-limit">
				<option value="5"<?php if ($this->limit == 5) { echo ' selected="selected"'; } ?>>5</option>
				<option value="10"<?php if ($this->limit == 10) { echo ' selected="selected"'; } ?>>10</option>
				<option value="20"<?php if ($this->limit == 20) { echo ' selected="selected"'; } ?>>20</option>
				<option value="50"<?php if ($this->limit == 50) { echo ' selected="selected"'; } ?>>50</option>
				<option value="100"<?php if ($this->limit == 100) { echo ' selected="selected"'; } ?>>100</option>
				<option value="all"<?php if ($this->limit == 0) { echo ' selected="selected"'; } ?>>All</option>
			</select>
		</h4>
<?php } ?>
		<div id="myresources-content">
<?php if (!$this->contributions) { ?>
			<p><?php echo JText::_('MOD_MYRESOURCES_NONE_FOUND'); ?></p>
<?php } else {
	ximport('Hubzero_View_Helper_Html');
?>
			<ul class="expandedlist">
<?php
	for ($i=0; $i < count($this->contributions); $i++)
	{
		// Determine css class
		switch ($this->contributions[$i]->published)
		{
			case 1:  $class = 'published';  break;  // published
			case 2:  $class = 'draft';      break;  // draft
			case 3:  $class = 'pending';    break;  // pending
			case 0:  $class = 'deleted';    break;  // pending
		}

		$dateformat = '%d %b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateformat = 'd M Y';
			$tz = true;
		}

		$thedate = JHTML::_('date', $this->contributions[$i]->publish_up, $dateformat, $tz);
?>
				<li class="<?php echo $class; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->contributions[$i]->id); ?>">
						<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->contributions[$i]->title), 40, 0); ?>
					</a>
					<span class="under">
						<?php echo $thedate . ' &nbsp; ' . stripslashes($this->contributions[$i]->typetitle); ?>
					</span>
				</li>
<?php 
	}
?>
			</ul>
<?php 
}
?>
		</div>
<?php if (!$this->no_html) { ?>
		<ul class="module-nav">
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=contributions&area=resources'); ?>">
					<?php echo JText::_('MOD_MYRESOURCES_ALL_PUBLICATIONS'); ?>
				</a>
			</li>
		</ul>
	</form>
<?php } ?>
