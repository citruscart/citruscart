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

Citruscart::load( 'CitruscartModelProducts', 'models.products' );

class CitruscartModelSearch extends CitruscartModelProducts
{
	/*
	 * Required the Table of products  it will return products table object
	 *  
	 */	
  	function &getTable($name='products', $prefix='CitruscartTable', $options = array())
    {
        if (empty($name)) {
            $name = $this->getName();
        }
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        if ($table = $this->_createTable( $name, $prefix, $options ))  {
            return $table;
        }

        JError::raiseError( 0, 'Table ' . $name . ' not supported. File not found.' );
        $null = null;
        return $null;
    }
}
