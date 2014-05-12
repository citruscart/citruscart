<?php
/**
 * @version    1.5
 * @package    Citruscart
 * @author     Dioscouri Design
 * @link     http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewCarts extends CitruscartViewBase  
{
    
    
    /**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
 /*	function _default($tpl='')
	{
		
				
		// form
			$validate = JSession::getFormToken();
			$form = array();
			$view = strtolower( JRequest::getVar('view') );
			$form['action'] = "index.php?option=com_citruscart&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
    }
   */ 
}