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



require_once(JPATH_SITE .'/libraries/dioscouri/library/controller/site.php');

jimport('joomla.application.component.controller');

class CitruscartController extends DSCControllerSite
{
    public $default_view = 'products';
    public $router = null;
    public $defines = null;

    function __construct( $config=array() )
    {
        parent::__construct( $config );

        $this->defines = Citruscart::getInstance();

        Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
        $this->router = new CitruscartHelperRoute();

        $this->user = JFactory::getUser();
    }
}
