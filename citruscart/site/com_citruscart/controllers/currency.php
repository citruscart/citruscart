<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Marvelic Engine Team - Citruscart http://www.marvelic.co.th
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerCurrency extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'currency');
	}

    function display($cachable=false, $urlparams = false)
    {
        $view = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
        $view->assign( 'currency', $url );

        parent::display($cachable, $urlparams);
    }

	function set() {
        $currency_id = JRequest::getVar('currency_id', 0);
        
        if($currency_id)
        {
            $helper = Citruscart::getClass('CitruscartHelperBase', 'helpers._base');
            $helper->setSessionVariable( 'currency_id', $currency_id );
        }
        
        $return = JRequest::getVar( 'return', '' );
        if( $return )
        {
            $url = base64_decode($return);
        }
        else
        {
            $url = 'index.php?option=com_citruscart&view=products';
        }
        
        $this->setRedirect(JRoute::_($url));
        $this->redirect();
        return;
	}
}

