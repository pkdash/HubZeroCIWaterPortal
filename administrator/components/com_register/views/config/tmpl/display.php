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

JToolBarHelper::title(JText::_('Registration'), 'addedit.png');
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<thead>
				<tr>
					<th><?php echo JText::_('Field/Area'); ?></th>
					<th><?php echo JText::_('Create'); ?></th>
					<th><?php echo JText::_('Proxy'); ?></th>
					<th><?php echo JText::_('Update'); ?></th>
					<th><?php echo JText::_('Edit'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($this->params as $field => $values)
{
	if (substr($field, 0, strlen('registration')) == 'registration') 
	{
		$title = $values[3];
		$value = $values[4];
		
		$create = strtoupper(substr($value, 0, 1));
		$proxy  = strtoupper(substr($value, 1, 1));
		$update = strtoupper(substr($value, 2, 1));
		$edit   = strtoupper(substr($value, 3, 1));

		$field = str_replace('registration', '', $field);
?>
				<tr>
					<td class="key"><?php echo $title; ?></td>
					<td>
						<select name="settings[<?php echo $field; ?>][create]">
							<option value="O"<?php if ($create == 'O') { echo ' selected="selected"'; }?>><?php echo JText::_('Optional'); ?></option>
							<option value="R"<?php if ($create == 'R') { echo ' selected="selected"'; }?>><?php echo JText::_('Required'); ?></option>
							<option value="H"<?php if ($create == 'H') { echo ' selected="selected"'; }?>><?php echo JText::_('Hide'); ?></option>
							<option value="U"<?php if ($create == 'U') { echo ' selected="selected"'; }?>><?php echo JText::_('Read only'); ?></option>
						</select>
					</td>
					<td>
						<select name="settings[<?php echo $field; ?>][proxy]">
							<option value="O"<?php if ($proxy == 'O') { echo ' selected="selected"'; }?>><?php echo JText::_('Optional'); ?></option>
							<option value="R"<?php if ($proxy == 'R') { echo ' selected="selected"'; }?>><?php echo JText::_('Required'); ?></option>
							<option value="H"<?php if ($proxy == 'H') { echo ' selected="selected"'; }?>><?php echo JText::_('Hide'); ?></option>
							<option value="U"<?php if ($proxy == 'U') { echo ' selected="selected"'; }?>><?php echo JText::_('Read only'); ?></option>
						</select>
					</td>
					<td>
						<select name="settings[<?php echo $field; ?>][update]">
							<option value="O"<?php if ($update == 'O') { echo ' selected="selected"'; }?>><?php echo JText::_('Optional'); ?></option>
							<option value="R"<?php if ($update == 'R') { echo ' selected="selected"'; }?>><?php echo JText::_('Required'); ?></option>
							<option value="H"<?php if ($update == 'H') { echo ' selected="selected"'; }?>><?php echo JText::_('Hide'); ?></option>
							<option value="U"<?php if ($update == 'U') { echo ' selected="selected"'; }?>><?php echo JText::_('Read only'); ?></option>
						</select>
					</td>
					<td>
						<select name="settings[<?php echo $field; ?>][edit]">
							<option value="O"<?php if ($edit == 'O') { echo ' selected="selected"'; }?>><?php echo JText::_('Optional'); ?></option>
							<option value="R"<?php if ($edit == 'R') { echo ' selected="selected"'; }?>><?php echo JText::_('Required'); ?></option>
							<option value="H"<?php if ($edit == 'H') { echo ' selected="selected"'; }?>><?php echo JText::_('Hide'); ?></option>
							<option value="U"<?php if ($edit == 'U') { echo ' selected="selected"'; }?>><?php echo JText::_('Read only'); ?></option>
						</select>
					</td>
				</tr>
<?php
	}
}
?>
			</tbody>
		</table>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
		
		<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>