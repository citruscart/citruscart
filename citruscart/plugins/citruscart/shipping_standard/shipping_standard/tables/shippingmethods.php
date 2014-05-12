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

class CitruscartTableShippingMethods extends CitruscartTable
{
    function CitruscartTableShippingMethods ( &$db )
    {

        $tbl_key    = 'shipping_method_id';
        $tbl_suffix = 'shippingmethods';
        $this->set( '_suffix', $tbl_suffix );
        $name       = 'citruscart';

        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
    }

    function check()
    {
        if ((float) $this->subtotal_maximum == (float) '0.00000')
        {
            $this->subtotal_maximum = '-1';
        }
        return true;
    }

}
