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
if( $order->currency == '' )
	$order->currency = $config->get( 'default_currencyid', 1);
	?>

<span class="header">
	<span class="inner"><?php echo JText::_('COM_CITRUSCART_TAX_AND_SHIPPING_TOTALS'); ?></span><br/>
</span>

<?php
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
			<span class="left62">
				<span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
			</span>
      <span class="left38 right">
        <span class="inner"><?php echo CitruscartHelperBase::currency( $amount, $order->currency); ?></span>
     	</span>
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
			<span class="left62">
				<span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
			</span>
			<span class="left38 right">
				<span class="inner"><?php echo CitruscartHelperBase::currency( $amount , $order->currency); ?></span>
			</span>
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
			<span class="left62">
				<span class="inner"><?php echo JText::_( $tax_desc ).":"; ?></span>
			</span>
			<span class="left38 right">
				<span class="inner"><?php echo CitruscartHelperBase::currency( $amount , $order->currency); ?></span>
			</span>
		<?php
			}
			foreach( $tax_rates as $taxrate )
			{
				$tax_desc = $taxrate->tax_rate_description ? $taxrate->tax_rate_description : 'Tax';
				$amount = $taxrate->applied_tax;
				if ( $amount && $taxrate->tax_class_id == $taxclass->tax_class_id )
				{
					?>
			<span class="left62">
				<span class="inner">- <?php echo JText::_( $tax_desc ).":"; ?></span>
			</span>
			<span class="left38 right">
				<span class="inner"><?php echo CitruscartHelperBase::currency( $amount, $order->currency); ?></span>
			</span>
					<?php
		   	}
			}
		}
	break;
	case 4 : // All in One Line
	if( $order->order_tax )
		{
	  ?>
			<span class="left62">
				<span class="inner">
				<?php
					if (!empty($this->show_tax)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_INCLUDED').":"; }
						elseif (!empty($this->using_default_geozone)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_ESTIMATE').":"; } 
					  	else { echo JText::_('COM_CITRUSCART_PRODUCT_TAX').":"; }    
				?>
				</span>
			</span>
			<span class="left38 right">
				<span class="inner"><?php echo CitruscartHelperBase::currency($order->order_tax, $order->currency ) ?></span>
			</span>
		<?php
	  }
	break;
}
?>
<span class="left62">
	<span class="inner">
	<?php 
	if (!empty($this->showShipping))
	{
		/* Get the application */
		$app = JFactory::getApplication();
		
		//$task = JRequest::get( 'task' );
		$task = $app->input->get( 'task' );
		
		echo JText::_('COM_CITRUSCART_SHIPPING_AND_HANDLING').":";
		if( isset( $order->shipping ) && ( $task == 'setShippingMethod' || $task == 'display' ) )
		{
		?>
			<input type="hidden" name="shipping_hash" id="shipping_hash" value="<?php echo $this->generateHash( $order->shipping );?>" />
		<?php
		}
	}
	?>
	</span>
</span>
               
<span class="left38 right">
	<span class="inner">
	<?php 
  if (!empty($this->showShipping))
  	echo CitruscartHelperBase::currency($order->order_shipping, $order->currency );
	?>
	</span>
</span>                  
<span class="left62">
	<span class="inner">
	<?php 
		if( !empty($this->showShipping) && $display_shipping_tax && $order->order_shipping_tax )
			echo JText::_('COM_CITRUSCART_SHIPPING_TAX').":";
	?>
	</span>
</span>                  
<span class="left38 right">
	<span class="inner">
	<?php 
		if( !empty($this->showShipping) && $display_shipping_tax && $order->order_shipping_tax )
			echo CitruscartHelperBase::currency( (float) $order->order_shipping_tax, $order->currency );
	?>
	</span>
</span>