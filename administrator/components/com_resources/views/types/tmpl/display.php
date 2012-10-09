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

$canDo = ResourcesHelper::getActions('type');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Resources') . '</a>: <small><small>[' . JText::_('Types') . ']</small></small>', 'addedit.png');
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
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="Category"><?php echo JText::_('Category'); ?>:</label> 
		<?php echo ResourcesHtml::selectType($this->cats, 'category', $this->filters['category'], 'Select...', '', '', ''); ?>
	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	<span class="clr"></span>
	
	<table class="adminlist" summary="<?php echo JText::_('A list of resource types and their grouping'); ?>">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Title'), 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Alias'), 'alias', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$cat_title = '';

	foreach ($this->cats as $cat)
	{
		if ($row->category == $cat->id)
		{
			$cat_title = $cat->type;
		}
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
<?php } ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<?php echo $this->escape(stripslashes($row->type)); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->type)); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->alias)); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($cat_title)); ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="sort" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="sort_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>