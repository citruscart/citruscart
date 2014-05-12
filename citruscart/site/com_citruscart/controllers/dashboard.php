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

class CitruscartControllerDashboard extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		if (empty(JFactory::getUser()->id))
		{
			$url = JRoute::_( "index.php?option=com_citruscart&view=dashboard" );
			Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
			$redirect = JRoute::_( CitruscartHelperUser::getUserLoginUrl( $url ), false );
			JFactory::getApplication()->redirect( $redirect );
			return;
		}

		parent::__construct();
		$this->set('suffix', 'dashboard');
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/CitruscartController::display()
	 */
	function display( $cachable = false, $urlparams = '')
	{
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		JFactory::getApplication()->input->set( 'view', $this->get('suffix') );
		$view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
		$model  = $this->getModel( $this->get('suffix') );
		$this->_setModelState();
		$view->set('_doTask', true);
		$view->setModel( $model, true );
		$view->setLayout('default');

		$user_id = JFactory::getUser()->id;
		$userinfo = JTable::getInstance('UserInfo', 'CitruscartTable');
		$userinfo->load( array( 'user_id'=>$user_id ) );
		$view->assign( 'userinfo', $userinfo );

		$view->display();
		$this->footer();
		return;
	}
}