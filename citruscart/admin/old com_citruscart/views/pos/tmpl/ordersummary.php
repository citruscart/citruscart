<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'citruscart.css', 'media/com_citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
JHTML::_('script', 'citruscart_admin.js', 'media/com_citruscart/js/');
Citruscart::load( 'citruscartGrid', 'library.grid' );
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;
$display_credits = Citruscart::getInstance()->get( 'display_credits', '0' );

?>
<div class="cartitems">
           <table class="table table-striped table-bordered" style="clear: both;">
            <thead>
                <tr>
                    <th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; ?> 
            <?php foreach ($items as $item) : ?>
                <tr class="row<?php echo $k; ?>">
                    <td>
                        <a href="<?php echo JRoute::_("index.php?option=com_citruscart&controller=products&view=products&task=view&id=".$item->product_id); ?>">
                            <?php echo $item->orderitem_name; ?>
                        </a>
                        <br/>
                        
                        <?php if (!empty($item->orderitem_attribute_names)) : ?>
                            <?php echo $item->orderitem_attribute_names; ?>
                            <br/>
                        <?php endif; ?>
                        
                        <?php if (!empty($item->orderitem_sku)) : ?>
                            <b><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</b>
                            <?php echo $item->orderitem_sku; ?>
                            <br/>
                        <?php endif; ?>

                        <?php if ($item->orderitem_recurs) : ?>
                            <?php $recurring_subtotal = $item->recurring_price; ?>
                            <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_price); ?>
                            (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>) 
                            <?php if ($item->recurring_trial) : ?>
                                <br/>
                                <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
                            <?php endif; ?>    
                        <?php else : ?>
                            <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>:
                            <?php echo CitruscartHelperBase::currency($item->price); ?>                         
                        <?php endif; ?> 
                        
					    <?php if (!empty($this->onDisplayOrderItem) && (!empty($this->onDisplayOrderItem[$i]))) : ?>
					        <div class='onDisplayOrderItem_wrapper_<?php echo $i?>'>
					        <?php echo $this->onDisplayOrderItem[$i]; ?>
					        </div>
					    <?php endif; ?>  

                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php echo $item->orderitem_quantity;?>  
                    </td>
                    <td style="text-align: right;">
                        <?php echo CitruscartHelperBase::currency($item->orderitem_final_price); ?>
                                               
                    </td>
                </tr>
              
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: left;font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo CitruscartHelperBase::currency($order->order_subtotal); ?>
                    </td>
                </tr>
                
                <?php if (!empty($order->_coupons['order_price'])) : ?>
                <tr>
                    <td colspan="2" style="text-align: left;font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_('COM_CITRUSCART_DISCOUNT'); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo CitruscartHelperBase::currency($order->order_discount); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>
        <table class="adminlist" style="clear: both;">
                <tr>
                    <td colspan="2" style="white-space: nowrap;">
                        <b><?php echo JText::_('COM_CITRUSCART_TAX_AND_SHIPPING_TOTALS'); ?></b>
                        <br/>
                    </td>
                    <td colspan="2" style="text-align: right;">
                    <?php 
                    	$display_shipping_tax = Citruscart::getInstance()->get('display_shipping_tax', '1');
	              			$display_tax_checkout = Citruscart::getInstance()->get('show_tax_checkout', '1');

	              switch( $display_tax_checkout )
	              {
	              	case 1 : // Tax Rates in Separate Lines
		                	$taxes = $order->getTaxRates();
		                	foreach ( $taxes as $taxrate)
		                  {
		                   	$tax_desc = $taxrate->tax_rate_description ? $taxrate->tax_rate_description : 'Tax';
		                   	$amount = $taxrate->applied_tax;
		                   	if ( $amount )
			                   	echo JText::_( $tax_desc ).":<br />";
		                  }
	              		break;
	              	case 2 : // Tax Classes in Separate Lines
		                	$taxes = $order->getTaxClasses();
		                	foreach ( $taxes as $taxclass)
		                  {
		                   	$tax_desc = $taxclass->tax_class_description ? $taxclass->tax_class_description : 'Tax';
		                   	$amount = $taxclass->applied_tax;
		                   	if ( $amount )
													echo JText::_( $tax_desc ).":<br />";
		                  }
	              		break;
	              	case 3 : // Tax Classes and Tax Rates in Separate Lines
		                	$tax_classes = $order->getTaxClasses();
		                	$tax_rates = $order->getTaxRates();
		                	foreach ( $tax_classes as $taxclass)
		                  {
		                   	$tax_desc = $taxclass->tax_class_description ? $taxclass->tax_class_description : 'Tax';
		                   	$amount = $taxclass->applied_tax;
		                   	if ( $amount )
		                   	{
		                   		echo JText::_( $tax_desc ).":<br />";

		                      foreach( $tax_rates as $taxrate )
		                      {
				                   	$tax_desc = $taxrate->tax_rate_description ? $taxrate->tax_rate_description : 'Tax';
				                   	$amount = $taxrate->applied_tax;
				                   	if ( $amount && $taxrate->tax_class_id == $taxclass->tax_class_id )
				                   		echo JText::_( $tax_desc )." &nbsp;&nbsp; :<br />";
		                      }
		                    }
		                  }
	              		break;
	              	case 4 : // All in One Line
	                	if( $order->order_tax )
	                    {
												if (!empty($this->show_tax)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_INCLUDED').":<br/>"; }
					              	elseif (!empty($this->using_default_geozone)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_ESTIMATE').":<br/>"; } 
					                	else { echo JText::_('COM_CITRUSCART_PRODUCT_TAX').":<br/>"; }    
	                    }
	              		break;
	              }
	              			
   						
                    	if (!empty($this->showShipping))
                    	{
                            echo JText::_('COM_CITRUSCART_SHIPPING_AND_HANDLING').":";
                            if ($display_shipping_tax && $order->order_shipping_tax ) {
                                echo "<br>".JText::_('COM_CITRUSCART_SHIPPING_TAX').":";
                            }                    	    
                    	}

                    ?>
                    </td>
                    <td colspan="2" style="text-align: right;">
                     <?php 
	                    	
	              switch( $display_tax_checkout )
	              {
	              	case 1 : // Tax Rates in Separate Lines
		                	$taxes = $order->getTaxRates();
		                	foreach ( $taxes as $taxrate)
		                  {
		                   	$amount = $taxrate->applied_tax;
		                   	if ( $amount )
			                   	echo CitruscartHelperBase::currency( $amount, $order->currency).'<br />';
		                  }
	              		break;
	              	case 2 : // Tax Classes in Separate Lines
		                	$taxes = $order->getTaxClasses();
		                	foreach ( $taxes as $taxclass)
		                  {
		                   	$amount = $taxclass->applied_tax;
		                   	if ( $amount )
		                   		echo CitruscartHelperBase::currency( $amount , $order->currency).'<br />';
		                  }
	              		break;
	              	case 3 : // Tax Classes and Tax Rates in Separate Lines
		                	$tax_classes = $order->getTaxClasses();
		                	$tax_rates = $order->getTaxRates();
		                	foreach ( $tax_classes as $taxclass)
		                  {
		                   	$amount = $taxclass->applied_tax;
		                   	if ( $amount )
		                   	{
		                   		echo CitruscartHelperBase::currency( $amount , $order->currency).'<br />';
			                    foreach( $tax_rates as $taxrate )
			                    {
				                  	$amount = $taxrate->applied_tax;
				                  	if ( $amount && $taxrate->tax_class_id == $taxclass->tax_class_id )
				                   		echo CitruscartHelperBase::currency( $amount, $order->currency).'<br />';		                     		}
		                     	}
		                  }
	              		break;
	              	case 4 : // All in One Line
	                	if( $order->order_tax )
			                echo CitruscartHelperBase::currency($order->order_tax).'<br />';
	              		break;
		              }
                        if (!empty($this->showShipping))
                        {
                            echo CitruscartHelperBase::currency($order->order_shipping);
                            if ($display_shipping_tax && $order->order_shipping_tax ) {
                                echo "<br>" . CitruscartHelperBase::currency( (float) $order->order_shipping_tax);
                            }                               
                        }

                    ?>                  
                    </td>
                </tr>
                <?php if( $display_credits ): ?>
                <tr>
                	<td colspan="3" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                       - <?php echo CitruscartHelperBase::currency($order->order_credit); ?>
                    </td>
                </tr> 
                <?php endif; ?>
                <tr>
                	<td colspan="3" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo CitruscartHelperBase::currency($order->order_total); ?>
                    </td>
                </tr>                
        </table>        
</div>
