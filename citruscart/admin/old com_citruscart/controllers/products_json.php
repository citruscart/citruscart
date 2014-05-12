<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load('CitruscartControllerProducts', 'controllers.products');
Citruscart::load('CitruscartControllerProtocolJson', 'library.interfaces.protocols');

class CitruscartControllerProductsJson extends CitruscartControllerProducts implements CitruscartControllerProtocolJson
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		// Set the format to raw, no matter what
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		CitruscartHelperBase::setFormat('raw');
	}

	/**
	 * Adds a product relationship
	 */
	function removeRelationship()
	{
		$app = JFactory::getApplication();
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();

		// get elements from post
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $app->input->getArray( 'elements', '', 'post', 'string' ) ) );
		//$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );

		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
		$submitted_values = $helper->elementsToArray( $elements );

		$product_id = $submitted_values['new_relationship_productid_from'];
		$productrelation_id = $app->input->getInt('productrelation_id');

		$table = JTable::getInstance('ProductRelations', 'CitruscartTable');
		$table->delete( $productrelation_id );

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
        $model->clearCache();

		$response['error'] = '0';
		$response['msg'] = $this->getRelationshipsHtml( null, $product_id );

		echo json_encode($response);

		// Close the application.
		JFactory::getApplication()->close();
	}

	/**
	 *
	 * Adds a product relationship
	 */
	function addRelationship()
	{

		$app = JFactory::getApplication();
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();

		// get elements from post
		//$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n',$app->input->get( 'elements', '', 'post', 'string' ) ) );


		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
		$submitted_values = $helper->elementsToArray( $elements );

		$product_id = $submitted_values['new_relationship_productid_from'];
		$product_to = $submitted_values['new_relationship_productid_to'];
		$relation_type = $submitted_values['new_relationship_type'];

		// verify product id exists
		$product = JTable::getInstance('Products', 'CitruscartTable');
		$product->load( $product_to, true, false );
		if (empty($product->product_id) || $product_id == $product_to)
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_INVALID_PRODUCT') );
			$response['msg'] .= $this->getRelationshipsHtml( null, $product_id );
			echo ( json_encode( $response ) );
			return;
		}

		// and that relationship doesn't already exist
		$producthelper = CitruscartHelperBase::getInstance( 'Product' );
		if ($producthelper->relationshipExists( $product_id, $product_to, $relation_type ))
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_RELATIONSHIP_ALREADY_EXISTS') );
			$response['msg'] .= $this->getRelationshipsHtml( null, $product_id );
			echo ( json_encode( $response ) );
			return;
		}

		switch ($relation_type)
		{
			case "child":
			case "required_by":
				// for these two, we must flip to/from
				switch ($relation_type)
				{
					case "child":
						$rtype = 'parent';
						break;
					case "required_by":
						$rtype = 'requires';
						break;
				}

				// check existence of required_by relationship
				if ($producthelper->relationshipExists( $product_to, $product_id, $rtype ))
				{
					$response['error'] = '1';
					$response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_RELATIONSHIP_ALREADY_EXISTS') );
					$response['msg'] .= $this->getRelationshipsHtml( null, $product_id );
					echo ( json_encode( $response ) );
					return;
				}

				// then add it, need to flip to/from
				$table = JTable::getInstance('ProductRelations', 'CitruscartTable');
				$table->product_id_from = $product_to;
				$table->product_id_to = $product_id;
				$table->relation_type = $rtype;
				$table->save();
				break;
			default:
				$table = JTable::getInstance('ProductRelations', 'CitruscartTable');
				$table->product_id_from = $product_id;
				$table->product_id_to = $product_to;
				$table->relation_type = $relation_type;
				$table->save();
				break;
		}

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->clearCache();

		$response['error'] = '0';
		$response['msg'] = $this->getRelationshipsHtml( null,  $product_id );

		echo json_encode($response);

		// Close the application.
		JFactory::getApplication()->close();
	}

	/**
	 * Change the default image
	 * @return unknown_type
	 */
	function updateDefaultImage()
	{
		$app = JFactory::getApplication();
		$response = array();
		$response['default_image'] = '';
		$response['default_image_name'] = '';

		$product_id = $app->input->getInt('product_id');
		Citruscart::load( 'CitruscartUrl', 'library.url' );
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );

		$row = JTable::getInstance('Products', 'CitruscartTable');
		$row->load($product_id);

		$response['default_image'] = CitruscartHelperProduct::getImage($row->product_id, 'id', $row->product_name, 'full', false, false, array( 'height'=>80 ) );
		$response['default_image_name'] = $row->product_full_image;

		echo json_encode($response);

		// Close the application.
		JFactory::getApplication()->close();
	}

	/**
	 * Get a json list of products
	 * @api
	 */
	function getList()
	{
		$response = array();

		$model 	= $this->getModel( $this->get('suffix') );
		parent::_setModelState();
		$products = $model->getList();

		echo json_encode($products);


		// Close the application.
		JFactory::getApplication()->close();
	}

}

