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

class CitruscartModelAccounts extends CitruscartModelBase 
{
	function getTable($name='', $prefix=null, $options = array())
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$table = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
		return $table;
	}
	
    protected function _buildQueryWhere(&$query)
    {
        $filter_userid      = $this->getState('filter_userid');

        if ($filter_userid)
        {
            $query->where('tbl.user_id = '.$filter_userid);
        }
    }
}
