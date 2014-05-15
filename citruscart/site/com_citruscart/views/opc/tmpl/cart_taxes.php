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
$config = Citruscart::getInstance();
$display_tax_checkout = $config->get('show_tax_checkout', '1');
$display_shipping_tax = $config->get('display_shipping_tax', '1');
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

$order = $this->order;
if ( empty($order->currency) ) {
	$order->currency = $config->get( 'default_currencyid', 1);
}

switch( $display_tax_checkout )
{
	case 1 : // Tax Rates in Separate Lines
		$taxes = $order->getTaxRates();
		foreach ( $taxes as $taxrate)
		{
			$tax_desc = $taxrate->tax_rate_description ? $taxrate->tax_rate_description : 'Tax';
			$amount = $taxrate->applied_tax;
		 	if ( $amount )
			{
			?>
			<tr>
                <td colspan="2">
                	<span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
                </td>
                <td>
                    <span class="inner"><?php echo CitruscartHelperBase::currency( $amount, $order->currency); ?></span>
                </td>
            </tr>
			<?php
			}
		}
	break;
	case 2 : // Tax Classes in Separate Lines
		$taxes = $order->getTaxClasses();
		foreach ( $taxes as $taxclass)
		{
			$tax_desc = $taxclass->tax_class_description ? $taxclass->tax_class_description : 'Tax';
			$amount = $taxclass->applied_tax;
			if ( $amount )
			{
			?>
            <tr>
                <td colspan="2">
                    <span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
                </td>
                <td>
                    <span class="inner"><?php echo CitruscartHelperBase::currency( $amount , $order->currency); ?></span>
                </td>
            </tr>
			<?php
			}
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
            ?>
            <tr>
                <td colspan="2">
                    <span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
                </td>
                <td>
                    <span class="inner"><?php echo CitruscartHelperBase::currency( $amount , $order->currency); ?></span>
                </td>
            </tr>
            <?php
			}
			foreach( $tax_rates as $taxrate )
			{
				$tax_desc = $taxrate->tax_rate_description ? $taxrate->tax_rate_description : 'Tax';
				$amount = $taxrate->applied_tax;
				if ( $amount && $taxrate->tax_class_id == $taxclass->tax_class_id )
				{
					?>
					<tr>
                        <td colspan="2">
                            <span class="inner">- <?php echo JText::_( $tax_desc ).":"; ?></span>
                        </td>
                        <td>
                            <span class="inner"><?php echo CitruscartHelperBase::currency( $amount, $order->currency); ?></span>
                        </td>
                    </tr>
					<?php
		   	}
			}
		}
	break;
	case 4 : // All in One Line
    if( $order->order_tax )
    {
        ?>
        <tr>
            <td colspan="2">
                <span class="inner">
                <?php
                if (!empty($this->show_tax)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_INCLUDED').":"; }
                elseif (!empty($this->using_default_geozone)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_ESTIMATE').":"; } 
                else { echo JText::_('COM_CITRUSCART_PRODUCT_TAX').":"; }    
                ?>
                </span>
            </td>
            <td>
                <span class="inner"><?php echo CitruscartHelperBase::currency($order->order_tax, $order->currency ) ?></span>
            </td>
        </tr>
        <?php
    }
	break;
}
?>

<?php if (!empty($this->showShipping)) { ?>
<tr>
	<td colspan="2">
		<span class="inner">
			 <?php echo JText::_('COM_CITRUSCART_SHIPPING_AND_HANDLING'); ?>
		</span>
    </td>
    <td>
    	<span class="inner">
    		<?php echo CitruscartHelperBase::currency($order->order_shipping, $order->currency ); ?>
    	</span>
    </td>
</tr>        
<?php } ?>

<?php if (!empty($this->showShipping) && $display_shipping_tax && $order->order_shipping_tax) { ?>
<tr>
	<td colspan="2">
		<span class="inner">
			 <?php echo JText::_('COM_CITRUSCART_SHIPPING_TAX'); ?>
		</span>
    </td>
    <td>
    	<span class="inner">
    		<?php echo CitruscartHelperBase::currency( (float) $order->order_shipping_tax, $order->currency ); ?>
    	</span>
    </td>
</tr>        
<?php } ?>
