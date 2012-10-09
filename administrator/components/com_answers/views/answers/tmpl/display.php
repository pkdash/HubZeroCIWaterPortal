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

$canDo = AnswersHelper::getActions('answer');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Answers Manager') . '</a>', 'answers.png');
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
ximport('Hubzero_View_Helper_Html');

$dateFormat = '%d %b %Y';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = 0;
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
		<label for="filterby">Filter by:</label> 
		<select name="filterby" id="filterby" onchange="document.adminForm.submit( );">
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>>All Responses</option>
			<option value="accepted"<?php if ($this->filters['filterby'] == 'accepted') { echo ' selected="selected"'; } ?>>Accepted Response</option>
			<option value="rejected"<?php if ($this->filters['filterby'] == 'rejected') { echo ' selected="selected"'; } ?>>Unaccepted Responses</option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<caption>
			<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=questions&amp;task=edit&amp;id[]=<?php echo $this->filters['qid']; ?>" title="Edit this question">
				<?php echo $this->escape(stripslashes($this->question->subject)); ?>
			</a>
		</caption>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Answer', 'answer', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Accepted', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created by', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Helpful', 'helpful', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
for ($i=0, $n=count( $this->results ); $i < $n; $i++)
{
	$row =& $this->results[$i];

	switch ($row->state)
	{
		case '1':
			$task = 'reject';
			$img = 'publish_g.png';
			$alt = JText::_( 'Accepted' );
			$cls = 'published';
			break;
		case '0':
			$task = 'accept';
			$img = 'publish_x.png';
			$alt = JText::_( 'Unaccepted' );
			$cls = 'unpublished';
			break;

	}

	$row->answer = stripslashes($row->answer);
	$row->answer = Hubzero_View_Helper_Html::shortenText($row->answer, 75, 0);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>&amp;qid=<?php echo $this->question->id; ?>" title="Edit this Answer">
						<span><?php echo $row->answer; ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;qid=<?php echo $this->question->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>">
						<span><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span>
					</a>
				</td>
				<td style="white-space: nowrap;">
					<time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz) ?></time>
				</td>
				<td>
					<a class="glyph user" href="index.php?option=com_members&amp;controller=members&amp;task=edit&amp;id[]=<?php echo $row->created_by; ?>">
						<span><?php echo $this->escape(stripslashes($row->name)).' ('.$row->created_by.')'; ?></span>
					</a>
<?php if ($row->anonymous) { ?>
					<br /><span>(anonymous)</span>
<?php } ?>
				</td>
				<td>
					<span class="vote like">+<?php echo $row->helpful; ?></span> 
					<span class="vote dislike">-<?php echo $row->nothelpful; ?></span>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="qid" value="<?php echo $this->question->id ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>