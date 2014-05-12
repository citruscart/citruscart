<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
 ?>
<?php  $form = $this->form; ?>

<?php $row = $this->row;

JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">


			<table class="table table-striped table-bordered">
				<tr>
					<td width="100" align="right" class="key">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
					</td>
					<td>
						<input type="text" name="country_name" id="country_name" size="48" maxlength="250" value="<?php echo $row->country_name; ?>" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <label for="enabled">
                        <?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
                        </label>
                    </td>
                    <td>
                        <?php echo CitruscartSelect::btbooleanlist( 'country_enabled', '', $row->country_enabled ); ?>
                    </td>
                </tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="country_isocode_2">
						<?php echo JText::_('COM_CITRUSCART_ISO_CODE_2'); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="country_isocode_2" id="country_isocode_2" size="10" maxlength="250" value="<?php echo $row->country_isocode_2; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="country_isocode_3">
						<?php echo JText::_('COM_CITRUSCART_ISO_CODE_3'); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="country_isocode_3" id="country_isocode_3" size="10" maxlength="250" value="<?php echo $row->country_isocode_3; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="address_format">
						<?php echo JText::_('COM_CITRUSCART_ADDRESS_FORMAT'); ?>:
						</label>
					</td>
					<td>
						<textarea name="address_format" id="address_format" cols="25" rows="7"><?php if(isset($row->address_format)){ echo $row->address_format; } ?></textarea>
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->country_id?>" />
			<input type="hidden" name="task" value="" />
</form>