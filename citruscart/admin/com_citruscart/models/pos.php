<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelOrders', 'models.orders' );

class CitruscartModelPOS extends CitruscartModelOrders
{
    public $cache_enabled = false;
    
    function getTable($name='Orders', $prefix='CitruscartTable', $options = array())
    {
        return parent::getTable( $name, $prefix, $options );
    }
}