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

require_once JPATH_SITE .'/libraries/dioscouri/library/parameter.php';

class CitruscartControllerProducts extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'products');
		$this->registerTask( 'product_enabled.enable', 'boolean' );
		$this->registerTask( 'product_enabled.disable', 'boolean' );
		$this->registerTask( 'selected_enable', 'selected_switch' );
		$this->registerTask( 'selected_disable', 'selected_switch' );
		$this->registerTask( 'saveprev', 'save' );
		$this->registerTask( 'savenext', 'save' );
		$this->registerTask( 'prev', 'jump' );
		$this->registerTask( 'next', 'jump' );
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
		$state['filter_quantity_from'] 	= $app->getUserStateFromRequest($ns.'quantity_from', 'filter_quantity_from', '', '');
		$state['filter_quantity_to'] 		= $app->getUserStateFromRequest($ns.'quantity_to', 'filter_quantity_to', '', '');
		$state['filter_category'] 		= $app->getUserStateFromRequest($ns.'category', 'filter_category', '', '');
		$state['filter_sku'] 		= $app->getUserStateFromRequest($ns.'sku', 'filter_sku', '', '');
		$state['filter_price_from'] 	= $app->getUserStateFromRequest($ns.'price_from', 'filter_price_from', '', '');
		$state['filter_price_to'] 		= $app->getUserStateFromRequest($ns.'price_to', 'filter_price_to', '', '');
		$state['filter_taxclass']   = $app->getUserStateFromRequest($ns.'taxclass', 'filter_taxclass', '', '');
		$state['filter_ships']   = $app->getUserStateFromRequest($ns.'ships', 'filter_ships', '', '');
		$state['filter_group']   = Citruscart::getInstance()->get('default_user_group', '1');
		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.ordering', 'cmd');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function edit($cachable=false, $urlparams = false)
	{
		$view   = $this->getView( $this->get('suffix'), 'html' );
		$model  = $this->getModel( $this->get('suffix') );
		$view->set( 'hidemenu', false);
		$view->assign( 'product_relations', $this->getRelationshipsHtml($view, $model->getId()) );
		$view->setLayout( 'form' );
		$view->setTask(true);
		parent::edit();
	}

	/**
	 * Checks in the current item and displays the previous/next one in the list
	 * @return unknown_type
	 */
	function jump()
	{
		/* Get the applicaiton */
		$app = JFactory::getApplication();
		$model  = $this->getModel( $this->get('suffix') );
		$row = $model->getTable();
		$row->load( $model->getId() );
		if (isset($row->checked_out) && !JTable::isCheckedOut( JFactory::getUser()->id, $row->checked_out) )
		{
			$row->checkin();
		}
		$task = $app->input->get( "task" );
		$redirect = "index.php?option=com_citruscart&view=products";
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$surrounding = CitruscartHelperProduct::getSurrounding( $model->getId() );
		switch ($task)
		{
			case "prev":
				if (!empty($surrounding['prev']))
				{
					$redirect .= "&task=view&id=".$surrounding['prev'];
				}
				break;
			case "next":
				if (!empty($surrounding['next']))
				{
					$redirect .= "&task=view&id=".$surrounding['next'];
				}
				break;
		}
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves an item and redirects based on task
	 * @return void
	 */
	function save()
	{
		$app = JFactory::getApplication();
	    $task = $app->input->getString( 'task' );
		$post = $app->input->getArray($_POST);
		$model 	= $this->getModel( $this->get('suffix') );
		$isSaveAs = false;
        $row = $model->getTable();
        $row->load( $model->getId() );
        // to set the Product Id
        $row->product_id = (isset($post['id'])) ? $post['id'] : "";
        $row->bind($post);
		$row->product_description = $app->input->get( 'product_description', '', 'RAW');
		$row->product_description_short =$app->input->get( 'product_description_short', '', 'RAW');

		// set the id as 0 for new entry
		if ( $task == "save_as" )
		{
			unset($row);
			// load WITHOUT EAV! otherwise the save will fail
			$row = $model->getTable();

			$row->load( $model->getId(), true, false );
			//$row->bind( JRequest::get('POST') );
			$row->bind( $app->input->getArray($_POST) );

			$row->product_description = $app->input->get( 'product_description', '', 'RAW');
			$row->product_description_short =$app->input->get( 'product_description_short', '', 'RAW');

		    $isSaveAs = true;
			$oldProductImagePath = $row->getImagePath();
			$pk = $row->getKeyName();
			$oldPk = $row->$pk;

            // these get reset
			$row->$pk = 0;
			$row->product_images_path = '';
			$row->product_rating = '';
			$row->product_comments = '';

		}
		$row->_isNew = empty($row->product_id);

		$fieldname = 'product_full_image_new';
		//$userfiles = JRequest::getVar( $fieldname, '', 'files', 'array' );
		$userfiles = $app->input->post->get( $fieldname, '', 'files', 'array' );

		// save the integrations
		$row = $this->prepareParameters( $row );

		//check if normal price exists
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );

        if ($isSaveAs)
        {
        	// and the prices
        	$prices = CitruscartHelperProduct::getPrices($oldPk);
        }
            else
        {
        	$prices = CitruscartHelperProduct::getPrices( $row->product_id );
        }



		if ($row->save() )
		{
			$row->product_id = $row->id;

			$model->setId( $row->id );
			$this->messagetype 	= 'message';
			$this->message  	= JText::_('COM_CITRUSCART_SAVED');

			// check it's new entry or empty price but not save as
			if (( $row->_isNew || empty($prices) ) && ! $isSaveAs)
			{
				// set price if new or no prices set
				$price = JTable::getInstance( 'Productprices', 'CitruscartTable' );
				$price->product_id = $row->id;
				$price->product_price = $app->input->get( 'product_price' );
				$price->group_id = Citruscart::getInstance()->get('default_user_group', '1');
				if (!$price->save())
				{
					$this->messagetype 	= 'notice';
					$this->message .= " :: ".$price->getError();
				}
			}



			if ($row->_isNew && !$isSaveAs)
			{
				// set category
				$category = JTable::getInstance( 'Productcategories', 'CitruscartTable' );

				$category->product_id = $row->id;


				$category->category_id = $app->input->getInt( 'category_id' );



				if (!$category->save())
				{


					$this->messagetype 	= 'notice';
					$this->message .= " :: ".$category->getError();
				}

				// save default quantity
				$quantity = JTable::getInstance( 'Productquantities', 'CitruscartTable' );
				$quantity->product_id = $row->id;
				$quantity->quantity = $app->input->getInt('product_quantity' );
				if (!$quantity->save())
				{
					$this->messagetype  = 'notice';
					$this->message .= " :: ".$quantity->getError();
				}

			}

			if ($isSaveAs)
			{
				// set price when cloning
				$priceTable = JTable::getInstance( 'Productprices', 'CitruscartTable' );
				foreach($prices as $price)
				{
    				$priceTable->product_id = $row->id;
    				$priceTable->product_price = $price->product_price;
    				$priceTable->product_price_startdate = $price->product_price_startdate;
    				$priceTable->product_price_enddate = $price->product_price_enddate;
    				$priceTable->created_date = $price->created_date;
    				$priceTable->modified_date = $price->modified_date;
    				$priceTable->group_id = $price->group_id;
    				$priceTable->price_quantity_start = $price->price_quantity_start;
    				$priceTable->price_quantity_end = $price->price_quantity_end;
    				if (!$priceTable->save())
    				{
    					$this->messagetype 	= 'notice';
    					$this->message .= " :: ".$priceTable->getError();
    				}
				}

			    // set category
			    $categoryTable = JTable::getInstance( 'Productcategories', 'CitruscartTable' );
			    $categories = CitruscartHelperProduct::getCategories($oldPk);
			    foreach ($categories as $category)
			    {
    			   	$categoryTable->product_id = $row->id;
    				$categoryTable->category_id = $category;
    			    if (!$categoryTable->save())
    				{
    					$this->messagetype 	= 'notice';
    					$this->message .= " :: ".$categoryTable->getError();
    				}
     		    }

			   	// TODO Save Attributes

				  // An array to map attribute id  old attribute id  are as key and new attribute id are as value
				  $attrbuteMappingArray = array();
				  $attrbuteParentMappingArray = array();

			    $attributes  = CitruscartHelperProduct::getAttributes($oldPk);

			    foreach ($attributes as $attribute)
			    {
			    	$attributeTable = JTable::getInstance( 'ProductAttributes', 'CitruscartTable' );
    			  $attributeTable->productattribute_name = $attribute->productattribute_name;
    			  $attributeTable->product_id = $row->id;
    			  $attributeTable->ordering = $attribute->ordering;

            if ($attributeTable->save())
    			  {
              $attrbuteMappingArray[$attribute->productattribute_id] = $attributeTable->productattribute_id;
    			    $attrbuteParentMappingArray[$attributeTable->productattribute_id] = $attribute->parent_productattributeoption_id;
            }
        		else
    			  {
    			    $this->messagetype 	= 'notice';
    					$this->message .= " :: ".$attributeTable->getError();
    			  }
			    }


			    // set Attribute options

				$attrbuteOptionsMappingArray = array();
				foreach ($attrbuteMappingArray as $oldAttrbuteId => $newAttributeId)
				{
            // set Attribute options
    				$options  = CitruscartHelperProduct::getAttributeOptionsObjects($oldAttrbuteId);
    				foreach ($options as $option)
    				{
    					$attributeOptionsTable = JTable::getInstance( 'ProductAttributeOptions', 'CitruscartTable' );
    					$attributeOptionsTable->productattribute_id   = $newAttributeId ;
    			    	$attributeOptionsTable->productattributeoption_name   = $option->productattributeoption_name ;
    			    	$attributeOptionsTable->productattributeoption_price   = $option->productattributeoption_price ;
    			    	$attributeOptionsTable->productattributeoption_prefix   = $option->productattributeoption_prefix ;
    			    	$attributeOptionsTable->productattributeoption_code   = $option->productattributeoption_code ;
    			    	$attributeOptionsTable->ordering   = $option->ordering ;

        			    if ($attributeOptionsTable->save())
    			    	{
    			    		$attrbuteOptionsMappingArray[$option->productattributeoption_id] = $attributeOptionsTable->productattributeoption_id;
    			    	}
        			    else
        			    {
        			       	$this->messagetype 	= 'notice';
        					$this->message .= " :: ".$attributeOptionsTable->getError();
        				}
    			    }

            // save parent relationship
            if( $attrbuteParentMappingArray [ $newAttributeId ] )
            {
  			    	$attributeTable = JTable::getInstance( 'ProductAttributes', 'CitruscartTable' );
              $attributeTable->load( $newAttributeId );
              $attributeTable->parent_productattributeoption_id = $attrbuteOptionsMappingArray[ $attrbuteParentMappingArray [ $newAttributeId ] ];
              if (!$attributeTable->save())
      			  {
      			    $this->messagetype 	= 'notice';
      					$this->message .= " :: ".$attributeTable->getError();
      			  }
            }
				}

				// set quantity
			    $quantityTable = JTable::getInstance( 'Productquantities', 'CitruscartTable' );
			    $quantities  = CitruscartHelperProduct::getProductQuantitiesObjects($oldPk );
			    foreach ($quantities as $quantity)
			    {
    			   	$quantityTable->product_attributes = $quantity->product_attributes ;
    			   	$quantityTable->product_id= $row->id;
    			   	$quantityTable->vendor_id = $quantity->vendor_id ;
    			   	$quantityTable->quantity = $quantity->quantity ;

    			   	$optionsCSV=$quantity->product_attributes;

    			   	$options= explode(",",$optionsCSV);
    			   	$newOptions=array();
    			   	foreach ($options as $option)
    			   	{
    			   		$newOptions[]=$attrbuteOptionsMappingArray[$option];
    			   	}
    			    $optionsCSV =implode(",",$newOptions);
    			    $quantityTable->product_attributes=$optionsCSV;

    			    if (!$quantityTable->save())
    				{
    					$this->messagetype 	= 'notice';
    					$this->message .= " :: ".$quantityTable->getError();
    				}

				}

				// copy all gallery files
				jimport( 'joomla.filesystem.folder' );
				jimport( 'joomla.filesystem.file' );
				$galleryFiles = JFolder::files( $oldProductImagePath ); // get all gallery images
				if( count( $galleryFiles ) )// if there are any
				{
					JFolder::create( $row->getImagePath() ); // create folder for images
					JFolder::create( $row->getImagePath().'thumbs' ); // create folder for thumbnails images
					for( $i = 0, $c = count( $galleryFiles ); $i < $c; $i++ )
					{
						// copy only images with both original file and a corresponding thumbnail
						if( JFile::exists( $oldProductImagePath.'thumbs/'.$galleryFiles[$i] ) && JFile::exists( $oldProductImagePath.$galleryFiles[$i] ) )
						{
							JFile::copy( $oldProductImagePath.$galleryFiles[$i], $row->getImagePath().DS.$galleryFiles[$i] );
							JFile::copy( $oldProductImagePath.'thumbs/'.$galleryFiles[$i], $row->getImagePath().'/thumbs/'.$galleryFiles[$i] );
						}
					}
				}

				// duplicate product files (only in db)
				$modelFiles = $this->getModel( 'productfiles' );
				$modelFiles->setState( 'filter_product', $oldPk );
				$listFiles = $modelFiles->getList();
				if( count( $listFiles ) ) // if there are files attached to the first product, we should duplicate the record in db
				{
					$row_file = JTable::getInstance( 'Productfiles', 'CitruscartTable' );
					for( $i = 0, $c = count( $listFiles ); $i < $c; $i++ )
					{
						$row_file->bind( $listFiles[$i] ); // bind old data
						$row_file->productfile_id = 0; // will be set
						$row_file->product_id = $row->product_id; // use clone's ID
						$row_file->save(); // save the data
					}
				}

			// create duplicate connections for EAV custom fields
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
	    	$model = JModelLegacy::getInstance('EavAttributes', 'CitruscartModel');
	    	$model->setState('filter_entitytype', 'products' );
	    	$model->setState('filter_entityid', $oldPk);
	    	$listEAV = $model->getList();
	    	$teav = $model->getTable();

				if( is_array( $listEAV ) ) // are there custom fields to clone?
				{
					for( $i = 0, $c = count( $listEAV ); $i < $c; $i++ )
					{
						$tblEAV = JTable::getInstance( 'EavAttributeEntities', 'CitruscartTable' );
						$tblEAV->eaventity_id = $row->product_id;
						$tblEAV->eaventity_type = 'products';
						$tblEAV->eavattribute_id = $listEAV[$i]->eavattribute_id;
						$tblEAV->save();

						// Clone the values too!
						$teav->load($listEAV[$i]->eavattribute_id);
						$value = CitruscartHelperEav::getAttributeValue($teav, 'products', $row->product_id);

						$newValue = JTable::getInstance('EavValues', 'CitruscartTable');
						$newValue->setType($teav->eavattribute_type);
		    			$newValue->eavattribute_id = $listEAV[$i]->eavattribute_id;
		    			$newValue->eaventity_id = $row->product_id;

			    		// Store the value
			    		$newValue->eavvalue_value = $value;
			    		$newValue->eaventity_type = 'products';
			    		$newValue->store();
					}
				}
			 }


			// Multiple images processing
			$i = 0;
			$error = false;
			while (!empty($userfiles['size'][$i]))
			{
				$dir = $row->getImagePath(true);

				if ($upload = $this->addimage( $fieldname, $i, $dir ))
				{
					// The first One is the default (if there is no default yet)
					if ($i == 0 && (empty($row->product_full_image) || $row->product_full_image == ''))
					{
						$row->product_full_image = $upload->getPhysicalName();
						// need to re-save in this instance
						// should we be storing or saving?
						$row->save();
					}
				}
				else
				{
					$error = true;
				}
				$i++;
			}

			if ($error)
			{
				$this->messagetype  = 'notice';
				$this->message .= " :: ".$this->getError();
			}

			$helper = new CitruscartHelperProduct();
			$helper->onAfterSaveProducts( $row );

			$model->clearCache();


			JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart";
		switch ($task)
		{
            case "save_as":
                $redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$row->product_id;
                $this->message .= " - " . JText::_('COM_CITRUSCART_YOU_ARE_NOW_EDITING_NEW_PRODUCT');
                break;
			case "saveprev":
				$redirect .= '&view='.$this->get('suffix');
				// get prev in list
				Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
				$surrounding = CitruscartHelperProduct::getSurrounding( $model->getId() );
				if (!empty($surrounding['prev']))
				{
					$redirect .= '&task=edit&id='.$surrounding['prev'];
				}
				break;
			case "savenext":
				$redirect .= '&view='.$this->get('suffix');
				// get next in list
				Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
				$surrounding = CitruscartHelperProduct::getSurrounding( $model->getId() );
				if (!empty($surrounding['next']))
				{
					$redirect .= '&task=edit&id='.$surrounding['next'];
				}
				break;
			case "savenew":
				$redirect .= '&view='.$this->get('suffix').'&task=add';
				break;
			case "apply":
				$redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$model->getId();
				break;
			case "save":
			default:
				$redirect .= "&view=".$this->get('suffix');
				break;
		}

		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 *
	 * A separate space for working through all the different integrations
	 */
	function prepareParameters( &$row )
	{
		$app = JFactory::getApplication();
  	// this row's product_params has already been set from the textarea's POST
		// so we need to add to it
		$params = new DSCParameter( trim($row->product_params) );
		$params->set( 'amigos_commission_override', $app->input->get('amigos_commission_override') );
		$params->set( 'billets_ticket_limit_increase',$app->input->get('billets_ticket_limit_increase') );
		$params->set( 'billets_ticket_limit_exclusion',$app->input->get('billets_ticket_limit_exclusion') );
		$params->set( 'billets_hour_limit_increase', $app->input->get('billets_hour_limit_increase') );
		$params->set( 'billets_hour_limit_exclusion', $app->input->get('billets_hour_limit_exclusion') );
		$params->set( 'juga_group_csv_add', $app->input->get('juga_group_csv_add') );
		$params->set( 'juga_group_csv_remove', $app->input->get('juga_group_csv_remove') );
        $params->set( 'juga_group_csv_add_expiration', $app->input->get('juga_group_csv_add_expiration') );
        $params->set( 'juga_group_csv_remove_expiration', $app->input->get('juga_group_csv_remove_expiration') );
		$params->set( 'core_user_change_gid', $app->input->get('core_user_change_gid') );
		$params->set( 'core_user_new_gid', $app->input->get('core_user_new_gid') );
        $params->set( 'ambrasubs_type_id', $app->input->get('ambrasubs_type_id') );
        $params->set( 'hide_quantity_input', $app->input->get('param_hide_quantity_input') );
        $params->set( 'default_quantity', $app->input->get('param_default_quantity') );
        $params->set( 'hide_quantity_cart', $app->input->get('param_hide_quantity_cart') );
        $params->set( 'show_product_compare', $app->input->getInt('param_show_product_compare', 1) );

		$row->product_params = trim( $params->__toString() );
		return $row;
	}

	/**
	 * Adds a thumbnail image to item
	 * @return unknown_type
	 */
	function addimage( $fieldname = 'product_full_image_new', $num = 0, $path = 'products_images' )
	{
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$upload = new CitruscartImage();
		// handle upload creates upload object properties
		$upload->handleMultipleUpload( $fieldname, $num );
		// then save image to appropriate folder
		if ($path == 'products_images') { $path = Citruscart::getPath( 'products_images' ); }
		$upload->setDirectory( $path );

		// Do the real upload!
		$upload->upload();

		Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
		$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');

		if (!$imgHelper->resizeImage( $upload, 'product'))
		{
			JFactory::getApplication()->enqueueMessage( $imgHelper->getError(), 'notice' );
		}

		return $upload;
	}

	/**
	 * Loads view for assigning product to categories
	 *
	 * @return unknown_type
	 */
	function selectcategories()
	{
		$app = JFactory::getApplication();

		//$app->input->set('suffix', 'categories');

		$this->set('suffix', 'categories');

		$state = parent::_setModelState();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_parentid'] 	= $app->getUserStateFromRequest($ns.'parentid', 'filter_parentid', '', '');

		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.lft', 'cmd');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$id = $app->input->getInt( 'id',0);
		$row = $model->getTable( 'products' );
		$row->load($id);
		$view	= $this->getView( 'products', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=selectcategories&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'selectcategories' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Loads view to show the gallery
	 *
	 * @return unknown_type
	 */
	function viewGallery()
	{
		$app = JFactory::getApplication();
		$id = $app->input->getInt( 'id',0 );
		$row = JTable::getInstance('Products', 'CitruscartTable');
		$row->load( $id );
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$helper = CitruscartHelperBase::getInstance('Product', 'CitruscartHelper');
		$gallery_path = $helper->getGalleryPath($row->product_id);
		$gallery_url = $helper->getGalleryUrl($row->product_id);
		$images = $helper->getGalleryImages($gallery_path);

		$view	= $this->getView( 'products', 'html' );
		$model = $this->getModel($this->get('suffix'));

		$view->setModel($model, true);
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=viewGallery&tmpl=component&id=".$id);
		$view->assign( 'row', $row );
		$view->assign( 'images', $images );
		$view->assign( 'url', $gallery_url );
		$view->setLayout( 'gallery' );
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
		$this->messagetype	= '';
		$this->message 		= '';

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();

		$id = $app->input->getInt( 'id', 0);
		$cids = $app->input->get('cid', array (0), 'Array');
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
				$this->messagetype 	= 'notice';
				$this->message 		= JText::_('COM_CITRUSCART_INVALID_TASK');
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
				break;
		}

		$keynames = array();
		foreach ($cids as $cid)
		{
			$table = JTable::getInstance('ProductCategories', 'CitruscartTable');
			$keynames["product_id"] = $id;
			$keynames["category_id"] = $cid;
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
					$table->product_id = $id;

					$table->category_id = $cid;
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
						$table->product_id = $id;
						$table->category_id = $cid;
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
		base64_decode( $app->input->getString( 'return' ) ) : "index.php?option=com_citruscart&view=products&task=selectcategories&tmpl=component&id=".$id;
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/*
	 * Creates a popup where quantities can be set
	 */
	function setquantities()
	{
		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('id');
		$this->set('suffix', 'productquantities');
		$model = $this->getModel( $this->get('suffix') );
		$model->setState('filter_productid', $model->getId());
		$model->setState('filter_vendorid', '0');
		$items = $model->getAll();

		$row = JTable::getInstance('Products', 'CitruscartTable');
		$row->load($model->getId());
//		$productModel = JModelLegacy::getInstance('Products', 'CitruscartModel');

		//$row = $productModel->getItem();

		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		// $csvs = CitruscartHelperProduct::getProductAttributeCSVs( $row->product_id );
		// $items = CitruscartHelperProduct::reconcileProductAttributeCSVs( $row->product_id, '0', $items, $csvs );
		CitruscartHelperProduct::doProductQuantitiesReconciliation( $product_id );
		$state = parent::_setModelState();
		$ns = $this->getNamespace();

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$view   = $this->getView( 'products', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setquantities&id={$model->getId()}&tmpl=component" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $model->getList() );
		$view->setLayout( 'setquantities' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Saves the quantities for all product attributes in list
	 *
	 * @return unknown_type
	 */
	function savequantities()
	{
		$app =JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$model = $this->getModel('productquantities');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0), 'Array');


		$quantities = $app->input->get('quantity', array(0),  'Array');
		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->quantity = $quantities[$cid];

			if (!$row->save())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setquantities&id={$row->product_id}&tmpl=component";

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/*
	 * Creates a popup where prices can be edited & created
	 */
	function setprices()
	{

		$this->set('suffix', 'productprices');

		$state = parent::_setModelState();

		$app = JFactory::getApplication();

		$model = $this->getModel( $this->get('suffix') );

		$ns = $this->getNamespace();

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		$row = JTable::getInstance('Products', 'CitruscartTable');

		$row->load($model->getId());

		$model->setState('filter_id', $model->getId());
		$view	= $this->getView( 'productprices', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setprices&id={$model->getId()}&tmpl=component" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Creates a price and redirects
	 *
	 * @return unknown_type
	 */
	function createprice()
	{	$app = JFactory::getApplication();
		JPluginHelper::importPlugin('citruscart');
		$this->set('suffix', 'productprices');
		$model 	= $this->getModel( $this->get('suffix') );
		$row = $model->getTable();
		$row->product_id =$app->input->getInt( 'id',0 );
		$row->product_price = $app->input->get( 'createprice_price' );
		$row->product_price_startdate = $app->input->get( 'createprice_date_start' );
		$row->product_price_enddate = $app->input->get( 'createprice_date_end' );
		$row->price_quantity_start = $app->input->get( 'createprice_quantity_start' );
		$row->price_quantity_end = $app->input->get( 'createprice_quantity_end' );
		$row->group_id = $app->input->get( 'createprice_group_id' );
		if ( $row->save() )
		{
		    $model->clearCache();
	 		$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setprices&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves the properties for all prices in list
	 *
	 * @return unknown_type
	 */
	function saveprices()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';

		$model = $this->getModel('productprices');

		$row = $model->getTable();
		//$row = $model->getItem();
		$row = $model->getTable();
		//$row->load($model->getId());

		//print_r($_POST); exit;

		//print_r($row); exit;
		$cids = $app->input->get('cid', array(0), 'Array');
		$prices = $app->input->get('price', array(0), 'Array');
		$date_starts =$app->input->get('date_start', array(0), 'Array');
		$date_ends = $app->input->get('date_end', array(0), 'Array');
		$quantity_starts = $app->input->get('quantity_start', array(0), 'Array');
		$quantity_ends = $app->input->get('quantity_end', array(0), 'Array');
		$user_groups = $app->input->get('price_group_id', array(0), 'Array');

		/* foreach ($cids as $cid)
		{
			$trow = $model->getTable();

			$trow->load( $cid );
			$row->product_price = $prices[$cid];
			$row->product_price_startdate = $date_starts[$cid];
			$row->product_price_enddate = $date_ends[$cid];
			$row->price_quantity_start = $quantity_starts[$cid];
			$row->price_quantity_end = $quantity_ends[$cid];
			$row->group_id = $user_groups[$cid];

			if (!$trow->save($row))
			{
				$this->message .= $trow->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		} */

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->product_price = $prices[$cid];
			$row->product_price_startdate = $date_starts[$cid];
			$row->product_price_enddate = $date_ends[$cid];
			$row->price_quantity_start = $quantity_starts[$cid];
			$row->price_quantity_end = $quantity_ends[$cid];
			$row->group_id = $user_groups[$cid];

			if (!$row->save())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		$productModel = JModelLegacy::getInstance('products','CitruscartModel');

		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setprices&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		//$app->redirect($redirect,$this->message);
	}

	/*
	 * Creates a popup where issues can be edited & created
	 */
	function setissues()
	{
		$this->set('suffix', 'productissues');
		$ns = $this->getNamespace();
		$app = JFactory::getApplication();

		$app->setUserState( $ns.'.filter_order', $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.publishing_date', 'cmd') );
		$app->setUserState( $ns.'.filter_direction', $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word') );
		$state = parent::_setModelState();
		$model = $this->getModel( $this->get('suffix') );
		foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$row = JTable::getInstance('Products', 'CitruscartTable');
		$row->load( $model->getId() );
		$model->setState('filter_product_id', $model->getId());
		$view	= $this->getView( 'productissues', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setissues&id={$model->getId()}&tmpl=component" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Creates an issue and redirects
	 *
	 * @return unknown_type
	 */
	function createissue()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$this->set('suffix', 'productissues');
		$model 	= $this->getModel( $this->get('suffix') );

		$row = $model->getTable();
		$row->product_id = $app->input->getInt( 'id',0 );
		$row->issue_num = $app->input->get( 'issue_num' );
		$row->volume_num = $app->input->get( 'volume_num' );
		$row->publishing_date = $app->input->get( 'publishing_date'  );

		if ( $row->save() )
		{
		    $model->clearCache();

			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setissues&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves the properties for all issues in list
	 *
	 * @return unknown_type
	 */
	function saveissues()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';

		$model = $this->getModel('productissues');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0), 'Array');
		$issues_num = $app->input->get( 'issues_num', array(0), 'Array' );
		$volumes_num = $app->input->get( 'volumes_num', array(0), 'Array' );
		$publishing_dates = $app->input->get( 'publishing_dates', array(0), 'Array' );

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->issue_num = $issues_num[$cid];
			$row->volume_num = $volumes_num[$cid];
			$row->publishing_date = $publishing_dates[$cid];

			if (!$row->save())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setissues&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Loads view for assigning product attributes
	 *
	 * @return unknown_type
	 */
	function setattributes()
	{
		$this->set('suffix', 'productattributes');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_product'] = $model->getId();

		$state['order'] = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.ordering', 'cmd');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		// get the Product Model
		$productModel = JModelLegacy::getInstance('Products','CitruscartModel');

		$row = $productModel->getItem();

		/*
		  Error In this Code

		  $row = JTable::getInstance('Products', 'CitruscartTable');

		  $row->load($model->getId());

		*/
		$view   = $this->getView( 'productattributes', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setattributes&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Creates a price and redirects
	 *
	 * @return unknown_type
	 */
	function createattribute()
	{
		$app = JFactory::getApplication();

		JPluginHelper::importPlugin('citruscart');



		$this->set('suffix', 'productattributes');

		$model  = $this->getModel( $this->get('suffix') );

		$row = $model->getTable();

		$row->product_id = $app->input->getInt( 'id',0 );
		$row->productattribute_name = $app->input->getString( 'createproductattribute_name' );
        $row->ordering = '99';

		if ($row->save())
		{
		    $model->clearCache();

			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );

		}else{

			$this->messagetype  = 'notice';

			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setattributes&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );

		//$app->redirect($redirect,$this->message,$this->messagetype);
	}

	/**
	 * Saves the properties for all attributes in list
	 *
	 * @return unknown_type
	 */
	function saveattributes()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productattributes');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0), 'Array');
		$name = $app->input->get('name', array(0), 'Array');
		$parent = $app->input->get('attribute_parent', array(0), 'Array');
		$ordering = $app->input->get('ordering', array(0), 'Array');

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->productattribute_name = $name[$cid];
			$row->parent_productattributeoption_id = $parent[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setattributes&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Loads view for assigning product attribute options
	 *
	 * @return unknown_type
	 */
	function setattributeoptions()
	{
		$this->set('suffix', 'productattributeoptions');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_attribute']   = $model->getId();
		$state['order'] = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.ordering', 'cmd');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$row = JTable::getInstance('ProductAttributes', 'CitruscartTable');
		$row->load($model->getId());

		$view   = $this->getView( 'productattributeoptions', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setattributeoptions&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Loads view for assigning product attribute option values
	 *
	 * @return unknown_type
	 */
	function setattributeoptionvalues()
	{
		$this->set('suffix', 'productattributeoptionvalues');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_option']   = $model->getId();

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$row = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
		$row->load($model->getId());

		$view   = $this->getView( 'productattributeoptionvalues', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setattributeoptionvalues&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Creates an option and redirects
	 *
	 * @return unknown_type
	 */
	function createattributeoption()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$this->set('suffix', 'productattributeoptions');

		$model  = $this->getModel( $this->get('suffix') );

		$row = $model->getTable();
		$row->productattribute_id = $app->input->getInt( 'id',0 );
		$row->productattributeoption_name = $app->input->get( 'createproductattributeoption_name' );
		$row->productattributeoption_price = $app->input->get( 'createproductattributeoption_price' );
		$row->productattributeoption_code = $app->input->get( 'createproductattributeoption_code' );
		$row->productattributeoption_prefix = $app->input->get( 'createproductattributeoption_prefix' );
		$row->productattributeoption_weight = $app->input->get( 'createproductattributeoption_weight' );
		$row->productattributeoption_prefix_weight = $app->input->get( 'createproductattributeoption_prefix_weight' );
		$row->is_blank = $app->input->get( 'createproductattributeoption_blank' );
		$row->ordering = '99';

		if ( $row->save() )
		{
		    $model->clearCache();


			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setattributeoptions&id={$row->productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Creates an option value and redirects
	 *
	 * @return unknown_type
	 */
	function createattributeoptionvalue()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$this->set('suffix', 'productattributeoptionvalues');
		$model  = $this->getModel( $this->get('suffix') );

		$row = $model->getTable();
		$row->productattributeoption_id = $app->input->get( 'id' );
		$row->productattributeoptionvalue_field = $app->input->get( 'createproductattributeoptionvalue_field' );
		$row->productattributeoptionvalue_operator = $app->input->get( 'createproductattributeoptionvalue_operator' );
		$row->productattributeoptionvalue_value = $app->input->get( 'createproductattributeoptionvalue_value' );

		if ( $row->save() )
		{
		    $model->clearCache();
			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setattributeoptionvalues&id={$row->productattributeoption_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves the properties for all attribute options in list
	 *
	 * @return unknown_type
	 */
	function saveattributeoptions()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productattributeoptions');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0), 'Array');
		$name = $app->input->get('name', array(0), 'Array');
		$prefix = $app->input->get('prefix', array(0), 'Array');
		$price = $app->input->get('price', array(0), 'Array');
		$prefix_weight = $app->input->get('prefix_weight', array(0), 'Array');
		$weight = $app->input->get('weight', array(0), 'Array');
		$code = $app->input->get('code', array(0), 'Array');
		$parent = $app->input->get('attribute_parent', array(0), 'Array');
		$ordering = $app->input->get('ordering', array(0), 'Array');
		$blank = $app->input->get( 'blank', array( 0 ), 'Array' );

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->productattributeoption_name = $name[$cid];
			$row->productattributeoption_prefix = $prefix[$cid];
			$row->productattributeoption_price = $price[$cid];
			$row->productattributeoption_prefix_weight = $prefix_weight[$cid];
			$row->productattributeoption_weight = $weight[$cid];
			$row->productattributeoption_code = $code[$cid];
			$row->parent_productattributeoption_id = $parent[$cid];
			$row->ordering = $ordering[$cid];
			$row->is_blank = $blank[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('COM_CITRUSCART_PRODUCT_OPTION_SUCCESSFULLY_SAVED');
			$this->messagetype="Message";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setattributeoptions&id={$row->productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$app->redirect($redirect,$this->message,$this->messagetype);
		//$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves the properties for all attribute option values in list
	 *
	 * @return unknown_type
	 */
	function saveattributeoptionvalues()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productattributeoptionvalues');
		$row = $model->getTable();

		$id = $app->input->getInt('id', 0);
		$cids = $app->input->get('cid', array(0), 'request', 'array');
		$field = $app->input->get('field', array(0), 'request', 'array');
		$operator = $app->input->get('operator', array(0), 'request', 'array');
		$value = $app->input->get('value', array(0), 'request', 'array');

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->productattributeoptionvalue_field = $field[$cid];
			$row->productattributeoptionvalue_operator = $operator[$cid];
			$row->productattributeoptionvalue_value = $value[$cid];
			echo Citruscart::dump( $row );

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('COM_CITRUSCART_PRODUCT_OPTIONVALUES_SAVED_SUCCESSFULLY');
			$this->messagetype="Message";
		}
		$redirect = "index.php?option=com_citruscart&view=products&task=setattributeoptionvalues&id={$id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$app->redirect($redirect,$this->message,$this->messagetype);

		//$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Loads view for managing product files
	 *
	 * @return unknown_type
	 */
	function setfiles()
	{
		$this->set('suffix', 'productfiles');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_product'] = $model->getId();
		//$state['order'] = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.ordering', 'cmd');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$row = JTable::getInstance('Products', 'CitruscartTable');
		$row->load($model->getId());

		$view   = $this->getView( 'productfiles', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_citruscart&view=products&task=setfiles&tmpl=component&id=".$model->getId() );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Creates a file and redirects
	 *
	 * @return unknown_type
	 */
	function createfile()
	{

		JPluginHelper::importPlugin('citruscart');
        $app = JFactory::getApplication();
		$this->set('suffix', 'productfiles');
		$model  = $this->getModel( $this->get('suffix') );

		$row = $model->getTable();
		$row->product_id = $app->input->get( 'id' );
		$row->productfile_name = $app->input->get( 'createproductfile_name' );
		$row->productfile_enabled = $app->input->get( 'createproductfile_enabled' );
		$row->purchase_required = $app->input->get( 'createproductfile_purchaserequired' );
		$row->max_download = $app->input->getInt( 'createproductfile_max_download', -1 );

		$fieldname = 'createproductfile_file';
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$path = CitruscartHelperProduct::getFilePath( $row->product_id );
		$userfile = $app->input->get( $fieldname, '', 'files', 'array' );
		if (!empty($userfile['size']))
		{
			if ($upload = $this->addfile( $fieldname, $path ))
			{
				if (empty($row->productfile_name)) { $row->productfile_name = $upload->proper_name; }
				$row->productfile_extension = $upload->getExtension();
				$row->productfile_path = $upload->full_path;
			}
			else
			{
				$error = true;
			}
		}
		// TODO Enable remotely-stored files with file_url

		if ( $row->save() )
		{
		    $model->clearCache();
			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_UPLOAD_WAS_SUCCESSFULL');
					}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setfiles&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$app->redirect($redirect,$this->message,$this->messagetype);
		//$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Creates a file from disk and redirects
	 *
	 * @return unknown_type
	 */
	function createfilefromdisk()
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$this->set('suffix', 'productfiles');
		$model  = $this->getModel( $this->get('suffix') );

		$file = $app->input->get( 'createproductfileserver_file' );

		$row = $model->getTable();
		$row->product_id = $app->input->get( 'id' );
		$row->productfile_name = $app->input->get( 'createproductfileserver_name' );
		$row->productfile_enabled = $app->input->get( 'createproductfileserver_enabled' );
		$row->purchase_required = $app->input->get( 'createproductfileserver_purchaserequired' );
		$row->max_download = $app->input->getInt( 'createproductfileserver_max_download', -1 );

		if(empty($row->productfile_name))
		$row->productfile_name = $file;

		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$path = CitruscartHelperProduct::getFilePath( $row->product_id ) . DS . $file;
		$namebits = explode('.', $file);
		$extension = $namebits[count($namebits)-1];

		$row->productfile_extension = $extension;
		$row->productfile_path = $path;

		if ( $row->save() )
		{
		    $model->clearCache();
			$app->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_UPLOAD_WAS_SUCCESSFULL');
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setfiles&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	/**
	 * Uploads a file to associate to an item
	 *
	 * @return unknown_type
	 */
	function addfile( $fieldname = 'createproductfile_file', $path = 'products_files' )
	{
		Citruscart::load( 'CitruscartFile', 'library.file' );
		$upload = new CitruscartFile();
		// handle upload creates upload object properties
		$upload->handleUpload( $fieldname );
		// then save image to appropriate folder
		if ($path == 'products_files') { $path = Citruscart::getPath( 'products_files' ); }
		$upload->setDirectory( $path );
		$dest = $upload->getDirectory().DS.$upload->getPhysicalName();
		// delete the file if dest exists
		if ($fileexists = JFile::exists( $dest ))
		{
			JFile::delete($dest);
		}
		// save path and filename or just filename
		if (!JFile::upload($upload->file_path, $dest))
		{
			$this->setError( sprintf( JText::_('COM_CITRUSCART_MOVE_FAILED_FROM'), $upload->file_path, $dest) );
			return false;
		}

		$upload->full_path = $dest;
		return $upload;
	}

	/**
	 * Saves the properties for all files in list
	 *
	 * @return unknown_type
	 */
	function savefiles()
	{

		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productfiles');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0),'Array');
		$name = $app->input->get('name', array(0), 'Array');
		$ordering = $app->input->get('ordering', array(0), 'Array');
		$enabled = $app->input->get('enabled', array(0), 'Array');
		$purchaserequired = $app->input->get('purchaserequired', array(0), 'Array');
		$max_download = $app->input->get('max_download', array(0), 'Array');

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->productfile_name = $name[$cid];
			$row->ordering = $ordering[$cid];
			$row->productfile_enabled = $enabled[$cid];
			$row->purchase_required = $purchaserequired[$cid];
			$row->max_download = $max_download[$cid];
     		if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		$productModel = $this->getModel('products');
		$productModel->clearCache();

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=setfiles&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Delete a product Image.
	 * Expected to be called via Ajax
	 */
	function deleteImage()
	{

		$app = JFactory::getApplication();

	    $format = $app->input->get('format');

		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );

		$product_id = $app->input->getInt( 'product_id', 0);
		//$image = $app->input->get('image', '', 'request');
		$image = $app->input->getString('image');

		$image = html_entity_decode($image);

		// Find and delete the product image
		$helper = CitruscartHelperBase::getInstance('Product', 'CitruscartHelper');

		$path = $helper->getGalleryPath($product_id);

		$redirect = $app->input->getString( 'return' ) ?
		base64_decode( $app->input->getString( 'return' ) ) : "index.php?option=com_citruscart&view=products&task=viewGallery&id={$product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		// Check if the data is ok
		if (empty($product_id) || empty($image))
		{
			$msg = JText::_('COM_CITRUSCART_INPUT_DATA_NOT_VALID');

			$redirect = "index.php?option=com_citruscart&view=products";
			$redirect = JRoute::_( $redirect, false );

			if ($format == 'raw')
			{
			    return;
			}
			$this->setRedirect( $redirect, $msg, 'notice' );
			return;
		}


		// Delete the image if it exists
		if(JFile::exists($path.$image)){
			$success = JFile::delete($path.$image);

			// Try to delete the thumb, too
			if ($success)
			{
				if (JFile::exists($path.'thumbs/'.$image))
				{
					JFile::delete($path.'thumbs/'.$image);
					$msg = JText::_('COM_CITRUSCART_IMAGE_DELETED');
				}
				else
				{
					$msg = JText::_("COM_CITRUSCART_CANNOT_DELETE_IMAGE_THUMBNAIL".$path.'thumbs/'.$image);
				}

				// if it is the primary image, let's clear the product_image field in the db
				$model = $this->getModel('products');
				$row = $model->getTable();
				$row->load($product_id);

				if ($row->product_full_image == $image)
				{
					$row->product_full_image = '';
				}
				// TODO Save or store here?
				$row->store();
			}
			else
			{
				$msg = JText::_('COM_CITRUSCART_CANNOT_DELETE_IMAGE'.$path.$image);
			}
		}
		else
		{
			$msg = JText::_("COM_CITRUSCART_CANNOT_DELETE_IMAGE".$path.$image);
		}

		if ($format == 'raw')
		{
		    return;
		}

		$this->setRedirect( $redirect, $msg, 'notice' );
		return;
	}

	function setDefaultImage()
	{
		$app = JFactory::getApplication();
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );

		$product_id = $app->input->getInt( 'product_id', 0);

		$image =$app->input->get('image');
		//$image = JRequest::getVar('image', '', 'request');
		$image = html_entity_decode($image);

		// Find the product image
		$helper = CitruscartHelperBase::getInstance('Product');
		$path = $helper->getGalleryPath($product_id);
		$gallery_url = $helper->getGalleryUrl($product_id);

		// Check if the data is ok
		if (empty($product_id) || empty($image))
		{
			$msg = JText::_('COM_CITRUSCART_INPUT_DATA_NOT_VALID');
			$redirect = "index.php?option=com_citruscart&view=products&task=viewGallery&id={$product_id}&tmpl=component";
			$redirect = JRoute::_( $redirect, false );
			$this->setRedirect( $redirect, $msg, 'notice' );
			return;
		}

		// Check if the image exists
		if (JFile::exists($path.$image) || JFile::exists($path.'/'.$image))
		{
			// Update
			$model = $this->getModel('products');
			$row = $model->getTable();
			$row->load( array( 'product_id'=>$product_id ) );
			$row->product_full_image = $image;
			if (!$row->store())
			{
				JFactory::getApplication()->enqueueMessage( $row->getError(), 'notice' );
			}

			$model->clearCache();

			$this->message = JText::_('COM_CITRUSCART_UPDATE_SUCCESSFUL');
			$this->messagetype = 'message';
		}
		else
		{
			$this->message = JText::_("COM_CITRUSCART_IMAGE_DOES_NOT_EXIST".$path.$image);
			$this->messagetype = 'notice';
		}

		//$format = JRequest::get('format');
		$format = $app->input->get('format');

		if ($format == 'raw')
		{
		    $html = '<img src="'.$gallery_url.'thumbs/'.$image.'" class="img-polaroid" />';
		    $response = new stdClass();
		    $response->html = $html;
		    echo json_encode($response);
		    return;
		}

		$redirect = "index.php?option=com_citruscart&view=products&task=viewGallery&id={$product_id}&tmpl=component&update_parent=1";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return;

	}

	/**
	 * Batch resize of thumbs
	 * @author Skullbock
	 */
	function recreateThumbs(){

		$app = JFactory::getApplication();
		// this will only be if there is only 1 image per product
		$per_step = 100;
		//$from_id = JRequest::getInt('from_id', 0);
		$from_id = $app->input->getInt('from_id', 0);
		$to_id =  $from_id + $per_step;
		$done =  $app->input->getInt('done', 0);

		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$width = Citruscart::getInstance()->get('product_img_width', '0');
		$height = Citruscart::getInstance()->get('product_img_height', '0');

		$helper = CitruscartHelperBase::getInstance('Product', 'CitruscartHelper');

		$model = $this->getModel('Products', 'CitruscartModel');
		$count = $model->getTotal();
		$model->setState('filter_id_from', $from_id);
		$model->setState('filter_id_to', $to_id);

		$row = $model->getTable();



		$products = $model->getList();

		// Explanation: $i contains how many images we have processed till now
		// $k contains how many products we have checked.
		// Max $per_step images resized per call.
		// So we continue to cicle on this controller call until $done, which contains
		// how many products we have passed till now (in total), does not reach the
		// total number of products in the db.
		$i = 0;
		$k = 0;
		$last_id = $from_id;
		foreach ($products as $p)
		{
			$k++;
			$path = $helper->getGalleryPath($p->product_id);
			$images = $helper->getGalleryImages($path);

			foreach ($images as $image)
			{
				$i++;
				if ($image != '')
				{
					$img = new CitruscartImage($path.$image);
					$img->setDirectory( $path );

					// Thumb
					Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
					$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
					$imgHelper->resizeImage( $img );
				}
			}
			$last_id = $p->product_id;

			if ($i >= $per_step)
			break;
		}

		$done += $k;

		if ($done < $count)
		$redirect = "index.php?option=com_citruscart&view=products&task=recreateThumbs&from_id=".($last_id+1)."&done=".$done;
		else
		$redirect = "index.php?option=com_citruscart&view=config";

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, JText::_('COM_CITRUSCART_DONE'), 'notice' );
		return;
	}

	/**
	 * Gets an address formatted for display
	 *
	 * @param int $address_id
	 * @return string html
	 */
	function getRelationshipsHtml( $view, $product_id )
	{
		$app = JFactory::getApplication();
		$html = '';
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->setState('filter_product', $product_id);

		if ($items = $model->getList())
		{
			if( $view === null )
			{
			    $view   = $this->getView( 'products', 'html' );
			    $view->set( '_controller', 'products' );
			    $view->set( '_view', 'products' );
			    $view->set( '_doTask', true);
			    $view->set( 'hidemenu', true);
			    $view->setModel( $model, true );
			}
			$view->setLayout( 'form_relations' );
			$view->set('items', $items);
			$view->set('product_id', $product_id);

			ob_start();
			echo $view->loadTemplate( null );
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}

	public function refreshProductGallery()
	{
		$app = JFactory::getApplication();
	    $html = '';

	    //$product_id = JRequest::getInt('product_id');
	  $product_id = $app->input->getInt('product_id',0);
	    $model = $this->getModel( $this->get('suffix') );

	    //$model = $this->getModel();
	    $model->setId($product_id);

	    $item = $model->getItem();
	    if ($item)
	    {
	       $view   = $this->getView( $this->get('suffix'), 'html' );
	       //$view = new CitruscartViewProduct();
	       $view->set( '_doTask', true);
	       $view->setModel( $model, true );
	       $view->setLayout( 'form_gallery' );
	       $view->set('row', $item);
	       $html = $view->loadTemplate();

		 }

	    $response = new stdClass();

	    $response->html = $html;

	    echo json_encode($response);

	    $app->close();
	}

	/**
	 * Upload via ajax through Uploadify
	 * It's here because when the swf connects to the admin side, it would need to login.
	 */
	function uploadifyImage( )
	{
		$app = JFactory::getApplication();

	    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	    $product_id = $app->input->getInt( 'product_id', 0 );


	    if ($product_id )
	    {
	        Citruscart::load( 'CitruscartImage', 'library.image' );
	        $upload = new CitruscartImage( );
	        // handle upload creates upload object properties
	        $upload->handleUpload( 'Filedata' );

	        // then save image to appropriate folder
	        $product = JTable::getInstance( 'Products', 'CitruscartTable' );
	        $product->load( $product_id );
	        $path = $product->getImagePath( );



	        $upload->setDirectory( $path );

	        // Do the real upload!
	        $success = $upload->upload( );

	        Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );

	        $imgHelper = CitruscartHelperBase::getInstance( 'Image', 'CitruscartHelper' );

	        if ( !$imgHelper->resizeImage( $upload, 'product' ) )
	        {
	            $success = false;
	        }

	        if ( $success )
	        {
	            // save as default?
	            if ( empty( $product->product_full_image ) )
	            {
	                $product->product_full_image = $upload->getPhysicalName( );
	                $product->save( );
	            }
	            echo JText::_('COM_CITRUSCART_IMAGE_UPLOADED_CORRECTLY');

	        }
	        else
	        {
	            echo 'Error: ' . $upload->getError( );
	        }
	    }
	}
}

