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

$canDo = ResourcesHelper::getActions('license');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Resource License') . '</a>: <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	/*var form = document.getElementById('adminForm');
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	var field = document.getElementById('field-title');
	if (field.value == '') {
		alert( 'Type must have a title' );
	} else {
		alert('vff');*/
		submitform( pressbutton );
		return;
	//}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-70 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('RESOURCES_TYPES_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td><input type="text" name="fields[title]" id="field-title" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-name"><?php echo JText::_('Name'); ?>:</label></td>
						<td>
							<input type="text" name="fields[name]" id="field-name" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" /><br />
							<span class="hint"><?php echo JText::_('If no name is provided, one will be generated from the title.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></td>
						<td>
							<input type="text" name="fields[url]" id="field-url" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->url)); ?>" /><br />
							<span class="hint"><?php echo JText::_('URL to the license.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Content'); ?>:<span class="required">*</span></label></td>
						<td><?php 
							$editor =& JFactory::getEditor();
							echo $editor->display('fields[text]', stripslashes($this->row->text), '', '', '45', '10', false);
						?></td>
					</tr>
				</tbody>
			</table>

			<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
<?php if ($this->row->id) { ?>
				<tr>
					<th><?php echo JText::_('Ordering'); ?></th>
					<td><?php echo $this->row->ordering; ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		
		<p><?php echo JText::_('RESOURCES_REQUIRED_EXPLANATION'); ?></p>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>