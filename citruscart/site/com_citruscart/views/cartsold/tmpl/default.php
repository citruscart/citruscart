<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;
$state = @$this->state;
Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
$router = new CitruscartHelperRoute();
$quantities = array();
Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
Citruscart::load( 'CitruscartHelperEav', 'helpers.eav' );
?>

<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_MY_SHOPPING_CART'); ?></span>
</div>

    <?php if ($menu = CitruscartMenu::getInstance( @$this->submenu )) { $menu->display(); } ?>
    
<div class="cartitems">
    <?php if (!empty($items)) { ?>
    <form action="<?php echo JRoute::_('index.php?option=com_citruscart&view=carts&task=update&Itemid='.$router->findItemid( array('view'=>'carts') ) ); ?>" method="post" name="adminForm" enctype="multipart/form-data">

        <div style="float: right;">
        <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout&Itemid='.$this->checkout_itemid ); ?>" onclick="return CitruscartCheckUpdateCartQuantities(document.adminForm, '<?php echo JText::_('COM_CITRUSCART_CHECK_CART_UPDATE'); ?>');">
            <?php echo JText::_('COM_CITRUSCART_BEGIN_CHECKOUT'); ?>
        </a>
        </div>
        <div class="reset"></div>
        <div id="onCheckoutCart_wrapper">
        <table class="adminlist">
            <thead>
                <tr>
                    <th style="width: 20px;">
                	   <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( @$items ); ?>);" />
                    </th>
                    <th colspan="2" style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; $subtotal = 0;?> 
            <?php foreach ($items as $item) : ?>
            	
            	<?php            	
            		$params = new DSCParameter( trim(@$item->cartitem_params) );
            		$default_url = "index.php?option=com_citruscart&view=products&task=view&id=".$item->product_id;
            		$attributes = CitruscartHelperProduct::convertAttributesToArray( $item->product_id, $item->product_attributes );
            		for( $j = 0, $c = count( $attributes ); $j < $c; $j++ )
            		{
            			$default_url .= '&attribute_'.$attributes[$j][0].'='.$attributes[$j][1];
            		}	
            		
            		$eavs = CitruscartHelperEav::getAttributes( 'products', $item->product_id, true, 2 );
            		
            		for( $j = 0,$cj = count( $eavs ); $j < $cj; $j++ )
            			$default_url .= '&'.urlencode( $eavs[$j]->eavattribute_alias ).'='.urlencode( CitruscartHelperEav::getAttributeValue( $eavs[$j], 'carts', $item->cart_id, false, true ) );

            		$link = $params->get('product_url', $default_url );
            		$link = JRoute::_($link);
            	?>
            
                <tr class="row<?php echo $k; ?>">
                    <td style="width: 20px; text-align: center;">
                        <input type="checkbox" id="cb<?php echo $i; ?>" name="cid[<?php echo $item->cart_id; ?>]" value="<?php echo $item->product_id; ?>" onclick="isChecked(this.checked);" />
                    </td>
                    <td style="text-align: center; width: 50px;">
                        <?php echo CitruscartHelperProduct::getImage($item->product_id, 'id', $item->product_name, 'full', false, false, array( 'width'=>48 ) ); ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>">
                            <?php echo $item->product_name; ?>
                        </a>
                        <br/>
                        
                        <?php if (!empty($item->attributes_names)) : ?>
	                        <?php echo $item->attributes_names; ?>
	                        <br/>
	                    <?php endif; ?>
	                    <input name="product_attributes[<?php echo $item->cart_id; ?>]" value="<?php echo $item->product_attributes; ?>" type="hidden" />                       
                      
                        <?php if (!empty($item->product_sku)) : ?>
                            <b><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</b>
                            <?php echo $item->product_sku; ?>
                            <br/>
                        <?php endif; ?>
                      
                        <?php if ($item->product_recurs) : ?>
                            <?php $recurring_subtotal = $item->recurring_price; ?>
                            <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_price); ?>
                            (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>) 

										            <?php if( $item->subscription_prorated ) : ?>
		                                <br/>
		                                <?php echo JText::_('COM_CITRUSCART_INITIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
		                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
										            <?php else : ?>
			                            <?php if ($item->recurring_trial) : ?>
			                                <br/>
		                                <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
		                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
										            <?php endif;?>
                            <?php endif; ?>    
                        <?php else : ?>
                            <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->product_price); ?>                         
                        <?php endif; ?> 
                        
					    <?php if (!empty($this->onDisplayCartItem) && (!empty($this->onDisplayCartItem[$i]))) : ?>
					        <div class='onDisplayCartItem_wrapper_<?php echo $i?>'>
					        <?php echo $this->onDisplayCartItem[$i]; ?>
					        </div>
					    <?php endif; ?>                        
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php $type = 'text'; 
                        if ($item->product_parameters->get('hide_quantity_cart') == '1') { 
                            $type = 'hidden';
                            echo $item->product_qty;
                        } ?>
                        
                        <input class="input-mini" name="quantities[<?php echo $item->cart_id; ?>]" type="<?php echo $type; ?>" size="3" maxlength="3" value="<?php echo $item->product_qty; ?>" />
                        
                        <!-- Keep Original quantity to check any update to it when going to checkout -->
                        <input name="original_quantities[<?php echo $item->cart_id; ?>]" type="hidden" value="<?php echo $item->product_qty; ?>" />
                    </td>
                    <td style="text-align: right;">                       
                        <?php $subtotal = $subtotal + $item->subtotal; ?>
                        <?php echo CitruscartHelperBase::currency($item->subtotal); ?>
                    </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: left;">
                        <input type="submit" class="btn" value="<?php echo JText::_('COM_CITRUSCART_REMOVE_SELECTED'); ?>" name="remove" />
                    </td>
                    <td colspan="2">
                        <input style="float: right;" type="submit" class="btn" value="<?php echo JText::_('COM_CITRUSCART_UPDATE_QUANTITIES'); ?>" name="update" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="font-weight: bold;">
                        <?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
                    </td>
                    <td style="text-align: right;">
                        <span id="totalAmountDue"><?php echo CitruscartHelperBase::currency($subtotal); ?></span>
                    </td>
                </tr>
                <tr>
                	<td colspan="5" style="white-space: nowrap;">
                        <b><?php echo JText::_('COM_CITRUSCART_TAX_AND_SHIPPING_TOTALS'); ?></b>
                        <br/>
                        <?php
                            echo JText::_('COM_CITRUSCART_CALCULATED_DURING_CHECKOUT_PROCESS');
                    	?>
              	 	</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <?php if (!empty($this->return)) { ?>
                        <a href="<?php echo $this->return; ?>" class="btn btn-info">
                            <?php echo JText::_('COM_CITRUSCART_CONTINUE_SHOPPING'); ?>
                        </a>
                        <?php } ?>
                    </td>
                    <td style="text-align: right;" nowrap>
				        <div style="float: right;">
				        <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout&Itemid='.$this->checkout_itemid ); ?>" onclick="return CitruscartCheckUpdateCartQuantities(document.adminForm, '<?php echo JText::_('COM_CITRUSCART_CHECK_CART_UPDATE'); ?>');">
				            <?php echo JText::_('COM_CITRUSCART_BEGIN_CHECKOUT'); ?>
				        </a>
				        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>        
        <input type="hidden" name="boxchecked" value="" />
        <?php echo $this->form['validate']; ?>
        
    </form>
    <?php } else { ?>
    <p><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_IN_YOUR_CART'); ?></p>
    <?php } ?>
</div>