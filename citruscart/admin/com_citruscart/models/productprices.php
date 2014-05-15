<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelProductPrices extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter_id	        = $this->getState('filter_id');
        $filter_productid   = $this->getState('filter_productid');
        $filter_quantity	= $this->getState('filter_quantity');
       	$filter_user_group	= $this->getState('filter_user_group');
        $filter_date		= $this->getState('filter_date');

		if (strlen($filter_id))
        {
            $query->where('tbl.product_id = '.(int) $filter_id);
       	}

       	if (strlen($filter_productid))
       	{
       	    $query->where('tbl.product_id = '.(int) $filter_productid);
       	}

    	if (strlen($filter_user_group))
        {
            $query->where('tbl.group_id = '.(int) $filter_user_group);
       	}

    	if (strlen($filter_quantity))
        {
        	$query->where("(
        		(tbl.price_quantity_start <= '".(int) $filter_quantity."' AND tbl.price_quantity_end >= '".(int) $filter_quantity."')
        		OR
        		(tbl.price_quantity_end = '0' AND tbl.price_quantity_start <= '".(int) $filter_quantity."' )
			)");
       	}

        if (strlen($filter_date))
        {
        	$nullDate	= JFactory::getDBO()->getNullDate();
        	$query->where("tbl.product_price_startdate <= '".$filter_date."'");
  			$query->where("(tbl.product_price_enddate >= '".$filter_date."' OR tbl.product_price_enddate = '$nullDate' )");
       	}
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_products AS p ON tbl.product_id = p.product_id');
        $query->join('LEFT', '#__citruscart_groups AS g ON g.group_id = tbl.group_id');
    }

    protected function _buildQueryFields(&$query)
    {
        $fields = array();
        $fields[] = " p.product_recurs ";

        $query->select( $this->getState( 'select', 'tbl.*' ) );
        $query->select( $fields );
    }

	public function getList($refresh = false)
	{

		$db = JFactory::getDbo();
		$query=$db->getQuery(true);
		$this->_buildQueryFields($query);
		$this->_buildQueryFrom($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		$this->_buildQueryGroup($query);
		$this->_buildQueryHaving($query);
		$this->_buildQueryOrder($query);
		$db->setQuery($query);
		$list = $db->loadObjectList();

        if (empty( $this->_list ) || $refresh)
        {
           // $list = parent::getList($refresh);
            $nullDate = JFactory::getDBO()->getNullDate();

            if ( empty( $list ) ) {
                return array();
            }

            foreach ($list as $item)
            {
                $item->link_remove = 'index.php?option=com_citruscart&controller=productprices&task=delete&cid[]='.$item->product_price_id;
                /*
                // convert working dates to localtime for display
                $item->product_price_startdate = ($item->product_price_startdate != $nullDate) ? JHTML::_( "date", $item->product_price_startdate, '%Y-%m-%d %H:%M:%S' ) : $item->product_price_startdate;
                $item->product_price_enddate = ($item->product_price_enddate != $nullDate) ? JHTML::_( "date", $item->product_price_enddate, '%Y-%m-%d %H:%M:%S' ) : $item->product_price_enddate;
                */
            }

            $this->_list = $list;
        }

		return $this->_list;
	}

    /**
     * Gets an item for displaying (as opposed to saving, which requires a JTable object)
     * using the query from the model
     *
     * @return database->loadObject() record
     */
    public function getItem($pk=null, $refresh=true, $emptyState=true)
    {
        if (empty( $this->_item ) || $refresh)
        {
            $query = $this->getQuery();
            $this->_db->setQuery( (string) $query );
            $this->_item = $this->_db->loadObject();
            if (is_object($this->_item))
            {
                $nullDate = JFactory::getDBO()->getNullDate();
                // convert working dates to localtime for display
                $this->_item->product_price_startdate = ($this->_item->product_price_startdate != $nullDate) ? JHTML::_( "date", $this->_item->product_price_startdate, '%Y-%m-%d %H:%M:%S' ) : $this->_item->product_price_startdate;
                $this->_item->product_price_enddate = ($this->_item->product_price_enddate != $nullDate) ? JHTML::_( "date", $this->_item->product_price_enddate, '%Y-%m-%d %H:%M:%S' ) : $this->_item->product_price_enddate;
            }
        }


		JFactory::getApplication()->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix'), array( &$this->_item ) );

        return $this->_item;
    }
}
