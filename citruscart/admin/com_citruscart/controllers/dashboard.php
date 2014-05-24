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

// no direct access
defined('_JEXEC') or die('Restricted access');

class CitruscartControllerDashboard extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'dashboard');
	}

	public function display($cachable=false, $urlparams = false)
	{

	    $model = $this->getModel( $this->get('suffix') );
	    
	    $state = $model->getState();
	    $app = JFactory::getApplication();
	    $state->stats_interval = $app->input->getString('stats_interval', 'last_thirty');

	    $model->setState('stats_interval', $state->stats_interval);

	    $cache = JFactory::getCache('com_citruscart');
	    $cache->setCaching(true);
	    $cache->setLifeTime('900');
	    $orders = $cache->call(array($model, 'getOrdersChartData'), $state->stats_interval);

	    //$models = JModelLegacy::getInstance( 'Dashboard', 'CitruscartModel' );
        
	    //$models = JModelItem::getInstance('OrderItems', 'CitruscartModel');
	    $models = JModelLegacy::getInstance( 'Dashboard', 'CitruscartModel' );
	    $totalorderitems = count($models->getOrderedItemsChartData()); 
	    	    
	    $revenue = $cache->call(array($model, 'getRevenueChartData'), $state->stats_interval);
	    $total = $cache->call(array($model, 'getSumChartData'), $orders);
	    $sum = $cache->call(array($model, 'getSumChartData'), $revenue);
        	    
        $interval = $model->getStatIntervalValues($state->stats_interval);
		//print_r($interval);
	    $view = $this->getView( $this->get('suffix'), 'html' );
	    $view->assign( 'orders', $orders );
	    $view->assign( 'revenue', $revenue );
        $view->assign( 'total', $total );
        $view->assign( 'orderedItems', $totalorderitems);
        $view->assign( 'sum', $sum );
        $view->assign( 'interval', $interval );

	    parent::display($cachable, $urlparams);
	}

	function search()
	{
		$app = JFactory::getApplication();

		$filter =$app->input->getString('citruscart_search_admin_keyword');
		$filter_view  = $app->input->get('citruscart_search_admin_view');
	    //$filter_view = JRequest::get('citruscart_search_admin_view');

	    $redirect = "index.php?option=com_citruscart&view=" . $filter_view . "&filter=" . urlencode( $filter );

	    JFactory::getApplication()->redirect( $redirect );
	}
}

