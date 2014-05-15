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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerProductFiles extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'productfiles');
	}

	/**
	 * downloads a file
	 *
	 * @return void
	 */
	function downloadFile( )
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser( );
		$productfile_id = $app->input->getInt( 'id',0);
		//$productfile_id = intval( JRequest::getvar( 'id', '', 'request', 'int' ) );
		$product_id = $app->input->getInt(  'product_id', 0);
		$link = 'index.php?option=com_citruscart&view=products&task=edit&id=' . $product_id;

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance( 'ProductDownload', 'CitruscartHelper' );

		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_citruscart' . DS . 'tables' );
		$productfile = JTable::getInstance( 'ProductFiles', 'CitruscartTable' );
		$productfile->load( $productfile_id );
		if ( empty( $productfile->productfile_id ) )
		{
			$this->messagetype = 'notice';
			$this->message = JText::_('COM_CITRUSCART_INVALID FILE');
			$this->setRedirect( $link, $this->message, $this->messagetype );
			return false;
		}

		// log and download
		Citruscart::load( 'CitruscartFile', 'library.file' );

		// geting the ProductDownloadId to updated for which productdownload_max  is greater then 0
		$productToDownload = $helper->getProductDownloadInfo( $productfile->productfile_id, $user->id );

		if ( $downloadFile = CitruscartFile::download( $productfile ) )
		{
			$link = JRoute::_( $link, false );
			$this->setRedirect( $link );
		}
	}
}

