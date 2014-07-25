<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerEavAttributes extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'eavattributes');
        $this->registerTask( 'enabled.enable', 'boolean' );
        $this->registerTask( 'enabled.disable', 'boolean' );
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

        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_entitytype']         = $app->getUserStateFromRequest($ns.'entitytype', 'filter_entitytype', '', '');
		$state['order']     = $app->getUserStateFromRequest($ns.'filter_order', 'filter_order', 'tbl.ordering', 'cmd');


        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        return $state;
    }

	/**
	 * Loads view for assigning entities to attributes
	 *
	 * @return unknown_type
	 */
	function selectentities()
	{
		$app = JFactory::getApplication();

		$type = $app->input->get('eaventity_type', 'products');
		//$type = JRequest::getVar('eaventity_type', 'products');


		$this->set('suffix', $type);
		$state = parent::_setModelState();

		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$id = $app->input->get('id',0);
		$row = $model->getTable( 'eavattributes' );
		$row->load( $id );
		$view   = $this->getView( 'eavattributes', 'html' );
		$view->set( '_controller', 'eavattributes' );
		$view->set( '_view', 'eavattributes' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=eavattributes&task=selectentities&tmpl=component&eaventity_type=$type&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'select'.$type );
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
		$type = $app->input->get('eaventity_type', 'products');
		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();
		$id = $app->input->get('id' , 0);
		$cids = JRequest::getVar('cid', array (0), 'request', 'array');
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
			$table = JTable::getInstance('EavAttributeEntities', 'CitruscartTable');
			$keynames["eavattribute_id"] = $id;
			$keynames["eaventity_id"] = $cid;
			$keynames["eaventity_type"] = $type;

			if ($switch)
			{
				if (isset($table->eaventity_id))
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
					$table->eaventity_id = $cid;
					$table->eavattribute_id = $id;
					$table->eaventity_type = $type;
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
						$table->eaventity_id = $cid;
						$table->eavattribute_id = $id;
						$table->eaventity_type = $type;
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
		base64_decode( $app->input->getString('return') ) : "index.php?option=com_citruscart&controller=eavattributes&task=selectentities&tmpl=component&eaventity_type={$type}&id=".$id;
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

}

