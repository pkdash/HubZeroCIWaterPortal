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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelper::getActions('comment');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Wiki') . '</a>', 'wiki.png');

if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList('', 'delete');
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
JHTML::_('behavior.tooltip');

$dateFormat = '%d %b %Y';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = 0;
}

ximport('Hubzero_View_Helper_Html');
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

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="7">
					(<?php echo $this->escape(stripslashes($this->entry->pagename)); ?>) &nbsp; <?php echo $this->escape(stripslashes($this->entry->title)); ?>
				</th>
			</tr>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Comment', 'content', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Author', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Anonymous', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'State', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$config	=& JFactory::getConfig();
$now	=& JFactory::getDate();
$db		=& JFactory::getDBO();

$nullDate = $db->getNullDate();
$rows = $this->rows;
for ($i=0, $n=count($rows); $i < $n; $i++)
{
	$row =& $rows[$i];

	if (!$row->anonymous) 
	{
		$cimg2  = 'publish_x.png';
		$calt2  = JText::_('Off');
		$cls2   = 'unpublish';
		$state2 = 1;
	} 
	else 
	{
		$cimg2  = 'publish_g.png';
		$calt2  = JText::_('On');
		$cls2   = 'publish';
		$state2 = 0;
	}
	
	switch ($row->status) 
	{
		case 2:
			$cimg1  = 'disabled.png';
			$calt1  = JText::_('Trashed');
			$cls1   = 'trash';
			$state1 = 'publish';
		break;

		case 1:
			$cimg1  = 'publish_x.png';
			$calt1  = JText::_('Abusive');
			$cls1   = 'unpublish';
			$state1 = 'publish';
		break;

		case 0:
		default:
			$cimg1  = 'publish_g.png';
			$calt1  = JText::_('Published');
			$cls1   = 'publish';
			$state1 = 'unpublish';
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
					<?php echo $row->treename; ?>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<?php echo Hubzero_View_Helper_Html::shortenText($this->escape(stripslashes($row->ctext)), 90, 0); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo Hubzero_View_Helper_Html::shortenText($this->escape(stripslashes($row->ctext)), 90, 0); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->name)); ?>
				</td>
				<td>
					<a class="state <?php echo $cls2; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=anonymous&amp;state=<?php echo $state2; ?>&amp;id[]=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $cimg2; ?>" width="16" height="16" border="0" alt="<?php echo $calt2; ?>" /><?php } else { echo $calt2; } ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $cls1; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $state1; ?>&amp;id[]=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $cimg1; ?>" width="16" height="16" border="0" alt="<?php echo $calt1; ?>" /><?php } else { echo $calt1; } ?></span>
					</a>
				</td>
				<td>
					<time datetime="<?php echo $row->created; ?>">
						<?php echo JHTML::_('date', $row->created, $dateFormat, $tz) ?>
					</time>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
