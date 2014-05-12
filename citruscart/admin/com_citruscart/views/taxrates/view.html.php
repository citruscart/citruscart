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
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewTaxrates extends CitruscartViewBase 
{
	/*
	 * Gets names of tax rates at the same level
	 * 
	 * @params $level						level of taxes
	 * @params $geozone_id 			ID of a geozone (null means all)
	 * @params $tax_class_id 		ID of a tax class (null means all)
	 * @params $tax_type				for the future use
	 * @params $update					update cached info
	 * 
	 * @return Array with names of tax rates at the same level
	 */
	function getAssociatedTaxRates( $level, $geozone_id = null, $tax_class_id = null, $tax_type = null, $update = false )
	{
		static $taxrates = null; // static array for caching results
		if( $taxrates === null )
			$taxrates = array();
			
		if( !$geozone_id )
			$geozone_id = -1;
		if( !$tax_class_id )
			$tax_class_id = -1;
		
		if( isset( $taxrates[$tax_class_id][$geozone_id][$level] ) && !$update )
			return $taxrates[$tax_class_id][$geozone_id][$level];

		$res = $this->getModel()->getTaxRatesAtLevel( ( int )$level, $geozone_id, $tax_class_id, $tax_type, $update );

		$result = array();
		for( $i = 0, $c = count( $res ); $i < $c; $i++ )
			$result []= $res[$i]->tax_rate_description;
		
		$taxrates[$tax_class_id][$geozone_id][$level] = $result;
		return $taxrates[$tax_class_id][$geozone_id][$level];
	}

	/*
	 * Generate list of levels in taxes
	 * 
	 * @param $selected				Selected tax rate level
	 * @param $taxrate_id			Taxrate ID
	 * @param $tax_class_id		Tax class ID
	 * 
	 * @return HTML of a select with list of levels of taxes
	 */
	function listRateLevels( $selected, $taxrate_id, $tax_class_id )
	{
		$list = array();
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$q = new CitruscartQuery();
		$db = JFactory::getDbo();
		$q->select( 'max( level ) as `max_level`, min( level ) as `min_level`' );
		$q->from( '#__citruscart_taxrates' );
		$q->where( 'tax_class_id = '.$tax_class_id );
		$db->setQuery( $q );
		$levels = $db->loadObject();
		if( !strlen( $levels->min_level )  )
			$levels->min_level = 0;
		for( $i = $levels->min_level; $i <= $levels->max_level + 1; $i++ )
  		$list[] = JHTML::_('select.option',  $i, 'Level - '.$i );
    return JHTML::_( 'select.genericlist', $list, 'levels['.$taxrate_id.']', array('class' => 'inputbox', 'size' => '1'), 'value', 'text', $selected );
	}
}
