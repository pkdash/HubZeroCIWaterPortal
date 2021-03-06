<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = ForumHelper::getActions('category');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Forums') . '</a>', 'forum.png');
if ($canDo->get('core.admin')) {
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state')) {
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) {
	JToolBarHelper::addNew();
}
if ($canDo->get('core.delete')) {
	JToolBarHelper::deleteList();
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label><?php echo JText::_('Category:'); ?></label> 
		<select name="category_id" id="field-category_id" onchange="document.adminForm.submit( );">
			<option value="-1"><?php echo JText::_('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
<?php
	foreach ($this->sections as $group => $sections)
	{
?>
			<optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
<?php
			foreach ($sections as $section)
			{
?>
				<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->title)); ?>">
<?php
				foreach ($section->categories as $category)
				{
?>
					<option value="<?php echo $category->id; ?>"<?php if ($this->filters['category_id'] == $category->id) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($category->title)); ?></option>
<?php
				}
?>
				</optgroup>
<?php
			}
?>
			</optgroup>
<?php
	}
?>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
<?php if (is_object($this->thread)) { ?>
		<caption><?php echo JText::_('Thread') . ': ' . $this->escape(stripslashes($this->thread->title)); ?></caption>
<?php } ?>
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'State', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Group', 'group_alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Creator', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->results)
{
	$k = 0;
	for ($i=0, $n=count( $this->results ); $i < $n; $i++) 
	{
		$row =& $this->results[$i];
		switch ($row->state) 
		{
			case '2':
				$task = 'publish';
				$img = 'disabled.png';
				$alt = JText::_('Trashed');
				$cls = 'trash';
			break;
			case '1':
				$task = 'unpublish';
				$img = 'publish_g.png';
				$alt = JText::_('Published');
				$cls = 'publish';
			break;
			case '0':
			default:
				$task = 'publish';
				$img = 'publish_x.png';
				$alt = JText::_('Unpublished');
				$cls = 'unpublish';
			break;
		}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;category_id=<?php echo $this->filters['category_id']; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $cls; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<span class="group">
						<?php echo $this->escape($row->group_alias); ?>
					</span>
				</td>
				<td>
					<span class="creator">
						<span><?php echo $this->escape($row->created_by); ?></span>
					</span>
				</td>
				<td>
					<span class="created">
						<span><?php echo $row->created; ?></span>
					</span>
				</td>
			</tr>
<?php
		$k = 1 - $k;
	}
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="parent" value="<?php echo $this->filters['parent']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>