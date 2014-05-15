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
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_SITE.'/libraries/dioscouri/library/controller/admin.php');

class CitruscartControllerElementArticle extends DSCControllerAdmin
{
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'elementarticle');
	}

    function display($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
        $this->hidefooter = true;
        $object = $app->input->get('object');
        $view = $this->getView( $this->get('suffix'), 'html' );
        $view->assign( 'object', $object );
        parent::display();
    }
}
