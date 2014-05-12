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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
Citruscart::load( 'CitruscartHelperManufacturer', 'helpers.manufacturer' );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >
			<table class="table table-striped table-bordered">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
					</td>
					<td>
						<input type="text" name="manufacturer_name" id="manufacturer_name" value="<?php echo $row->manufacturer_name; ?>" size="48" maxlength="250" />
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
					</td>
					<td>
						<?php echo CitruscartSelect::btbooleanlist(  'manufacturer_enabled', '', $row->manufacturer_enabled ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_CURRENT_IMAGE'); ?>:
					</td>
					<td>
						<?php
						jimport('joomla.filesystem.file');
						if (!empty($row->manufacturer_image) && JFile::exists( Citruscart::getPath( 'manufacturers_images').DS.$row->manufacturer_image ))
						{
							echo CitruscartUrl::popup( CitruscartHelperManufacturer::getImage($row->manufacturer_id, '', '', 'full', true), CitruscartHelperManufacturer::getImage($row->manufacturer_id), array('update' => false, 'img' => true));
						}
						?>
						<br />
						<input type="text" name="manufacturer_image" id="manufacturer_image" value="<?php echo $row->manufacturer_image; ?>" size="48" maxlength="250" />
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_UPLOAD_NEW_IMAGE'); ?>:
					</td>
					<td>
						<input name="manufacturer_image_new" type="file" size="40" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>:
                    </td>
                    <td>
                        <?php $editor = JFactory::getEditor(); ?>
                        <?php echo $editor->display( 'manufacturer_description',  $row->manufacturer_description, '100%', '450', '100', '20' ) ; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_PARAMETERS'); ?>:
                    </td>
                    <td>
                        <textarea name="manufacturer_params" id="manufacturer_params" rows="10" cols="35"><?php echo $row->manufacturer_params; ?></textarea>
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->manufacturer_id; ?>" />
			<input type="hidden" name="task" value="" />
</form>