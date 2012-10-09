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

// Push some styles to the template
//$document =& JFactory::getDocument();
//$document->addStyleSheet('components' . DS . $this->option . DS . 'assets' . DS . 'css' . DS . 'conditions.css');

$tmpl = JRequest::getVar('tmpl', '');
$no_html = JRequest::getInt('no_html', 0);

$juser = JFactory::getUser();
?>
<?php 
if (!$tmpl && !$no_html) { 
	ximport('Hubzero_Document');
	Hubzero_Document::addComponentScript($this->option, 'assets/js/json2');
	Hubzero_Document::addComponentScript($this->option, 'assets/js/condition.builder');
	Hubzero_Document::addComponentStylesheet($this->option, 'assets/css/conditions.css');
?>
	<form action="index.php" method="post" name="adminForm" id="item-form">
		<div class="col width-100">
			<fieldset class="adminform">
				<legend><?php echo JText::_('Details'); ?></legend>

				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="field-iscore"><?php echo JText::_('Type:'); ?></label></td>
							<td colspan="2">
								<select name="fields[iscore]" id="field-iscore">
									<option value="2"<?php if ($this->row->iscore == 2) { echo ' selected="selected"'; }; ?>>Common</option>
									<option value="1"<?php if ($this->row->iscore == 1) { echo ' selected="selected"'; }; ?>>Mine</option>
									<option value="0"<?php if ($this->row->iscore == 0) { echo ' selected="selected"'; }; ?>>Custom</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?></label></td>
							<td colspan="2"><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
						</tr>
						<tr>
							<td colspan="3">
								<fieldset class="query">
									<?php
										if ($this->row->conditions)
										{
											$condition = json_decode($this->row->conditions);
											//foreach ($conditions as $condition)
											//{
												$view = new JView(array(
													'name'   => $this->controller,
													'layout' => 'condition'
												));
												$view->option     = $this->option;
												$view->controller = $this->controller;
												$view->condition  = $condition;
												$view->conditions = $this->conditions;
												$view->row        = $this->row;
												$view->display();
											//}
										}
									?>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="field-sort"><?php echo JText::_('Sort results by:'); ?></label></td>
							<td>
								<select name="fields[sort]" id="field-sort">
									<option value="open"<?php if ($this->row->sort == 'open') { echo ' selected="selected"'; }; ?>>Open/Closed</option>
									<option value="status"<?php if ($this->row->sort == 'status') { echo ' selected="selected"'; }; ?>>Status</option>
									<option value="login"<?php if ($this->row->sort == 'login') { echo ' selected="selected"'; }; ?>>Submitter</option>
									<option value="owner"<?php if ($this->row->sort == 'owner') { echo ' selected="selected"'; }; ?>>Owner</option>
									<option value="group"<?php if ($this->row->sort == 'group') { echo ' selected="selected"'; }; ?>>Group</option>
									<option value="id"<?php if ($this->row->sort == 'id') { echo ' selected="selected"'; }; ?>>ID</option>
									<option value="report"<?php if ($this->row->sort == 'report') { echo ' selected="selected"'; }; ?>>Report</option>
									<option value="resolved"<?php if ($this->row->sort == 'resolved') { echo ' selected="selected"'; }; ?>>Resolution</option>
									<option value="severity"<?php if ($this->row->sort == 'severity') { echo ' selected="selected"'; }; ?>>Severity</option>
									<option value="tag"<?php if ($this->row->sort == 'tag') { echo ' selected="selected"'; }; ?>>Tag</option>
									<option value="type"<?php if ($this->row->sort == 'type') { echo ' selected="selected"'; }; ?>>Type</option>
									<option value="created"<?php if ($this->row->sort == 'created') { echo ' selected="selected"'; }; ?>>Created</option>
								</select>
							</td>
							<td>
								<select name="fields[sort_dir]" id="field-sort_dir">
									<option value="DESC"<?php if (strtolower($this->row->sort_dir) == 'desc') { echo ' selected="selected"'; }; ?>>desc</option>
									<option value="ASC"<?php if (strtolower($this->row->sort_dir) == 'asc') { echo ' selected="selected"'; }; ?>>asc</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

		<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="fields[conditions]" id="field-conditions" value="<?php echo $this->escape(stripslashes($this->row->conditions)); ?>" />
		<input type="hidden" name="fields[user_id]" value="<?php echo $juser->get('id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : JRequest::getInt('no_html', 0); ?>" />
		<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo JHTML::_('form.token'); ?>
	</form>
	<!-- <script type="text/javascript" src="/media/system/js/jquery.js"></script>
	<script type="text/javascript" src="/media/system/js/jquery.noconflict.js"></script>
	<script type="text/javascript" src="<?php echo 'components' . DS . $this->option . DS . 'assets' . DS . 'js' . DS . 'json2.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo 'components' . DS . $this->option . DS . 'assets' . DS . 'js' . DS . 'condition.builder.js'; ?>"></script> -->
	<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var $ = jq, query = {};

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			var query = {};
			query = Conditions.getCondition('.query > fieldset');
			$('#field-conditions').val(JSON.stringify(query));

			submitform( pressbutton );
		}

		Conditions.option = <?php echo json_encode($this->conditions); ?>

		jQuery(document).ready(function($){
			Conditions.addqueryroot('.query', true);
		});
	</script>
<?php 
} else { 
	if ($this->row->iscore != 0)
	{
		$this->row->title .= ' ' . JText::_('(copy)');
	}
?>
	<form action="index.php" method="post" name="adminForm" id="queryForm">
		<h3>
			<span style="float: right">
				<input type="submit" value="<?php echo JText::_('Save'); ?>" />
				<!-- <button type="button" onclick="saveAndUpdate();"><?php echo JText::_( 'Save' );?></button>
				<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Cancel' );?></button> -->
			</span>
			<span class="configuration">
				<?php echo JText::_('Query builder') ?>
			</span>
		</h3>
		<fieldset class="wrapper">

		<fieldset class="fields title">
			<label for="field-title"><?php echo JText::_('Title'); ?></label>
			<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
		</fieldset>

		<fieldset class="query">
<?php
	if ($this->row->conditions)
	{
		$condition = json_decode($this->row->conditions);
		//foreach ($conditions as $condition)
		//{
			$view = new JView(array(
				'name'   => $this->controller,
				'layout' => 'condition'
			));
			$view->option     = $this->option;
			$view->controller = $this->controller;
			$view->condition  = $condition;
			$view->conditions = $this->conditions;
			$view->row        = $this->row;
			$view->display();
			
		//}
	}
?>
		</fieldset>

		<fieldset class="fields sort">
			<p>
				<label for="field-sort"><?php echo JText::_('Sort results by:'); ?></label>
				<select name="fields[sort]" id="field-sort">
					<option value="open"<?php if ($this->row->sort == 'open') { echo ' selected="selected"'; }; ?>>Open/Closed</option>
					<option value="status"<?php if ($this->row->sort == 'status') { echo ' selected="selected"'; }; ?>>Status</option>
					<option value="login"<?php if ($this->row->sort == 'login') { echo ' selected="selected"'; }; ?>>Submitter</option>
					<option value="owner"<?php if ($this->row->sort == 'owner') { echo ' selected="selected"'; }; ?>>Owner</option>
					<option value="group"<?php if ($this->row->sort == 'group') { echo ' selected="selected"'; }; ?>>Group</option>
					<option value="id"<?php if ($this->row->sort == 'id') { echo ' selected="selected"'; }; ?>>ID</option>
					<option value="report"<?php if ($this->row->sort == 'report') { echo ' selected="selected"'; }; ?>>Report</option>
					<option value="resolved"<?php if ($this->row->sort == 'resolved') { echo ' selected="selected"'; }; ?>>Resolution</option>
					<option value="severity"<?php if ($this->row->sort == 'severity') { echo ' selected="selected"'; }; ?>>Severity</option>
					<option value="tag"<?php if ($this->row->sort == 'tag') { echo ' selected="selected"'; }; ?>>Tag</option>
					<option value="type"<?php if ($this->row->sort == 'type') { echo ' selected="selected"'; }; ?>>Type</option>
					<option value="created"<?php if ($this->row->sort == 'created') { echo ' selected="selected"'; }; ?>>Created</option>
				</select>
				<select name="fields[sort_dir]" id="field-sort_dir">
					<option value="DESC"<?php if (strtolower($this->row->sort_dir) == 'desc') { echo ' selected="selected"'; }; ?>>desc</option>
					<option value="ASC"<?php if (strtolower($this->row->sort_dir) == 'asc') { echo ' selected="selected"'; }; ?>>asc</option>
				</select>
			</p>
		</fieldset>

		<input type="hidden" name="fields[id]" value="<?php echo ($this->row->iscore == 0) ? $this->row->id : 0; ?>" />
		<input type="hidden" name="fields[conditions]" id="field-conditions" value="<?php echo $this->escape(stripslashes($this->row->conditions)); ?>" />
		<input type="hidden" name="fields[user_id]" value="<?php echo $juser->get('id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : JRequest::getInt('no_html', 0); ?>" />
		<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo JHTML::_('form.token'); ?>
		</fieldset>
	</form>
	<!-- <script type="text/javascript" src="/media/system/js/jquery.js"></script>
	<script type="text/javascript" src="/media/system/js/jquery.noconflict.js"></script>
	<script type="text/javascript" src="<?php echo 'components' . DS . $this->option . DS . 'assets' . DS . 'js' . DS . 'json2.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo 'components' . DS . $this->option . DS . 'assets' . DS . 'js' . DS . 'condition.builder.js'; ?>"></script> -->
	<script type="text/javascript">
		/*function submitbutton(pressbutton) 
		{
			var $ = jq;

			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			var query = {};
			query = Conditions.getCondition('.query > fieldset');
			$('#field-conditions').val(JSON.stringify(query));

			//submitform( pressbutton );
		}

		function saveAndUpdate()
		{
			var $ = jq, query = {};

			if (!$('#field-title').val()) {
				alert('Please provide a title.');
				return false;
			}

			query = Conditions.getCondition('.query > fieldset');
			$('#field-conditions').val(JSON.stringify(query));

			$.post('index.php', $("#queryForm").serialize(), function(data){
				window.parent.document.getElementById('custom-views').innerHTML = data;
				window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);
			});
		}*/

		Conditions.option = <?php echo json_encode($this->conditions); ?>

		/*jQuery(document).ready(function(jq){
			Conditions.addqueryroot('.query', true);
		});*/
	</script>
<?php } ?>