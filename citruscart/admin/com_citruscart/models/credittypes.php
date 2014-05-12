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

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelCreditTypes extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.credittype_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.credittype_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.credittype_code) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
       	}
    }

}
