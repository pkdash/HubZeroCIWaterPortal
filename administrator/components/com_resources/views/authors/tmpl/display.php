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
defined('_JEXEC') or die('Restricted access');

$canDo = ResourcesHelper::getActions('contributor');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Resources') . '</a>: <small><small>[' . JText::_('Authors') . ']</small></small>', 'addedit.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
/*if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}*/
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
/*if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}*/
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
		<label for="filter_search"><?php echo JText::_('Search'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

		<input type="submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'authorid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Name'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Member'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Resources'), 'total', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="4"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	
	if ($row->authorid > 0) 
	{
		$stickyTask = '1';
		$stickyImg = 'publish_g.png';
		$stickyAlt = JText::_('Member');
		$scls = 'member';
	}
	else 
	{
		$stickyTask = '0';
		$stickyImg = 'publish_x.png';
		$stickyAlt = JText::_('Not member');
		$scls = 'notmember';
	}
	if ($row->authorid > 0 && !$row->name)
	{
		$u = JUser::getInstance($row->authorid);
		$row->name = $u->get('name');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->authorid ?>" onclick="isChecked(this.checked, this);" />
<?php } ?>
				</td>
				<td>
					<?php echo $row->authorid; ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->authorid; ?>" title="<?php echo JText::_('COM_RESOURCES_EDIT_LIST'); ?>">
						<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : JText::_('[unknown]'); ?></span>
					</a>
<?php } else { ?>
					<span>
						<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : JText::_('[unknown]'); ?></span>
					</span>
<?php } ?>
				</td>
				<td>
<?php if ($row->authorid > 0) { ?>
					<a class="state <?php echo $scls; ?>" href="index.php?option=com_members&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->authorid; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $stickyImg;?>" width="16" height="16" border="0" alt="<?php echo $stickyAlt; ?>" /><?php } else { echo $alt; } ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $scls; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $stickyImg;?>" width="16" height="16" border="0" alt="<?php echo $stickyAlt; ?>" /><?php } else { echo $alt; } ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<span>
						<span><?php echo JText::sprintf('%s resource(s)', $this->escape(stripslashes($row->resources))); ?></span>
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
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
