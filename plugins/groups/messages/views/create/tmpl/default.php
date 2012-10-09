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

$group_statuses = array(
	'all' => JText::_('All Group Members'),
	'managers' => JText::_('All Group Managers'),
	'invitees' => JText::_('All Group Invitees'),
	'applicants' => JText::_('All Group Applicants')
);

$role_id = JRequest::getVar('role_id');
if($role_id) {
	foreach($this->member_roles as $role) {
		if($role['id'] == $role_id) {
			$role_name = $role['role'];
			break;
		}
	}
}
?>
<div class="subject">
	<?php if(!$this->no_html): ?>
	<ul class="entries-menu">
		<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
		<li><a class="active" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
	</ul>
	<br class="clear" />
	<?php endif; ?>
	
	<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
		<fieldset class="hub-mail">
			<div class="cont" style="background:#fff url('<?php echo $this->params->get('stamp_logo'); ?>') no-repeat 99% 4%;">
				<h3><?php echo JText::_('Compose Message to Group'); ?></h3>
				<label class="width-65"><?php echo JText::_('GROUP_MESSAGE_USERS'); ?>  <span class="required">Required</span>
					<select name="users[]" id="msg-recipient">
						<optgroup label="Group Status">
							<?php foreach($group_statuses as $val => $name) { ?>
								<?php $sel = ($val == $this->users[0]) ? "selected" : ""; ?> 
								<option <?php echo $sel; ?> value="<?php echo $val; ?>"><?php echo $name; ?></option>
							<?php } ?>
						</optgroup>
						<?php if(count($this->member_roles) > 0) { ?>
							<optgroup label="Group Member Roles">
								<?php foreach($this->member_roles as $role) { ?>
									<?php $sel = ($role['role'] == $role_name) ? "selected" : ""; ?>
									<option <?php echo $sel; ?> value="role_<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
								<?php } ?>
							</optgroup>
						<?php } ?>
						<?php if(count($this->members) > 0) { ?>
							<optgroup label="Group Members">
								<?php foreach($this->members as $m) { ?>
									<?php $u =& JUser::getInstance($m); ?>
									<?php $sel = ($u->get('id') == $this->users[0]) ? "selected" : ""; ?> 
									<option <?php echo $sel; ?> value="<?php echo $u->get('id'); ?>"><?php echo $u->get('name'); ?></option>
								<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
				</label>
				<label>
					<?php echo JText::_('GROUP_MESSAGE_SUBJECT'); ?> <span class="required">Required</span>
					<input type="text" name="subject" id="msg-subject" value="" />
				</label>
				<label>
					<?php echo JText::_('GROUP_MESSAGE'); ?> <span class="required">Required</span>
					<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
				</label>
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('GROUP_MESSAGE_SEND'); ?>" />
				</p>
			<div><!-- /.cont -->
		</fieldset>
		<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="active" value="messages" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="send" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	</form>
</div><!-- // .subject -->

