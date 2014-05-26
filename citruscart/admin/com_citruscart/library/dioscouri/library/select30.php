<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE.'/libraries/cms/html/select.php' );


class DSCSelect extends JHTMLSelect
{
    /**
    * Generates a yes/no radio list with the arguments in a consistent order
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function booleanlist( $selected, $name='', $attribs = null, $yes = 'yes', $no = 'no', $id = false )
    {
        return parent::booleanlist( $name, $attribs, $selected, $yes, $no, $id );
    }

	/**
	* Generates a yes/no select list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function booleans( $selected, $name = 'filter_enabled', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title='Select State', $yes = 'Enabled', $no = 'Disabled' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  '0', JText::_( $no ) );
		$list[] = JHTML::_('select.option',  '1', JText::_( $yes ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}	
	

	/**
	* Generates range list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function range( $selected, $name = 'filter_range', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title = 'Select Range' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  'today', JText::_( "Today" ) );
		$list[] = JHTML::_('select.option',  'yesterday', JText::_( "Yesterday" ) );
		$list[] = JHTML::_('select.option',  'last_seven', JText::_( "Last Seven Days" ) );
		$list[] = JHTML::_('select.option',  'last_thirty', JText::_( "Last Thirty Days" ) );
		$list[] = JHTML::_('select.option',  'ytd', JText::_( "Year to Date" ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

    /**
    * Generates range list
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function reportrange( $selected, $name = 'filter_range', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title = 'Select Range' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  'custom', JText::_( "Custom" ) );
        $list[] = JHTML::_('select.option',  'yesterday', JText::_( "Yesterday" ) );
        $list[] = JHTML::_('select.option',  'last_week', JText::_( "Last Week" ) );
        $list[] = JHTML::_('select.option',  'last_month', JText::_( "Last Month" ) );
        $list[] = JHTML::_('select.option',  'ytd', JText::_( "Year to Date" ) );
        $list[] = JHTML::_('select.option',  'all', JText::_( "All Time" ) );

        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }

    /**
    * Generates a created/modified select list
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function datetype( $selected, $name = 'filter_datetype', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title='Select Type' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  'created', JText::_( "Created" ) );
        $list[] = JHTML::_('select.option',  'modified', JText::_( "Modified" ) );
        $list[] = JHTML::_('select.option',  'shipped', JText::_( "Shipped" ) );

        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }

    /**
    * Generates a Period Unit Select List for recurring payments
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function periodUnit( $selected, $name = 'filter_periodunit', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title='Select Period Unit' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  'D', JText::_( "Day" ) );
        $list[] = JHTML::_('select.option',  'W', JText::_( "Week" ) );
        $list[] = JHTML::_('select.option',  'M', JText::_( "Month" ) );
        $list[] = JHTML::_('select.option',  'Y', JText::_( "Year" ) );

        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }

}
