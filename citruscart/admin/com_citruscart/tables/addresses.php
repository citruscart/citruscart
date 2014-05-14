<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableAddresses extends CitruscartTable
{
    public function __construct( $db=null, $tbl_name=null, $tbl_key=null )
    {
        $tbl_key 	= 'address_id';
        $tbl_suffix = 'addresses';
        $this->set( '_suffix', $tbl_suffix );
        $name 		= 'citruscart';
        if (empty($db)) {
            $db = JFactory::getDBO();
        }

        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
    }

    /**
     * First stores the record
     * Then checks if it should be the default
     *
     * @see Citruscart/admin/tables/CitruscartTable#store($updateNulls)
     */
    function store( $updateNulls=false )
    {
        if ( $return = parent::store( $updateNulls ))
        {
            if ($this->is_default_shipping == '1' || $this->is_default_billing == '1')
            {
                // update the defaults
                $query = new CitruscartQuery();
                $query->update( "#__citruscart_addresses" );
                $query->where( "`user_id` = '{$this->user_id}'" );
                $query->where( "`address_id` != '{$this->address_id}'" );
                if ($this->is_default_shipping == '1')
                {
                    $query->set( "`is_default_shipping` = '0'" );
                }
                if ($this->is_default_billing == '1')
                {
                    $query->set( "`is_default_billing` = '0'" );
                }
                $this->_db->setQuery( (string) $query );
                if (!$this->_db->query())
                {
                    $this->setError( $this->_db->getErrorMsg() );
                    return false;
                }
            }
        }
        return $return;
    }

    /**
     * Checks the entry to maintain DB integrity
     * @return unknown_type
     */
    function check()
    {
        $config = Citruscart::getInstance();

        if(!$this->addresstype_id)
        {
            $this->addresstype_id = '1';
        }
        $address_type = $this->addresstype_id;

        if (empty($this->user_id))
        {
            $this->user_id = JFactory::getUser()->id;
            if (empty($this->user_id))
            {
                $this->setError( JText::_('COM_CITRUSCART_USER_REQUIRED') );
            }
        }
        Citruscart::load( 'CitruscartHelperAddresses', 'helpers.addresses' );
        $elements  = CitruscartHelperAddresses::getAddressElementsData( $address_type );



        if (empty($this->address_name)) {
            $this->address_name = $this->address_1;
        }

        if (empty($this->address_name) && $elements['address_name'][1] )
        {
            $this->setError( JText::_("COM_CITRUSCART_PLEASE_INCLUDE_AN_ADDRESS_TITLE".$address_type) );
        }

        $address_checks = array(
                array( 'first_name' ,'name', "COM_CITRUSCART_FIRST_NAME_REQUIRED" ),
                array( 'middle_name' ,'middle', "COM_CITRUSCART_MIDDLE_NAME_REQUIRED" ),
                array( 'last_name', 'last', "COM_CITRUSCART_LAST_NAME_REQUIRED" ),
                array( 'address_1', 'address1', "COM_CITRUSCART_AT_LEAST_ONE_ADDRESS_LINE_IS_REQUIRED" ),
                array( 'address_2' ,'address2', "COM_CITRUSCART_SECOND_ADDRESS_LINE_IS_REQUIRED" ),
                array( 'company', 'company', "COM_CITRUSCART_COMPANY_REQUIRED" ),
                array( 'tax_number', 'tax_number', "COM_CITRUSCART_COMPANY_TAX_NUMBER_REQUIRED" ),
                array( 'city', 'city', "COM_CITRUSCART_CITY_REQUIRED" ),
                array( 'postal_code', 'zip', "COM_CITRUSCART_POSTAL_CODE_REQUIRED" ),
                array( 'phone_1', 'phone', "COM_CITRUSCART_PHONE_REQUIRED" )
        );
        for( $i = 0, $c = count( $address_checks ); $i < $c; $i++ )
        {
            $current = $address_checks[$i];
            if( empty( $this->$current[0] ) && $elements[ $current[1] ][1] )
            {
                $this->setError( JText::_($current[2]) );
            }
        }

        if( empty( $this->country_id ) )
        {
            if ( $elements['country'][1] )
            {
                $this->setError( JText::_('COM_CITRUSCART_COUNTRY_REQUIRED') );
            }
            else
            {
                $this->country_id = 9999;
            }
        }

        $countryA = explode(',', trim($config->get('ignored_countries', '83,188,190')));

        print_r($countryA); exit;





        if ( empty( $this->zone_id ) && !in_array( $this->country_id, $countryA ) )
        {
            if(isset($elements['zone'][1] ))
            {
                $this->setError( JText::_('COM_CITRUSCART_ZONE_REQUIRED') );
            }
            else
            {
                $this->zone_id = 9999;
            }
        }

        return parent::check();
    }

    public function getZone()
    {
        if (empty($this->zone_id))
        {
            return false;
        }

        DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = DSCModel::getInstance( 'Zones', 'CitruscartModel' );
        $model->setId( $this->zone_id );

        return $model->getItem();
    }

    public function getCountry()
    {
        if (empty($this->country_id))
        {
            return false;
        }

        DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = DSCModel::getInstance( 'Countries', 'CitruscartModel' );
        $model->setId( $this->country_id );

        return $model->getItem();
    }

    public function getSummary( $item=null )
    {
        if (!empty($item) && is_numeric($item)) {
            $this->load( $item );
        } elseif (is_object($item) || is_array($item)) {
            $this->bind($item);
        }

        $lines = array();

        // TODO Get the fields enabled in config,
        $lines[] = $this->first_name . " " . $this->last_name;
        $lines[] = $this->address_1;
        if ($this->address_2) {
            $lines[] = $this->address_2;
        }
        $lines[] = $this->city;
        if ($zone = $this->getZone()) {
            $lines[] = $zone->zone_name;
        }
        $lines[] = $this->postal_code;
        if ($country = $this->getCountry()) {
            $lines[] = $country->country_name;
        }

        $return = implode(', ', $lines);
        return $return;
    }
}
