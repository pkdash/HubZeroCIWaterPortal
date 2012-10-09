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
?>
<form action="index.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<table class="formed">
		<thead>
			<tr>
				<th><label for="image"><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="tmpl" value="component" />
					<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
					<input type="hidden" name="task" value="upload" />
		
					<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
					<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
				</td>
			</tr>
		</tbody>
	</table>
	
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

	<table class="formed">
		<thead>
			<tr>
				<th colspan="4"><label for="image"><?php echo JText::_('PICTURE'); ?></label></th>
			</tr>
		</thead>
		<tbody>
<?php
	$k = 0;

	if ($this->file && file_exists(JPATH_ROOT . $this->path . DS . $this->file)) 
	{
		ximport('Hubzero_View_Helper_Html');
		$this_size = filesize(JPATH_ROOT . $this->path . DS . $this->file);
		list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . $this->path . DS . $this->file);
?>
			<tr>
				<td rowspan="6">
					<img src="<?php echo '..' . $this->path . DS . $this->file; ?>" alt="<?php echo JText::_('PICTURE'); ?>" id="conimage" />
				</td>
				<td><?php echo JText::_('FILE'); ?>:</td>
				<td><?php echo $this->file; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('SIZE'); ?>:</td>
				<td><?php echo Hubzero_View_Helper_Html::formatsize($this_size); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('WIDTH'); ?>:</td>
				<td><?php echo $width; ?> px</td>
			</tr>
			<tr>
				<td><?php echo JText::_('HEIGHT'); ?>:</td>
				<td><?php echo $height; ?> px</td>
			</tr>
			<tr>
				<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
				<td><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;tmpl=component&amp;task=delete&amp;current=<?php echo $this->file; ?>&amp;id=<?php echo $this->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
			</tr>
<?php } else { ?>
			<tr>
				<td colspan="4">
					<img src="<?php echo '../components/' . $this->option . '/assets/img/nophoto.gif'; ?>" alt="<?php echo JText::_('NO_PICTURE'); ?>" />
					<input type="hidden" name="currentfile" value="" />
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
	<?php echo JHTML::_('form.token'); ?>
</form>