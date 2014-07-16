<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class CitruscartControllerAddresses extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'addresses');
	}

	/**
	 * @Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
	    $state = parent::_setModelState();
	    $app = JFactory::getApplication();
	    $model = $this->getModel( $this->get('suffix') );
	    $ns = $this->getNamespace();

	    $state['filter_userid']         = $app->getUserStateFromRequest($ns.'userid', 'filter_userid', '', '');
	    $state['filter_user']         = $app->getUserStateFromRequest($ns.'user', 'filter_user', '', '');
	    $state['filter_address']         = $app->getUserStateFromRequest($ns.'address', 'filter_address', '', '');

	    foreach ($state as $key=>$value)
	    {
	        $model->setState( $key, $value );
	    }
	    return $state;
	}

	/**
     * @Returns a selectlist of zones
     * @Called via Ajax
     *
     * @return unknown_type
     */
    function getZones()
    {
    	$app = JFactory::getApplication();
        Citruscart::load( 'CitruscartSelect', 'library.select' );
        $html = '';
        $text = '';



    	$country_id =$app->input->getInt('country_id');
    	$name = $app->input->get('name', 'zone_id');
    	if (empty($country_id)) {
    	    $html = JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
    	} else {
        	$html = CitruscartSelect::zone( '', $name, $country_id );
    	}

        $response = array();
        $response['msg'] = $html;
        $response['error'] = '';

        // encode and echo (need to echo to send back to browser)
        echo ( json_encode($response) );

        return;
    }

	function getAddressData(){
		// set response array
		$html = '';
		$app = JFactory::getApplication();
		//get addressid from request
		$addressid = $app->input->get( 'addressid', '', 'request', 'int' );

		//load model: addresses
		$model = $this->getModel( $this->get('suffix') );
		$model->setId($addressid);
		$item = $model->getItem();
		if(!empty($item)){
			$fulladdress = '<br/><ul class=\'addresslist\'>';
			$fulladdress .= '<li><b>'.JText::_('COM_CITRUSCART_USING_SELECTED_ADDRESS').'</b></li>';
			$fulladdress .= $this->setAddressOption($item->company);

			$fulladdress .= '<li>';
			$fulladdress .= $this->setAddressOptionMultiValue($item->title, ' ');
			$fulladdress .= $this->setAddressOptionMultiValue($item->first_name, ' ');
			$fulladdress .= $this->setAddressOptionMultiValue($item->middle_name, ' ');
			$fulladdress .= $this->setAddressOptionMultiValue($item->last_name, '');
			$fulladdress .= "</li>";

			$fulladdress .= $this->setAddressOption($item->address_1);
			$fulladdress .= $this->setAddressOption($item->address_2);
			$fulladdress .= $this->setAddressOption($item->city);
			$fulladdress .= $this->setAddressOption($item->postal_code);
			$fulladdress .= $this->setAddressOption($item->zone_name);
			$fulladdress .= $this->setAddressOption($item->country_name);
			$fulladdress .= "<li></li>";
			$fulladdress .= $this->setAddressOption($item->phone_1);
			$fulladdress .= $this->setAddressOption($item->phone_2);
			$fulladdress .= $this->setAddressOption($item->fax);
			$fulladdress .= '</ul>';
			$html = $fulladdress;
		}

		$response = array();
		$response['msg'] = $html;
		$response['error'] = '';

		echo ( json_encode( $response ) );
		return;
	}

	function setAddressOption($optionValue)
	{
		$optionText = '';
		if (isset($optionValue)){
			$optionText = '<li>'.$optionValue.'</li>';
		}
		return $optionText;
	}

	function setAddressOptionMultiValue($optionValue, $separator)
	{
		$optionText = '';
		if (isset($optionValue)){
			$optionText .= $optionValue;
			if (strlen($separator)){
				$optionText .= 	$separator;
			}
		}
		return $optionText;
	}
}

