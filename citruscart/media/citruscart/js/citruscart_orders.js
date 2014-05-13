/**
 * Based on the session contents,
 * calculates the order total
 * and returns HTML
 *
 * @return
 */
function citruscartGetOrderTotals() {
	var url = 'index.php?option=com_citruscart&controller=orders&task=getOrderTotals&format=raw';
	citruscartDoTask(url, 'order_totals_div', document.adminForm);
}

/**
 * Will close the lightbox and update the product list
 * in the admin-side order creation form
 *
 * @return
 */
function citruscartAddProductsToOrder() {
	document.getElementById('sbox-window').close();

	var url = 'index.php?option=com_citruscart&controller=orders&task=getProducts&format=raw';
	citruscartDoTask(url, 'order_products_div', document.adminForm);
	citruscartPause(1000);
	citruscartGetOrderTotals();
}

/**
 * Updates Product Quantities
 *
 * @return
 */
function citruscartUpdateProductQuantities() {
	url = 'index.php?option=com_citruscart&controller=orders&task=updateProductQuantities&format=raw';
	citruscartDoTask(url, 'order_products_div', document.adminForm);
	citruscartPause(1500);
	citruscartGetOrderTotals();
}

/**
 * Removes Products from the Order
 *
 * @return
 */
function citruscartRemoveProducts(message) {
	// if !boxchecked, alert, else doTask
	if (document.adminForm.boxchecked.value == 0) {
		alert(message);
	} else {
		url = 'index.php?option=com_citruscart&controller=orders&task=removeProducts&format=raw';
		citruscartDoTask(url, 'order_products_div', document.adminForm);
		citruscartPause(1500);
		citruscartGetOrderTotals();
	}
}

/**
 * Gets an address
 *
 * @param addressType
 * @return
 */
function citruscartSetAddressToDiv(addressType) {
	var addressDropdownName = addressType + '_address_id';
	var addressInputTable = addressType + 'AddressInputFormTable';
	var selectedAddressDiv = addressType + 'SelectedAddressDiv';
	var saveToAddressBookControl = document.getElementById(addressType + '_save_to_address_book_div');

	var dropdown = document.getElementById(addressDropdownName);
	if (dropdown != null) {
		var addressid = dropdown.value;
		if (addressid > 0) {

			if (saveToAddressBookControl != null) {
				saveToAddressBookControl.style.display = 'none';
			}

			//send addressid to address controller and get information back
			var url = 'index.php?option=com_citruscart&controller=addresses&task=getaddressdata&format=raw&addressid=' + addressid.__toString();
			//call controller
			try {
				var a = new Request({
					url : url,
					method : "post",
					onSuccess : function(response) {
						var resp = Json.evaluate(response);
						document.getElementById(selectedAddressDiv).innerHTML = resp.msg;
						document.getElementById(selectedAddressDiv).style.display = 'inline';
						document.getElementById(addressInputTable).style.display = 'none';
					}
				}).send();
			} catch(err) {
				alert(err.description);
			}
		} else {
			if (saveToAddressBookControl != null) {
				saveToAddressBookControl.style.display = 'block';
			}

			document.getElementById(selectedAddressDiv).style.display = 'none';
			document.getElementById(addressInputTable).style.display = 'inline';
		}
	}
}

/**
 * If Same as Billing checkbox is selected
 * this disables all the input fields in the shipping address form
 *
 * @param checkbox
 * @return
 */
function citruscartDisableShippingAddressControls(checkbox) {
	var disable = false;
	if (checkbox.checked) {
		disable = true;
	}
	var fields = "address_id;title;first_name;middle_name;last_name;company;address_1;address_2;city;country_id;zone_id;postal_code;phone_1;phone_2;fax";
	var fieldList = fields.split(';');

	for (var index = 0; index < fieldList.length; index++) {
		shippingControl = document.getElementById('shipping_input_' + fieldList[index]);
		if (shippingControl != null) {
			shippingControl.disabled = disable;
		}
	}

	var selectedAddressDiv = document.getElementById('selectedShippingAddressDiv');
	if (selectedAddressDiv != null) {
		if (disable) {
			selectedAddressDiv.style.display = 'none';
		} else {
			selectedAddressDiv.style.display = 'inline';
		}
	}
}

/**
 * If the save to address book box is checked
 * this displays the row for the address name
 *
 * @param checkbox
 * @param addressNameRowId
 * @param addressNameControlId
 * @return
 */
function citruscartShowAddressNameForSaveToAddressBook(checkbox, addressNameRowId, addressNameControlId) {
	var addressNameRow = document.getElementById(addressNameRowId);
	if (checkbox.checked) {
		addressNameRow.style.display = 'table-row';
		var addressNameControl = document.getElementById(addressNameControlId);
		if (addressNameControl != null) {
			addressNameControl.focus();
		}
	} else {
		addressNameRow.style.display = 'none';
	}
}

/**
 * Upon loading the admin orders form,
 * displays the user's default addresses
 * @return
 */
function citruscartSelectDefaultAddresses() {
	citruscartSetAddressToDiv('billing');
	citruscartSetAddressToDiv('shipping');
}
