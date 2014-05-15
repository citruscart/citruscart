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

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row;
;

JFilterOutput::objectHTMLSafe( $row );
Citruscart::load( 'CitruscartHelperManufacturer', 'helpers.manufacturer' );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered" style="width: 100%">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>:
					</td>
					<td>
						<?php echo $row->product_name; ?>
						 <?php //echo $this->elementArticle_product; ?>
                         <?php //echo $this->resetArticle_product; ?> 
					</td>
				</tr>
				
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_USER_NAME'); ?>:
					</td>
					<td>
					<?php echo $row->user_name; ?>
						 <?php //echo $this->elementUser_product; ?>
                         <?php //echo $this->resetUser_product; ?> 
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_COMMENT'); ?>:
					</td>
					<td>
					<?php echo $row->productcomment_text; ?>
						<!--
							<textarea name="productcomment_text" id="productcomment_text" style="width: 100%;" rows="10"><?php echo @$row->productcomment_text; ?></textarea>
						 -->
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_USER_RATING'); ?>:
					</td>
					<td>
                        <?php Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' ); ?>
                        <?php echo CitruscartHelperProduct::getRatingImage( $row->productcomment_rating, $this ); ?>
						<input type="hidden" id="productcomment_rating" name="productcomment_rating" value="<?php echo $row->productcomment_rating; ?>" size="10" />
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_PUBLISHED'); ?>:
					</td>
					<td>
							<?php echo CitruscartSelect::btbooleanlist(  'productcomment_enabled', '', $row->productcomment_enabled ); ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->productcomment_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>
