<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewOrders extends CitruscartViewBase
{
    /**
     *
     * @param $tpl
     * @return unknown_type
     */
    function display($tpl=null, $perform = true )
    {
        $layout = $this->getLayout();
        switch(strtolower($layout))
        {
            case "email":
        	case "print":
            case "view":
                $this->_form($tpl);
              break;
            case "form":
                JFactory::getApplication()->input->set('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->_default($tpl);
              break;
        }
        parent::display($tpl);
    }

    /**
     * Basic methods for a form
     * @param $tpl
     * @return unknown_type
     */
    function _form($tpl='')
    {
        parent::_form($tpl);

        $shop_info = array();

   		 // Get the shop country name
		$countryModel = JModelLegacy::getInstance('Countries', 'CitruscartModel');
		$countryModel->setId(Citruscart::getInstance()->get('shop_country'));
		$countryItem = $countryModel->getItem();
		if($countryItem){
			$shop_info['shop_country_name'] = $countryItem->country_name;
		}

		// Get the shop zone name
		$zoneModel = JModelLegacy::getInstance('Zones', 'CitruscartModel');
		$zoneModel->setId(Citruscart::getInstance()->get('shop_zone'));
		$zoneItem = $zoneModel->getItem();
		if($zoneItem){
			$shop_info['shop_zone_name'] = $zoneItem->zone_name;
		}

		$this->assign('shop_info', (object) $shop_info);
    }

    /*
     * Loads layour for displaying taxes
     *
     * @params $tpl Specifies name of layout (null means cart_taxes)
     *
     * @return Content of a layout with taxes
     */
	function displayTaxes( $tpl = null )
	{
		$tmpl = 'cart_taxes';
		if( $tpl !== null )
			$tmpl = $tpl;
		$this->setLayout( $tmpl );

		return $this->loadTemplate( null );
	}


	/*
	 * To hide useless buttons from POS back-end
	 */
	function _formToolbar($isNew = null)
	{
	}

	/*
	 * To hide useless buttons from POS back-end
	 */
	function _defaultToolbar($isNew = null)
	{
	}
}
