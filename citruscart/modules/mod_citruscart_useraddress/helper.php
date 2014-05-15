<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class modCitruscartUserAddressHelper
{
    static public function getAddresses()
    {
    	/* Get the application */
    	$app = JFactory::getApplication();
        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );

        // get the user's addresses using the address model
    	$model = JModelLegacy::getInstance( 'Addresses', 'CitruscartModel' );
    	$model->setState('filter_userid', $app->input->get('id', 0, 'request', 'int'));
    	//$model->setState('filter_userid', JRequest::getVar('id', 0, 'request', 'int'));
    	$model->setState('filter_deleted', 0);
    	$userAddresses = $model->getList();
    	return $userAddresses;
    }
}
