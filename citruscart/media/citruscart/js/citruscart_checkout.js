/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @return
 */
function CitruscartGetPaymentForm( element, container )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=getPaymentForm&format=raw&payment_element=' + element;

   	CitruscartGrayOutAjaxDiv( container, Joomla.JText._( 'COM_CITRUSCART_UPDATING_PAYMENT_METHODS' ) );
	CitruscartDoTask( url, container, document.adminForm, '', false, CitruscartDeletePaymentGrayDiv );    	
}


function CitruscartGetShippingRates( container, form, callback )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=updateShippingRates&format=raw';
    
	// loop through form elements and prepare an array of objects for passing to server
	var str = CitruscartGetFormInputData( form );
	
   	CitruscartGrayOutAjaxDiv( container, Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
   	
   	// execute Ajax request to server
    var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
            CitruscartJQ( container ).html( resp.msg );
            if( resp.default_rate && resp.default_rate != null ) { 
                // if only one rate was found - set it as default
                CitruscartSetShippingRate(resp.default_rate['name'], resp.default_rate['price'], resp.default_rate['tax'], resp.default_rate['extra'], resp.default_rate['code'], callback != null );                
            }
            
            if (typeof callback == 'function') {
                callback();
            }
            return true;
        },
        onFailure : function(response) {
            CitruscartDeleteShippingGrayDiv();
        },
        onException : function(response) {
            CitruscartDeleteShippingGrayDiv();
        }
    }).send();
    
    CitruscartDeleteShippingGrayDiv();
}

function CitruscartSetShippingRate(name, price, tax, extra, code, combined )
{
	CitruscartJQ('shipping_name').value = name;
	CitruscartJQ('shipping_code').value = code;
	CitruscartJQ('shipping_price').value = price;
	CitruscartJQ('shipping_tax').value = tax;
	CitruscartJQ('shipping_extra').value = extra;

	CitruscartGrayOutAjaxDiv( 'onCheckoutShipping_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
	CitruscartGrayOutAjaxDiv( 'onCheckoutCart_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_CART' ) );		
	CitruscartGetCheckoutTotals( combined ); // combined = true - both shipping rates and addresses are updating at the same time
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @param combined If true, both shipping rated and addresses are updating at the same time
 * @return
 */
function CitruscartGetCheckoutTotals( combined )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=setShippingMethod&format=raw';
//    if( typeof( combined ) == 'undefined' )
 //   	CitruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false );
    if( combined )
    	CitruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false, CitruscartDeleteCombinedGrayDiv );    	
    else
    	CitruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false, CitruscartDeleteShippingGrayDiv );
}

/**
 * Recalculates the currency amounts
 * @return
 */
function CitruscartGetCurrencyTotals()
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=setCurrency&format=raw';
    CitruscartDoTask( url, 'onCheckoutReview_wrapper', document.adminForm );    
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @return
 */
function CitruscartRefreshTotalAmountDue()
{
	if( CitruscartJQ( 'payment_info' ) )
	{
		var url = 'index.php?option=com_citruscart&view=checkout&task=totalAmountDue&format=raw';
		CitruscartGrayOutAjaxDiv( 'payment_info', Joomla.JText._( 'COM_CITRUSCART_UPDATING_BILLING' ) ); 
	    CitruscartDoTask( url, 'totalAmountDue', document.adminForm, '', false, CitruscartDeleteTotalAmountDueGrayDiv );		
	}
}

/**
 * If Same as Billing checkbox is selected
 * this disables all the input fields in the shipping address form
 * 
 * @param checkbox
 * @return
 */
function CitruscartDisableShippingAddressControls(checkbox, form)
{
    
	var disable = false;
    if (checkbox.checked){
        disable = true;
        CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', form );
    }
    
    var fields = "address_name;address_id;title;first_name;middle_name;last_name;company;tax_number;address_1;address_2;city;country_id;zone_id;postal_code;phone_1;phone_2;fax";
    var fieldList = fields.split(';');

//    for(var index=0;index<fieldList.length;index++){
//        shippingControl = document.getElementById('shipping_input_'+fieldList[index]);
//        if(shippingControl != null){
//            shippingControl.disabled = disable;
//        }
//    }
 
    for(var index=0;index<fieldList.length;index++){
    	billingControl = document.getElementById('billing_input_'+fieldList[index]);
        shippingControl = document.getElementById('shipping_input_'+fieldList[index]);
        if(shippingControl != null){
    		shippingControl.disabled = disable;           
            if(billingControl != null)
            {
            	if( fieldList[index] == 'zone_id' ) // special care for zones
            	{
            		if( disable )
            			CitruscartDoTask( 'index.php?option=com_citruscart&format=raw&controller=checkout&task=getzones&prefix=shipping_input_&disabled=1&country_id='+document.getElementById('billing_input_country_id').value+'&zone_id='+document.getElementById('billing_input_zone_id').value, 'shipping_input_zones_wrapper', '');
            		else
            			shippingControl.disabled = false;
            	}
            	else // the rest of fields is OK the way they are handled now
            		{
            			if( shippingControl.getAttribute( 'type' ) != 'hidden' )
            				shippingControl.value = disable ? billingControl.value : '';            		
            		}
            }
        }
    }
    
    CitruscartDeleteGrayDivs();
}

function CitruscartManageShippingRates()
{
	CitruscartJQ('shipping_form_div').getElements('input[name=shipping_rate]').addEvent('click', function() {
		CitruscartGetCheckoutTotals();
	}
	);
}

function CitruscartDeleteAddressGrayDiv()
{
	el_billing = $$( '#billingAddress .CitruscartAjaxGrayDiv' );
	if( !el_billing )
		return;
	CitruscartSetColorInContainer( 'billingAddress', '' );
	el_billing.destroy();
	
	if( CitruscartJQ( 'shippingAddress' ) && ( !CitruscartJQ( 'sameasbilling' ) || ( CitruscartJQ( 'sameasbilling' ) && !CitruscartJQ( 'sameasbilling' ).checked ) ) )
	{
		CitruscartSetColorInContainer( 'shippingAddress', '' );
		$$( '#shippingAddress .CitruscartAjaxGrayDiv' ).destroy();		
	}
}

function CitruscartDeletePaymentGrayDiv()
{
	if( CitruscartJQ( 'onCheckoutPayment_wrapper' ) )
		CitruscartSetColorInContainer( 'onCheckoutPayment_wrapper', '' );
}

function CitruscartDeleteTotalAmountDueGrayDiv()
{
	el = $$( '#payment_info .CitruscartAjaxGrayDiv' );
	if( el != '' )
		el.destroy();
	
	CitruscartSetColorInContainer( 'payment_info', '' );
}

function CitruscartDeleteShippingGrayDiv()
{
	if( CitruscartJQ( 'onCheckoutShipping_wrapper' ) == null )
		return;

	el = $$( '#onCheckoutShipping_wrapper .CitruscartAjaxGrayDiv' );
	if( el != '' )
		el.destroy();

	
	if( CitruscartJQ( 'onCheckoutShipping_wrapper' ).css( 'color' ) != '' )
	{
		CitruscartSetColorInContainer( 'onCheckoutShipping_wrapper', '' );
		
		// selected shipping rate has to be checked manually
		if( CitruscartJQ( 'shipping_name' ) )
		{
			shipping_plugin = CitruscartJQ( 'shipping_name' ).get( 'value' );
			$$( '#onCheckoutShipping_wrapper input[type=radio]' ).each( function( e ){
				if( e.get( 'rel' ) == shipping_plugin )
					e.set( 'checked', true );
			} );			
		}
	}
	CitruscartDeleteCartGrayDiv();
}

function CitruscartDeleteCartGrayDiv()
{
	if( CitruscartJQ('onCheckoutCart_wrapper') )
		CitruscartSetColorInContainer( 'onCheckoutCart_wrapper', '' );
}

function CitruscartDeleteCombinedGrayDiv()
{
	CitruscartDeleteAddressGrayDiv();

	if( CitruscartJQ( 'onCheckoutShipping_wrapper' ) )
		CitruscartDeleteShippingGrayDiv();
	else // no shipping address so delete gray div from cart
		CitruscartDeleteCartGrayDiv();
}

function CitruscartGrayOutAddressDiv( prefix )
{
	if( !CitruscartJQ( 'shippingAddress' ) )
		return;
	values = CitruscartStoreFormInputs( document.adminForm );
	CitruscartGrayOutAjaxDiv( 'billingAddress', Joomla.JText._( 'COM_CITRUSCART_UPDATING_ADDRESS=' ), prefix );
	if( CitruscartJQ( 'shippingAddress' ) && ( !CitruscartJQ( 'sameasbilling' ) || ( CitruscartJQ( 'sameasbilling' ) && !CitruscartJQ( 'sameasbilling' ).checked ) ) )
		CitruscartGrayOutAjaxDiv( 'shippingAddress', Joomla.JText._( 'COM_CITRUSCART_UPDATING_ADDRESS=' ), prefix );
	CitruscartRestoreFormInputs( document.adminForm , values );
}

/*
 * Method to disable UI and update shipping rates
 * 
 */
function CitruscartCheckoutAutomaticShippingRatesUpdate( obj_id )
{
	obj = document.getElementById( obj_id );

	// see, if you find can find payment_wrapper and update payment methods
	if( $( 'onCheckoutPayment_wrapper' ) && obj_id.substr( 0, 8 ) == 'billing_' ) // found the payment_wrapper - update payment methods && this is a billing input
	{
		if( !$( 'shippingAddress' ) ) // no shipping
		{
			CitruscartGrayOutAddressDiv();
			CitruscartGetPaymentOptions('onCheckoutPayment_wrapper', document.adminForm, '', CitruscartDeleteAddressGrayDiv );
		}
		else
			CitruscartGetPaymentOptions('onCheckoutPayment_wrapper', document.adminForm, '' );
	}

	if( !$( 'shippingAddress' ) ) {
	    // no shipping
	    return;        
	}		

	only_shipping = !CitruscartJQ( 'sameasbilling' ) || !CitruscartJQ( 'sameasbilling' ).get( 'checked' );
	if( only_shipping )
	{
		CitruscartGrayOutAddressDiv();
		CitruscartGrayOutAjaxDiv( 'onCheckoutShipping_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
		if( obj_id.substr( 0, 9 ) == 'shipping_' ) // shipping input
		{
			CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm, CitruscartDeleteAddressGrayDiv );
		}
		else // billing input
		{
			CitruscartGrayOutAjaxDiv( 'onCheckoutCart_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_CART' ) );
			CitruscartGetCheckoutTotals( true );
		}
	}
	else // same as billing
	{
		if( obj_id.substr( 0, 8 ) == 'billing_' ) // billing input
		{
			CitruscartGrayOutAddressDiv();
			CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm, CitruscartDeleteAddressGrayDiv );
		}
	}
}

/**
 * Simple function to check a password strength
 * 
 */
function CitruscartCheckPassword( container, form, psw, min_length, req_num, req_alpha, req_spec )
{
    val_errors = [];
	var pass_ok = true;
		
	act_pass = CitruscartJQ( psw ).get( 'value' );
	if( act_pass.length < min_length ) // password is not long enough
	{
	    str = Joomla.JText._('COM_CITRUSCART_PASSWORD_MIN_LENGTH');
	    str = str.replace('%s',min_length);
	    val_errors.push(str);
		pass_ok = false;
	}
	else
	{
		if( req_num ) // checks, if the password contains a number
		{
			var patt_num = /\d/;
			has_num = patt_num.test( act_pass );
			if (!has_num) {
			    str = Joomla.JText._('COM_CITRUSCART_PASSWORD_REQ_NUMBER');
			    val_errors.push(str);
			    pass_ok = false;
			}
		}
		
		if( pass_ok && req_alpha ) // checks, if the password contains an alphabetical character
		{
			var patt_alpha = /[a-zA-Z]/;
			has_alpha = patt_alpha.test( act_pass );
            if (!has_alpha) {
                str = Joomla.JText._('COM_CITRUSCART_PASSWORD_REQ_ALPHA');
                val_errors.push(str);
                pass_ok = false;
            }
		}

		if( pass_ok && req_spec ) // checks, if the password contains a special character ?!@#$%^&*{}[]()-=+.,:\\/\"<>'_;|
		{
			var patt_spec = /[\\/\|_\-\+=\.\"':;\[\]~<>!@?#$%\^&\*()]/;
			has_special = patt_spec.test( act_pass );
            if (!has_special) {
                str = Joomla.JText._('COM_CITRUSCART_PASSWORD_REQ_SPEC');
                val_errors.push(str);
                pass_ok = false;
            }
		}
	}

	if( pass_ok )
	{
		val_img 	= 'accept_16.png';
		val_alt	 	= Joomla.JText._( 'COM_CITRUSCART_SUCCESS' );
		val_text 	= Joomla.JText._( 'COM_CITRUSCART_PASSWORD_VALID' );
		val_class	= 'validation-success';
	}
	else
	{
		val_img 	= 'remove_16.png';
		val_alt	 	= Joomla.JText._( 'COM_CITRUSCART_ERROR' );
		val_errors_string = val_errors.join(".\n");
		val_text 	= Joomla.JText._( 'COM_CITRUSCART_PASSWORD_INVALID' );
		val_text    = val_text + ' ' + val_errors_string;
		val_class	= 'validation-fail';
	}

	content = '<div class="citruscart_validation"><img src="'+window.com_citruscart.jbase+'media/com_citruscart/images/'+val_img+'" alt="'+val_alt+'"><span class="'+val_class+'">'+val_text+'</span></div>';
	if( $( container ) )
		$( container ).set('html',  content );
}

/**
 * Simple function to compare passwords
 */
function CitruscartCheckPassword2( container, form, psw1, psw2 )
{
	if( CitruscartJQ( psw1 ).get( 'value' ) == CitruscartJQ( psw2 ).get( 'value' ) )
	{
		val_img 	= 'accept_16.png';
		val_alt	 	= Joomla.JText._( 'COM_CITRUSCART_SUCCESS' );
		val_text 	= Joomla.JText._( 'COM_CITRUSCART_PASSWORD_MATCH' );
		val_class	= 'validation-success';
	}
	else
	{
		val_img 	= 'remove_16.png';
		val_alt	 	= Joomla.JText._( 'COM_CITRUSCART_ERROR' );
		val_text 	= Joomla.JText._( 'COM_CITRUSCART_PASSWORD_DO_NOT_MATCH' );
		val_class	= 'validation-fail';
	}
	
	content = '<div class="citruscart_validation"><img src="'+window.com_citruscart.jbase+'media/com_citruscart/images/'+val_img+'" alt="'+val_alt+'"><span class="'+val_class+'">'+val_text+'</span></div>';
	if( CitruscartJQ( container ) )
		CitruscartJQ( container ).set('html',  content );
}


/*
 * This method checks availability of the email address
 */
function CitruscartCheckoutCheckEmail( container, form )
{
	user_email = 'email_address';
	// send AJAX request to validate the email address against other users
	var url = 'index.php?option=com_citruscart&controller=checkout&task=checkEmail&format=raw';
		    
	// loop through form elements and prepare an array of objects for passing to server
    var str = CitruscartGetFormInputData( form );
    // execute Ajax request to server
    CitruscartPutAjaxLoader( container, Joomla.JText._( 'COM_CITRUSCART_VALIDATING' ) );
    var a = new Request({
            url: url,
            method:"post",
        data:{"elements":JSON.encode(str)},
        onSuccess: function(response){
           var resp=JSON.decode(response, false);
            if( resp.error != '0' )
            {
        		CitruscartJQ(container).set('html', resp.msg);
            }
            else
       		{
        		CitruscartJQ( container ).set('html',  resp.msg );
       		}
            return true;
        }
    }).send();
}

function CitruscartHideInfoCreateAccount( )
{	
	CitruscartJQ('create_account').addEvent('change', function() {
		CitruscartJQ('citruscart_user_additional_info').toggleClass('hidden');
	});
}

function CitruscartGetPaymentOptions(container, form, msg, callback) {
    var payment_plugin = $$('input[name=payment_plugin]:checked');

    if (payment_plugin) {
        payment_plugin = payment_plugin.value;
    }       
        
    var str = CitruscartGetFormInputData( form );
    var url = 'index.php?option=com_citruscart&view=checkout&task=updatePaymentOptions&format=raw';
    
    CitruscartGrayOutAjaxDiv('onCheckoutPayment_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_PAYMENT_METHODS'));
    
    // execute Ajax request to server
    var a = new Request({
        url : url,
        method : "post",
        data : {
            "elements" : JSON.encode(str)
        },
        onSuccess : function(response) {
            var resp = JSON.decode(response, false);
            $( container ).set('html',  resp.msg );
            
            if (typeof callback == 'function') {
                callback();
            }
            return true;
        },
        onFailure : function(response) {
            CitruscartDeletePaymentGrayDiv();
            CitruscartDeleteAddressGrayDiv();
            CitruscartDeleteShippingGrayDiv();
        },
        onException : function(response) {
            CitruscartDeletePaymentGrayDiv();
            CitruscartDeleteAddressGrayDiv();
            CitruscartDeleteShippingGrayDiv();
        }
    }).send();  

    if (payment_plugin) {
        $$('#onCheckoutPayment_wrapper input[name=payment_plugin]').each(function(e) {
            if (e.get('value') == payment_plugin)
                e.set('checked', true);
        });
    }
}