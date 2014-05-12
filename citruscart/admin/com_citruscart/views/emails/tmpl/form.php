<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
?>
<?php $form = $this->form; ?>
<?php $row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<fieldset>
		<legend><?php echo JText::_('COM_CITRUSCART_LANGUAGE_INFORMATION'); ?></legend>
			<table class="table table-striped table-bordered">
				<tr>
					<td width="100" align="right" class="key">
						<?php echo JText::_('COM_CITRUSCART_NAME')?>
					</td>
					<td>
						<?php echo $row->name; ?>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<?php echo JText::_('COM_CITRUSCART_CODE'); ?>
					</td>
					<td>
						<?php echo $row->code; ?>
					</td>
				</tr>
			</table>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_CITRUSCART_STRINGS'); ?></legend>
			<table class="table table-striped table-bordered">
			<?php foreach($row->strings['strings'] as $k => $v){ ?>
				<tr>
					<td width="100" align="right" class="key">
						<?php echo $k; ?>:
					</td>
					<td>
					    <textarea name="<?php echo $k; ?>" id="<?php echo $k; ?>" rows="8" cols="50"><?php echo $v; ?></textarea>
					</td>
				</tr>
			<?php } ?>
			</table>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $row->code; ?>" />
	<input type="hidden" name="task" value="" />
</form>