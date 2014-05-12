<?php
/**
 * @version 1.5
 * @package Citruscart
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

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