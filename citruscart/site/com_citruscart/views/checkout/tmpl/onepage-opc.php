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
	JHTML::_('stylesheet', 'Citruscart_checkout_onepage.css', 'media/citruscart/css/');
	JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
	JHTML::_('script', 'Citruscart_checkout.js', 'media/citruscart/js/');
	JHTML::_('script', 'Citruscart_checkout_onepage.js', 'media/citruscart/js/');
	JHTML::_('behavior.mootools' );
	Citruscart::load('CitruscartHelperImage', 'helpers.image');
	$image = CitruscartHelperImage::getLocalizedName("help_tooltip.png", Citruscart::getPath('images'));
	$enable_tooltips = Citruscart::getInstance()->get('one_page_checkout_tooltips_enabled', 0);
	$display_credits = Citruscart::getInstance()->get( 'display_credits', '0' );
	$guest_enabled = Citruscart::getInstance()->get('guest_checkout_enabled', 0);
	
	$this->section = 1;
	$js_strings = array( 'COM_CITRUSCART_UPDATING_PAYMENT_METHODS', 'COM_CITRUSCART_CHECKING_COUPON',
											 'COM_CITRUSCART_UPDATING_BILLING', 'COM_CITRUSCART_UPDATING_SHIPPING_RATES', 
											 'COM_CITRUSCART_UPDATING_CART', 'COM_CITRUSCART_UPDATING_ADDRESS', 'COM_CITRUSCART_VALIDATING' );
	
	CitruscartHelperImage::addJsTranslationStrings( $js_strings );
?>
<a name="Citruscart-method"></a> 

<div id="citruscart_checkout_pane">
<a name="CitruscartRegistration" id="citruscartRegistration"></a>

<?php // login link ?>
<?php if(!$this->user->id ) : ?>
	<div class="citruscart_checkout_method">
		<?php
			$uri = JFactory::getURI( );
			$return_link = base64_encode( $uri->__toString( ) );
			$asklink = "index.php?option=com_citruscart&view=checkout&task=registrationLink&tmpl=component&return=" . $return_link;
				
				$asktxt = CitruscartUrl::popup( "{$asklink}.&tmpl=component", JText::_('COM_CITRUSCART_CLICK_HERE_TO_LOGIN'),
						array(
							'width' => '490', 'height' => '320'
						) );
				$asktxt = "<a class=\"Citruscart-modal\" href='{$asklink}'>";
				$asktxt .= JText::_('COM_CITRUSCART_CLICK_HERE_TO_LOGIN');
				$asktxt .= "</a>";
		?>
		[<?php echo $asktxt; ?>]
	</div>
<?php endif; ?>

<form action="<?php echo JRoute::_( $form['action'] ); ?>" method="post" name="adminForm" enctype="multipart/form-data">

<div class="floatbox">

	<!-- CUSTOMER, BILLING & SHIPPING ADDRESS FORMS -->
	<div class="opc-customer-billship-address">
		<div class="inner col3">
			
			<div class="contentheading">
				<?php echo $this->section.'. '.JText::_('COM_CITRUSCART_CUSTOMER_INFORMATION'); $this->section++; ?>
				<?php if( $enable_tooltips ): ?>
				<a class="img_tooltip" href="" > 
					<img src="<?php echo Citruscart::getUrl('images').$image; ?>" alt='<?php echo JText::_('COM_CITRUSCART_HELP'); ?>' />
					<span>
						<?php echo JText::_('COM_CITRUSCART_ORDER_INFORMATION_WILL_BE_SENT_TO_YOUR_ACCOUNT_E-MAIL_LISTED_BELOW'); ?>												
					</span>
				</a>
				<?php endif; ?>
			</div>
				
			<!-- ID-CUSTOMER PANE -->
			<div id="citruscart_customer">
				<div class="citruscart_checkout_method_user_email">
					<?php
						if($this->user->id)
							$email_address = $this->user->email;
						else
							$email_address = '';
					?>

					<?php echo JText::_('COM_CITRUSCART_E-MAIL_ADDRESS');?>:<br/>
						<input type="text" id="email_address" class="inputbox" name="email_address" value="<?php echo $email_address; ?>" onblur="CitruscartCheckoutCheckEmail( 'user_email_validation',document.adminForm, '<?php echo JText::_('COM_CITRUSCART_VALIDATING'); ?>' )"/> *
				</div>
				<div id="user_email_validation"></div>
			</div>
			<!-- ID-CUSTOMER PANE END -->
			
			<!-- BILLING-SHIPPING PANE -->
			<div class="citruscart-expanded" id="billing-shipping-pane">
				
				<div class="contentheading">
					<?php echo $this->showShipping ? JText::_('COM_CITRUSCART_BILLING_AND_SHIPPING_INFORMATION') : JText::_('COM_CITRUSCART_BILLING_INFORMATION'); ?>
				</div>
				
				<div id="citruscart_billing-shipping">
	        <div id="billingAddress">						
						<div>
							<?php echo JText::_('COM_CITRUSCART_BILLING_ADDRESS')?>
						</div>
        			<?php 
						$baseurl = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getAddress&address_id=";                   
	            		$billattribs = array(
	                		'class' => 'inputbox',    
	                    	'size' => '1',
	                    	'onchange' => "CitruscartCheckoutSetBillingAddress('$baseurl'+this.options[this.selectedIndex].value, 'billingDefaultAddress', this.options[this.selectedIndex].value, this.form );"
	                	);
	                        
	                	// display select list of stored addresses
	                	echo CitruscartSelect::address( $this->user->id, $this->billing_address->address_id, 'billing_address_id', 1, $billattribs, 'billing_address_id', false, true );
	           		?>
						
						<div id="billingDefaultAddress">
							<?php 
								if ( !empty( $this->billing_address ) ):
									echo $this->billing_address->title . " ". $this->billing_address->first_name . " ". $this->billing_address->last_name . "<br>";
									echo $this->billing_address->company . "<br>";
									echo $this->billing_address->address_1 . " " . $this->billing_address->address_2 . "<br>";
									echo $this->billing_address->city . ", " . $this->billing_address->zone_name ." " . $this->billing_address->postal_code . "<br>";
									echo $this->billing_address->country_name . "<br>";
								endif;
							?>
						</div>
						<?php echo $this->billing_address_form; ?>
					</div>
     			<div class="reset marginbot"></div>
					<?php if(!$this->user->id ) : ?>
					<div class="citruscart_checkout_method">
						<input type="checkbox" id="create_account" name="create_account" <?php if( !$guest_enabled ) echo 'checked disabled'; ?> value="on" />
						<label for="field-create-account"><?php echo JText::_('COM_CITRUSCART_CREATE_A_NEW_ACCOUNT');?></label>
						<div id="citruscart_user_additional_info" <?php if( $guest_enabled ) echo 'class="hidden"'; ?>>
                <?php echo $this->form_user_register;?>
             </div>
     			</div>
     			<?php endif; ?>
           		
     			<?php if($this->showShipping):?>				
     			<div class="reset marginbot"></div>
							<div>
								<?php echo JText::_('COM_CITRUSCART_SHIPPING_ADDRESS'); ?>
							</div>
          			<div class="reset marginbot"></div>
		            <div id="shippingAddress">
					<!--    SHIPPING ADDRESS  -->	         
	                <?php if (empty($this->shipping_address)) : ?>
	                    <div>
	                        <input id="sameasbilling" name="sameasbilling" type="checkbox" checked="checked" onclick="citruscartShowHideDiv( 'shipping_input_addressForm' ); CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm ); CitruscartGetPaymentOptions( 'onCheckoutPayment_wrapper', document.adminForm ); "/>&nbsp;
	                        <?php echo JText::_('COM_CITRUSCART_SAME_AS_BILLING_ADDRESS'); ?>
	                    </div>
					<?php endif; ?>
            		<?php
		                $shipattribs = array(
		                   'class' => 'inputbox',    
		                   'size' => '1',
		                   'onchange' => "CitruscartCheckoutSetShippingAddress('$baseurl'+this.options[this.selectedIndex].value, 'shippingDefaultAddress', this.form, this.options[this.selectedIndex].value ); "
		                );
		                
		                // display select list of stored addresses
		                echo CitruscartSelect::address( JFactory::getUser()->id, $this->shipping_address->address_id, 'shipping_address_id', 2, $shipattribs, 'shipping_address_id', false, true );
					?>
						<div id="shippingDefaultAddress">
							<?php 
								if ( !empty( $this->shipping_address ) )
								{
					        		echo $this->shipping_address->title . " ". $this->shipping_address->first_name . " ". $this->shipping_address->last_name . "<br>";
									echo $this->shipping_address->company . "<br>";
									echo $this->shipping_address->address_1 . " " . $this->shipping_address->address_2 . "<br>";
									echo $this->shipping_address->city . ", " . $this->shipping_address->zone_name ." " . $this->shipping_address->postal_code . "<br>";
									echo $this->shipping_address->country_name . "<br>";
								}
							?>
						 </div>
							  <?php echo $this->shipping_address_form; ?>
					</div>
	           		<?php else :?>
			             <input type="hidden" id="shippingrequired" name="shippingrequired" value="0"  />
			        <?php endif;?>           
				</div>		
			</div> 
			<!-- BILLING-SHIPPING PANE END -->
		</div>
	</div>
	<!-- CUSTOMER, BILLING & SHIPPING ADDRESS FORMS -->
	
	<!-- RIGHT SIDE OF THE LAYOUT -->
	<div class="right-side">
	
		<!-- SHIPPING METHOD -->
		<div class="opc-method">	
			<div class="inner col3">	 
				<?php if($this->showShipping):?>	
				<div class="citruscart-expanded" id="shippingcost-pane">
					<div class="contentheading">
						<?php echo $this->section.'. '.JText::_('COM_CITRUSCART_SELECT_A_SHIPPING_METHOD'); $this->section++; ?>
					</div>
					<div id="onCheckoutShipping_wrapper">
						<?php echo $this->shipping_method_form;?>
					</div>		
				</div>  
				<?php endif;?>
			</div> 
		</div>
		<!-- SHIPPING METHOD END -->

        <?php if (!empty($this->onBeforeDisplaySelectPayment)) : ?>
            <div id='onBeforeDisplaySelectPayment_wrapper'>
            <?php echo $this->onBeforeDisplaySelectPayment; ?>
            </div>
        <?php endif; ?>			
		<!-- PAYMENT METHOD -->
		<div class="opc-method">
			<div class="inner col3">	
				<div class="citruscart-expanded" id="paymentmethod-pane">
					<div class="contentheading">
						<?php echo $this->section.'. '.JText::_('COM_CITRUSCART_SELECT_A_PAYMENT_METHOD'); $this->section++;?>
								<?php if( $enable_tooltips ) : ?>
								<a class="img_tooltip" href="" > 
									<img src="<?php echo Citruscart::getUrl('images').$image; ?>" alt='<?php echo JText::_('COM_CITRUSCART_HELP'); ?>' />
									<span class="img_tooltip_left">
										<?php echo JText::_('COM_CITRUSCART_PLEASE_SELECT_YOUR_PREFERRED_PAYMENT_METHOD_BELOW'); ?>												
									</span>
								</a>
								<?php endif; ?>
					</div>		
					<div id="onCheckoutPayment_wrapper">
						<?php if(!count($this->payment_plugins)):?>
								<div class="note">
										<?php echo JText::_('COM_CITRUSCART_NO_PAYMENT_METHOD_AVAILABLE_FOR_YOUR_ADDRESS'); ?>
								</div>
						<?php endif;?>
						<?php echo $this->payment_options_html;?>                   
					</div>		
				</div> 
			</div>
		</div>
		<!-- PAYMENT METHOD END -->
        <?php if (!empty($this->onAfterDisplaySelectPayment)) : ?>
            <div id='onAfterDisplaySelectPayment_wrapper'>
            <?php echo $this->onAfterDisplaySelectPayment; ?>
            </div>
        <?php endif; ?>			
		
		<!-- REVIEW & PLACE ORDER -->
		<div class="opc-review-place-order">
			<div class="inner col3">
				
				<!--    ORDER SUMMARY   -->
				<h3 class="contentheading">
					<?php echo $this->section.'. '.JText::_('COM_CITRUSCART_REVIEW_AND_PLACE_ORDER');$this->section++; ?>
				</h3>
				<div id='onCheckoutCart_wrapper'> 
					<?php echo $this->orderSummary; 	?> 
				</div>
				<!--    ORDER SUMMARY END  -->
				
				<div class="reset marginbot"></div>
				
				<?php $coupons_enabled = Citruscart::getInstance()->get('coupons_enabled'); ?>
		 		<?php if ($coupons_enabled && $this->coupons_present) : ?>
					<div class="citruscart-expanded" id="coupon-pane">						
						<div id="coupon_code_area">
		            	 	<div id="coupon_code_form">  
		            	 		<div class="contentheading">
									<?php echo JText::_('COM_CITRUSCART_COUPON_CODE')?>
									<?php $mult_enabled = Citruscart::getInstance()->get('multiple_usercoupons_enabled'); ?>
			            			<?php $string = "COM_CITRUSCART_COUPON_CODE_HELP"; if ($mult_enabled) { $string = "COM_CITRUSCART_COUPON_CODE_HELP_MULTIPLE"; } ?>
			            	<?php if( $enable_tooltips ) : ?>
			            			<a class="img_tooltip" href="" > 
										<img src="<?php echo Citruscart::getUrl('images').$image; ?>" alt='<?php echo JText::_('COM_CITRUSCART_HELP'); ?>' />
										<span>
											<?php echo JText::_($string); ?>												
										</span>
									</a>
									<?php endif; ?>
								</div>    	           	 			
		            			<div id="coupon_code_message"></div>
		            			<input type="text" name="new_coupon_code" id="new_coupon_code" value="" />
		            			<input type="button" name="coupon_submit" value="<?php echo JText::_('COM_CITRUSCART_ADD_COUPON_TO_ORDER'); ?>"  onClick="CitruscartAddCoupon( document.adminForm, '<?php if ($mult_enabled) { echo "1"; } else { echo "0"; } ?>' );"/>
		            		</div>
		            		<div id='coupon_codes' style="display: none;"></div>
		        		</div>	
					</div>  
				<?php endif;?>
								
				<?php if( $display_credits ): ?>
				<div class="reset marginbot"></div>
				<?php if ($this->userinfo->credits_total > '0.00') : ?>
            	<!-- STORE CREDITS -->
		            <div id="credits_area" class="address">
		                <div id="credits_form">
		                <h3><?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?></h3>
		                <div id="credit_help"><?php echo sprintf( JText::_('COM_CITRUSCART_YOU_HAVE_STORE_CREDIT'), CitruscartHelperBase::currency( $this->userinfo->credits_total, Citruscart::getInstance()->get( 'default_currencyid', 1) ) ); ?></div>
		                <div id="credit_message"></div>
		                <input type="text" name="apply_credit_amount" id="apply_credit_amount" value="" />
		                <input type="button" name="credit_submit" value="<?php echo JText::_('COM_CITRUSCART_APPLY_CREDIT_TO_ORDER'); ?>"  onClick="CitruscartAddCredit( document.adminForm );"/>
		                </div>
		            </div>
		        <?php endif; ?>
		        <div id='applied_credit' style="display: none;"></div>				
				<?php endif; ?>				
				<div class="reset marginbot"></div>
							
				<div class="citruscart-expanded" id="comments-pane">
				<div class="contentheading">
					<?php echo JText::_('COM_CITRUSCART_ORDER_COMMENTS')?>
					<?php if( $enable_tooltips ): ?>
					<a class="img_tooltip" href="" > 
						<img src="<?php echo Citruscart::getUrl('images').$image; ?>" alt='<?php echo JText::_('COM_CITRUSCART_HELP'); ?>' />
						<span>
							<?php echo JText::_('COM_CITRUSCART_USE_THIS_AREA_FOR_SPECIAL_INSTRUCTIONS_OR_QUESTIONS_REGARDING_YOUR_ORDER');?>												
						</span>
					</a>
					<?php endif; ?>
				</div>
			
				<div id="citruscart_comments">	
					<textarea id="customer_note" name="customer_note" rows="5" cols="41"></textarea>		
				</div>		
				</div>  
				
				<div class="reset marginbot"></div>	
				<div class="citruscart-expanded" id="shipping_terms-pane">
				 <?php 
		    		if( Citruscart::getInstance()->get('require_terms', '1') )
		    		{
		    			$terms_article = Citruscart::getInstance()->get('article_terms');
		    			$terms_link = JRoute::_('index.php?option=com_content&view=article&id='.$terms_article);
		    		?>
	            	<div><?php echo JText::_('COM_CITRUSCART_TERMS_AND_CONDITIONS'); ?></div>
					<div id="shipping_terms" >
						<br/>
						<input type="checkbox" name="shipping_terms" value="1" /> <a href="<?php echo $terms_link; ?>" target="_blank"><?php echo JText::_('COM_CITRUSCART_ACCEPT_TERMS_AND_CONDITIONS');?></a>
	         			<br/>
	            	</div>
					
	        	<?php } ?>
				</div>
				<div id="validationmessage" style="padding-top: 10px;"></div> 
				<div id="citruscart_btns">
					<input type="button" class="btn" onclick="CitruscartSaveOnepageOrder('Citruscart_checkout_pane', 'validationmessage', this.form )" value="<?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_CONTINUE'); ?>" />
					<div class="reset marginbot"></div>	
					<a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=carts'); ?>"><?php echo JText::_('COM_CITRUSCART_RETURN_TO_SHOPPING_CART'); ?></a> 
				</div>
			</div>
		</div> 
		<!-- REVIEW & PLACE ORDER END -->
	
	</div>
	<!-- RIGHT SIDE OF THE LAYOUT -->

</div>
<!-- END FLOATBOX -->

</div>

<input type="hidden" id="currency_id" name="currency_id" value="<?php echo $this->order->currency_id; ?>" />
<input type="hidden" id="order_total" name="order_total" value="<?php echo $this->order->order_total; ?>" />
<input type="hidden" id="task" name="task" value="onepageSaveOrder" />
<?php echo JHTML::_( 'form.token' ); ?>

</form>
<div id="refreshpage" style="display: none; text-align: right;"><a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout')?>"><?php echo JText::_('COM_CITRUSCART_BACK')?></a></div>

<script type="text/javascript">
window.addEvent('domready', function() {
<?php if( $this->billing_address->address_id ): ?>
	citruscartShowHideDiv( 'billing_input_addressForm' );
<?php endif; ?>

<?php if( $this->showShipping  ):?>	
	citruscartShowHideDiv( 'shipping_input_addressForm' );
	<?php if( !$this->shipping_address->address_id ): ?>
		document.id( 'sameasbilling' ).addEvent( 'change', function() { CitruscartCopyBillingAdToShippingAd( document.getElementById( 'sameasbilling' ), document.adminForm ) } );
	<?php endif; ?>
<?php endif; ?>

<?php if( !$this->user->id ) : ?>
	CitruscartHideInfoCreateAccount();
<?php endif; ?>
});
</script>