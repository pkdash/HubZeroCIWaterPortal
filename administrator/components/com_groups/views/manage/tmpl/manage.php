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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_GROUPS') . ': <small><small>[' . JText::_('Manage') . ']</small></small>', 'groups.png');
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	// do field validation
	submitform(pressbutton);
}
</script>
<h3><?php echo $this->group->description; ?> (<?php echo $this->group->cn; ?>)</h3>
<?php
if ($this->getError()) {
	echo '<p style="color: #c00;"><strong>'.$this->getError().'</p>';
}
?>
<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" name="adminForm" id="adminForm" method="post">
	<fieldset id="filter-bar">
		<label for="filter-usernames"><?php echo JText::_('COM_GROUPS_ADD_USERNAME'); ?></label> 
		<input type="text" name="usernames" class="input-username" id="filter-usernames" value="" />
		
		<label for="filter-tbl"><?php echo JText::_('COM_GROUPS_TO'); ?></label> 
		<select name="tbl" id="filter-tbl">
			<option value="invitees"><?php echo JText::_('COM_GROUPS_INVITEES'); ?></option>
			<option value="applicants"><?php echo JText::_('COM_GROUPS_APPLICANTS'); ?></option>
			<option value="members" selected="selected"><?php echo JText::_('COM_GROUPS_MEMBERS'); ?></option>
			<option value="managers"><?php echo JText::_('COM_GROUPS_MANAGERS'); ?></option>
		</select>
		
		<input type="submit" name="action" value="<?php echo JText::_('COM_GROUPS_MEMBER_ADD'); ?>" />
	</fieldset>
	<div class="clr"></div>

<?php
	$view = new JView(array(
		'name'   => $this->controller, 
		'layout' => 'table'
	));
	$view->option = $this->option;
	$view->task = $this->task;
	$view->gid = $this->group->cn;
	$view->authorized = $this->authorized;

	$view->groupusers = $this->invitees;
	$view->table = 'invitees';
	$view->display();

	$view->groupusers = $this->pending;
	$view->table = 'pending';
	$view->display();

	$view->groupusers = $this->managers;
	$view->table = 'managers';
	$view->display();

	$view->groupusers = $this->members;
	$view->table = 'members';
	$view->display();
?>
	<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
