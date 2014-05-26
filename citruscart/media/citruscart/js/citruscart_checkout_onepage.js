/**
 * Method to copy all data from Billing Address fields to Shipping Address fields
 * @param billingprefix
 * @param shippingprefix
 * @return
 */
function citruscartCopyBillingAdToShippingAd(checkbox, form) {
	var disable = false;
	if (checkbox.checked) {
		disable = true;
		citruscartGrayOutAddressDiv();
		citruscartGetShippingRates('onCheckoutShipping_wrapper', form, citruscartDeleteAddressGrayDiv);
		citruscartGetPaymentOptions('onCheckoutPayment_wrapper', form);
	}
}

function citruscartSaveOnepageOrder(container, errcontainer, form) {
	
	var url = 'index.php?option=com_citruscart&view=checkout&controller=checkout&task=saveOrderOnePage&format=raw';
	var str = citruscartGetFormInputData(form);
		// execute Ajax request to server
	citruscartPutAjaxLoader(errcontainer, Joomla.JText._('COM_CITRUSCART_VALIDATING'));
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
			//console.log(response);			
			var resp = JSON.decode(response, false);

			if (resp.error != '1') {
			    if (resp.redirect) {
			        window.location = resp.redirect;
			        return;
			    }
			    
				if ($(container)) {
					$(container).set('html', resp.msg);
				}
				if ($('onCheckoutCart_wrapper')) {
					$('onCheckoutCart_wrapper').set('html', resp.summary);
				}
				if ($('citruscartbtns')) {
					$('citruscartbtns').setStyle('display', 'none');
				}
				if ($('refreshpage')) {
					$('refreshpage').setStyle('display', 'block');
				}
				if ($('validationmessage')) {
					$('validationmessage').set('html', '');
				}
				window.location = String(window.location).replace(/\#.*$/, "") + "#citruscart-method";
			} else {
				if ($(errcontainer)) {
					$(errcontainer).set('html', resp.msg);
				}
				if (resp.anchor) {
					window.location = String(window.location).replace(/\#.*$/, "") + resp.anchor;
				}
			}
		}
	}).send();
}

function citruscartGetFinalForm(container, form, msg) {
	var url = 'index.php?option=com_citruscart&view=checkout&task=getRegisterForm&format=raw';
	citruscartDoTask(url, container, form, msg);
	$('citruscart-method-pane').set('html', $('hiddenregvalue').value);
}

function citruscartGetView(url, container, labelcont) {
	// execute Ajax request to server
	var a = new Request({
		url : url,
		method : "post",
		onSuccess : function(response) {
			var resp = JSON.decode(response, false);

			if (resp.error != '1') {
				if ($(container)) {
					$(container).set('html', resp.msg);
				}
				if (labelcont) {
					$(labelcont).set('html', resp.label);
				}
			}
		}
	}).send();
}

function citruscartGetRegistrationForm(container, form, msg) {
	var url = 'index.php?option=com_citruscart&view=checkout&task=getRegisterForm&format=raw';
	citruscartGetView(url, container, 'citruscart-method-pane');
}

/**
 * method to hide billing fields
 */
function citruscartHideBillingFields() {
	$('billingToggle_show').set('class', 'hidden');

	$('field-toggle').addEvent('change', function() {
		///$$('#billingDefaultAddress', '#billingToggle_show', '#billingToggle_hide').toggleClass('hidden');
		document.getElementById('billingDefaultAddress').toggleClass('hidden');
		document.getElementById('billingToggle_show').toggleClass('hidden');
		document.getElementById('billingToggle_hide').toggleClass('hidden');
		
	});
}

function citruscartCheckoutSetBillingAddress(url, container, selected, form) {
	var divContainer = document.getElementById(container);
	var divForm = document.getElementById('billing_input_addressForm');

	if (selected > 0)// address was selected -> get shipping rates
	{
		values = citruscartStoreFormInputs(form);
		divContainer.style.display = "";
		divForm.style.display = "none";
		citruscartGrayOutAddressDiv();
		citruscartDoTask(url, container, '', '', false);
		if ($('onCheckoutShipping_wrapper'))
			citruscartGrayOutAjaxDiv('onCheckoutShipping_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_SHIPPING_RATES'));
		citruscartGrayOutAjaxDiv('onCheckoutCart_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_CART'));

		citruscartGetCheckoutTotals(true);
		citruscartRestoreFormInputs(form, values);
		console.log(values);
	} else// user wants to create a new address
	{
		divContainer.style.display = "none";
		divForm.style.display = "";
	}
}

function citruscartCheckoutSetShippingAddress(url, container, form, selected) {
	var divContainer = document.getElementById(container);
	var divForm = document.getElementById('shipping_input_addressForm');
	if (selected > 0)// address was selected -> get shipping rates
	{
		values = citruscartStoreFormInputs(form);
		divContainer.style.display = "";
		divForm.style.display = "none";
		citruscartGrayOutAddressDiv();
		citruscartDoTask(url, container, '', '', false);
		citruscartGrayOutAjaxDiv('onCheckoutShipping_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_SHIPPING_RATES'));
		citruscartGetShippingRates('onCheckoutShipping_wrapper', form, CitruscartDeleteAddressGrayDiv);
		citruscartRestoreFormInputs(form, values);
	} else// user wants to create a new address
	{
		divContainer.style.display = "none";
		divForm.style.display = "";
	}
}