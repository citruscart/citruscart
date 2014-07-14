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


if (version_compare(JVERSION, '3.0', 'ge'))
{
     require_once( JPATH_SITE . '/libraries/dioscouri/library/grid30.php' );

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
    require_once( JPATH_SITE . '/libraries/dioscouri/library/grid16.php' );

}
else
{
    require_once( JPATH_SITE . '/libraries/dioscouri/library/grid15.php' );

}