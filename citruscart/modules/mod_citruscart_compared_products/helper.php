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

jimport( 'joomla.application.component.model' );

class modCitruscartComparedProductsHelper extends JObject
{
	/**
	 * Method to get the compared product of the current user
	 * @return array
	 */
    function getComparedProducts()
    {
    	JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );
		$user_id = JFactory::getUser()->id;
		$session =  JFactory::getSession();

    	$model  = JModelLegacy::getInstance( 'ProductCompare', 'CitruscartModel' );
     	$model->setState('filter_user', $user_id );

     	if (empty($this->user_id))
        {
            $model->setState('filter_session', $session->getId() );
        }

		$items = $model->getList();

		foreach($items as $item)
		{
			$table = JTable::getInstance('Products', 'CitruscartTable');
			$table->load(array('product_id'=> $item->product_id));

			$item->product_name = $table->product_name;
			$item->link = 'index.php?option=com_citruscart&view=products&task=view&id='.$item->product_id;
		}

		return $items;
    }
}
