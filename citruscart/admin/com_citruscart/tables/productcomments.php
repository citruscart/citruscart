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

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableProductComments extends CitruscartTable 
{
	function __construct( &$db ) 
	{
		$tbl_key 	= 'productcomment_id';
		$tbl_suffix = 'productcomments';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= "citruscart";
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}

	function check()
	{
		$nullDate	= $this->_db->getNullDate();

		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}		
		return true;
	}
	
	/**
	 * 
	 * @param $oid
	 * @return unknown_type
	 */
	function delete( $oid=null, $doReconciliation=true )
	{
	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }
	    
	    if ($doReconciliation)
	    {
	        if ($oid)
	        {
	            $row = JTable::getInstance('ProductComments', 'CitruscartTable');
	            $row->load( $oid );
	            $product_id = $row->product_id;
	        }
	        else
	        {
	            $product_id = $this->product_id;
	        }
	    }
		
		if ( parent::delete( $oid ) )
		{
		    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		    $model = DSCModel::getInstance( 'ProductCommentsHelpfulness', 'CitruscartModel' );
		    $model->setState('filter_comment', $this->$k );
		    if ($items = $model->getList())
		    {
		        $table = $model->getTable();
		        foreach ($items as $item)
		        {
		            if (!$table->delete( $item->productcommentshelpfulness_id ))
		            {
		                $this->setError( $table->getError() );
		            }
		        }
		    }
		    
		    if ($doReconciliation) 
		    {
		        $product = JTable::getInstance('Products', 'CitruscartTable');
		        $product->load( $product_id );
		        $product->updateOverallRating();
		        if ( !$product->save() )
		        {
		            $this->setError( $product->getError() );
		        }
		    }
		}
		
		return parent::check();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DSCTable::save()
	 */
	function save($src='', $orderingFilter = '', $ignore = '')
	{
		
	    $isNew = false;
        if (empty($this->productcomment_id))
        {
            $isNew = true;
        }
        
        if ($save = parent::save($src, $orderingFilter, $ignore))
        {
            if ($this->productcomment_enabled && empty($this->rating_updated))
            {
                // get the product row
                $product = JTable::getInstance('Products', 'CitruscartTable');
                $product->load( $this->product_id );
                
                $product->updateOverallRating();
                
                if (!$product->save())
                {
                    $this->setError( $product->getError() );
                }
                    else
                {
                    $this->rating_updated = '1';
                    parent::store();
                }
            }
                elseif (!$this->productcomment_enabled && !empty($this->rating_updated) )
            {
                // comment has been disabled after it already updated the overall rating
                // so remove it from the overall rating
                
                // get the product row
                $product = JTable::getInstance('Products', 'CitruscartTable');
                $product->load( $this->product_id );
                
                $product->updateOverallRating();
                                                
                if (!$product->save())
                {
                    $this->setError( $product->getError() );
                }
                    else
                {
                    $this->rating_updated = '0';
                    parent::store();
                }
            }
        }
        
        return $save;
    }
}
