<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('Tools'), 'tools.png');
//JToolBarHelper::spacer();
//JToolBarHelper::addNew();
JToolBarHelper::deleteList();

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filters-bar">
		<a class="refresh" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;username=&amp;appname=&amp;start=0">
			<span><?php echo JText::_('Clear filters'); ?></span>
		</a>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Session', 'sessnum', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Owner', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Started', 'start', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Last accessed', 'accesstime', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Tool', 'tool', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('Stop'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows) 
{
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->sessnum; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo $this->escape(stripslashes($row->sessname)); ?>::Host: <?php echo $row->exechost; ?>&lt;br /&gt;IP: <?php echo $row->remoteip; ?>">
						<span><?php echo $this->escape($row->sessnum); ?></span>
					</span>
				</td>
				<td>
					<a class="user" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;username=<?php echo $row->username; ?>">
						<span><?php echo $this->escape($row->username); ?></span>
					</a>
				</td>
				<td>
					<time datetime="<?php echo $this->escape($row->start); ?>">
						<?php echo $this->escape($row->start); ?>
					</time>
				</td>
				<td>
					<time datetime="<?php echo $this->escape($row->accesstime); ?>">
						<?php echo $this->escape($row->accesstime); ?>
					</time>
				</td>
				<td>
					<a class="tool" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;appname=<?php echo $row->appname; ?>">
						<span><?php echo $this->escape($row->appname); ?></span>
					</a>
				</td>
				<td>
					<a class="state trash" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=remove&amp;id[]=<?php echo $row->sessnum; ?>" title="<?php echo JText::_('Terminate'); ?>">
						<span><?php echo JText::_('Terminate'); ?></span>
					</a>
				</td>
			</tr>
<?php
		$i++;
	}
}
?>
		</tbody>
	</table>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>