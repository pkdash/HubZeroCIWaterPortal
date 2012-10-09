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

JToolBarHelper::title(JText::_('Support').' <small><small>[ '.JText::_('ACL').' ]</small></small>', 'support.png');
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('acl.html', true);
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

/**
* Toggles the check state of a group of boxes
*
* Checkboxes must have an id attribute in the form cb0, cb1...
* @param The number of box to 'check'
* @param An alternative field name
*/
function checkAllOptions() 
{
	var f = document.adminForm;
	var c = f.toggleOpt.checked;
	console.log(f.toggle);
	$$('.chk').each(function(el){
		el.checked = c;
	});
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th> </th>
				<th> </th>
				<th> </th>
				<th> </th>
				<th colspan="3"><?php echo JText::_('Tickets'); ?></th>
				<th colspan="2"><?php echo JText::_('Comments'); ?></th>
				<th colspan="2"><?php echo JText::_('Private Comments'); ?></th>
				<th> </th>
			</tr>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('SUPPORT_COL_ID'); ?></th>
				<th><?php echo JText::_('Object'); ?></th>
				<th><?php echo JText::_('Model'); ?></th>
				<th><?php echo JText::_('Read'); ?></th>
				<th><?php echo JText::_('Update'); ?></th>
				<th><?php echo JText::_('Delete'); ?></th>
				<th><?php echo JText::_('Create'); ?></th>
				<th><?php echo JText::_('Read'); ?></th>
				<th><?php echo JText::_('Create'); ?></th>
				<th><?php echo JText::_('Read'); ?></th>
				<th> </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td> </td>
				<td>
					<input type="hidden" name="aro[id]" id="aro_id" value="" />
				</td>
				<td>
					<label for="aro_foreign_key">Alias or ID:</label>
					<input type="text" name="aro[foreign_key]" id="aro_foreign_key" size="20" value="" />
				</td>
				<td>
					<select name="aro[model]" id="aro_model">
						<option value="user"><?php echo JText::_('user'); ?></option>
						<option value="group"><?php echo JText::_('group'); ?></option>
					</select>
					<input type="checkbox" name="toggleOpt" value="" onclick="checkAllOptions();" /> <abbr title="<?php echo JText::_('Check all options'); ?>"><?php echo JText::_('All'); ?></abbr>
				</td>
				<td>
					<input type="hidden" name="map[tickets][id]" value="0" />
					<input type="hidden" name="map[tickets][aro_id]" value="0" />
					<input type="hidden" name="map[tickets][aco_id]" value="1" />
					<input type="hidden" name="map[tickets][action_create]" value="1" />
					<input type="checkbox" class="chk" name="map[tickets][action_read]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[tickets][action_update]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[tickets][action_delete]" value="1" />
				</td>
				<td>
					<input type="hidden" name="map[comments][id]" value="0" />
					<input type="hidden" name="map[comments][aro_id]" value="0" />
					<input type="hidden" name="map[comments][aco_id]" value="2" />
					<input type="checkbox" class="chk" name="map[comments][action_create]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[comments][action_read]" value="1" />
					<input type="hidden" name="map[comments][action_update]" value="0" />
					<input type="hidden" name="map[comments][action_delete]" value="0" />
				</td>
				<td>
					<input type="hidden" name="map[private_comments][id]" value="0" />
					<input type="hidden" name="map[private_comments][aro_id]" value="0" />
					<input type="hidden" name="map[private_comments][aco_id]" value="3" />
					<input type="checkbox" class="chk" name="map[private_comments][action_create]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[private_comments][action_read]" value="1" />
					<input type="hidden" name="map[private_comments][action_update]" value="0" />
					<input type="hidden" name="map[private_comments][action_delete]" value="0" />
				</td>
				<td>
					<input type="submit" name="newacl" value="Add" onclick="submitbutton('save');" />
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
	FROM #__support_acl_aros_acos AS m 
	LEFT JOIN #__support_acl_aros AS r ON m.aro_id=r.id 
	LEFT JOIN #__support_acl_acos AS c ON m.aco_id=c.id 
	WHERE r.foreign_key=$row->foreign_key AND r.model='$row->model'
	ORDER BY aro_foreign_key, aro_model";
	$this->database->setQuery($sql);
	$lines = $this->database->loadObjectList();

	$data = array();
	$data['tickets']['create'] = 0;
	$data['tickets']['read'] = 0;
	$data['tickets']['update'] = 0;
	$data['tickets']['delete'] = 0;

	$data['comments']['create'] = 0;
	$data['comments']['read'] = 0;
	$data['comments']['update'] = 0;
	$data['comments']['delete'] = 0;

	$data['private_comments']['create'] = 0;
	$data['private_comments']['read'] = 0;
	$data['private_comments']['update'] = 0;
	$data['private_comments']['delete'] = 0;

	foreach ($lines as $line)
	{
		$data[$line->aco_model]['id'] = $line->id;
		$data[$line->aco_model]['create'] = $line->action_create;
		$data[$line->aco_model]['read'] = $line->action_read;
		$data[$line->aco_model]['update'] = $line->action_update;
		$data[$line->aco_model]['delete'] = $line->action_delete;
	}
?>
			<tr>
				<td style="text-align:center;"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td style="text-align:center;"><?php echo $row->id; ?></td>
				<td><?php echo $row->alias; ?> (<?php echo $row->foreign_key; ?>)</td>
				<td><?php echo $row->model; ?></td>
				
				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['tickets']['read']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['tickets']['id']; ?>&amp;action=read&amp;value=<?php echo $data['tickets']['read'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['tickets']['update']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['tickets']['id']; ?>&amp;action=update&amp;value=<?php echo $data['tickets']['update'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['tickets']['delete']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['tickets']['id']; ?>&amp;action=delete&amp;value=<?php echo $data['tickets']['delete'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>

				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['comments']['create']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['comments']['id']; ?>&amp;action=create&amp;value=<?php echo $data['comments']['create'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					if ($data['comments']['read']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['comments']['id']; ?>&amp;action=read&amp;value=<?php echo $data['comments']['read'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>

				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['private_comments']['create']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['private_comments']['id']; ?>&amp;action=create&amp;value=<?php echo $data['private_comments']['create'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php 
					$cimg = 'publish_x.png';
					$calt = JText::_('no');
					$cls = 'unpublish';
					if ($data['private_comments']['read']) {
						$cimg = 'publish_g.png';
						$calt = JText::_('yes');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;id=<?php echo $data['private_comments']['id']; ?>&amp;action=read&amp;value=<?php echo $data['private_comments']['read'] ? '0' : '1' ?>&amp;<?php //echo JUtility::getToken(); ?>vfd=1">
						<span><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /></span>
					</a>
				</td>
				<td> </td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
