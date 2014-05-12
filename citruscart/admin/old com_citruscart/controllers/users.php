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

class CitruscartControllerUsers extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->set('suffix', 'users');
		$this->registerTask( 'change_subnum', 'change_subnum' );
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
		$state['filter_username']         = $app->getUserStateFromRequest($ns.'username', 'filter_username', '', '');
		$state['filter_email']         = $app->getUserStateFromRequest($ns.'email', 'filter_email', '', '');
		$state['filter_group']         = $app->getUserStateFromRequest($ns.'group', 'filter_group', '', '');
		if( Citruscart::getInstance()->get( 'display_subnum', 0 ) )
		$state['filter_subnum']       = $app->getUserStateFromRequest($ns.'subnum', 'filter_subnum', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	function view($cachable=false, $urlparams = false)
	{
		$app = JFactory::getApplication();
		
		$id = $app->input->get('id');
				
		$model = $this->getModel( $this->get('suffix') );
		
		$model->getId();
		$row = $model->getItem($id);
				
		$view   = $this->getView( $this->get('suffix'), 'html' );
		$view->setModel( $model, true );
		$view->assign( 'row', $row );
		$view->setLayout( 'view' );
					
		$orderstates_csv = Citruscart::getInstance()->get('orderstates_csv', '2, 3, 5, 17');
		$orderstates_array=explode(',', $orderstates_csv);

		//Get Data From OrdersItems Model
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$modelOrders= JModelLegacy::getInstance( 'Orders', 'CitruscartModel');
		$modelOrders->setState( 'filter_userid',  $row->id );
		$modelOrders->setState( 'order', 'tbl.created_date' );
		$modelOrders->setState( 'direction', 'DESC' );
		$modelOrders->setState( 'filter_orderstates',  $orderstates_array);
		$allorders = $modelOrders->getList();
		$modelOrders->setState( 'limit', '5');
		$lastfiveorders = $modelOrders->getList( true );
		$view->assign( 'orders', $lastfiveorders );
		//$view->display();
				
		$spent = 0;
		
		foreach ($allorders as $orderitem)
		{
			$spent += $orderitem->order_total;
		}
		$view->assign( 'spent', $spent );
	
		//Get Data From Carts Model
		$modelCarts = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );

		$modelCarts->setState( 'filter_user', $row->id );

		$carts = $modelCarts->getList();
		$view->assign( 'carts', $carts );
		$total_cart=0;

		foreach ($carts as $cart)
		{
			$cart->total_price=$cart->product_price *$cart->product_qty;
			$total_cart+=$cart->total_price;
		}
		$view->assign( 'total_cart', $total_cart );

		//Subcription Data
		$modelSubs= JModelLegacy::getInstance( 'subscriptions', 'CitruscartModel');
		$modelSubs->setState( 'filter_userid',  $row->id );
		$modelSubs->setState( 'filter_enabled', 1 );
		$modelOrders->setState( 'limit', '5' );
		$subs= $modelSubs->getList();
		$view->assign( 'subs',$subs );
		
		//Get Data from Productcomments Model and left join to products
		/*$database = $model->getDbo();
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();
		$query->select( 'tbl.*');
		$query->select( 'substring(tbl.productcomment_text, 1, 250) AS trimcom' );
		$query->from( '#__citruscart_productcomments AS tbl' );
		$query->select('p.product_name AS p_name');
		$query->join('LEFT', '#__citruscart_products AS p ON p.product_id = tbl.product_id');
		$query->where("tbl.user_id='$row->id'");
		 */
		$database = JFactory::getDbo();
		$query = $database->getQuery(true);
		$query->select("tbl.* , substring(tbl.productcomment_text,1,250) AS trimcom");
		$query->from('#__citruscart_productcomments AS tbl');
		$query->leftJoin("#__citruscart_products AS p ON p.product_id = tbl.product_id");
		$query->select('p.product_name AS p_name');
		$query->where("tbl.user_id=".$database->q($row->id));
		
		$database->setQuery( $query );
		//$database->setQuery( (string) $query );
		$procoms = $database->loadObjectList();
		$view->assign( 'procoms', $procoms);

		$model->emptyState();
		$this->_setModelState();
		$surrounding = $model->getSurrounding( $model->getId() );
		$view->assign( 'surrounding', $surrounding );
		$view->setTask(true);
				
		$view->display();
		$this->footer();
		return;
	}

	function change_subnum()
	{
		$app = JFactory::getApplication();
		$sub_num  = $app->input->getInt( 'sub_number', 0 );
		$model = JModelLegacy::getInstance( 'Users', 'CitruscartModel' );
		$id = $model->getId();
		$url = JRoute::_( 'index.php?option=com_citruscart&controller=users&view=users&task=view&id='.$id, false );

		$db = JFactory::getDbo();
		$q = 'SELECT `user_info_id` FROM `#__citruscart_userinfo` WHERE `user_id` <> '.$id.' AND `sub_number` = '.$sub_num;
		$db->setQuery( $q );
		$res = $db->loadResult();
		if( $res !== null )
		{
			$this->setRedirect( $url, JText::_('COM_CITRUSCART_COULD_NOT_CHANGE_SUB_NUMBER'), 'error' );
			return;
		}
		$q = 'UPDATE `#__citruscart_userinfo` SET `sub_number` = '.$sub_num.' WHERE `user_id` = '.$id;
		$db->setQuery( $q );
		$db->query( $q );
		if( $db->getAffectedRows() == 1 )
			$this->setRedirect( $url, JText::_('COM_CITRUSCART_SUB_NUMBER_CHANGED') );
		else
			$this->setRedirect( $url, JText::_('COM_CITRUSCART_NO_SUB_NUMBER_CHANGED'), 'notice' );
		return;
	}
}

