<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Ticket Stats' ).' ]</small></small>', 'support.png' );

JToolBarHelper::spacer();
JToolBarHelper::help('stats.html', true);
?>
<form action="index.php" method="get" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="ticket-stats">
	
	<fieldset id="filter-bar">
		<label for="type"><?php echo JText::_('Type'); ?></label>
		<select name="type" id="type">
			<option value="0"<?php if ($this->type == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Submitted Tickets'); ?></option>
<?php if (JComponentHelper::isEnabled('com_tools')) { ?>
			<option value="1"<?php if ($this->type == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Automatic Tickets'); ?></option>
<?php } ?>
		</select>
		
		<label for="year"><?php echo JText::_('Year'); ?>:</label> 
		<select name="year" id="year">
<?php
			$y = date("Y");
			$y++;
			for ($i=2004, $n=$y; $i < $n; $i++)
			{
?>
				<option value="<?php echo $i; ?>"<?php if ($this->year == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
<?php
			}
?>
		</select>
		
		<label for="acgroup"><?php echo JText::_('Group'); ?>:</label> 
		<?php 
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
		$gc = $dispatcher->trigger( 'onGetSingleEntry', array(array('groups', 'group', 'acgroup','',$this->group)) );
		if (count($gc) > 0) {
			echo $gc[0];
		} else { ?>
			<input type="text" name="group" value="<?php echo $this->group; ?>" id="acgroup" size="35" autocomplete="off" />
		<?php } ?>
		
		<input type="submit" name="submit" value="View" />
	</fieldset>
	<div class="clr"></div>
	
	<fieldset class="adminform">
	<table class="support-stats-overview open-tickets" summary="Overview of open support tickets">
		<thead>
			<tr>
				<th scope="col">Opened this year</th>
				<th scope="col">Opened this month</th>
				<th scope="col">Opened this week</th>
				<th scope="col">Open</th>
				<th scope="col">Unassigned</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $this->opened['year']; ?></td>
				<td><?php echo $this->opened['month']; ?></td>
				<td><?php echo $this->opened['week']; ?></td>
				<td class="major"><?php echo $this->opened['open']; ?></td>
				<td class="critical"><?php echo $this->opened['unassigned']; ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="support-stats-overview closed-tickets" summary="Overview of closed support tickets">
		<thead>
			<tr>
				<th scope="col">Closed this year</th>
				<th scope="col">Closed this month</th>
				<th scope="col">Closed this week</th>
				<th scope="col" colspan="2" class="block">Average lifetime</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $this->closed['year']; ?></td>
				<td><?php echo $this->closed['month']; ?></td>
				<td><?php echo $this->closed['week']; ?></td>
				<td colspan="2" class="block"><?php echo (isset($this->lifetime[0])) ? $this->lifetime[0] : 0; ?> <span>days</span> <?php echo (isset($this->lifetime[1])) ? $this->lifetime[1] : 0; ?> <span>hours</span> <?php echo (isset($this->lifetime[2])) ? $this->lifetime[2] : 0; ?> <span>minutes</span></td>
			</tr>
		</tbody>
	</table>
	</fieldset>
	
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span>People</span></legend>
			
			<table class="admintable support-stats-people" summary="Breakdown of people and the number of tickets closed">
				<thead>
					<tr>
						<th scope="col"><a<?php if ($this->sort == 'name') { echo ' class="active"'; } ?> href="index.php?option=com_support&amp;controller=<?php echo $this->controller; ?>&amp;type=<?php if ($this->type == 1) { echo 'automatic'; } ?>&amp;sort=name&amp;group=<?php echo $this->group; ?>&amp;year=<?php echo $this->year; ?>" title="Sort by name">&darr; Person</a></th>
						<th scope="col"><a<?php if ($this->sort == 'year') { echo ' class="active"'; } ?> href="index.php?option=com_support&amp;controller=<?php echo $this->controller; ?>&amp;type=<?php if ($this->type == 1) { echo 'automatic'; } ?>&amp;sort=year&amp;group=<?php echo $this->group; ?>&amp;year=<?php echo $this->year; ?>" title="Sort by year count">&darr; Closed this year</a></th>
						<th scope="col"><a<?php if ($this->sort == 'month') { echo ' class="active"'; } ?> href="index.php?option=com_support&amp;controller=<?php echo $this->controller; ?>&amp;type=<?php if ($this->type == 1) { echo 'automatic'; } ?>&amp;sort=month&amp;group=<?php echo $this->group; ?>&amp;year=<?php echo $this->year; ?>" title="Sort by month count">&darr; Closed this month</a></th>
						<th scope="col"><a<?php if ($this->sort == 'week') { echo ' class="active"'; } ?> href="index.php?option=com_support&amp;controller=<?php echo $this->controller; ?>&amp;type=<?php if ($this->type == 1) { echo 'automatic'; } ?>&amp;sort=week&amp;group=<?php echo $this->group; ?>&amp;year=<?php echo $this->year; ?>" title="Sort by week count">&darr; Closed this week</a></th>
					</tr>
				</thead>
				<tbody>
<?php
if ($this->users) {
	$cls = 'even';
	foreach ($this->users as $user)
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';
?>
					<tr class="<?php echo $cls; ?>">
						<th scope="row"><?php echo $this->escape(stripslashes($user->name)); ?></th>
						<td><?php echo $user->closed['year']; ?></td>
						<td><?php echo $user->closed['month']; ?></td>
						<td><?php echo $user->closed['week']; ?></td>
					</tr>
<?php
	}
}
?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span>Tickets Submitted (red) vs. Closed (green)</span></legend>
			<canvas id="line1" width="475" height="250">[Please wait...]</canvas>
		
			<!-- <h3>Ticket Total</h3>
			<canvas id="line2" width="475" height="250">[Please wait...]</canvas> -->
			<script type="text/javascript" src="../components/com_support/assets/js/rgraph/RGraph.common.js" ></script>
			<script type="text/javascript" src="../components/com_support/assets/js/rgraph/RGraph.line.js" ></script>
			<!--[if IE]><script src="../components/com_support/assets/js/excanvas/excanvas.compressed.js"></script><![endif]-->
<?php
$closeddata = '';
if ($this->closedmonths) {
	$closeddata = implode(',',$this->closedmonths);
}
$mcd = max($this->closedmonths);

$openeddata = '';
if ($this->openedmonths) {
	foreach ($this->openedmonths as $k=>$v)
	{
		$o[$k] = $this->openedmonths[$k]; // - $this->closedmonths[$k];
	}
	$openeddata = implode(',',$o);
}
$ocd = max($this->openedmonths);

$number = max($ocd, $mcd);
$max = ceil($number/10)*10;
?>
		<script type="text/javascript">
		window.onload = function ()
		{
			var line1 = new RGraph.Line('line1', [<?php echo $openeddata; ?>], [<?php echo $closeddata; ?>]);
			line1.Set('chart.colors', ['red', 'green']);
			line1.Set('chart.tickmarks', 'circle');
			line1.Set('chart.linewidth', 1);
			line1.Set('chart.background.barcolor1', 'white');
			line1.Set('chart.background.barcolor2', 'white');
			//line1.Set('chart.filled', 'true');
			//line1.Set('chart.fillstyle', ['rgba(255,130,130,0.5)','rgba(128,255,128,0.5)']);
			line1.Set('chart.text.angle', 45);
			line1.Set('chart.text.color', '#777777');
			line1.Set('chart.gutter', 35);
			line1.Set('chart.noaxes', true);
			line1.Set('chart.background.grid', true);
			line1.Set('chart.background.grid.vsize', 36.9);
			line1.Set('chart.yaxispos', 'left');
			line1.Set('chart.ymax', <?php echo $max; ?>);
			line1.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
			line1.Draw();
			
			/*var line2 = new RGraph.Line('line2', [20,25,13,14,16,18,21,32,12,15,0,0]);
			line2.Set('chart.colors', ['red']);
			line2.Set('chart.tickmarks', 'circle');
			line2.Set('chart.linewidth', 1);
			line2.Set('chart.background.barcolor1', 'white');
			line2.Set('chart.background.barcolor2', 'white');
			line2.Set('chart.filled', 'true');
			line2.Set('chart.fillstyle', ['#fcc']);
			line2.Set('chart.text.angle', 45);
			line2.Set('chart.text.color', '#777777');
			line2.Set('chart.gutter', 35);
			line2.Set('chart.noaxes', true);
			line2.Set('chart.background.grid', true);
			line2.Set('chart.yaxispos', 'left');
			line2.Set('chart.ymax', 100);
			line2.Set('chart.background.grid.vsize', 36.9);
			line2.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
			line2.Draw();*/
		}
		</script>
		</fieldset>
	</div>
	<div class="clr"></div>
</div><!-- / .section -->
<input type="hidden" name="task" value="display" />
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>
