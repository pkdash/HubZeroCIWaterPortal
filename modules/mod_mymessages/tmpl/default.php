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

if ($this->getError()) {
	echo '<p class="error">' . JText::_('MOD_MYMESSAGES_ERROR') . '</p>' . "\n";
} else {
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
<?php if (count($this->rows) <= 0) { ?>
	<p><?php echo JText::_('MOD_MYMESSAGES_NO_MESSAGES'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
	$dateformat = '%d %b %Y %I:%M %p';
	$tz = 0;
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateformat = 'd M Y H:i p';
		$tz = true;
	}
	
	foreach ($this->rows as $row)
	{
		$cls = 'box';
		if ($row->actionid) 
		{
			$cls = 'actionitem';
		}
		if ($row->component == 'support' || $row->component == 'com_support') 
		{
			$fg = explode(' ',$row->subject);
			$fh = array_pop($fg);
			$row->subject = implode(' ', $fg);
		}
?>
		<li class="<?php echo $cls; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=messages&msg=' . $row->id); ?>">
				<?php echo stripslashes($row->subject); ?>
			</a>
			<span>
				<span>
					<time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateformat, $tz); ?></time>
				</span>
			</span>
		</li>
<?php
	}
?>
	</ul>
<?php } ?>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=messages'); ?>"><?php echo JText::_('MOD_MYMESSAGES_ALL_MESSAGES'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=messages&task=settings'); ?>"><?php echo JText::_('MOD_MYMESSAGES_MESSAGE_SETTINGS'); ?></a></li>
	</ul>
</div>
<?php } ?>