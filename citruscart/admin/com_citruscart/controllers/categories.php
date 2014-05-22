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

class CitruscartControllerCategories extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'categories');
		$this->registerTask( 'category_enabled.enable', 'boolean' );
		$this->registerTask( 'category_enabled.disable', 'boolean' );
		$this->registerTask( 'selected_enable', 'selected_switch' );
		$this->registerTask( 'selected_disable', 'selected_switch' );
		$this->registerTask( 'saveprev', 'save' );
		$this->registerTask( 'savenext', 'save' );
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

		$state['order']             = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.lft', 'cmd');
		$state['filter_id_from'] 	= $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to'] 		= $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_name'] 		= $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
		$state['filter_parentid'] 	= $app->getUserStateFromRequest($ns.'parentid', 'filter_parentid', '', '');
		$state['filter_enabled'] 	= $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
		$state['limit'] 	= $app->getUserStateFromRequest($ns.'limit', 'limit', 0, 'int');
		$state['limitstart'] 	= $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');




		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

    /**
     * Reorders multiple items (based on form input from list) and redirects to default layout
     * @return void
     */
    function ordering()
    {
        parent::ordering();
        $this->rebuild();
        $row->reorder();
    }

	/**
	 * Rebuilds the tree using a recursive loop on the parent_id
	 * Useful after importing categories (from other shopping carts)
	 * Or for when tree becomes corrupted
	 *
	 * @return unknown_type
	 */
	function rebuild()
	{
		JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->updateParents();
		JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->rebuildTreeOrdering();

		$redirect = "index.php?option=com_citruscart&view=".$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves an item and redirects based on task
	 * @return void
	 */
	function save()
	{
		$app =JFactory::getApplication();

		JPluginHelper::importPlugin('citruscart');

		if (!$row = parent::save())
		{
		    return $row;
		}

		$model 	= $this->getModel( $this->get('suffix') );

		$error = false;

	    $row->category_description =$app->input->getString('category_description');

		$fieldname = 'category_full_image_new';

		//$userfile = $app->input->files->get( $fieldname, '', 'files', 'array' );

		$userfile = $app->input->files->get( $fieldname, array(),'Array' );

		if (!empty($userfile['size']))
		{
			if ($upload = $this->addfile( $fieldname ))
			{
				$row->category_full_image = $upload->getPhysicalName();
			}
			else
			{
				$error = true;
			}
		}

		if ( $row->save() )
		{
			$model->setId( $row->id );
			$this->messagetype 	= 'message';
			$this->message  	= JText::_('COM_CITRUSCART_SAVED');
			if ($error)
			{
				$this->messagetype 	= 'notice';
				$this->message .= " :: ".$this->getError();
			}


			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

	}

	/**
	 * Adds a thumbnail image to item
	 * @return unknown_type
	 */
	function addfile( $fieldname = 'category_full_image_new' )
	{
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$upload = new CitruscartImage();
		// handle upload creates upload object properties
		$upload->handleUpload( $fieldname );
		// then save image to appropriate folder
		$upload->setDirectory( Citruscart::getPath('categories_images'));

		// do upload!
		$upload->upload();

		// Thumb
		Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
		$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
		if (!$imgHelper->resizeImage( $upload, 'category'))
		{
		    JFactory::getApplication()->enqueueMessage( $imgHelper->getError(), 'notice' );
		}

		return $upload;
	}

	/**
	 * Loads view for assigning products to categories
	 *
	 * @return unknown_type
	 */
	function selectproducts()
	{
		$this->set('suffix', 'products');

		$app = JFactory::getApplication();
		$model =JModelLegacy::getInstance($this->get('suffix') ,'CitruscartModel');
		//$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		//$state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$state['limit'] =  $app->getUserStateFromRequest($ns.'limit', 'limit', 20, 'int');
		$state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
		$state['order']     = $app->getUserStateFromRequest($ns.'.selectproducts.filter_order', 'filter_order', 'tbl.product_name', 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.selectproducts.filter_direction', 'filter_direction', 'ASC', 'word');
		$state['filter']    = $app->getUserStateFromRequest($ns.'.selectproducts.filter', 'filter', '', 'string');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		//$id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
		$id = $app->input->getInt('id',0);
		$row = $model->getTable( 'categories' );
		$row->load( $id );

		$view   = $this->getView( 'categories', 'html' );
		$view->set( '_controller', 'categories' );
		$view->set( '_view', 'categories' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=categories&task=selectproducts&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState());
		$view->assign( 'row', $row );
		$view->setLayout( 'selectproducts' );
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

		$id =$app->input->getInt( 'id',0);
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
			$table = JTable::getInstance('ProductCategories', 'CitruscartTable');
			$keynames["category_id"] = $id;
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
					$table->category_id = $id;
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
						$table->category_id = $id;
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

		$redirect = $app->input->getString( 'return' ) ?
		base64_decode( $app->input->getString( 'return' ) ) : "index.php?option=com_citruscart&controller=categories&task=selectproducts&tmpl=component&id=".$id;
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Batch resize of thumbs
	 * @author Skullbock
	 */
	function recreateThumbs(){

		$app = JFactory::getApplication();
		$per_step = 100;
		$from_id = $app->input->getInt('from_id', 0);
		$to =  $from_id + $per_step;

		Citruscart::load( 'CitruscartHelperCategory', 'helpers.category' );
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$width = Citruscart::getInstance()->get('category_img_width', '0');
		$height = Citruscart::getInstance()->get('category_img_height', '0');

		$model = $this->getModel('Categories', 'CitruscartModel');
		$model->setState('limistart', $from_id);
		$model->setState('limit', $to);

		$row = $model->getTable();

		$count = $model->getTotal();

		$categories = $model->getList();

		$i = 0;
		$last_id = $from_id;
		foreach($categories as $p){
			$i++;
			$image = $p->category_full_image;
			$path = Citruscart::getPath('categories_images');

			if($image != ''){

				$img = new CitruscartImage($path.'/'.$image);
				$img->setDirectory( Citruscart::getPath('categories_images'));

				// Thumb
				Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
				$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
				$imgHelper->resizeImage( $img, 'category');
			}

			$last_id = $p->category_id;
		}

		if($i < $count)
		$redirect = "index.php?option=com_citruscart&controller=categories&task=recreateThumbs&from_id=".($last_id+1);
		else
		$redirect = "index.php?option=com_citruscart&view=config";

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, JText::_('COM_CITRUSCART_DONE'), 'notice' );
		return;
	}
}

