<?php
/**
 * @package	DSC
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class DSCSocialGoogle extends DSCSocial
{

    function sharebutton($url = NULL)
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }

        $html = '<script src="https://apis.google.com/js/plusone.js"></script>
					<g:plus action="share"></g:plus>';
        return $html;
    }

    function customsharebutton($url = NULL, $attribs = array())
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }
        $text = 'Google';
        if ($attibs['text']) {
            $text = $attibs['text'];
        }
        if ($attibs['img']) {
            $text = $attibs['img'];
        }
        $onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
        $html    = '<a class="btn socialBtn socialbtnGoogle socialbtnGoogleShare" href="https://plus.google.com/share?url=' . $url . '" onclick="' . $onclick . '">' . $text . '</a>';


        return $html;
    }

}


