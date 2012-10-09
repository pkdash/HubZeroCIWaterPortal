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

$canDo = WishlistHelper::getActions('component');

JToolBarHelper::title('<a href="index.php?option=com_wishlist">' . JText::_('Wishlist Manager') . '</a>', 'wishlist.png');
if ($canDo->get('core.edit.state')) 
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('COM_WISHLIST_SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />
		
		<label for="filter-by"><?php echo JText::_('COM_WISHLIST_FILTERBY'); ?>:</label> 
		<select name="filterby" id="filter-by">
			<option value="all"<?php echo ($this->filters['filterby'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('[ none ]'); ?></option>
			<option value="granted"<?php echo ($this->filters['filterby'] == 'general') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Granted'); ?></option>
			<option value="open"<?php echo ($this->filters['filterby'] == 'group') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Open'); ?></option>
			<option value="accepted"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Accepted'); ?></option>
			<option value="pending"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Pending'); ?></option>
			<option value="rejected"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Rejected'); ?></option>
			<option value="withdrawn"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Withdrawn'); ?></option>
			<option value="deleted"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Deleted'); ?></option>
			<option value="useraccepted"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('User accepted'); ?></option>
			<option value="private"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Private'); ?></option>
			<option value="public"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Public'); ?></option>
			<option value="assigned"<?php echo ($this->filters['filterby'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Assigned'); ?></option>
		</select>
		
		<input type="submit" value="<?php echo JText::_('COM_WISHLIST_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="8">
					(<?php echo $this->escape(stripslashes($this->wishlist->category)); ?>) &nbsp; <?php echo $this->escape(stripslashes($this->wishlist->title)); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_WISH_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_TITLE'), 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_PROPOSED_BY'), 'proposed_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_PROPOSED'), 'proposed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_STATE'), 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_ACCESS'), 'private', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_WISHLIST_COMMENTS'), 'comments', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	switch ($row->status)
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = JText::_('COM_WISHLIST_PUBLISHED');
		break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = JText::_('COM_WISHLIST_TRASHED');
		break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('COM_WISHLIST_UNPUBLISHED');
		break;
	}

	if ($row->private) 
	{
		$color_access = 'style="color: red;"';
		$task_access = 'accessregistered';
		$groupname = 'Private';
	} 
	else 
	{
		$color_access = 'style="color: black;"';
		$task_access = 'accesspublic';
		$groupname = 'Public';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_WISHLIST_EDIT_WISH'); ?>">
						<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
					</a>
<?php } else { ?>
					<span>
						<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->authorname)); ?>
				</td>
				<td>
					<time datetime="<?php echo $row->proposed; ?>"><?php echo $row->proposed; ?></time>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::sprintf('COM_WISHLIST_SET_TASK',$task);?>">
						<span><?php echo $alt; ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; ?>" <?php echo $color_access; ?> title="<?php echo JText::_('COM_WISHLIST_CHANGE_ACCESS'); ?>">
						<?php echo $groupname; ?>
					</a>
<?php } else { ?>
					<span <?php echo $color_access; ?>>
						<?php echo $groupname; ?>
					</span>
<?php } ?>
				</td>
				<td>
					<span class="glyph comment">
						<span><?php echo $this->escape(stripslashes($row->comments)); ?></span>
					</span>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['id']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
