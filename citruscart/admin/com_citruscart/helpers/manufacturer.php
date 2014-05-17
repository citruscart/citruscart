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
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
jimport('joomla.filesystem.file');

class CitruscartHelperManufacturer extends CitruscartHelperBase
{
	public static function getImage( $id, $by='id', $alt='', $type='thumb', $url=false )
	{
		switch($type)
		{
			case "full":
				$path = 'manufacturers_images';
			  break;
			case "thumb":
			default:
				$path = 'manufacturers_thumbs';
			  break;
		}
		
		$tmpl = "";
		if (strpos($id, '.'))
		{
			// then this is a filename, return the full img tag if file exists, otherwise use a default image
			$src = (JFile::exists( Citruscart::getPath( $path ).'/'.$id))
				? Citruscart::getUrl( $path ).$id : 'media/citruscart/images/noimage.png';
			
			// if url is true, just return the url of the file and not the whole img tag
			$tmpl = ($url)
				? $src : "<img src='".$src."' alt='".JText::_( $alt )."' title='".JText::_( $alt )."' name='".JText::_( $alt )."' align='center' border='0' >";

		}
			else
		{
			if (!empty($id))
			{
				// load the item, get the filename, create tmpl
				JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
				$row = JTable::getInstance('Manufacturers', 'CitruscartTable');
				$row->load( (int) $id );
				$id = $row->manufacturer_image;

				$src = (JFile::exists( Citruscart::getPath( $path ).'/'.$row->manufacturer_image))
					? Citruscart::getUrl( $path ).$id : 'media/citruscart/images/noimage.png';

				// if url is true, just return the url of the file and not the whole img tag
				$tmpl = ($url)
					? $src : "<img src='".$src."' alt='".JText::_( $alt )."' title='".JText::_( $alt )."' name='".JText::_( $alt )."' align='center' border='0' >";
			}			
		}
		return $tmpl;
	}

	/**
	 * Method to calculate statistics about manufacturers in an order
	 * 
	 * @param $items Array of order items
	 * 
	 * @return	Array with list of manufacturers and their stats
	 */
	function calculateStatsOrder( $items )
	{
		$db = JFactory::getDbo();
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		Citruscart::load( 'CitruscartQuery' ,'library.query' );
		$q = new CitruscartQuery();
		$q->select( 'manufacturer_id' );
		$q->from( '`#__citruscart_products`' );
		
		$result = array();
		foreach( $items as $item )
		{
			$q->where( 'product_id = '.(int)$item->product_id );
			$db->setQuery( $q );
			$res = $db->loadObject();
			if( $res == null )
				$man_id = 0;
			else
				$man_id = $res->manufacturer_id;
			if( !isset( $result[ $man_id ] ) )
			{
				$model = JModelLegacy::getInstance( 'Manufacturers', 'CitruscartModel' );
				$model->setId( $man_id );
				if (!$man_item = $model->getItem()) {
				    $man_item = new stdClass();
				}
				$result[ $man_id ] = $man_item;
				$result[ $man_id ]->subtotal = 0;
				$result[ $man_id ]->total_tax = 0;
			}
			$result[ $man_id ]->subtotal += $item->orderitem_final_price;
			$result[ $man_id ]->total_tax += $item->orderitem_tax;
		}
		return $result;
	}
}
