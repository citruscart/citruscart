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

require_once(JPATH_SITE.'/libraries/dioscouri/library/model/element.php');

class CitruscartModelElementUser extends DSCModelElement
{
    var $title_key = 'name';
    var $select_title_constant = 'COM_CITRUSCART_SELECT_A_USER';
    var $select_constant = 'COM_CITRUSCART_SELECT';
    var $clear_constant = 'COM_CITRUSCART_CLEAR_SELECTION';

    function getTable($name='', $prefix=null, $options = array())
    {
        $table = JTable::getInstance('User', 'DSCTable');
        return $table;
    }



}




