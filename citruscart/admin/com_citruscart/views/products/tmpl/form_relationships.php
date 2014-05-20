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
$helper_product = new CitruscartHelperProduct();
?>

<div style="width: 100%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_ADD_NEW_RELATIONSHIP'); ?>
        </legend>
        <div id="new_relationship" class="dsc-wrap dsc-table">
            <div class="dsc-row">
                <div class="dsc-cell">

                    <?php echo CitruscartSelect::relationship('', 'new_relationship_type');    ?>
                </div>
                <div class="dsc-cell">
                    <?php
                    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
                    $model = DSCModel::getInstance( 'ElementProduct', 'CitruscartModel' );
                    echo $model->fetchElement( 'new_relationship_productid_to' );
                    echo $model->clearElement( 'new_relationship_productid_to' );
                    //<input name="new_relationship_productid_to" size="15" type="text" />
                    ?>

                    <input name="new_relationship_productid_from" value="<?php echo $row->product_id; ?>" type="hidden" />
                </div>
                <div class="dsc-cell">
                    <input type="button" value="<?php echo JText::_('COM_CITRUSCART_ADD'); ?>" class="btn btn-success" onclick="citruscartAddRelationship('existing_relationships', '<?php echo JText::_('COM_CITRUSCART_UPDATING_RELATIONSHIPS'); ?>');" value="<?php echo JText::_('COM_CITRUSCART_ADD'); ?>" />
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

<div style="width: 100%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_EXISTING_RELATIONSHIPS'); ?>
        </legend>
        <div id="existing_relationships">
            <?php echo $this->product_relations; ?>
        </div>
    </div>
</div>

<?php
// fire plugin event here to enable extending the form
JDispatcher::getInstance()->trigger('onDisplayProductFormRelations', array( $row ) );
?>

<div style="clear: both;"></div>
