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

jimport('joomla.plugin.plugin');

class plgCitruscartCitruscart_socialshare extends CitruscartPluginBase
{
	public $_element ='citruscart_socialshare';
    function __construct(& $subject, $config)
    {
		 parent::__construct($subject, $config);
    }

    function onAfterDisplayProductDescription(){
    	ob_start();
        $layout = $this->_getLayoutPath( $plugin="citruscart_socialshare", $group='citruscart', $layout="default" );
        include($layout);
        $html = ob_get_contents();
        ob_end_clean( );
        return $html;
       }

}
