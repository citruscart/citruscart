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


class CitruscartControllerManufacturers extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'manufacturers');
		$this->registerTask( 'manufacturer_enabled.enable', 'boolean' );
		$this->registerTask( 'manufacturer_enabled.disable', 'boolean' );
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

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Saves an item and redirects based on task
	 * @return void
	 */
	function save()
	{

		$app = JFactory::getApplication();


	   	if (!$row = parent::save())
	    {

	        return $row;
	    }

	    $model 	= $this->getModel( $this->get('suffix') );
	    $error = false;

		//$row->manufacturer_description = JRequest::getVar( 'manufacturer_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
	     $row->manufacturer_description = $app->input->getString('manufacturer_name');
	    $row->manufacturer_description =$app->input->getString('manufacturer_description');
		$fieldname = 'manufacturer_image_new';


		$userfile = $app->input->files->get ($fieldname, '', 'files', 'array' );
		//$userfile = JRequest::getVar( $fieldname, '', 'files', 'array' );

		if (!empty($userfile['size']))
		{
			if ($upload = $this->addfile( $fieldname ))
			{
				$row->manufacturer_image = $upload->getPhysicalName();
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


	        JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
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
	function addfile( $fieldname = 'manufacturer_image_new' )
	{
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$upload = new CitruscartImage();
		// handle upload creates upload object properties
		$upload->handleUpload( $fieldname );
		// then save image to appropriate folder
		$upload->setDirectory( Citruscart::getPath('manufacturers_images') );

		// Do the real upload!
		$upload->upload();

		// Thumb
		Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
		$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
		$imgHelper->resizeImage( $upload, 'manufacturer');

		return $upload;
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

		Citruscart::load( 'CitruscartHelperManufacturer', 'helpers.manufacturer' );
		Citruscart::load( 'CitruscartImage', 'library.image' );
		$width = Citruscart::getInstance()->get('manufacturer_img_width', '0');
		$height = Citruscart::getInstance()->get('manufacturer_img_height', '0');

		$model = $this->getModel('Manufacturers', 'CitruscartModel');
		$model->setState('limistart', $from_id);
		$model->setState('limit', $to);

		$row = $model->getTable();

		$count = $model->getTotal();

		$manufacturers = $model->getList();

		$i = 0;
		$last_id = $from_id;
		foreach($manufacturers as $p){
			$i++;
			$image = $p->manufacturer_full_image;

			if($image != ''){

				$img = new CitruscartImage($image, 'manufacturer');
				$img->setDirectory( Citruscart::getPath('manufacturers_images'));

				// Thumb
				Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
				$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
				$imgHelper->resizeImage( $img, 'manufacturer');
			}

			$last_id = $p->manufacturer_id;
		}

		if($i < $count)
		$redirect = "index.php?option=com_citruscart&controller=manufacturers&task=recreateThumbs&from_id=".($last_id+1);
		else
		$redirect = "index.php?option=com_citruscart&view=config";

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, JText::_('COM_CITRUSCART_DONE'), 'notice' );
		return;
	}

}

