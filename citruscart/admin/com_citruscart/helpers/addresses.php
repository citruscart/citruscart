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

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

class CitruscartHelperAddresses extends CitruscartHelperBase
{
    /**
    * Gets data about which address fields should be visible and validable on a form
    *
    * @params $address_type	Address type
    *
    * @return 2-dimensional associative array with data
    */
    public static function getAddressElementsData( $address_type )
    {
        $config = Citruscart::getInstance();
        $address_fields = array( 'address_name', 'title', 'name', 'middle',
                'last', 'address1', 'address2', 'country', 'city',
                'zip', 'zone', 'phone', 'company', 'tax_number' );
        $elements = array();
        for( $i = 0, $c = count( $address_fields ); $i < $c; $i++ )
        {
            $f = $address_fields[$i];
            $show = $config->get('show_field_'.$f, '3');
            $valid = $config->get('validate_field_'.$f, '3');
            $elements[ $f ] = array(
                    $show == '3' || $show == $address_type,
                    $valid == '3' || $valid == $address_type,
            );
        }
        return $elements;
    }
}
