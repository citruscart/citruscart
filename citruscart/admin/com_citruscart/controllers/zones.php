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

class CitruscartControllerZones extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'zones');
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'c.country_name', 'cmd');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_code']         = $app->getUserStateFromRequest($ns.'code', 'filter_code', '', '');
		$state['filter_countryid'] 	= $app->getUserStateFromRequest($ns.'countryid', 'filter_countryid', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$query = $model->getQuery( );
		$query->order( 'tbl.zone_name' );
		$model->setQuery( $query );

		return $state;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function filterZones()
	{
		$app = JFactory::getApplication();

		JLoader::import( 'com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components' );
		Citruscart::load( 'CitruscartSelect', 'library.select' );

		$idtag = 'zone_id';
		$countryid = $app->input->getInt( 'countryid',0);
		$idprefix =$app->input->getInt( 'idprefix', 0);

		/*
		$countryid = JRequest::getVar( 'countryid', '', 'request', 'int' );
		$idprefix = JRequest::getVar( 'idprefix', '', 'request');
		 */

		if (count($idprefix)>0){$idtag = $idprefix.$idtag;}

		$url = "index.php?option=com_citruscart&format=raw&controller=zones&task=addZone&geozoneid=";
		$attribs = array(
			'class' => 'inputbox',
			'size' => '1');

		$hookgeozone = $app->input->get( 'hookgeozone',TRUE);
		//$hookgeozone = JRequest::getVar( 'hookgeozone', TRUE, 'request', 'boolean' );
		if($hookgeozone){
			$attribs['onchange'] = 'citruscartDoTask( \''.$url.'\'+document.getElementById(\'geozone_id\').value+\'&zoneid=\'+this.options[this.selectedIndex].value, \'current_zones_wrapper\', \'\');';
		}

		$html = CitruscartSelect::zone( '', $idtag, $countryid, $attribs, $idtag, true);

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo ( json_encode( $response ) );

		return;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function addZone()
	{
		$app = JFactory::getApplication();
		JLoader::import( 'com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components' );

		$zoneid = $app->input->getInt( 'zoneid',0);
		$geozoneid = $app->input->getInt( 'geozoneid',0 );
		/*$zoneid = JRequest::getVar( 'zoneid', '', 'request', 'int' );
		$geozoneid = JRequest::getVar( 'geozoneid', '', 'request', 'int' );
         */
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$zonerelation = JTable::getInstance( 'Zonerelations', 'CitruscartTable' );
		$zonerelation->zone_id = $zoneid;
		$zonerelation->geozone_id = $geozoneid;

		if ($zonerelation->save())
		{
		    $model = $this->getModel( 'zonerelations' );
		    $model->clearCache();

		    $html = CitruscartHTML::zoneRelationsList($geozoneid);
		}
		else
		{
		    $html = $zonerelation->getError();
		}

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo ( json_encode( $response ) );

		return;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function removeZone()
	{
		$app = JFactory::getApplication();
		JLoader::import( 'com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components' );

		$zrid = $app->input->getInt( 'zrid');

		//$zrid = JRequest::getVar( 'zrid', '', 'request', 'int' );

		$geozoneid =$app->input->getInt( 'geozoneid');
		//$geozoneid = JRequest::getVar( 'geozoneid', '', 'request', 'int' );

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$zonerelation = JTable::getInstance( 'Zonerelations', 'CitruscartTable' );
		$zonerelation->load( $zrid );

		if ($zonerelation->delete())
		{
		    $model = $this->getModel( 'zonerelations' );
		    $model->clearCache();

		    $html = CitruscartHTML::zoneRelationsList($geozoneid);
		}
		else
		{
		    $html = $zonerelation->getError();
		}

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo ( json_encode( $response ) );

		return;
	}
}

