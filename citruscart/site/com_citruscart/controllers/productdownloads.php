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

class CitruscartControllerProductDownloads extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		if (empty(JFactory::getUser()->id))
		{
			$url = JRoute::_( "index.php?option=com_citruscart&view=productdownloads" );
			Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
			$redirect = JRoute::_( CitruscartHelperUser::getUserLoginUrl( $url ), false );
			JFactory::getApplication()->redirect( $redirect );
			return;
		}
		parent::__construct();

		$this->set('suffix','productdownloads');
	}

	/**
 	 *
	 * @return unknown_type
	*/
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();
		$config = Citruscart::getInstance();
		// adjust offset for when filter has changed
		if (
			$app->getUserState( $ns.'product_id' ) != $app->getUserStateFromRequest($ns.'product_id', 'filter_product_id', '', '')
		)
		{
			$state['limitstart'] = '0';
		}

		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.productdownload_startdate', 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
		$state['filter_product_id'] = $app->getUserStateFromRequest($ns.'product_id', 'filter_product_id', '', 'integer');
		$state['filter_user']     = JFactory::getUser()->id;
		$state['filter']      = $app->getUserStateFromRequest($ns.'filter', 'filter', '', 'word');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		return $state;
	}
}