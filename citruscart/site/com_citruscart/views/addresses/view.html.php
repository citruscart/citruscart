<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewAddresses extends CitruscartViewBase
{
	function _default($tpl='', $onlyPagination = false)
	{
		/* Get the application */
		$app = JFactory::getApplication();

		parent::_default($tpl, $onlyPagination );
        //if (JRequest::getVar('tmpl') == 'component')

        if ($app->input->getString('tmpl') == 'component')
        {
        	$this->assign( 'tmpl', '&amp;tmpl=component' );
        }
	}
}
