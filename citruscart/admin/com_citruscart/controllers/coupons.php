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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerCoupons extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'coupons');
		$this->registerTask( 'coupon_enabled.enable', 'boolean' );
		$this->registerTask( 'coupon_enabled.disable', 'boolean' );
		$this->registerTask( 'selected_enable', 'selected_switch' );
		$this->registerTask( 'selected_disable', 'selected_switch' );
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

    	$state['filter_id_from'] 	= $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
    	$state['filter_id_to'] 		= $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
    	$state['filter_name'] 		= $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
		$state['filter_enabled'] 	= $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
		$state['filter_code'] 		= $app->getUserStateFromRequest($ns.'code', 'filter_code', '', '');
		$state['filter_value'] 		= $app->getUserStateFromRequest($ns.'value', 'filter_value', '', '');
		$state['filter_type'] 		= $app->getUserStateFromRequest($ns.'type', 'filter_type', '', '');

    	foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
  		return $state;
    }

	/**
	 * Loads view for assigning products to coupons
	 *
	 * @return unknown_type
	 * @enterprise
	 */
	function selectproducts()
	{
		$this->set('suffix', 'products');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_category'] 		= $app->getUserStateFromRequest($ns.'category', 'filter_category', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		//$id = JRequest::getInt( 'id', 0 );
		$id = $app->input->getInt( 'id', 0 );

		$row = $model->getTable( 'coupons' );

		$row->load( $id );

		$view   = $this->getView( 'coupons', 'html' );
		$view->set( '_controller', 'coupons' );
		$view->set( '_view', 'coupons' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=coupons&task=selectproducts&tmpl=component&id=".$id );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'selectproducts' );
		$view->setTask(true);
		//JRequest::setVar( 'hidemainmenu', '1' );
		$app->input->set( 'hidemainmenu', '1' );
		$view->display();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function selected_switch()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();

		$id= $app->input->getInt('id');
		$cids = $app->input->get('cid',array(0),'request','array');
		$task = $app->input->getString('task');
		$vals = explode('_', $task);

		$field = $vals['0'];
		$action = $vals['1'];

		switch (strtolower($action))
		{
			case "switch":
				$switch = '1';
				break;
			case "disable":
				$enable = '0';
				$switch = '0';
				break;
			case "enable":
				$enable = '1';
				$switch = '0';
				break;
			default:
				$this->messagetype  = 'notice';
				$this->message      = JText::_('COM_CITRUSCART_INVALID_TASK');
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
				break;
		}

		$keynames = array();
		foreach ($cids as $cid)
		{
			$table = JTable::getInstance('ProductCoupons', 'CitruscartTable');
			$keynames["coupon_id"] = $id;
			$keynames["product_id"] = $cid;
			$table->load( $keynames );
			if ($switch)
			{
				if (isset($table->product_id))
				{
					if (!$table->delete())
					{
						$this->message .= $cid.': '.$table->getError().'<br/>';
						$this->messagetype = 'notice';
						$error = true;
					}
				}
				else
				{
					$table->product_id = $cid;
					$table->coupon_id = $id;
					if (!$table->save())
					{
						$this->message .= $cid.': '.$table->getError().'<br/>';
						$this->messagetype = 'notice';
						$error = true;
					}
				}
			}
			else
			{
				switch ($enable)
				{
					case "1":
						$table->product_id = $cid;
						$table->coupon_id = $id;
						if (!$table->save())
						{
							$this->message .= $cid.': '.$table->getError().'<br/>';
							$this->messagetype = 'notice';
							$error = true;
						}
						break;
					case "0":
					default:
						if (!$table->delete())
						{
							$this->message .= $cid.': '.$table->getError().'<br/>';
							$this->messagetype = 'notice';
							$error = true;
						}
						break;
				}
			}
		}

		$model->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . ": " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = $app->input->getString('return') ?

		base64_decode( $app->input->getString('return') ) : "index.php?option=com_citruscart&controller=coupons&task=selectproducts&tmpl=component&id=".$id;

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

}
