<?php 
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); 

$this->form_prefix = 'shipping_input_';

$config = Citruscart::getInstance();
$one_page = $config->get('one_page_checkout', 0);
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
$addressTable = $this->getModel('addresses')->getTable();
?>

<form id="opc-shipping-form" name="opc-shipping-form" action="" method="post">

    <?php if (!empty($this->user->id) && !empty($this->addresses)) { ?>
    <fieldset id="existing-shipping-addresses">
        <select id="existing-shipping-address" name="shipping_address_id">
            <?php foreach ($this->addresses as $address) { ?>
                <option value="<?php echo $address->address_id; ?>"><?php echo is_a($address, 'CitruscartTableAddresses') ? $address->getSummary() : $addressTable->getSummary( $address ); ?></option>
            <?php } ?>
            <option id="create-new-shipping-address" value="0"><?php echo JText::_( "COM_CITRUSCART_NEW_ADDRESS" ); ?></option>
        </select>
    </fieldset>
    <?php } ?>
    
    <fieldset id="new-shipping-address" class="<?php if (!empty($this->user->id) && !empty($this->addresses)) { echo "opc-hidden"; } ?>">
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
				<div class="control-group">
                    <label for="<?php echo $this->form_prefix; ?>address_name"><?php echo JText::_('COM_CITRUSCART_ADDRESS_TITLE'); ?>
                    	<?php echo JText::_('COM_CITRUSCART_ADDRESS_TITLE_FOR_YOUR_REFERENCE'); ?>
                    </label>
                    <input name="<?php echo $this->form_prefix; ?>address_name" id="<?php echo $this->form_prefix; ?>address_name" class="<?php if( $elements['address_name'][1] ) { echo "required"; } ?>" type="text" maxlength="250" data-required="<?php echo $elements['address_name'][1] ? 'true' : false; ?>" />
                </div>
				<?php
			}
		}
		?>

    	<?php if( $elements['title'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>title">
				<?php echo JText::_('COM_CITRUSCART_TITLE'); ?>
			</label>
			<input name="<?php echo $this->form_prefix; ?>title" id="<?php echo $this->form_prefix; ?>title" class="<?php if( $elements['title'][1] ) { echo "required"; } ?>" type="text" maxlength="250" data-required="<?php echo $elements['title'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
    
    	<?php if( $elements['name'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>first_name">
				<?php echo JText::_('COM_CITRUSCART_FIRST_NAME'); ?>
			</label>
			<input name="<?php echo $this->form_prefix; ?>first_name"	id="<?php echo $this->form_prefix; ?>first_name" class="<?php if( $elements['name'][1] ) { echo "required"; } ?>" type="text" maxlength="250" data-required="<?php echo $elements['name'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
    
		<?php if( $elements['middle'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>middle_name">
				<?php echo JText::_('COM_CITRUSCART_MIDDLE_NAME'); ?> 
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>middle_name" id="<?php echo $this->form_prefix; ?>middle_name" class="<?php if( $elements['middle'][1] ) { echo "required"; } ?>" maxlength="250" data-required="<?php echo $elements['middle'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
    
        <?php if( $elements['last'][0] ) :?>
        <div class="control-group">
        	<label for="<?php echo $this->form_prefix; ?>last_name">
        		<?php echo JText::_('COM_CITRUSCART_LAST_NAME'); ?>
        	</label>
        	<input type="text" name="<?php echo $this->form_prefix; ?>last_name" id="<?php echo $this->form_prefix; ?>last_name" class="<?php if( $elements['last'][1] ) { echo "required"; } ?>" size="45" maxlength="250" data-required="<?php echo $elements['last'][1] ? 'true' : false; ?>" />
        </div>
        <?php endif; ?>

    	<?php if( $elements['company'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>company">
				<?php echo JText::_('COM_CITRUSCART_COMPANY'); ?>
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>company" id="<?php echo $this->form_prefix; ?>company" class="<?php if( $elements['company'][1] ) { echo "required"; } ?>" size="48" maxlength="250" data-required="<?php echo $elements['company'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>
    		
		<?php if( $elements['tax_number'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>tax_number">
				<?php echo JText::_('COM_CITRUSCART_CO_TAX_NUMBER'); ?>
			</label>
			<input type="text" name="<?php echo $this->form_prefix; ?>tax_number" id="<?php echo $this->form_prefix; ?>tax_number" class="<?php if( $elements['tax_number'][1] ) { echo "required"; } ?>" size="48" maxlength="250" data-required="<?php echo $elements['tax_number'][1] ? 'true' : false; ?>" />
		</div>
		<?php endif; ?>

    
    	<?php if( $elements['address1'][0] ) :?>
    	<div class="control-group">
    		<label for="<?php echo $this->form_prefix; ?>address_1">
    			<?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1'); ?>
    		</label>
    		<input type="text"	name="<?php echo $this->form_prefix; ?>address_1" id="<?php echo $this->form_prefix; ?>address_1" class="<?php if( $elements['address1'][1] ) { echo "required"; } ?>" size="48" maxlength="250" data-required="<?php echo $elements['address1'][1] ? 'true' : false; ?>" />
    	</div>
    	<?php endif; ?>
    	
    	<?php if( $elements['address2'][0] ) :?>
    	<div class="control-group">
    		<label for="<?php echo $this->form_prefix; ?>address_2">
    			<?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2'); ?>
    		</label>
    		<input type="text" name="<?php echo $this->form_prefix; ?>address_2" id="<?php echo $this->form_prefix; ?>address_2" class="<?php if( $elements['address2'][1] ) { echo "required"; } ?>" size="48" maxlength="250" data-required="<?php echo $elements['address2'][1] ? 'true' : false; ?>" />
    	</div>
    	<?php endif; ?>
    
    	<?php if( $elements['country'][0] ) :?>
    	<div class="control-group">
    		<label class="key">
    			<?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>
    		</label>
    		<?php
    		$url = "index.php?option=com_citruscart&format=raw&controller=checkout&task=getzones&prefix={$this->form_prefix}&country_id=";
    
    		$onchange = 'CitruscartDoTask( \''.$url.'\'+document.getElementById(\''.$this->form_prefix.'country_id\').value, \''.$this->form_prefix.'zones_wrapper\', \'\', \'\', false );';
    
    		$attribs = array('class' => 'required','size' => '1','onchange' => $onchange );
    		echo CitruscartSelect::country( $this->default_country_id, $this->form_prefix.'country_id', $attribs, $this->form_prefix.'country_id', false, true );
    		?>
    	</div>
    	<?php endif; ?>
    
    	<?php if( $elements['city'][0] ) :?>
    	<div class="control-group">
    		<label for="<?php echo $this->form_prefix; ?>city">
    			<?php echo JText::_('COM_CITRUSCART_CITY'); ?>
    		</label>
    		<input type="text" name="<?php echo $this->form_prefix; ?>city" id="<?php echo $this->form_prefix; ?>city" class="<?php if( $elements['city'][1] ) { echo "required"; } ?>" size="48" maxlength="250" />
    	</div>
    	<?php endif; ?>
    
		<?php if( $elements['zone'][0] ) :?>
		<div class="control-group">
			<label class="key">
				<?php echo JText::_('COM_CITRUSCART_STATE_PROVINCE'); ?>
			</label>
			<div id="<?php echo $this->form_prefix; ?>zones_wrapper">
				<?php
			    $attribs = array('class' => 'required','size' => '1' );
				echo CitruscartSelect::zone( '', $this->form_prefix.'zone_id', $this->default_country_id , $attribs, $this->form_prefix.'zone_id' );
				?>
			</div>
		</div>
		<?php endif; ?>
    
    	<?php if( $elements['zip'][0] ) :?>
		<div class="control-group">
			<label for="<?php echo $this->form_prefix; ?>postal_code">
				<?php echo JText::_('COM_CITRUSCART_POSTAL_CODE'); ?>
			</label>
			
			<?php $onchange = ''; ?>
			<input type="text" name="<?php echo $this->form_prefix; ?>postal_code" id="<?php echo $this->form_prefix; ?>postal_code" class="<?php if( $elements['zip'][1] ) { echo "required"; } ?>" size="25" maxlength="250" <?php if ( !empty( $onchange ) ) { ?> onchange="<?php echo $onchange; ?>" <?php } ?> />
		</div>
		<?php endif; ?>
    
    	<?php if( $elements['phone'][0] ) :?>
    	<div class="control-group">
    		<label name="<?php echo $this->form_prefix; ?>phone_1">
    			<?php echo JText::_('COM_CITRUSCART_PHONE'); ?>
    		</label>
    		<input name="<?php echo $this->form_prefix; ?>phone_1" id="<?php echo $this->form_prefix; ?>phone_1" class="<?php if( $elements['phone'][1] ) { echo "required"; } ?>" type="text" size="25" maxlength="250" data-required="<?php echo $elements['phone'][1] ? 'true' : false; ?>" />
    	</div>
    	<?php endif; ?>
    
    	<?php
    	$data = new JObject();
    	
    	JFactory::getApplication()->triggerEvent('onAfterDisplayAddressDetails', array($data, $this->form_prefix) );
    	?>
    
        <ul class="unstyled">
            <li class="control">
                <label for="<?php echo $this->form_prefix; ?>same_as_billing" class="checkbox">
                    <input type="checkbox" onclick="Opc.shipping.setSameAsBilling(this.checked)" title="<?php echo JText::_( "COM_CITRUSCART_USE_BILLING_ADDRESS" ); ?>" value="0" id="<?php echo $this->form_prefix; ?>same_as_billing" name="<?php echo $this->form_prefix; ?>same_as_billing">
                    <?php echo JText::_('COM_CITRUSCART_USE_BILLING_ADDRESS'); ?>
                </label>
            </li>
        </ul>
    </fieldset>
    
    <a id="opc-shipping-button" class="btn btn-primary"><?php echo JText::_('COM_CITRUSCART_CONTINUE') ?></a>
    
</form>
