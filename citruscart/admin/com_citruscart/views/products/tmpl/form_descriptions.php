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

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>

<table class="table table-striped table-bordered" style="width: 100%;">
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_FULL_DESCRIPTION'); ?>:</td>
        <td>
	        <?php $editor = JFactory::getEditor(); ?>
	        <?php // echo $editor->display( 'product_description',  $row->product_description, '100%', '300', '75', '20' ) ;
	       	 echo $editor->display('product_description', $row->product_description, "100%",'300', "75","20", $buttons = true, $id ='product_long_desc', $asset = null, $author = null, $params = array())
	        ?>
        </td>
    </tr>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SHORT_DESCRIPTION'); ?>:</td>
        <td><?php $editor = JFactory::getEditor(); ?>
        	<?php echo $editor->display( 'product_description_short',  $row->product_description_short, '100%', '300', '75', '10') ; ?>
        </td>
    </tr>
    <?php
    if (!empty($row->product_id))
    {
        $tagsHelper = new CitruscartHelperTags();
        if ($tagsHelper->isInstalled())
        {
            ?>
    <tr>
        <td colspan="2">
            <?php echo $tagsHelper->getForm( $row->product_id ); ?>
        </td>
    </tr>
    <?php
        }
    }
    ?>
</table>
</form>