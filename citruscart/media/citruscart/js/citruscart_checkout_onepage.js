/**
 * Method to copy all data from Billing Address fields to Shipping Address fields
 * @param billingprefix
 * @param shippingprefix
 * @return
 */
function CitruscartCopyBillingAdToShippingAd(checkbox, form) {
	var disable = false;
	if (checkbox.checked) {
		disable = true;
		CitruscartGrayOutAddressDiv();
		CitruscartGetShippingRates('onCheckoutShipping_wrapper', form, CitruscartDeleteAddressGrayDiv);
		CitruscartGetPaymentOptions('onCheckoutPayment_wrapper', form);
	}
}

function CitruscartSaveOnepageOrder(container, errcontainer, form) {
	var url = 'index.php?option=com_citruscart&view=checkout&controller=checkout&task=saveOrderOnePage&format=raw';
	var str = CitruscartGetFormInputData(form);

	// execute Ajax request to server
	CitruscartPutAjaxLoader(errcontainer, Joomla.JText._('COM_CITRUSCART_VALIDATING'));
	var a = new Request({
		url : url,
		method : "post",
		data : {
			"elements" : JSON.encode(str)
		},
		onSuccess : function(response) {
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
				if ($('citruscart_btns')) {
					$('citruscart_btns').setStyle('display', 'none');
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

function CitruscartGetFinalForm(container, form, msg) {
	var url = 'index.php?option=com_citruscart&view=checkout&task=getRegisterForm&format=raw';
	CitruscartDoTask(url, container, form, msg);
	$('citruscart-method-pane').set('html', $('hiddenregvalue').value);
}

function CitruscartGetView(url, container, labelcont) {
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

function CitruscartGetRegistrationForm(container, form, msg) {
	var url = 'index.php?option=com_citruscart&view=checkout&task=getRegisterForm&format=raw';
	CitruscartGetView(url, container, 'citruscart-method-pane');
}

/**
 * method to hide billing fields
 */
function CitruscartHideBillingFields() {
	$('billingToggle_show').set('class', 'hidden');

	$('field-toggle').addEvent('change', function() {
		$$('#billingDefaultAddress', '#billingToggle_show', '#billingToggle_hide').toggleClass('hidden');
	});
}

function CitruscartCheckoutSetBillingAddress(url, container, selected, form) {
	var divContainer = document.getElementById(container);
	var divForm = document.getElementById('billing_input_addressForm');

	if (selected > 0)// address was selected -> get shipping rates
	{
		values = CitruscartStoreFormInputs(form);
		divContainer.style.display = "";
		divForm.style.display = "none";
		CitruscartGrayOutAddressDiv();
		CitruscartDoTask(url, container, '', '', false);
		if ($('onCheckoutShipping_wrapper'))
			CitruscartGrayOutAjaxDiv('onCheckoutShipping_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_SHIPPING_RATES'));
		CitruscartGrayOutAjaxDiv('onCheckoutCart_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_CART'));

		CitruscartGetCheckoutTotals(true);
		CitruscartRestoreFormInputs(form, values);
	} else// user wants to create a new address
	{
		divContainer.style.display = "none";
		divForm.style.display = "";
	}
}

function CitruscartCheckoutSetShippingAddress(url, container, form, selected) {
	var divContainer = document.getElementById(container);
	var divForm = document.getElementById('shipping_input_addressForm');
	if (selected > 0)// address was selected -> get shipping rates
	{
		values = CitruscartStoreFormInputs(form);
		divContainer.style.display = "";
		divForm.style.display = "none";
		CitruscartGrayOutAddressDiv();
		CitruscartDoTask(url, container, '', '', false);
		CitruscartGrayOutAjaxDiv('onCheckoutShipping_wrapper', Joomla.JText._('COM_CITRUSCART_UPDATING_SHIPPING_RATES'));
		CitruscartGetShippingRates('onCheckoutShipping_wrapper', form, CitruscartDeleteAddressGrayDiv);
		CitruscartRestoreFormInputs(form, values);
	} else// user wants to create a new address
	{
		divContainer.style.display = "none";
		divForm.style.display = "";
	}
}