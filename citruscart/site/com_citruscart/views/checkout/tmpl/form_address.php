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

	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	Citruscart::load( 'CitruscartGrid', 'library.grid' );
	Citruscart::load( 'CitruscartHelperAddresses', 'helpers.addresses' );
	$config = Citruscart::getInstance();
	$one_page =$config->get('one_page_checkout', 0);
	$guest_enabled = $config->get('guest_checkout_enabled', 0);

	switch($this->form_prefix)
	{
		case 'shipping_input_':
			$address_type = '2';
			break;
		default:
		case 'billing_input_':
			$address_type = '1';
			break;
	}
	$elements  = CitruscartHelperAddresses::getAddressElementsData( $address_type );
	$js_strings = array( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES', 'COM_CITRUSCART_UPDATING_CART', 'COM_CITRUSCART_UPDATING_ADDRESS',  'COM_CITRUSCART_UPDATING_PAYMENT_METHODS' );
	CitruscartHelperAddresses::addJsTranslationStrings( $js_strings );
?>

<div id="<?php echo $this->form_prefix; ?>addressForm" class="address_form">
	<?php
		if( $elements['address_name'][0] )
		{
			if( $this->guest && !$one_page )
			{
				echo '<input value="'.JText::_('COM_CITRUSCART_TEMPORARY').'" name="'.$this->form_prefix.'address_name" id="'.$this->form_prefix.'address_name" type="hidden" />';
			}
			else
			{
				?>
	<label class="key" for="<?php echo $this->form_prefix; ?>address_name"><?php echo JText::_('COM_CITRUSCART_ADDRESS_TITLE'); ?>
		<?php if( $elements['address_name'][1] ): ?>
			<?php echo CitruscartGrid::required(); ?>
		<?php endif;?>
		<span class="block"><?php echo JText::_('COM_CITRUSCART_ADDRESS_TITLE_FOR_YOUR_REFERENCE'); ?>

		</span>
	</label>
	<input name="<?php echo $this->form_prefix; ?>address_name" id="<?php echo $this->form_prefix; ?>address_name" class="inputbox" type="text" maxlength="250" data-required="<?php echo $elements['address_name'][1] ? 'true' : false; ?>" />&nbsp;
				<?php
			}
		}
		?>

	<div class="floatbox">
	<?php if( $elements['title'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>title">
				<?php
					echo JText::_('COM_CITRUSCART_TITLE');
					if( $elements['title'][1] ):
						echo CitruscartGrid::required();
					endif;
				?>
			</label>
			<input name="<?php echo $this->form_prefix; ?>title"	id="<?php echo $this->form_prefix; ?>title" class="inputbox"	type="text" maxlength="250" data-required="<?php echo $elements['title'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>


	<?php if( $elements['name'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>first_name">
				<?php echo JText::_('COM_CITRUSCART_FIRST_NAME'); ?>
				<?php if( $elements['name'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<input name="<?php echo $this->form_prefix; ?>first_name"	id="<?php echo $this->form_prefix; ?>first_name" class="inputbox"	type="text" maxlength="250" data-required="<?php echo $elements['name'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>

		<?php if( $elements['middle'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>middle_name">
				<?php echo JText::_('COM_CITRUSCART_MIDDLE_NAME'); ?>
				<?php if( $elements['middle'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>middle_name" id="<?php echo $this->form_prefix; ?>middle_name" class="inputbox"	maxlength="250" data-required="<?php echo $elements['middle'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
	</div>

	<?php if( $elements['last'][0] ) :?>
	<div>
		<label class="key" for="<?php echo $this->form_prefix; ?>last_name">
			<?php echo JText::_('COM_CITRUSCART_LAST_NAME'); ?>
			<?php if( $elements['last'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<input type="text" name="<?php echo $this->form_prefix; ?>last_name"	id="<?php echo $this->form_prefix; ?>last_name" class="inputbox" size="45" maxlength="250" data-required="<?php echo $elements['last'][1] ? 'true' : false; ?>" />
	</div>
	<?php endif; ?>

<div class="floatbox">
	<?php if( $elements['company'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>company">
				<?php echo JText::_('COM_CITRUSCART_COMPANY'); ?>
				<?php if($elements['company'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>company" id="<?php echo $this->form_prefix; ?>company" class="inputbox" size="48" maxlength="250" data-required="<?php echo $elements['company'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
		<?php if( $elements['tax_number'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>tax_number">
				<?php echo JText::_('COM_CITRUSCART_CO_TAX_NUMBER'); ?>
				<?php if( $elements['tax_number'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>tax_number" id="<?php echo $this->form_prefix; ?>tax_number" class="inputbox" size="48" maxlength="250" data-required="<?php echo $elements['tax_number'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
	</div>

	<?php if( $elements['address1'][0] ) :?>
	<div>
		<label class="key" for="<?php echo $this->form_prefix; ?>address_1">
			<?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1'); ?>
			<?php if( $elements['address1'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<input type="text"	name="<?php echo $this->form_prefix; ?>address_1" id="<?php echo $this->form_prefix; ?>address_1" class="inputbox" size="48" maxlength="250" data-required="<?php echo $elements['address1'][1] ? 'true' : false; ?>" />
	</div>
	<?php endif; ?>

	<?php if( $elements['address2'][0] ) :?>
	<div>
		<label class="key" for="<?php echo $this->form_prefix; ?>address_2">
			<?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2'); ?>
			<?php if( $elements['address2'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<input type="text" name="<?php echo $this->form_prefix; ?>address_2" id="<?php echo $this->form_prefix; ?>address_2" class="inputbox" size="48" maxlength="250" data-required="<?php echo $elements['address2'][1] ? 'true' : false; ?>" />
	</div>
	<?php endif; ?>

	<?php if( $elements['country'][0] ) :?>
	<div>
		<label class="key">
			<?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>
			<?php if( $elements['country'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<?php
		$url = "index.php?option=com_citruscart&format=raw&controller=checkout&task=getzones&prefix={$this->form_prefix}&country_id=";

		$onchange = 'citruscartPutAjaxLoader( \''.$this->form_prefix.'zones_wrapper\' );citruscartDoTask( \''.$url.'\'+document.getElementById(\''.$this->form_prefix.'country_id\').value, \''.$this->form_prefix.'zones_wrapper\', \'\', \'\', false, function() {CitruscartCheckoutAutomaticShippingRatesUpdate( \''.$this->form_prefix.'country_id\' ); });';
		if( $one_page )
		{
			$onchange = 'citruscartPutAjaxLoader( \''.$this->form_prefix.'zones_wrapper\' );'.
									'citruscartDoTask( \''.$url.'\'+document.getElementById(\''.$this->form_prefix.'country_id\').value, \''.$this->form_prefix.'zones_wrapper\', \'\', \'\', false, '.
									'function() {citruscartCheckoutAutomaticShippingRatesUpdate( \''.$this->form_prefix.'country_id\' ); '.
									'
			});';
		}

		$attribs = array('class' => 'inputbox','size' => '1','onchange' => $onchange );
		echo CitruscartSelect::country( $this->default_country_id, $this->form_prefix.'country_id', $attribs, $this->form_prefix.'country_id', false, true );
		?>
	</div>
	<?php endif; ?>

	<?php if( $elements['city'][0] ) :?>
	<div>
		<label class="key" for="<?php echo $this->form_prefix; ?>city">
			<?php echo JText::_('COM_CITRUSCART_CITY'); ?>
			<?php if( $elements['city'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<input type="text" name="<?php echo $this->form_prefix; ?>city" id="<?php echo $this->form_prefix; ?>city" class="inputbox" size="48" maxlength="250" />
	</div>
	<?php endif; ?>

	<div class="floatbox">

		<?php if( $elements['zone'][0] ) :?>
		<div>
			<label class="key">
				<?php echo JText::_('COM_CITRUSCART_ZONE'); ?>
				<?php if( $elements['zone'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<div id="<?php echo $this->form_prefix; ?>zones_wrapper">
				<?php
				if (!empty($this->zones)) {
					echo $this->zones;
				} else {
					echo JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
				}
				?>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<?php if( $elements['zip'][0] ) :?>
		<div>
			<label class="key" for="<?php echo $this->form_prefix; ?>postal_code">
				<?php echo JText::_('COM_CITRUSCART_POSTAL_CODE'); ?>
				<?php if( $elements['zip'][1] ): ?>
					<?php echo CitruscartGrid::required(); ?>
				<?php endif;?>
			</label>
			<?php
			$onchange = '';
			if( !empty( $this->showShipping ) )
      {
        if( $one_page )
		  		$onchange = 'citruscartCheckoutAutomaticShippingRatesUpdate( \''.$this->form_prefix.'postal_code\' )';
        else
			    $onchange = 'citruscartGrayOutAddressDiv( \'Updating Address\' ); citruscartGetShippingRates( \'onCheckoutShipping_wrapper\', document.adminForm, citruscartDeleteAddressGrayDiv );';
			}
      ?>
			<input type="text" name="<?php echo $this->form_prefix; ?>postal_code" id="<?php echo $this->form_prefix; ?>postal_code" class="inputbox" size="25" maxlength="250" <?php if ( strlen( $onchange ) ) { ?> onchange="<?php echo $onchange; ?>" <?php } ?> />
		</div>
		<?php endif; ?>

	<?php if( $elements['phone'][0] ) :?>
	<div>
		<label class="key" name="<?php echo $this->form_prefix; ?>phone_1">
			<?php echo JText::_('COM_CITRUSCART_PHONE'); ?>
			<?php if( $elements['phone'][1] ): ?>
				<?php echo CitruscartGrid::required(); ?>
			<?php endif;?>
		</label>
		<input name="<?php echo $this->form_prefix; ?>phone_1" id="<?php echo $this->form_prefix; ?>phone_1" class="inputbox" type="text" size="25" maxlength="250" data-required="<?php echo $elements['phone'][1] ? 'true' : false; ?>" />
	</div>
	<?php endif; ?>



	<?php
	$data = new JObject();

	JFactory::getApplication()->triggerEvent('onAfterDisplayAddressDetails', array($data, $this->form_prefix) );
	?>

</div>
