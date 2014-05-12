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
	$form = $this->form;
	$row = $this->row;
?>


<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onBeforeDisplayCategoryForm', array( $row ) );
    ?>

    <table style="width: 100%">
    <tr>
        <td style="vertical-align: top; width: 65%;">

    			<table class="table table-striped table-bordered">
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="category_name">
    						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
    						</label>
    					</td>
    					<td>
    						<input type="text" name="category_name" id="category_name" size="48" maxlength="250" value="<?php echo $row->category_name; ?>" />
    					</td>
    				</tr>
                    <tr>
                        <td style="width: 100px; text-align: right;" class="key">
                            <?php echo JText::_('COM_CITRUSCART_ALIAS'); ?>:
                        </td>
                        <td>
                            <input name="category_alias" id="category_alias" value="<?php echo $row->category_alias; ?>" type="text" size="48" maxlength="250" />
                        </td>
                    </tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="parent_id">
    						<?php echo JText::_('COM_CITRUSCART_PARENT'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php $disabled = array( $row->category_id ); echo CitruscartSelect::category($row->parent_id, 'parent_id', '', 'parent_id', false, true, 'Select Category', 'COM_CITRUSCART_NO_PARENT' , null , $disabled  ); ?>
    					</td>
    				</tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="enabled">
    						<?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php echo CitruscartSelect::btbooleanlist( 'category_enabled', '', $row->category_enabled ); ?>
    					</td>
    				</tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="enabled">
    						<?php echo JText::_('COM_CITRUSCART_CATEGORY_NAME_IN_CATEGORIES_LISTING'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php echo CitruscartSelect::btbooleanlist( 'display_name_category', '', $row->display_name_category ); ?>
    					</td>
    				</tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="enabled">
    						<?php echo JText::_('COM_CITRUSCART_CATEGORY_NAME_IN_SUBCATEGORIES_LISTING'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php echo CitruscartSelect::btbooleanlist( 'display_name_subcategory', '', $row->display_name_subcategory ); ?>
    					</td>
    				</tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="category_full_image">
    						<?php echo JText::_('COM_CITRUSCART_CURRENT_IMAGE'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php
    						jimport('joomla.filesystem.file');
    						if (!empty($row->category_full_image) && JFile::exists( Citruscart::getPath( 'categories_images').DS.$row->category_full_image ))
    						{
    							echo CitruscartUrl::popup( Citruscart::getClass( 'CitruscartHelperCategory', 'helpers.category' )->getImage($row->category_id, '', '', 'full', true), CitruscartHelperCategory::getImage($row->category_id), array('update' => false, 'img' => true));
    						}
    						?>
    						<br />
    						<input type="text" name="category_full_image" id="category_full_image" size="48" maxlength="250" value="<?php echo $row->category_full_image; ?>" />
    					</td>
    				</tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="category_full_image_new">
    						<?php echo JText::_('COM_CITRUSCART_UPLOAD_NEW_IMAGE'); ?>:
    						</label>
    					</td>
    					<td>
    						<input name="category_full_image_new" type="file" size="40" />
    					</td>
    				</tr>
                    <tr>
                        <td style="vertical-align: top; width: 100px; text-align: right;" class="key">
                            <?php echo JText::_('COM_CITRUSCART_CATEGORY_LAYOUT_FILE'); ?>:
                        </td>
                        <td>
                            <?php echo CitruscartSelect::categorylayout( $row->category_layout, 'category_layout' ); ?>
                            <div class="well note">
                                <?php echo JText::_('COM_CITRUSCART_CATEGORY_LAYOUT_FILE_DESC'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; width: 100px; text-align: right;" class="key">
                            <?php echo JText::_('COM_CITRUSCART_CATEGORY_PRODUCTS_LAYOUT_FILE'); ?>:
                        </td>
                        <td>
                            <?php echo CitruscartSelect::productlayout( $row->categoryproducts_layout, 'categoryproducts_layout' ); ?>
                            <div class="well note">
                                <?php echo JText::_('COM_CITRUSCART_CATEGORY_PRODUCTS_LAYOUT_FILE_DESC'); ?>
                            </div>
                        </td>
                    </tr>
    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="category_description">
    						<?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php $editor = JFactory::getEditor(); ?>
    						<?php echo $editor->display( 'category_description',  $row->category_description, '100%', '450', '100', '20' ) ; ?>
    					</td>
    				</tr>
                    <tr>
                        <td style="vertical-align: top; width: 100px; text-align: right;" class="key">
                            <?php echo JText::_('COM_CITRUSCART_CATEGORY_PARAMS'); ?>:
                        </td>
                        <td>
                            <textarea name="category_params" id="category_params" rows="10" cols="35"><?php echo $row->category_params; ?></textarea>
                        </td>
                    </tr>
                    <?php
                    if(!empty($this->shippingHtml))
                    {
                    ?>

    				<tr>
    					<td style="width: 100px; text-align: right;" class="key">
    						<label for="shippingPlugins">
    						<?php echo JText::_('COM_CITRUSCART_SHIPPING_INFORMATION'); ?>:
    						</label>
    					</td>
    					<td>
    						<?php echo $this->shippingHtml ?>
    					</td>
    				</tr>
                    <?php
                    }
                    ?>
    			</table>

    			<input type="hidden" name="id" value="<?php echo $row->category_id?>" />
    			<input type="hidden" name="task" value="" />

            <?php
                // fire plugin event here to enable extending the form
                JDispatcher::getInstance()->trigger('onAfterDisplayCategoryFormMainColumn', array( $row ) );
            ?>

        </td>
        <td style="max-width: 35%; min-width: 35%; width: 35%; vertical-align: top;">

        <?php
            // fire plugin event here to enable extending the form
            JDispatcher::getInstance()->trigger('onAfterDisplayCategoryFormRightColumn', array( $row ) );
        ?>
        </td>
    </tr>
    </table>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplayCategoryForm', array( $row ) );
    ?>

</form>