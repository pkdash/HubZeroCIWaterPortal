<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit Host' ) : JText::_( 'New Host' ) );

JToolBarHelper::title( JText::_( 'Tools' ).': <small><small>[ '. $text.' ]</small></small>', 'tools.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="hostname"><?php echo JText::_('Hostname'); ?>:</label></td>
						<td>
							<input type="text" name="fields[hostname]" id="hostname" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->hostname)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="admin"><?php echo JText::_('Host types'); ?>:</label></td>
						<td>
							<select multiple="multiple" name="hosttype[]">
<?php
							for ($i=0; $i<count($this->hosttypes); $i++)
							{
								$r = $this->hosttypes[$i];
								if ((int)$r->value & (int)$this->row->provisions) { ?>
								<option selected="selected" value="<?php echo $r->name; ?>"><?php echo $r->name; ?></option>
								<?php } else { ?>
								<option value="<?php echo $r->name; ?>"><?php echo $r->name; ?></option>
								<?php }
							}
?>
							</select>
						</td>
					</tr>
					
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('Status'); ?></th>
					<td><?php echo $this->escape($this->row->status); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[status]" value="<?php echo ($this->row->status) ? $this->row->status : 'check'; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->hostname; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>