<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerGroups extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'groups');
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

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelStateUsers()
	{
		$this->set('suffix', 'users');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_username']         = $app->getUserStateFromRequest($ns.'username', 'filter_username', '', '');
        $state['filter_email']         = $app->getUserStateFromRequest($ns.'email', 'filter_email', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Loads view for assigning users to groups
	 *
	 * @return unknown_type
	 */
	function selectusers()
	{
		$this->set('suffix', 'users');
		$state = $this->_setModelStateUsers();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$id =$app->input->getInt('id',0);
		$row = $model->getTable( 'groups' );
		$model->setState( 'filter_usergroup', $id );
		$row->load( $id );

		$view   = $this->getView( 'groups', 'html' );
		$view->set( '_controller', 'groups' );
		$view->set( '_view', 'groups' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=groups&task=selectusers&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'selectusers' );
		$view->setTask(true);
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

		$id = $app->input->getInt( 'id', 0 );
		$cids = $app->input->get('cid', array (0), 'request', 'array');
		$task = $app->input->getString( 'task' );
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
			$table = JTable::getInstance('UserGroups', 'CitruscartTable');
			$keynames["group_id"] = $id;
			$keynames["user_id"] = $cid;
			$table->load( $keynames );

			if ($switch)
			{
				if (isset($table->user_id))
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
					$table->user_id = $cid;
					$table->group_id = $id;


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
						$table->user_id = $cid;
						$table->group_id = $id;
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

		$redirect = $app->input->get( 'return' ) ?
		base64_decode( $app->input->get( 'return' ) ) : "index.php?option=com_citruscart&controller=groups&task=selectusers&tmpl=component&id=".$id;
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

}

