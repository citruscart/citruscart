/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @return
 */

function citruscartGetPaymentForm( element, container )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=getPaymentForm&format=raw&payment_element=' + element;

   	citruscartGrayOutAjaxDiv( container, Joomla.JText._( 'COM_CITRUSCART_UPDATING_PAYMENT_METHODS' ) );
	citruscartDoTask( url, container, document.adminForm, '', false, citruscartDeletePaymentGrayDiv );    	
}


function citruscartGetShippingRates( container, form, callback )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=updateShippingRates&format=raw';
    
	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData( form );
	
   	citruscartGrayOutAjaxDiv( container, Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
   	
   	// execute Ajax request to server
    var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
            citruscartJQ( container ).html( resp.msg );
            if( resp.default_rate && resp.default_rate != null ) { 
                // if only one rate was found - set it as default
                citruscartSetShippingRate(resp.default_rate['name'], resp.default_rate['price'], resp.default_rate['tax'], resp.default_rate['extra'], resp.default_rate['code'], callback != null );                
            }
            
            if (typeof callback == 'function') {
                callback();
            }
            return true;
        },
        onFailure : function(response) {
            citruscartDeleteShippingGrayDiv();
        },
        onException : function(response) {
            citruscartDeleteShippingGrayDiv();
        }
    }).send();
    
    citruscartDeleteShippingGrayDiv();
}

function citruscartSetShippingRate(name, price, tax, extra, code, combined )
{
	citruscartJQ('shipping_name').value = name;
	citruscartJQ('shipping_code').value = code;
	citruscartJQ('shipping_price').value = price;
	citruscartJQ('shipping_tax').value = tax;
	citruscartJQ('shipping_extra').value = extra;

	citruscartGrayOutAjaxDiv( 'onCheckoutShipping_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
	citruscartGrayOutAjaxDiv( 'onCheckoutCart_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_CART' ) );		
	citruscartGetCheckoutTotals( combined ); // combined = true - both shipping rates and addresses are updating at the same time
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @param combined If true, both shipping rated and addresses are updating at the same time
 * @return
 */
function citruscartGetCheckoutTotals( combined )
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=setShippingMethod&format=raw';
//    if( typeof( combined ) == 'undefined' )
 //   	citruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false );
    if( combined )
    	citruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false, citruscartDeleteCombinedGrayDiv );    	
    else
    	citruscartDoTask( url, 'onCheckoutCart_wrapper', document.adminForm, '', false, citruscartDeleteShippingGrayDiv );
}

/**
 * Recalculates the currency amounts
 * @return
 */
function citruscartGetCurrencyTotals()
{
    var url = 'index.php?option=com_citruscart&view=checkout&task=setCurrency&format=raw';
    citruscartDoTask( url, 'onCheckoutReview_wrapper', document.adminForm );    
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 * 
 * @return
 */
function citruscartRefreshTotalAmountDue()
{
	if( citruscartJQ( 'payment_info' ) )
	{
		var url = 'index.php?option=com_citruscart&view=checkout&task=totalAmountDue&format=raw';
		citruscartGrayOutAjaxDiv( 'payment_info', Joomla.JText._( 'COM_CITRUSCART_UPDATING_BILLING' ) ); 
	    citruscartDoTask( url, 'totalAmountDue', document.adminForm, '', false, citruscartDeleteTotalAmountDueGrayDiv );		
	}
}

/**
 * If Same as Billing checkbox is selected
 * this disables all the input fields in the shipping address form
 * 
 * @param checkbox
 * @return
 */
function citruscartDisableShippingAddressControls(checkbox, form)
{
    
	var disable = false;
    if (checkbox.checked){
        disable = true;
        citruscartGetShippingRates( 'onCheckoutShipping_wrapper', form );
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
            			citruscartDoTask( 'index.php?option=com_citruscart&format=raw&controller=checkout&task=getzones&prefix=shipping_input_&disabled=1&country_id='+document.getElementById('billing_input_country_id').value+'&zone_id='+document.getElementById('billing_input_zone_id').value, 'shipping_input_zones_wrapper', '');
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
    
    citruscartDeleteGrayDivs();
}

function citruscartManageShippingRates()
{
	citruscartJQ('shipping_form_div').getElementsByTagName('input[name=shipping_rate]').addEvent('click', function() {
		citruscartGetCheckoutTotals();
	}
	);
}

function citruscartDeleteAddressGrayDiv()
{
	el_billing =document.getElementById('billingAddress').getElementsByClassName('citruscartAjaxGrayDiv');
	
	//$$( '#billingAddress .citruscartAjaxGrayDiv' );
	
	if( !el_billing )
		return;
	citruscartSetColorInContainer( 'billingAddress', '' );
	el_billing.remove();
	jQuery("."+el_billing).remove();
	
	//el_billing.destroy();
	
	if( citruscartJQ( 'shippingAddress' ) && ( !citruscartJQ( 'sameasbilling' ) || ( citruscartJQ( 'sameasbilling' ) && !citruscartJQ( 'sameasbilling' ).checked ) ) )
	{
		citruscartSetColorInContainer( 'shippingAddress', '' );
		//document.getElementById('shippingAddress').getElementsByClassName('citruscartAjaxGrayDiv').destroy();
		document.getElementById('shippingAddress').getElementsByClassName('citruscartAjaxGrayDiv').remove();
		//$$( '#shippingAddress .citruscartAjaxGrayDiv' ).destroy();		
	}
}

function citruscartDeletePaymentGrayDiv()
{
	if( citruscartJQ( 'onCheckoutPayment_wrapper' ) )
		citruscartSetColorInContainer( 'onCheckoutPayment_wrapper', '' );
}

function citruscartDeleteTotalAmountDueGrayDiv()
{
	//el = $$( '#payment_info .citruscartAjaxGrayDiv' );
	el = document.getElementById('payment_info').getElementsByClassName('citruscartAjaxGrayDiv');
	if( el != '' )
		el.destroy();
	
	citruscartSetColorInContainer( 'payment_info', '' );
}

function citruscartDeleteShippingGrayDiv()
{
	if( citruscartJQ( 'onCheckoutShipping_wrapper' ) == null )
		return;

	//el = $$( '#onCheckoutShipping_wrapper .citruscartAjaxGrayDiv' );
	el = document.getElementById('onCheckoutShipping_wrapper').getElementsByClassName('citruscartAjaxGrayDiv');
	if( el != '' )
		el.destroy();

	
	if( citruscartJQ( 'onCheckoutShipping_wrapper' ).css( 'color' ) != '' )
	{
		citruscartSetColorInContainer( 'onCheckoutShipping_wrapper', '' );
		
		// selected shipping rate has to be checked manually
		if( citruscartJQ( 'shipping_name' ) )
		{
			shipping_plugin = citruscartJQ( 'shipping_name' ).get( 'value' );
			document.getElementById('onCheckoutShipping_wrapper').getElements('input[type=radio]').each(function(e){
			//$$( '#onCheckoutShipping_wrapper input[type=radio]' ).each( function( e ){
				if( e.get( 'rel' ) == shipping_plugin )
					e.set( 'checked', true );
			} );			
		}
	}
	citruscartDeleteCartGrayDiv();
}

function citruscartDeleteCartGrayDiv()
{
	if( citruscartJQ('onCheckoutCart_wrapper') )
		citruscartSetColorInContainer( 'onCheckoutCart_wrapper', '' );
}

function citruscartDeleteCombinedGrayDiv()
{
	citruscartDeleteAddressGrayDiv();

	if( citruscartJQ( 'onCheckoutShipping_wrapper' ) )
		citruscartDeleteShippingGrayDiv();
	else // no shipping address so delete gray div from cart
		citruscartDeleteCartGrayDiv();
}

function citruscartGrayOutAddressDiv( prefix )
{
	if( !citruscartJQ( 'shippingAddress' ) )
		return;
	values = citruscartStoreFormInputs( document.adminForm );
	citruscartGrayOutAjaxDiv( 'billingAddress', Joomla.JText._( 'COM_CITRUSCART_UPDATING_ADDRESS=' ), prefix );
	if( citruscartJQ( 'shippingAddress' ) && ( !citruscartJQ( 'sameasbilling' ) || ( citruscartJQ( 'sameasbilling' ) && !citruscartJQ( 'sameasbilling' ).checked ) ) )
		citruscartGrayOutAjaxDiv( 'shippingAddress', Joomla.JText._( 'COM_CITRUSCART_UPDATING_ADDRESS=' ), prefix );
	citruscartRestoreFormInputs( document.adminForm , values );
}

/*
 * Method to disable UI and update shipping rates
 * 
 */
function citruscartCheckoutAutomaticShippingRatesUpdate( obj_id )
{
	obj = document.getElementById( obj_id );

	// see, if you find can find payment_wrapper and update payment methods
	if( $( 'onCheckoutPayment_wrapper' ) && obj_id.substr( 0, 8 ) == 'billing_' ) // found the payment_wrapper - update payment methods && this is a billing input
	{
		if( !$( 'shippingAddress' ) ) // no shipping
		{
			citruscartGrayOutAddressDiv();
			citruscartGetPaymentOptions('onCheckoutPayment_wrapper', document.adminForm, '', citruscartDeleteAddressGrayDiv );
		}
		else
			citruscartGetPaymentOptions('onCheckoutPayment_wrapper', document.adminForm, '' );
	}

	if( !$( 'shippingAddress' ) ) {
	    // no shipping
	    return;        
	}		

	only_shipping = !citruscartJQ( 'sameasbilling' ) || !citruscartJQ( 'sameasbilling' ).get( 'checked' );
	if( only_shipping )
	{
		citruscartGrayOutAddressDiv();
		citruscartGrayOutAjaxDiv( 'onCheckoutShipping_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_SHIPPING_RATES' ) );
		if( obj_id.substr( 0, 9 ) == 'shipping_' ) // shipping input
		{
			citruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm, citruscartDeleteAddressGrayDiv );
		}
		else // billing input
		{
			citruscartGrayOutAjaxDiv( 'onCheckoutCart_wrapper', Joomla.JText._( 'COM_CITRUSCART_UPDATING_CART' ) );
			citruscartGetCheckoutTotals( true );
		}
	}
	else // same as billing
	{
		if( obj_id.substr( 0, 8 ) == 'billing_' ) // billing input
		{
			citruscartGrayOutAddressDiv();
			citruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm, citruscartDeleteAddressGrayDiv );
		}
	}
}

/**
 * Simple function to check a password strength
 * 
 */
function citruscartCheckPassword( container, form, psw, min_length, req_num, req_alpha, req_spec )
{	
	console.log(confirm);
    val_errors = [];
	var pass_ok = true;
		
	act_pass = citruscartJQ( psw ).get( 'value' );
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

	content = '<div class="citruscart_validation"><img src="'+window.com_citruscart.jbase+'media/citruscart/images/'+val_img+'" alt="'+val_alt+'"><span class="'+val_class+'">'+val_text+'</span></div>';
	if( $( container ) )
		$( container ).set('html',  content );
}

/**
 * Simple function to compare passwords
 */
function citruscartCheckPassword2( container, form, psw1, psw2 )
{	
	console.log(container);
	if( citruscartJQ( psw1 ).get( 'value' ) == citruscartJQ( psw2 ).get( 'value' ) )
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
	
	content = '<div class="citruscart_validation"><img src="'+window.com_citruscart.jbase+'media/citruscart/images/'+val_img+'" alt="'+val_alt+'"><span class="'+val_class+'">'+val_text+'</span></div>';
	if( citruscartJQ( container ) )
		citruscartJQ( container ).set('html',  content );
}


/*
 * This method checks availability of the email address
 */
function citruscartCheckoutCheckEmail( container, form )
{
	user_email = 'email_address';
	// send AJAX request to validate the email address against other users
	var url = 'index.php?option=com_citruscart&controller=checkout&task=checkEmail&format=raw';
		    
	// loop through form elements and prepare an array of objects for passing to server
    var str = citruscartGetFormInputData( form );
       
    // execute Ajax request to server
    citruscartPutAjaxLoader( container, Joomla.JText._( 'COM_CITRUSCART_VALIDATING' ) );
    
    
    var a = new Request({
            url: url,
            method:"post",
        data:{"elements":JSON.encode(str)},
        onSuccess: function(response){
           var resp=JSON.decode(response, false);
               if( resp.error != '0' )
            {
        		//citruscartJQ(container).set('html', resp.msg);        		
        		$(container).set('html', resp.msg);       		
      		     		
            }	
            else
       		{	
            	$( container ).set('html',  resp.msg );
        		//citruscartJQ( container ).set('html',  resp.msg );
       		}
            return true;
        }
    }).send();
}

function citruscartHideInfoCreateAccount( )
{	/*
	citruscartJQ('create_account').addEvent('change', function() {
		citruscartJQ('citruscart_user_additional_info').toggleClass('hidden');
	}); */

	$('create_account').addEvent('change', function() {
		$('citruscart_user_additional_info').toggleClass('hidden');
	});

}

function citruscartGetPaymentOptions(container, form, msg, callback) {
    //var payment_plugin = $$('input[name=payment_plugin]:checked');
    var payment_plugin = document.getElementsByTagName('input[name=payment_plugin]:checked');

    if (payment_plugin) {
        payment_plugin = payment_plugin.value;
    }       
        
    var str = citruscartGetFormInputData( form );
    var url = 'index.php?option=com_citruscart&view=checkout&task=updatePaymentOptions&format=raw';
    
    citruscartGrayOutAjaxDiv('onCheckoutPayment_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_PAYMENT_METHODS'));
    
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
            citruscartDeletePaymentGrayDiv();
            citruscartDeleteAddressGrayDiv();
            citruscartDeleteShippingGrayDiv();
        },
        onException : function(response) {
            citruscartDeletePaymentGrayDiv();
            citruscartDeleteAddressGrayDiv();
            citruscartDeleteShippingGrayDiv();
        }
    }).send();  

    if (payment_plugin) {
    	document.getElementById("#onCheckoutPayment_wrapper").getElementsByTagName("input[name=payment_plugin]").each(function(e){
        //$$('#onCheckoutPayment_wrapper input[name=payment_plugin]').each(function(e) {
            if (e.get('value') == payment_plugin)
                e.set('checked', true);
        });
    }
}
