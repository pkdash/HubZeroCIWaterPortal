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
JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>: <small><small>[ Services ]</small></small>', 'addedit.png' );
//JToolBarHelper::addNew('newservice','New Service');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
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
		
		<table class="adminlist" summary="<?php echo JText::_('A list of paid/subscription-based HUB services'); ?>">
			<thead>
				<tr>
					<th width="2%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
					<th width="5%"><?php echo JText::_('ID'); ?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
                    <th><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
                    <th><?php echo JHTML::_('grid.sort', JText::_('Status'), 'status', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
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
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
				<tr class="<?php echo "row$k"; ?>">
					<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
					<td><?php echo $row->id; ?></td>
                    <td><?php echo $this->escape($row->title); ?></td>
					<td><?php echo $this->escape($row->category); ?></td>
                    <td>
						<span class="state <?php echo $row->status==1 ? 'publish' : 'unpublish'; ?>">
							<span><?php echo $row->status==1 ? JText::_('active') : JText::_('inactive') ; ?></span>
						</span>
					</td>
				</tr>
<?php
		$k = 1 - $k;
		$i++;
	}
?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="services" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>