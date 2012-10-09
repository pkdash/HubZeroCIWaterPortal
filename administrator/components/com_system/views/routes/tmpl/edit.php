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

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('SEF Manager') . ': <small><small>[ ' . $text . ' ]</small></small>', 'config.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
<!--
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	if (form.newurl.value == "") {
		alert("You must provide a URL for the redirection.");
	} else {
		submitform(pressbutton);
	}
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<?php
	if ($this->getError()) {
		echo '<p class="error">' . JText::_('Error:') . ' ' . implode('<br />', $this->getErrors()) . '</p>';
	}
	?>
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="oldurl">New <acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
					<td><input type="text" size="80" name="oldurl" id="oldurl" value="<?php echo $this->row->oldurl; ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="newurl">Old Non-<acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
					<td>
						<input type="text" size="80" name="newurl" id="newurl" value="<?php echo $this->row->newurl; ?>" />
						<p class="info">only relative redirection from the document root <em>without</em> a '/' at the begining</p>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
