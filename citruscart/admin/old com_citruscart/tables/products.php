<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTableEav', 'tables._baseeav' );

class CitruscartTableProducts extends CitruscartTableEav
{
	/* function CitruscartTableProducts( &$db ) */
	function __construct( &$db )
	{
		$tbl_key = 'product_id';
		$tbl_suffix = 'products';
		$this->set( '_suffix', $tbl_suffix );
		$name = 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	function check( )
	{
		$nullDate = $this->_db->getNullDate( );
		if ( empty( $this->created_date ) || $this->created_date == $nullDate )
		{
			$date = JFactory::getDate( );
			$this->created_date = $date->toSql( );
		}
		jimport( 'joomla.filter.output' );
		if ( empty( $this->product_alias ) )
		{
			$this->product_alias = $this->product_name;
		}
		$this->product_alias = JFilterOutput::stringURLSafe( $this->product_alias );

		$date = JFactory::getDate( );
		$this->modified_date = $date->toSql( );

		return true;
	}

	/**
	 *
	 * @param unknown_type $updateNulls
	 * @return unknown_type
	 */
	function store( $updateNulls = false )
	{
		if ( $return = parent::store( $updateNulls ) )
		{
			if ( empty( $this->_isNew ) )
			{
				// consolidate image gallery paths if necessary
				$helper = CitruscartHelperBase::getInstance( 'Product' );
				if ( $helper->consolidateGalleryImages( $this ) === true )
				{
					JFactory::getApplication( )->enqueueMessage( JText::_('COM_CITRUSCART_IMAGES_CONSOLIDATED_MESSAGE') );
				}
			}
		}
		return $return;
	}

	function load( $oid = null, $reset = true, $load_eav = true )
	{
		if ( $return = parent::load( $oid, $reset, $load_eav ) )
		{
			// consolidate image gallery paths if necessary (SHA1 Images)
			$helper = CitruscartHelperBase::getInstance( 'Product' );
			if ( $helper->consolidateGalleryImages( $this ) === true )
			{
				JFactory::getApplication( )->enqueueMessage( JText::_('COM_CITRUSCART_IMAGES_CONSOLIDATED_MESSAGE') );
			}
		}
		return $return;
	}

	/**
	 * Get the path to the product current Image
	 * @return string $dir
	 */

	function getImagePath( $check = true )
	{
		// Check where we should upload the file
		// This is the default one
		$dir = Citruscart::getPath( 'products_images' );

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

		$helper = CitruscartHelperBase::getInstance( );

		// is the image path overridden?
		if ( !empty( $this->product_images_path ) && $helper->checkDirectory( $this->product_images_path, $check ) )
		{
			$dir = $this->product_images_path;
		}
		else
		{
			// try with the SKU
			if ( Citruscart::getInstance( )->get( 'sha1_images', '0' ) ) // Sha1 images for 32k product invortories
			{
				if ( !empty( $this->product_sku ) )
				{
					$subdirs = $this->getSha1Subfolders( $this->product_sku );
					$image_dir = $dir . DIRECTORY_SEPARATOR . $subdirs . $this->product_sku . DIRECTORY_SEPARATOR;
				}
			}
			else
			{
				$image_dir = $dir . DIRECTORY_SEPARATOR . $this->product_sku . DIRECTORY_SEPARATOR;
			}

			if ( !empty( $this->product_sku ) && $helper->checkDirectory( $image_dir, $check ) )
			{
				$dir = $image_dir;
			}
			else
			{
				if ( Citruscart::getInstance( )->get( 'sha1_images', '0' ) )
				{
					$subdirs = $this->getSha1Subfolders( $this->product_id );
					$image_dir = $dir . DIRECTORY_SEPARATOR . $subdirs . $this->product_id . DIRECTORY_SEPARATOR;
				}
				else
				{
					$image_dir = $dir . DIRECTORY_SEPARATOR . $this->product_id . DIRECTORY_SEPARATOR;
				}

				// try with the product id
				if ( $helper->checkDirectory( $image_dir, $check ) )
				{
					$dir = $image_dir;
				}
			}
		}

		return $dir;
	}

	// Thanks to http://ryan.ifupdown.com/2008/08/17/warning-mkdir-too-many-links/
	protected function getSha1Subfolders( $string, $separator = DIRECTORY_SEPARATOR )
	{
		$sha1 = strtoupper( sha1( $string ) );

		// 4 level subfolding using sha1
		$i = 0;
		$subdirs = '';
		while ( $i < 4 )
		{
			if ( strlen( $string ) >= $i )
			{
				$subdirs .= $sha1[$i] . $separator;
			}
			$i++;
		}

		return $subdirs;
	}

	/**
	 * Get the URL to the path to images
	 * @return unknown_type
	 */
	function getImageUrl( )
	{
		// Check where we should upload the file
		// This is the default one
		$dir = Citruscart::getPath( 'products_images' );

		$url = Citruscart::getUrl( 'products_images' );

		$helper = CitruscartHelperBase::getInstance( );

		// is the image path overridden?
		if ( !empty( $this->product_images_path ) && $helper->checkDirectory( $this->product_images_path, false ) )
		{
			$url = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR, JURI::root(), $this->product_images_path);
		}
		else
		{
			// try with the SKU
			if ( Citruscart::getInstance( )->get( 'sha1_images', '0' ) ) // Sha1 images for 32k product invortories
			{
				if ( !empty( $this->product_sku ) )
				{
					$subdirs = $this->getSha1Subfolders( $this->product_sku, '/' );
					$image_dir = $url . $subdirs . $this->product_sku . '/';
				}
			}
			else
			{
				$image_dir = $url . $this->product_sku . '/';
			}

			// try with the SKU
			if ( !empty( $this->product_sku ) )
			{
				$url = $image_dir;
			}
			else
			{
				if ( Citruscart::getInstance( )->get( 'sha1_images', '0' ) )
				{
					$subdirs = $this->getSha1Subfolders( $this->product_id, '/' );
					$image_dir = $url . $subdirs . $this->product_id . '/';
				}
				else
				{
					$image_dir = $url . $this->product_id . '/';
				}

				$url = $image_dir;

			}
		}

		return $url;
	}

	/**
	 * Recalculates the product's overall rating
	 *
	 * @param $save    boolean
	 * @return unknown_type
	 */
	function updateOverallRating( $save = false )
	{
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_citruscart' . DIRECTORY_SEPARATOR . 'models' );
		$model = JModelLegacy::getInstance( 'ProductComments', 'CitruscartModel' );
		$model->setState( 'filter_product', $this->product_id );
		$model->setState( 'filter_enabled', '1' );

		// get the count of all enabled comments
		$count = $model->getResult( true );

		// get the sum product rating of all enabled comments
		$model->setState( 'select', 'SUM(productcomment_rating)' );
		$sum = $model->getResult( true );

		// get the avg product rating of all enabled comments
		$avg = $count ? $sum / $count : 0;

		// update the product row
		$this->product_rating = $avg;
		$this->product_comments = $count;

		if ( $save )
		{
			$this->save( );
		}
	}

	/**
	 * Creates a product and its related informations (price, quantity & categories)
	 * The price will be created from the $this->product_price property
	 * The quantity will be created from the $this->product_quantity property
	 * The categories will be created from the $this->product_category property
	 */
	function create( )
	{
		// If this product is already stored, we shouldn't create the product!
		if ( $this->product_id )
		{
			$this->setError( JText::_('COM_CITRUSCART_YOU_CANNOT_CREATE_AN_ALREADY_EXISTING_PRODUCT') );
			return false;
		}

		$product_price = $this->product_price;
		$product_quantity = $this->product_quantity;
		$product_categories = $this->product_category;

		if ( !is_array( $product_categories ) )
		{
			$product_categories = array(
				$product_categories
			);
		}

		unset( $this->product_price );
		unset( $this->product_quantity );
		unset( $this->product_category );

		// Save the product First
		$success = $this->save( );

		if ( $success )
		{
			//we dont do the $product_price checking since the product will not show if no entry in the #__citruscart_productprices table
			// now the price
			//if ( $product_price )
			//{
				Citruscart::load( 'CitruscartTableProductPrices', 'tables.productprices' );
				$price = JTable::getInstance( 'ProductPrices', 'CitruscartTable' );
				$price->product_id = $this->product_id;
				$price->product_price = $product_price;
				$price->group_id = Citruscart::getInstance( )->get( 'default_user_group', '1' );
				$success = $price->save( );

				if ( !$success )
				{
					$this->setError( $price->getError( ) );
					return false;
				}
			//}

			// now the quantities
			if ( $product_quantity )
			{
				Citruscart::load( 'CitruscartTableProductQuantities', 'tables.productquantities' );
				$quantity = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
				$quantity->product_id = $this->product_id;
				$quantity->quantity = $product_quantity;
				$success = $quantity->save( );

				if ( !$success )
				{
					$this->setError( $quantity->getError( ) );
					return false;
				}
			}

			// now the categories
			if ( $product_categories )
			{
				foreach ( $product_categories as $product_category )
				{
					// This is probably not the best way to do it
					// Numeric = id, string = category name
					if ( !is_numeric( $product_category ) )
					{
						// check for existance
						JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_citruscart' . DIRECTORY_SEPARATOR . 'models' );
						$model = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
						$model->setState( 'filter_name', $product_category );
						$matches = $model->getList( );
						$matched = false;

						if ( $matches )
						{
							foreach ( $matches as $match )
							{
								// is a perfect match?
								if ( trim( strtolower( $product_category ) ) == trim( strtolower( $match->category_name ) ) )
								{
									$product_category = $match->category_id;
									$matched = true;
								}
							}
						}

						// Not matched, create category
						if ( !$matched )
						{
							Citruscart::load( 'CitruscartTableCategories', 'tables.categories' );
							$category = JTable::getInstance( 'Categories', 'CitruscartTable' );
							$category->category_name = $product_category;
							$category->parent_id = 1;
							$category->category_enabled = 1;
							$category->save( );

							$product_category = $category->category_id;
						}

					}

					// save xref in every case
					Citruscart::load( 'CitruscartTableProductCategories', 'tables.productcategories' );
					$xref = JTable::getInstance( 'ProductCategories', 'CitruscartTable' );
					$xref->product_id = $this->product_id;
					$xref->category_id = $product_category;
					$xref->save( );
				}
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * Updates a product and its related informations (price, quantity & categories)
	 * The price will be created from the $this->product_price property
	 * The quantity will be created from the $this->product_quantity property
	 * The categories will be created from the $this->product_category property
	 */
	function update( )
	{
		// If this product is already stored, we shouldn't create the product!
		if ( !$this->product_id )
		{
			$this->setError( JText::_('COM_CITRUSCART_YOU_CANNOT_UPDATE_A_NON_EXISTING_PRODUCT') );
			return false;
		}

		$product_price = $this->product_price;
		$product_quantity = $this->product_quantity;
		$product_categories = $this->product_category;

		if ( !is_array( $product_categories ) )
		{
			$product_categories = array(
				$product_categories
			);
		}

		unset( $this->product_price );
		unset( $this->product_quantity );
		unset( $this->product_category );

		// Save the product First
		$success = $this->save( );

		if ( $success )
		{
			// now the price
			if ( $product_price )
			{
				// Load the default price
				Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
				$prices = CitruscartHelperProduct::getPrices( $this->product_id );


				if ( count( $prices ) )
				{
					$price_id = $prices[0]->product_price_id;
				}
				else
				{
					$price_id = 0;
				}

				Citruscart::load( 'CitruscartTableProductPrices', 'tables.productprices' );
				$price = JTable::getInstance( 'ProductPrices', 'CitruscartTable' );
				// load the price if it does exist
				if ( $price_id )
				{
					$price->load( $price_id );
				}
				// else just save it as a new price
				$price->product_id = $this->product_id;
				$price->product_price = $product_price;
				$price->group_id = Citruscart::getInstance( )->get( 'default_user_group', '1' );
				$success = $price->save( );

				if ( !$success )
				{
					$this->setError( $price->getError( ) );
					return false;
				}
			}

			// now the quantities
			if ( $product_quantity )
			{
				// Load the default quantity
				Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
				$quantities = CitruscartHelperProduct::getProductQuantities( $this->product_id );

				if ( count( $quantities ) )
				{
					$quantity_id = $quantities[0]->productquantity_id;
				}
				else
				{
					$quantity_id = 0;
				}

				Citruscart::load( 'CitruscartTableProductQuantities', 'tables.productquantities' );
				$quantity = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
				// load the quantity if it does exist
				if ( $quantity_id )
				{
					$quantity->load( $quantity_id );
				}
				// else just save it as a new quantity
				$quantity->product_id = $this->product_id;
				$quantity->quantity = $product_quantity;
				$success = $quantity->save( );

				if ( !$success )
				{
					$this->setError( $quantity->getError( ) );
					return false;
				}
			}

			// now the categories
			if ( $product_categories )
			{
				foreach ( $product_categories as $product_category )
				{
					// This is probably not the best way to do it
					// Numeric = id, string = category name
					if ( !is_numeric( $product_category ) )
					{
						// check for existance
						JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_citruscart' . DIRECTORY_SEPARATOR . 'models' );
						$model = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
						$model->setState( 'filter_name', $product_category );
						$matches = $model->getList( );
						$matched = false;

						if ( $matches )
						{
							foreach ( $matches as $match )
							{
								// is a perfect match?
								if ( strtolower( $product_category ) == strtolower( $match->category_name ) )
								{
									$product_category = $match->category_id;
									$matched = true;
								}
							}
						}

						// Not matched, create category
						if ( !$matched )
						{
							Citruscart::load( 'CitruscartTableCategories', 'tables.categories' );
							$category = JTable::getInstance( 'Categories', 'CitruscartTable' );
							$category->category_name = $product_category;
							$category->parent_id = 1;
							$category->category_enabled = 1;
							$category->save( );

							$product_category = $category->category_id;
						}

					}

					// save xref in every case
					Citruscart::load( 'CitruscartTableProductCategories', 'tables.productcategories' );
					$xref = JTable::getInstance( 'ProductCategories', 'CitruscartTable' );
					$xref->product_id = $this->product_id;
					$xref->category_id = $product_category;
					$xref->save( );
				}
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	public function delete( $oid=null )
	{
	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }

	    // is this product in an order?  if so, it cannot be deleted
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
	    $model = DSCModel::getInstance( 'Orderitems', 'CitruscartModel' );
	    $model->setState('filter_productid', $this->$k );
	    $model->setState('limit', '1');
	    if ($items = $model->getList()) {
	        $this->setError( JText::sprintf("COM_CITRUSCART_PRODUCT_CANNOT_DELETE_IN_ORDERS_DISABLED_INSTEAD", $this->$k) );
	        $this->load( $this->$k );
	        $this->product_enabled = 0;
	        $this->store();
	        return parent::check();
	    }

	    $delete = array();

	    $delete['attributes'] = $this->deleteItemsWithoutReconciliation( 'attributes', $this->$k );
	    $delete['categories'] = $this->deleteItemsXref( 'category', $this->$k );
	    $delete['comments'] = $this->deleteItemsWithoutReconciliation( 'comments', $this->$k );
	    $delete['compare'] = $this->deleteItems( 'compare', $this->$k );
	    $delete['coupons'] = $this->deleteItemsXref( 'coupon', $this->$k );
	    $delete['files'] = $this->deleteItems( 'files', $this->$k );
	    $delete['issues'] = $this->deleteItems( 'issues', $this->$k );
	    $delete['prices'] = $this->deleteItems( 'prices', $this->$k );
	    $delete['quantities'] = $this->deleteItems( 'quantities', $this->$k );
	    $delete['relations'] = $this->deleteItems( 'relations', $this->$k );

	    $delete['product'] = parent::delete( $this->$k );

	    $this->deleteResults = $delete;

	    return parent::check();
	}

	public function deleteItems( $type, $oid=null )
	{
	    $failed = false;

	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }

	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
	    $model = DSCModel::getInstance( 'Product'.$type, 'CitruscartModel' );
	    $model->setState('filter_productid', $this->$k );
	    if ($items = $model->getList())
	    {
	        $table = $model->getTable();
	        $table_pk = $table->getKeyName();
	        foreach ($items as $item)
	        {
	            if (!$table->delete( $item->$table_pk ))
	            {
	                $errors = $table->getErrors();
	                if (!empty($errors))
	                {
	                    foreach ($errors as $key=>$error)
	                    {
	                        $error = trim( $error );
	                        if (!empty($error))
	                        {
	                            $failed = true;
	                            $this->setError($error);
	                        }
	                    }
	                }
	            }
	        }
	    }

	    if ($failed) {
	        return false;
	    }
	    return true;
	}

	public function deleteItemsWithoutReconciliation( $type, $oid=null, $doReconciliation=false )
	{
	    $failed = false;

	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }

	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
	    $model = DSCModel::getInstance( 'Product'.$type, 'CitruscartModel' );
	    $model->setState('filter_productid', $this->$k );
	    if ($items = $model->getList())
	    {
	        $table = $model->getTable();
	        $table_pk = $table->getKeyName();
	        foreach ($items as $item)
	        {
	            if (!$table->delete( $item->$table_pk, $doReconciliation ))
	            {
	                $errors = $table->getErrors();
	                if (!empty($errors))
	                {
	                    foreach ($errors as $key=>$error)
	                    {
	                        $error = trim( $error );
	                        if (!empty($error))
	                        {
	                            $failed = true;
	                            $this->setError($error);
	                        }
	                    }
	                }
	            }
	        }
	    }

	    if ($failed) {
	        return false;
	    }
	    return true;
	}

	public function deleteItemsXref( $type, $oid=null )
	{
	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }

        $query = new CitruscartQuery();
        $query->delete();
        $query->from( '#__citruscart_product'.$type.'xref' );
        $query->where( 'product_id = '.$this->$k );
        $this->_db->setQuery( (string) $query );
        $this->_db->query();

        return true;
	}
}
