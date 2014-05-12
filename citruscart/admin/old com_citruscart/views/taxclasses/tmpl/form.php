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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm"  id="adminForm" enctype="multipart/form-data">

	<fieldset>
		<legend><?php echo JText::_('COM_CITRUSCART_FORM'); ?></legend>
			<table class="table table-striped table-bordered">
				<tr>
					<td width="100" align="right" class="key">
						<label for="tax_class_name">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="tax_class_name" id="tax_class_name" size="48" maxlength="250" value="<?php echo $row->tax_class_name; ?>"/>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="tax_class_description">
						<?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>:
						</label>
					</td>
					<td>
						<textarea name="tax_class_description" id="tax_class_description" rows="10" cols="25"><?php echo $row->tax_class_description; ?></textarea>
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->tax_class_id; ?>" />
			<input type="hidden" name="task" value="" />
	</fieldset>
</form>