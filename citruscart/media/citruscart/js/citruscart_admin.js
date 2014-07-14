if (typeof(Citruscart) === 'undefined') {
	var Citruscart = {};
}

Citruscart.saveConfigOnClick = function() {
    citruscartJQ('a.view-config').each(function(){
        var el = citruscartJQ(this);
        Citruscart.postConfigFormAndRedirect(el);
    });
    
    citruscartJQ('a.view-shipping').each(function(){
        var el = citruscartJQ(this);
        Citruscart.postConfigFormAndRedirect(el);
    });
    
    citruscartJQ('a.view-payment').each(function(){
        var el = citruscartJQ(this);
        Citruscart.postConfigFormAndRedirect(el);
    });
}

Citruscart.postConfigFormAndRedirect = function(el) {
    el.click(function(event){
        event.preventDefault();
        url = 'index.php?option=com_citruscart&view=config&format=raw';
        
        values = citruscartJQ("#adminForm").serializeArray();
        for (index = 0; index < values.length; ++index) {
            if (values[index].name == "task") {
                values[index].value = 'save';
                break;
            }
        }
        data = jQuery.param(values);
        
        citruscartJQ.post( url, data, function(response){
            window.location = el.attr('href');
        });
    });    
}

Citruscart.refreshProductGallery = function(product_id) {
    var url = 'index.php?option=com_citruscart&view=products&task=refreshProductGallery&product_id=' + product_id + '&tmpl=component&format=raw';
    var request = jQuery.ajax({
        type: 'get', 
        url: url,
    }).done(function(data){
        var response = JSON.decode(data, false);
        if (response.html) {
            citruscartJQ('#form-gallery').html(response.html);
            Citruscart.bindProductGalleryLinks();
        }
    }).fail(function(data){

    }).always(function(data){

    });
}

Citruscart.bindProductGalleryLinks = function() {
    citruscartJQ('.delete-gallery-image').each(function(){
        el = citruscartJQ(this);
        var url = el.attr('data-href');
        var product_id = el.attr('data-product_id');
        if (url) {
            el.off('click.pg').on('click.pg', function(event){
                event.preventDefault();
                var request = jQuery.ajax({
                    type: 'get', 
                    url: url,
                }).done(function(data){
                    var response = JSON.decode(data, false);
                    Citruscart.refreshProductGallery(product_id);
                }).fail(function(data){

                }).always(function(data){

                });                
            });            
        }
    });
    
    citruscartJQ('.set-default-gallery-image').each(function(){
        el = citruscartJQ(this);
        var url = el.attr('data-href');
        var image = el.attr('data-image');
        if (url) {
            el.off('click.pg').on('click.pg', function(event){
                event.preventDefault();
                var request = jQuery.ajax({
                    type: 'get', 
                    url: url,
                }).done(function(data){
                    var response = JSON.decode(data, false);
                    if (response.html) {
                        citruscartJQ('#default_image').html(response.html);
                    }
                    citruscartJQ('#product_full_image').val(image);
                }).fail(function(data){

                }).always(function(data){

                });                
            });            
        }
    });
}

Citruscart.DisableShippingAddressControls = function(check){
	var s_table = citruscartJQ("table[data-type='shipping_input'] :input");
	if( check ) {
		s_table.attr('disabled', 'true');
	} else {
		s_table.removeAttr('disabled');		
	}
	
}

function CitruscartUpdateParentDefaultImage(id) {
	var url = 'index.php?option=com_citruscart&view=products&task=updateDefaultImage&protocol=json&product_id=' + id;
	var form = document.adminForm;
	// default_image
	// default_image_name

	// loop through form elements and prepare an array of objects for passing to server
	var str = new Array();
	for(i=0; i<form.elements.length; i++) {
		postvar = {
			name : form.elements[i].name,
			value : form.elements[i].value,
			checked : form.elements[i].checked,
			id : form.elements[i].id
		};
		str[i] = postvar;
	}
	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
 			citruscartJQ('default_image').html ( resp.default_image );
	      	citruscartJQ('default_image_name').html( resp.default_image_name);
			return true;
		}
	}).send();
}

function citruscartSetShippingRate(name, price, tax, extra, code) {
	citruscartJQ('shipping_name').val( name );
	citruscartJQ('shipping_code').val( code );
	citruscartJQ('shipping_price').val( price );
	citruscartJQ('shipping_tax').val( tax );
	citruscartJQ('shipping_extra').val( extra );
	citruscartGetCheckoutTotals();
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 *
 * @return
 */
function citruscartGetCheckoutTotals() {
	var url = 'index.php?option=com_citruscart&view=pos&task=setShippingMethod&format=raw';
	citruscartDoTask( url, 'orderSummary', document.adminForm, '', false );
}

function citruscartGetShippingRates( container, form, msg, doModal ) {
	
	var url = 'index.php?option=com_citruscart&view=pos&task=updateShippingRates&format=raw';
	if (doModal != false) {
		Dsc.newModal(msg);
	}
	citruscartJQ('#validation_message').html( '' );

	// loop through form elements and prepare an array of objects for passing to server
	var str = new Array();
	for(i=0; i<form.elements.length; i++) {
		postvar = {
			name : form.elements[i].name,
			value : form.elements[i].value,
			checked : form.elements[i].checked,
			id : form.elements[i].id
		};
		str[i] = postvar;
	}
	// execute Ajax request to server
	 var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);

			if (resp.error != '1') {
				citruscartJQ("#"+container).html( resp.msg );
				citruscartGetCheckoutTotals();
			} else {
				citruscartJQ('#validation_message').html( resp.msg );
			}

			if (doModal != false) {
				if (doModal != false) { (function() { document.body.removeChild( document.getElementById('dscModal') ); }).delay(500); }
			}
			return true;
		}
	 }).send();
}

function citruscartGetPaymentForm( element, container ) {
	var url = 'index.php?option=com_citruscart&view=pos&task=getPaymentForm&format=raw&payment_element=' + element;
	citruscartDoTask( url, container, document.adminForm );
}

/**
 *
 */
function citruscartAddCoupon( form, mult_enabled ) {
	var new_coupon_code = document.getElementById('new_coupon_code').value;

	var url = 'index.php?option=com_citruscart&view=pos&task=validateCouponCode&format=raw&coupon_code='+new_coupon_code;
	var container = 'coupon_code_message';

	// loop through form elements and prepare an array of objects for passing to server
	var str = new Array();
	for(i=0; i<form.elements.length; i++) {
		postvar = {
			name : form.elements[i].name,
			value : form.elements[i].value,
			checked : form.elements[i].checked,
			id : form.elements[i].id
		};
		str[i] = postvar;
	}

	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
			if (resp.error != '1') {
				if (citruscartJQ(container)) {
					citruscartJQ(container).html( '');
				}

				// Push the code into the form
				var cc_html = citruscartJQ('coupon_codes').innerHTML + resp.msg;
				citruscartJQ('coupon_codes').html( cc_html );

				// Clear the field
				document.getElementById('new_coupon_code').value = '';

				// Update the summary
				citruscartGetCheckoutTotals();

				if (mult_enabled != 1) {
					citruscartShowHideDiv('coupon_code_form');
				}
			} else {
				if (citruscartJQ(container)) {
					JQ(container).html( resp.msg );
				}
			}
		}
	}).request();
}

/**
 * 
 */
function CitruscartAddCredit( form )
{
    var apply_credit_amount = document.getElementById('apply_credit_amount').value;
    
    var url = 'index.php?option=com_citruscart&view=pos&task=validateApplyCredit&format=raw&apply_credit_amount='+apply_credit_amount;
    var container = 'credit_message';
    
    // loop through form elements and prepare an array of objects for passing to server
    var str = new Array();
    for(i=0; i<form.elements.length; i++)
    {
        postvar = {
            name : form.elements[i].name,
            value : form.elements[i].value,
            checked : form.elements[i].checked,
            id : form.elements[i].id
        };
        str[i] = postvar;
    }
    
    // execute Ajax request to server
   var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
            if (resp.error != '1') 
            {
                if (citruscartJQ(container)) { citruscartJQ(container).html( ''); }
                citruscartJQ('applied_credit').html( resp.msg );
                // Clear the field
                citruscartJQ('apply_credit_amount').value = '';
                               
                 // Update the summary
                citruscartGetCheckoutTotals();                          
            }
                else
            {
                if (citruscartJQ(container)) { citruscartJQ(container).html( resp.msg ); }
            }
        }
    }).send();
}