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
<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p class="info"><?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_INVITATION'); ?></legend>

		<label>
			<?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_INVITATIONS'); ?><br />
<?php 
$names = array();
foreach ($this->users as $user)
{
	if(preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $user)) {
		$names[] = $user;
	} else {
		$u =& JUser::getInstance($user);
		$names[] = $u->get('name');
	}
?>
			<input type="hidden" name="users[]" value="<?php echo $user; ?>" />
<?php
}
?>
			<strong><?php echo implode(', ',$names); ?></strong>
		</label>
		<label for="reason">
			<?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_REASON'); ?>
			<textarea name="reason" id="reason" rows="12" cols="50"></textarea>
		</label>
	</fieldset><div class="clear"></div>
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="confirmcancel" />
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>
