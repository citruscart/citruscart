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

class DSCTemplateBootstrap {

	function __construct($parent) {
		if($parent->API->get('recompile_bootstrap', 0) == 1) {

			$framework = $parent->API->get('cssframework', 0);
			if(strlen($framework)) {
				// remove old Bootstrap CSS files
			jimport('joomla.filesystem.file');
			JFile::delete($parent->API->URLtemplatepath().'/css/base.css');
			JFile::delete($parent->API->URLtemplatepath().'/css/responsive.css');
			// generate new Bootstrap CSS files
			try {
				$less = new DSCTemplateHelperLessc;
				// normal Bootstrap code
			    $less->checkedCompile(
			    	$parent->API->URLtemplatepath().'/framework/'.$framework.'/less/bootstrap.less',
			    	$parent->API->URLtemplatepath().'/css/base.css'
			    );
			    // responsive Bootstrap code
			    $less->checkedCompile(
			    	$parent->API->URLtemplatepath().'/framework/'.$framework.'/less/responsive.less',
			    	$parent->API->URLtemplatepath().'/css/responsive.css'
			    );
			} catch (exception $ex) {
			    exit('LESS Parser fatal error:<br />'.$ex->getMessage());
			}


			}

		}
	}
}

// EOF