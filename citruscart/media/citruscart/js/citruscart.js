if (typeof(Citruscart) === 'undefined') {
    var Citruscart = {};
}

Citruscart.deleteCartItem = function(cartitem_id, prompt_text, callback_function) {

    if (!prompt_text) { prompt_text = "Are you sure you want to delete this item?"; }
    var r = confirm(prompt_text);
    
    if (r == true && cartitem_id) {
        var url = 'index.php?option=com_citruscart&view=carts&task=deleteCartItem&format=raw&cartitem_id=' + cartitem_id;
        var request = jQuery.ajax({
            type: 'post', 
            url: url
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    

                if( response.subtotal.length ) {
                	citruscartJQ('#totalAmountDue').html(response.subtotal);
                }
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.deleteWishlistItem = function(wishlistitem_id, prompt_text, callback_function) {

    if (!prompt_text) { prompt_text = "Are you sure you want to delete this item?"; }
    var r = confirm(prompt_text);
    
    if (r == true && wishlistitem_id) {
        var url = 'index.php?option=com_citruscart&view=wishlists&task=deleteWishlistItem&format=raw&wishlistitem_id=' + wishlistitem_id;
        var request = jQuery.ajax({
            type: 'post', 
            url: url
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.addToWishlist = function( form_id, container_id, callback_function ) {
    var url = 'index.php?option=com_citruscart&format=raw&view=products';
    
    var form_data = citruscartJQ('#'+form_id).serializeArray();
    citruscartJQ.each(form_data, function(index, value) {
        if (value.name == 'task') {
            form_data[index].value = 'addToWishlist';
        }
    });
    
    var request = jQuery.ajax({
        type: 'post', 
        url: url,
        data: form_data
    }).done(function(data){
        var response = JSON.decode(data, false);
        if (response.html) {
            citruscartJQ('#'+container_id).html(response.html);
        }        
        if ( typeof callback_function === 'function') {
            callback_function( response );
        }                    
    }).fail(function(data){
        
    }).always(function(data){

    });
}

Citruscart.privatizeWishlist = function(wishlist_id, privacy, callback_function) {

    if (wishlist_id && privacy) {
        var url = 'index.php?option=com_citruscart&view=wishlists&task=privatizeWishlist&format=raw&wishlist_id='+wishlist_id+'&privacy='+privacy;
        var request = jQuery.ajax({
            type: 'post', 
            url: url
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.deleteWishlist = function(wishlist_id, prompt_text, callback_function) {

    if (!prompt_text) { prompt_text = "Are you sure you want to delete this Wishlist?"; }
    var r = confirm(prompt_text);
    
    if (r == true && wishlist_id) {
        var url = 'index.php?option=com_citruscart&view=wishlists&task=deleteWishlist&format=raw&wishlist_id=' + wishlist_id;
        var request = jQuery.ajax({
            type: 'post', 
            url: url
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.createWishlist = function(wishlist_name, prompt_text, callback_function) {

    if (!wishlist_name) {
        if (!prompt_text) { prompt_text = "Please provide a name for this Wishlist:"; }
        var wishlist_name = prompt(prompt_text);
    };
    
    var post_data = {
            wishlist_name: wishlist_name
    }; 
    var url = 'index.php?option=com_citruscart&view=wishlists&task=createWishlist&format=raw';
    
    if (wishlist_name) {
        var request = jQuery.ajax({
            type: 'post', 
            url: url,
            data: post_data
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.renameWishlist = function(wishlist_id, prompt_text, callback_function) {

    if (!wishlist_name) {
        if (!prompt_text) { prompt_text = "Please provide a name for this Wishlist:"; }
        var wishlist_name = prompt(prompt_text);
    };
    
    var post_data = {
            wishlist_name: wishlist_name
    }; 
    var url = 'index.php?option=com_citruscart&view=wishlists&task=renameWishlist&format=raw&wishlist_id=' + wishlist_id;
    
    if (wishlist_name) {
        var request = jQuery.ajax({
            type: 'post', 
            url: url,
            data: post_data
        }).done(function(data){
            var response = JSON.decode(data, false);

            if (response.error) {
                alert(response.html);
            } else {
                if ( typeof callback_function === 'function') {
                    callback_function( response );
                }                                    
            } 

        }).fail(function(data){
            
        }).always(function(data){

        });        
    }
    
    return false;
}

Citruscart.addWishlistItemToWishlist = function( wishlistitem_id, wishlist_id, callback_function ) {
    var url = 'index.php?option=com_citruscart&format=raw&view=wishlists&task=addWishlistItemToWishlist&wishlistitem_id='+wishlistitem_id+'&wishlist_id='+wishlist_id;
        
    var request = jQuery.ajax({
        type: 'post', 
        url: url
    }).done(function(data){
        var response = JSON.decode(data, false);
        if ( typeof callback_function === 'function') {
            callback_function( response );
        }                    
    }).fail(function(data){
        
    }).always(function(data){

    });
}

Citruscart.UpdateAddToCart = function(page, container, form, working, callback) {
	
	//its working
	var url = com_citruscart.jbase + 'index.php?option=com_citruscart&format=raw&view=products&task=updateAddToCart&page=' + page;
	
	if( page == 'pos' ) {
		url = 'index.php?option=com_citruscart&format=raw&view=pos&task=updateAddToCart&page=' + page;
	}
	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData(form);
	// execute Ajax request to server
	if (working)
		citruscartGrayOutAjaxDiv(container, Joomla.JText._('COM_CITRUSCART_UPDATING_ATTRIBUTES'), '');
		
    citruscartJQ.post( url, { "elements" : JSON.encode(str) }, function(response){ 
    	    	
    	var resp = JSON.decode(response, false);
    	
    	
		
		if (document.getElementById(container)) {
			
			document.getElementById(container).set('html', resp.msg);
		} 
		
		document.getElementById(container).setStyle('color', '');
		
		Citruscart.updateProductDetail(resp, page, container, form);
		
		
		if ( typeof callback === 'function')
			callback(resp);
			
		if( typeof resp.callback !== 'undefined' && resp.callback.length ) {
			eval( resp.callback );
		}
		return true;
    });
}

/**
 * Updates a product detail page with new PAOVs
 * [Experimental]
 */
Citruscart.updateProductDetail = function(resp, page, container, form) {
	
    var f = citruscartJQ( form );
    var changed_attr = citruscartJQ( 'input[name="changed_attr"]', f ).val();
    
    if (!resp.paov_items || !changed_attr) {
        return;
    }
    
    new_image = null;
    paov_items = resp.paov_items;
    product_id = resp.product_id;
    citruscartJQ.each(paov_items, function(index, paov){
        if (paov.productattributeoptionvalue_field == 'product_full_image' && paov.productattributeoptionvalue_operator == 'replace' && paov.productattributeoptionvalue_value) {
            new_image = paov.productattributeoptionvalue_value;
        }
    });
    
    if (new_image) {
        jqzoom = jQuery('.product-' + product_id + ' #product_image a.zoom').data('jqzoom');
        if (jqzoom) {
            jqzoom.changeimage(new_image);
        }        
    }
}

/*
 * Changes ID of currently changed attribute on form
 */
Citruscart.UpdateChangedAttribute= function( form, attr_id ) {
	var f = citruscartJQ( form );
	//console.log(f);
	citruscartJQ( 'input[name="changed_attr"]', f ).val( attr_id );
}

/**
 * Simple function to refresh a page.
 */
function citruscartUpdate() {
	location.reload(true);
}

/**
 * Resets the filters in a form.
 * This should be renamed to CitruscartResetFormFilters
 *
 * @param form
 * @return
 */
function citruscartFormReset(form) {
	// loop through form elements
	Dsc.resetFormFilters(form);
}

/**
 *
 * @param {Object} order
 * @param {Object} dir
 * @param {Object} task
 */
function citruscartGridOrdering(order, dir, form) {
	Dsc.gridOrdering(order, dir, form);
}

/**
 *
 * @param id
 * @param change
 * @return
 */
function citruscartGridOrder(id, change, form) {
	Dsc.gridOrder(id, change, form);
}

/**
 * Sends form values to server for validation and outputs message returned.
 * Submits form if error flag is not set in response
 *
 * @param {String} url for performing validation
 * @param {String} form element name
 * @param {String} task being performed
 */
function citruscartFormValidation(url, container, task, form, doModal, msg, onCompleteFunction) {
	Dsc.formValidation(url, container, task, form, doModal, msg, onCompleteFunction);
}

/**
 * Submits form using onsubmit if present
 * @param task
 * @return
 */
function citruscartSubmitForm(task, form) {
	Dsc.submitForm(task, form);
}

/**
 * Overriding core submitbutton task to perform our onsubmit function
 * without submitting form afterwards
 *
 * @param task
 * @return
 */
function submitbutton(task) {
	if (task) {
		document.adminForm.task.value = task;
	}

	if ( typeof document.adminForm.onsubmit == "function") {
		document.adminForm.onsubmit();
	} else {
		submitform(task);
	}
}

/**
 *
 * @param {Object} divname
 * @param {Object} spanname
 * @param {Object} showtext
 * @param {Object} hidetext
 */
function citruscartDisplayDiv(divname, spanname, showtext, hidetext) {
	Dsc.displayDiv(divname, spanname, showtext, hidetext);
}

/**
 *
 * @param {Object} prefix
 * @param {Object} newSuffix
 */
function citruscartSwitchDisplayDiv(prefix, newSuffix) {
	Dsc.switchDisplayDiv(prefix, newSuffix);
}

function citruscartShowHideDiv(divname) {
	Dsc.showHideDiv(divname);
}

/**
 *
 * @param {String} url to query
 * @param {String} document element to update after execution
 * @param {String} form name (optional)
 * @param {String} msg message for the modal div (optional)
 * @param (Function) Function which is executed after the call is completed
 */
function citruscartDoTask(url, container, form, msg, doModal, onCompleteFunction) {

	Dsc.doTask(url, container, form, msg, doModal, onCompleteFunction);

}

/**
 *
 * @param {String} msg message for the modal div (optional)
 */
function citruscartNewModal(msg) {
	Dsc.newModal(msg);
}

/**
 * Gets the value of a selected radiolist item
 *
 * @param radioObj
 * @return string
 */
function citruscartGetCheckedValue(radioObj) {
	if (!radioObj) {
		return "";
	}

	var radioLength = radioObj.length;
	if (radioLength == undefined) {
		if (radioObj.checked)
			return radioObj.value;
		else
			return "";
	}

	for (var i = 0; i < radioLength; i++) {
		if (radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function citruscartVerifyZone() {
	var c = document.getElementById('country_id');
	var z = document.getElementById('zone_id');

	if (c != null && c != 'undefined' && c != '' && z != null && z != 'undefined' && z != '') {
		if (z.options[z.selectedIndex].value != "" && c.options[c.selectedIndex].value != "") {
			document.getElementById('task').value = 'addzone';
			document.adminForm.submit();
		} else {
			alert('Please select both a Country and a Zone.');
		}
	} else {
		alert('Please select both a Country and a Zone.');
	}
}

function submitCitruscartbutton(pressbutton, fieldname) {
	submitCitruscartform(pressbutton, fieldname);
}

/**
 * Submit the admin form using a custom task field name
 */
function submitCitruscartform(pressbutton, fieldname) {
	if (pressbutton) {
		document.adminForm.elements[fieldname].value = pressbutton;
	}
	if ( typeof document.adminForm.onsubmit == "function") {
		document.adminForm.onsubmit();
	}
	document.adminForm.submit();
}

/**
 * Pauses execution for the specified milliseconds
 * @param milliseconds
 * @return
 */
function citruscartPause(milliseconds) {
	var dt = new Date();
	while ((new Date()) - dt <= milliseconds) {/* Do nothing */
	}
}

/**
 *
 * @param {String} url to query
 * @param {String} document element to update after execution
 * @param {String} form name (optional)
 * @param {String} msg message for the modal div (optional)
 */
function citruscartAddToCart(url, container, form, msg) {
	var cartContainer = 'citruscartUserShoppingCart';
	var cartUrl = 'index.php?option=com_citruscart&format=raw&view=carts&task=displayCart';

	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData(form);

	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
			if (resp.error == '1') {
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', resp.msg);
				}
				return false;
			} else {
				citruscartPause(500);
				citruscartDoTask(cartUrl, cartContainer, '', '', false);
				return true;
			}
		}
	}).send();
}

function citruscartAddRelationship(container, msg) {
	var url = 'index.php?option=com_citruscart&view=products&task=addRelationship&protocol=json';
	citruscartDoTask(url, container, document.adminForm, msg, true);
	document.adminForm.new_relationship_productid_to.value = '';
}

function citruscartRemoveRelationship(id, container, msg) {
	var url = 'index.php?option=com_citruscart&view=products&task=removeRelationship&protocol=json&productrelation_id=' + id;
	citruscartDoTask(url, container, document.adminForm, msg, true);
}

function citruscartRating(id) {
	var count;
	document.getElementById('productcomment_rating').value = id;
	for ( count = 1; count <= id; count++) {
		document.getElementById('rating_'+count).getElementsByTagName("img")[0].src = window.com_citruscart.jbase + "media/citruscart/images/star_10.png";
	}

	for ( count = id + 1; count <= 5; count++) {
		document.getElementById('rating_'+count).getElementsByTagName("img")[0].src = window.com_citruscart.jbase + "media/citruscart/images/star_00.png";
	}
}

function citruscartCheckUpdateCartQuantities(form, text) {

	var quantities = form.getElements('input[name^=quantities]');
	var original_quantities = form.getElements('input[name^=original_quantities]');

	var returned = true;

	quantities.each(function(item, index) {
		if (item.value != original_quantities[index].value) {
			returned = confirm(text);
		}
	});

	return returned;

}

function citruscartPopulateAttributeOptions(select, target, opt_name, opt_id) {
	// Selected option
	var attribute_id = select.getSelected().getLast().value;

	citruscartGetAttributeOptions(attribute_id, target, opt_name, opt_id);
}

function citruscartGetAttributeOptions(attribute_id, container, opt_name, opt_id) {
	var url = 'index.php?option=com_citruscart&controller=productattributeoptions&task=getProductAttributeOptions&attribute_id=' + attribute_id + '&select_name=' + opt_name + '&select_id=' + opt_id + '&format=raw';
	citruscartDoTask(url, container);
}

/**
 * Sends form values to server for validation and outputs message returned.
 * Submits form if error flag is not set in response
 * Always performs validation, regardless of task value
 *
 * @param {String} url for performing validation
 * @param {String} html container to update with validation message
 * @param {String} task to be executed if form validates
 * @param {String} form name
 * @param {Boolean} display modal overlay?
 * @param {String} Text for modal overlay
 */
function citruscartValidation(url, container, task, form, doModal, msg) {
	if (doModal == true) {
		citruscartNewModal(msg);
	}

	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData(form);

	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);
			if (resp.error == '1') {
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', resp.msg);
				}
			}
			if (doModal != false) { (function() { document.body.removeChild(citruscartJQ('dscModal')); }).delay(500); }
			if (resp.error != '1') {
				form.task.value = task;
				form.submit();
			}
		}
	}).send();
}

function citruscartClearInput(element, value) {
	if (element.value == value) {
		element.value = '';
	}
}

function citruscartAddProductToCompare(id, container, obj, doModal) {
	var add = 0;
	var msg = Joomla.JText._("COM_CITRUSCART_REMOVING_PRODUCT");
	if (obj.checked == true) {
		add = 1;
		msg = Joomla.JText._("COM_CITRUSCART_ADDING_PRODUCT_FOR_COMPARISON");
	}
	if (doModal == true) {
		citruscartNewModal(msg);
	}
	var url = 'index.php?option=com_citruscart&view=productcompare&task=addProductToCompare&format=raw&product_id=' + id + '&add=' + add;

	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",

		onSuccess : function(response) {
			var resp = JSON.decode(response, false);

			if (doModal != false) { (function() { document.body.removeChild($('dscModal')); }).delay(500); }
			if (resp.error == '1') {
				if (citruscartJQ('validationmessage')) {
					citruscartJQ('validationmessage').set('html', resp.msg);
				}
			} else {
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', resp.msg);
				}
			}
		}
	}).send();
}

/**
 *
 */
function citruscartAddCoupon(form, mult_enabled) {
	var new_coupon_code = document.getElementById('new_coupon_code').value;

	var url = 'index.php?option=com_citruscart&view=checkout&task=validateCouponCode&format=raw&coupon_code=' + new_coupon_code;
	var container = 'coupon_code_message';

	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData(form);

	citruscartGrayOutAjaxDiv('coupon_code_area', Joomla.JText._('COM_CITRUSCART_CHECKING_COUPON'));
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
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', '');
				}

				// Push the code into the form
				var cc_html = citruscartJQ('coupon_codes').innerHTML + resp.msg;
				if (citruscartJQ('coupon_codes').set('html', cc_html)) {
				    citruscartGetPaymentOptions('onCheckoutPayment_wrapper', form, '' );
				}

				// Clear the field
				document.getElementById('new_coupon_code').value = '';

				// Update the summary
				citruscartGrayOutAjaxDiv('onCheckoutCart_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_CART'));
				citruscartGetCheckoutTotals(true);
				citruscartRefreshTotalAmountDue();

				if (mult_enabled != 1) {
					citruscartShowHideDiv('coupon_code_form');
				}
				
			} else {
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', resp.msg);
				}
			}

			el = $$('#coupon_code_area .citruscartAjaxGrayDiv');
			if (el != '')
				el.destroy();
			citruscartSetColorInContainer('coupon_code_area', '');
		}
	}).send();
}

/**
 *
 */
function citruscartAddCartCoupon(form, mult_enabled) {
	var new_coupon_code = document.getElementById('new_coupon_code').value;

	var url = 'index.php?option=com_citruscart&view=carts&task=validateCouponCode&format=raw&coupon_code=' + new_coupon_code;
	var container = 'coupon_code_message';

	// loop through form elements and prepare an array of objects for passing to server
	var str = citruscartGetFormInputData(form);

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
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', '');
				}

				// Push the code into the form
				var cc_html = $('coupon_codes').innerHTML + resp.msg;
				citruscartJQ('coupon_codes').set('html', cc_html);

				// Clear the field
				document.getElementById('new_coupon_code').value = '';

				// Update the summary
				citruscartGetCartCheckoutTotals();
				citruscartRefreshCartTotalAmountDue();

				if (mult_enabled != 1) {
					citruscartShowHideDiv('coupon_code_form');
				}
			} else {
				if (document.getElementById(container)) {
					document.getElementById(container).set('html', resp.msg);
				}
			}
		}
	}).send();
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 *
 * @return
 */
function citruscartGetCartCheckoutTotals() {
	var url = 'index.php?option=com_citruscart&view=carts&task=saveOrderCoupons&format=raw';
	citruscartDoTask(url, 'onCheckoutCart_wrapper', document.adminForm, '', true);
}

/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 *
 * @return
 */
function citruscartRefreshCartTotalAmountDue() {
	var url = 'index.php?option=com_citruscart&view=carts&task=totalAmountDue&format=raw';
	citruscartDoTask(url, 'totalAmountDue', document.adminForm, '', false, function() {
	});

	//url, container, form, msg, doModal, execFunc
}

/**
 * Puts an AJAX loader gif to a div element
 * @param container ID of the div element
 * @param text Text next to ajax loading picture
 * @param suffix Suffix of the AJAX loader gif (in case it's empty '_transp' is used)
 */
function citruscartPutAjaxLoader(container, text, suffix) {
	if (!suffix || suffix == '')
		suffix = '_transp';

	text_element = '';
	if (text != null && text != '')
		text_element = '<span> ' + text + '</span>';
	var img_loader = '<img src="' + window.com_citruscart.jbase + 'media/citruscart/images/ajax-loader' + suffix + '.gif' + '"/>';
	if (document.getElementById(container)) {
	    document.getElementById(container).set('html', img_loader + text_element);
	}
}

/**
 * Puts an AJAX loader gif to a div element and gray out that div
 * @param container 	ID of the div element
 * @param text 			Text which is displayed under the image
 * @param suffix 		Suffix of the AJAX loader gif (in case it's empty '_transp' is used)
 *
 */
function citruscartGrayOutAjaxDiv(container, text, suffix) {
	console.log(container);
	if (!suffix || suffix == '')
		suffix = '_transp';

	var img_loader = '<img src="' + window.com_citruscart.jbase + 'media/citruscart/images/ajax-loader' + suffix + '.gif' + '"/>';
	//document.getElementById(container).setStyle('position', 'relative');
	text_element = '';
	if (text && text.length)
		text_element = '<div class="text">' + text + '</div>';

	// make all texts in the countainer gray
	citruscartSetColorInContainer(container, '');
	console.log(img_loader);
	//document.getElementById(container).innerHTML += '<div class="citruscartAjaxGrayDiv">' + img_loader + text_element + '</div>';
	
	
}

function citruscartSetColorInContainer(container, color) {
	console.log(color);
	if (document.getElementById(container)) { document.getElementById(container).setStyle('color', color); }
	$$('#' + container + ' *' ).each(function(el) {
		el.setStyle('color', color);
	});
}

/*
 * Method to store values of all inputs on a form
 *
 * @param form Form
 *
 * @return Associative array
 */
function citruscartStoreFormInputs(form) {
	var values = new Array();
	for ( i = 0; i < form.elements.length; i++) {
		value = {
			value : form.elements[i].value,
			checked : form.elements[i].checked
		};
		values[form.elements[i].name] = value;
	}
	return values;
}

/*
 * Method to restore values of all inputs on a form
 *
 * @param form 		Form
 * @param values	Values which are being restored
 *
 * @return Associative array
 */
function citruscartRestoreFormInputs(form, values) {
	for ( i = 0; i < form.elements.length; i++) {
		if (form.elements[i].getAttribute('type') == 'checkbox')
			form.elements[i].checked = values[form.elements[i].name].checked;
		else if (citruscartJQ(form.elements[i].id))
			citruscartJQ(form.elements[i].id).val( values[form.elements[i].name].value);
	}
}

/*
 * Method to get value from all form inputs and put it in an array which will be passed via AJAX request
 *
 * @param form		Form with inputs
 *
 * @return Array with all data from all inputs on the form
 */
function citruscartGetFormInputData(form) {
	var str = new Array();
	for ( i = 0; i < form.elements.length; i++) {
		postvar = {
			name : form.elements[i].name,
			value : form.elements[i].value,
			checked : form.elements[i].checked,
			id : form.elements[i].id
		};
		str[i] = postvar;
	}
	return str;
}

function citruscartDeleteGrayDivs() {
    $$('.citruscartAjaxGrayDiv').each(function(el) {
        el.destroy();
    });
}