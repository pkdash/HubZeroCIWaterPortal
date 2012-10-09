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

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group btn" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('GROUPS_ALL_GROUPS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=confirm'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p class="info"><?php echo JText::_('GROUPS_JOIN_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('GROUPS_JOIN_HEADER'); ?></legend>

			<?php if ($this->group->get('restrict_msg')) { ?>
				<p class="warning"><?php echo JText::_('NOTE') . ': ' . $this->escape(stripslashes($this->group->get('restrict_msg'))); ?></p>
			<?php } ?>
			
			<label for="reason">
				<?php echo JText::_('GROUPS_JOIN_REASON'); ?>
				<textarea name="reason" id="reason" rows="10" cols="50"></textarea>
			</label>
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="confirm" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		</fieldset>
		<div class="clear"></div>
		<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
