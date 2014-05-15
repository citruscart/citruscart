<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
$items = $this->cartobj->items;
$subtotal = $this->cartobj->subtotal;
$state = $this->state;
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
?>

<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_SHOPPING_CART_SUMMARY'); ?></span>
</div>
    
<div class="cartitems">
    <form action="<?php echo JRoute::_('index.php?option=com_citruscart&view=carts&task=update'); ?>" method="post" name="adminForm" enctype="multipart/form-data">

        <div style="float: right;">
            <input onclick="window.parent.document.getElementById('sbox-window').close(); window.parent.location = '<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout'); ?>';" value="<?php echo JText::_('COM_CITRUSCART_BEGIN_CHECKOUT'); ?>" name="begincheckout" type="submit" class="btn" />
        </div>
        <div style="float: left;">
            <input onclick="window.parent.document.getElementById('sbox-window').close(); window.parent.location = '<?php echo JRoute::_('index.php?option=com_citruscart&view=carts'); ?>';" value="<?php echo JText::_('COM_CITRUSCART_VIEW_SHOPPING_CART'); ?>" name="begincheckout" type="submit" class="btn" />
        </div>
                                
        <table class="adminlist" style="clear: both;">
            <thead>
                <tr>
                    <th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; $subtotal = 0; ?> 
            <?php if(count($items)):?>
            <?php foreach ($items as $item) : ?>
                <tr class="row<?php echo $k; ?>">
                    <td>
                        <?php echo $item->product_name; ?>
                        <br/>
                        
                        <?php if (!empty($item->attributes_names)) : ?>
                            <?php echo $item->attributes_names; ?>
                            <br/>
                        <?php endif; ?>
                        
                        <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->product_price); ?> 
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php echo $item->product_qty; ?>
                    </td>
                    <td style="text-align: right;">                      
                        <?php $subtotal = $subtotal + $item->subtotal; ?>
                        <?php echo CitruscartHelperBase::currency($item->subtotal); ?>
                    </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            <?php endif;?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="font-weight: bold;">
                        <?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo CitruscartHelperBase::currency($subtotal); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="20">
                        <div style="float: right;">
                        <input onclick="window.parent.document.getElementById('sbox-window').close(); window.parent.location = '<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout'); ?>';" value="<?php echo JText::_('COM_CITRUSCART_BEGIN_CHECKOUT'); ?>" name="begincheckout" type="submit" class="btn" />
                        </div>
                        
                        <div style="float: left;">
                        <input onclick="window.parent.document.getElementById('sbox-window').close(); window.parent.location = '<?php echo JRoute::_('index.php?option=com_citruscart&view=carts'); ?>';" value="<?php echo JText::_('COM_CITRUSCART_VIEW_SHOPPING_CART'); ?>" name="begincheckout" type="submit" class="btn" />
                        </div>                        
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>

</div>