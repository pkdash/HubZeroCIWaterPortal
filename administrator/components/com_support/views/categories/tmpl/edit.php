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
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Category' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelcat') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('category').value == '') {
		alert( '<?php echo JText::_('CATEGORY_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="section"><?php echo JText::_('CATEGORY_SECTION'); ?>: <span class="required">*</span></label></td>
					<td>
						<select name="cat[section]" id="section">
<?php
					foreach ($this->sections as $anode)
					{
						$selected = ($anode->txt == $this->row->section)
								  ? ' selected="selected"'
								  : '';
						echo ' <option value="'.$anode->id.'"'.$selected.'>'.stripslashes($anode->txt).'</option>'."\n";
					}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="category"><?php echo JText::_('CATEGORY_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="cat[category]" id="category" value="<?php echo $this->escape($this->row->category); ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="cat[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>