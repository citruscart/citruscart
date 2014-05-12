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

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewProductCompare extends CitruscartViewBase
{

    /**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default($tpl=null, $onlyPagination = false)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$items = $model->getList();
		$app->input->set( 'page', 'category' ); // for "getCartButton"

		// form
			$validate = JSession::getFormToken();
			$form = array();
			$view = strtolower( $app->input->get('view') );
			$form['action'] = "index.php?option=com_citruscart&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
    }
}