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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelShippingRates extends CitruscartModelBase
{
    public $cache_enabled = false;

    protected function _buildQueryWhere(&$query)
    {
        $filter_id	= $this->getState('filter_id');
        $filter_shippingmethod  = $this->getState('filter_shippingmethod');
        $filter_weight = $this->getState('filter_weight');
        $filter_geozone = $this->getState('filter_geozone');
        $filter_geozones = $this->getState('filter_geozones');
		$filter_user_group = $this->getState('filter_user_group', '');

		if (strlen($filter_id))
        {
            $query->where('tbl.shipping_rate_id = '.(int) $filter_id);
       	}
        if (strlen($filter_shippingmethod))
        {
            $query->where('tbl.shipping_method_id = '.(int) $filter_shippingmethod);
        }
    	if (strlen($filter_weight))
        {
        	$query->where("(
        		tbl.shipping_rate_weight_start <= '".$filter_weight."'
        		AND (
                    tbl.shipping_rate_weight_end >= '".$filter_weight."'
                    OR
                    tbl.shipping_rate_weight_end = '0.000'
                    )
			)");
       	}
        if (strlen($filter_geozone))
        {
            $query->where('tbl.geozone_id = '.(int) $filter_geozone);
        }

        if (is_array($filter_geozones))
        {
            $query->where("tbl.geozone_id IN ('" . implode("', '", $filter_geozones ) . "')" );
        }

		if( is_array( $filter_user_group ) ) {
	              $query->where("tbl.group_id IN ('" . implode( ',', $filter_user_group ) . "')" );
	    } else {
	      if( strlen( $filter_user_group ) ) {
	              $query->where("tbl.group_id IN ('" . $filter_user_group . "')" );
	      }
	    }
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_geozones AS geozone ON tbl.geozone_id = geozone.geozone_id');
        $query->join('LEFT', '#__citruscart_groups AS g ON tbl.group_id = g.group_id');
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();
        $field[] = " geozone.geozone_name ";

        $query->select( $this->getState( 'select', 'tbl.*' ) );
        $query->select( $field );
    }

	public function getList($refresh = false)
	{
		$list = parent::getList($refresh);

		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }

		foreach($list as $item)
		{
			$item->link_remove = 'index.php?option=com_citruscart&controller=shippingrates&task=delete&cid[]='.$item->shipping_rate_id;
		}
		return $list;
	}
}
