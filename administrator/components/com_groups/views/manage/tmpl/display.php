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

$canDo = GroupsHelper::getActions('group');

JToolBarHelper::title(JText::_('COM_GROUPS'), 'groups.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences('com_groups', '550');
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
	JToolBarHelper::deleteList('delete', 'delete');
}

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('COM_GROUPS_SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />
		
		<label for="filter-type"><?php echo JText::_('COM_GROUPS_TYPE'); ?>:</label> 
		<select name="type" id="filter-type">
			<option value="all"<?php echo ($this->filters['type'][0] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Type...'); ?></option>
			<option value="hub"<?php echo ($this->filters['type'][0] == 'hub') ? ' selected="selected"' : ''; ?>>Hub</option>
<?php if ($canDo->get('core.admin')) { ?>
			<option value="system"<?php echo ($this->filters['type'][0] == 'system') ? ' selected="selected"' : ''; ?>>System</option>
<?php } ?>
			<option value="project"<?php echo ($this->filters['type'][0] == 'project') ? ' selected="selected"' : ''; ?>>Project</option>
			<option value="partner"<?php echo ($this->filters['type'][0] == 'partner') ? ' selected="selected"' : ''; ?>>Partner</option>
			<option value="course"<?php echo ($this->filters['type'][0] == 'course') ? ' selected="selected"' : ''; ?>>Course</option>
		</select>
		
		<label for="filter-privacy"><?php echo JText::_('Privacy'); ?>:</label> 
		<select name="privacy" id="filter-privacy">
			<option value=""<?php echo ($this->filters['privacy'] == '-1') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Privacy level...'); ?></option>
			<option value="public"<?php echo ($this->filters['privacy'] == 'public') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Public'); ?></option>
			<option value="protected"<?php echo ($this->filters['privacy'] == 'protected') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Protected'); ?></option>
			<option value="private"<?php echo ($this->filters['privacy'] == 'private') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Private'); ?></option>
		</select>
		
		<label for="filter-policy"><?php echo JText::_('Policy'); ?>:</label> 
		<select name="policy" id="filter-policy">
			<option value=""<?php echo ($this->filters['policy'] == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Policy type...'); ?></option>
			<option value="open"<?php echo ($this->filters['policy'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Open'); ?></option>
			<option value="restricted"<?php echo ($this->filters['policy'] == 'restricted') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Restricted'); ?></option>
			<option value="invite"<?php echo ($this->filters['policy'] == 'invite') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Invite only'); ?></option>
			<option value="closed"<?php echo ($this->filters['policy'] == 'closed') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Closed'); ?></option>
		</select>
		
		<input type="submit" value="<?php echo JText::_('COM_GROUPS_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>
	
	<table class="adminlist" summary="<?php echo JText::_('COM_GROUPS_TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_ID'); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_CN'); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_TYPE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_PUBLISHED'); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_MEMBERS'); ?></th>
				<th scope="col"><?php echo JText::_('Pages'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$group = new Hubzero_Group();
	//$group->gidNumber = $row->gidNumber;
	//$group->cn = $row->cn;
	$group->read($row->gidNumber);

	switch ($row->type)
	{
		case '0': $type = 'System';  break;
		case '1': $type = 'Hub';     break;
		case '2': $type = 'Project'; break;
		case '3': $type = 'Partner'; break;
		case '4': $type = 'Course';  break;
	}

	$members = count($group->get('members'));

	$tip  = '<table><tbody>';
	$tip .= '<tr><th>' . JText::_('COM_GROUPS_MEMBERS') . '</th><td>' . $members . '</td></tr>';
	$tip .= '<tr><th>' . JText::_('COM_GROUPS_MANAGERS') . '</th><td>' . count($group->get('managers')) . '</td></tr>';
	$tip .= '<tr><th>' . JText::_('COM_GROUPS_APPLICANTS') . '</th><td>' . count($group->get('applicants')) . '</td></tr>';
	$tip .= '<tr><th>' . JText::_('COM_GROUPS_INVITEES') . '</th><td>' . count($group->get('invitees')) . '</td></tr>';
	$tip .= '</tbody></table>';
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->cn ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->gidNumber); ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->cn; ?>">
						<?php echo $this->escape(stripslashes($row->description)); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->description)); ?>
					</span>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->cn; ?>">
						<?php echo $this->escape($row->cn); ?>
					</a>
<?php } else { ?>
					<?php echo $this->escape($row->cn); ?>
<?php } ?>
				</td>
				<td>
					<?php echo $type; ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<?php if ($row->published) { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=unpublish&amp;id[]=<?php echo $row->cn; ?>" title="<?php echo JText::_('Unpublish Group'); ?>">
						<span class="state publish">
							<span class="text"><?php echo JText::_('Published'); ?></span>
						</span>
					</a>
					<?php } else { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;id[]=<?php echo $row->cn; ?>" title="<?php echo JText::_('Publish Group'); ?>">
						<span class="state unpublish">
							<span class="text"><?php echo JText::_('Unpublished'); ?></span>
						</span>
					</a>
					<?php } ?>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.manage')) { ?>
					<a class="glyph member hasTip" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;gid=<?php echo $row->cn; ?>" title="<?php echo JText::_('Manage membership') . '::' . $tip; ?>">
						<?php echo $members; ?>
					</a>
<?php } else { ?>
					<span class="glyph member" title="<?php echo JText::_('Manage membership') . '::' . $tip; ?>">
						<?php echo $members; ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage')) { ?>
						<a href="index.php?option=<?php echo $this->option ?>&amp;controller=pages&amp;gid=<?php echo $row->cn; ?>">Group Pages</a>
					<?php } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>