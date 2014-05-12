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

			<table class="table table-striped table-bordered">
				<tr>
					<td width="100" align="right" class="key">
						<label for="zone_name">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="zone_name" id="zone_name" size="48" maxlength="250" value="<?php echo $row->zone_name; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="code">
						<?php echo JText::_('COM_CITRUSCART_CODE'); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="code" id="code" size="10" maxlength="250" value="<?php echo $row->code; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="country_id">
						<?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>:
						</label>
					</td>
					<td>
						<?php echo CitruscartSelect::country( $row->country_id, 'country_id' ); ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->zone_id?>" />
			<input type="hidden" name="task" value="" />

</form>