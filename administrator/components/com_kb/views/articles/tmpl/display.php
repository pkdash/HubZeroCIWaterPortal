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

$canDo = KbHelper::getActions('article');

if ($this->filters['orphans']) {
	$ttle = JText::_('COM_KB_ARTICLES').' (orphans)';
} else {
	$ttle = JText::_('COM_KB_ARTICLES');
}

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('COM_KB') . '</a> <span class="sep">&rsaquo;</span> <span>' . $ttle . '</span>', 'kb.png');
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

$juser = JFactory::getUser();
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
		<label><?php echo JText::_('COM_KB_CATEGORY'); ?>:</label>
		<?php
			if ($this->filters['cid']) {
				echo KbHtml::sectionSelect($this->sections, $this->filters['cid'], 'id');
			} else {
				echo KbHtml::sectionSelect($this->sections, $this->filters['id'], 'id');
			}
		?>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
 				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
 				<th><?php echo JHTML::_('grid.sort', JText::_('COM_KB_TITLE'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
 				<th><?php echo JHTML::_('grid.sort', JText::_('COM_KB_PUBLISHED'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
 				<th><?php echo JHTML::_('grid.sort', JText::_('COM_KB_CATEGORY'), 'section', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
 				<th><?php echo JText::_('COM_KB_VOTES'); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$database =& JFactory::getDBO();
//$sc = new SupportComment($database);
$st = new KbTags($database);

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	switch ($row->state)
	{
		case '1':
			$class = 'publish';
			$task = 'unpublish';
			$alt = JText::_('COM_KB_PUBLISHED');
			break;
		case '2':
			$class = 'expire';
			$task = 'publish';
			$alt = JText::_('COM_KB_TRASHED');
			break;
		case '0':
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('COM_KB_UNPUBLISHED');
			break;
	}

	$tags = $st->get_tag_cloud(3, 1, $row->id);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
<?php if ($row->checked_out && $row->checked_out != $juser->get('id')) { ?>
					<span class="checkedout" title="Checked out :: <?php echo $this->escape($row->editor); ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</span>
<?php } else { ?>
	<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_KB_EDIT_ARTICLE'); ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a>
	<?php } else { ?>
					<span>
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</span>
	<?php } ?>
<?php } ?>
<?php if ($tags) { ?>
					<br /><span><?php echo JText::_('COM_KB_TAGS'); ?>: <?php echo $tags; ?></span>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;cid=<?php echo $this->filters['id']; ?>" title="<?php echo JText::sprintf('COM_KB_SET_TASK',$task);?>">
						<span><?php echo $alt; ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape($row->ctitle); echo ($row->cctitle) ? ' ('.$this->escape($row->cctitle).')' : ''; ?>
				</td>
				<td>
					+<?php echo $row->helpful; ?> 
					-<?php echo $row->nothelpful; ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['cid']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
