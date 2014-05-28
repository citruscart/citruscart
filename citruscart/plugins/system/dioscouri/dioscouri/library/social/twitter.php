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

class DSCSocialTwitter extends DSCSocial
{

    function sharebutton($url = NULL)
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }

        $html = '<a href="https://twitter.com/share" class="twitter-share-button" data-via="">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

        return $html;
    }

    function customsharebutton($url = NULL, $attribs = array())
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }
        $text = 'Twitter';
        if ($attibs['text']) {
            $text = $attibs['text'];
        }
        if ($attibs['img']) {
            $text = $attibs['img'];
        }
        $onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
        $html    = '<a class="btn socialBtn socialbtnTwitter socialbtnTwitterShare" onclick="' . $onclick . '" href="https://twitter.com/intent/tweet?url=' . $url . '">' . $text . '</a>';

        return $html;

    }
}


