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

class DSCSocialFacebook extends DSCSocial
{

    function sharebutton($url = NULL)
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }

        $html = '<div class="fb_share"><a name="fb_share" type="box_count" share_url="$url"
     			 href="http://www.facebook.com/sharer.php">Share</a>
    			<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
			</div>';
        return $html;
    }

    function customsharebutton($url = NULL, $attribs = array())
    {
        if (empty($url)) {
            $url = JURI::getInstance()->__toString();
        }
        $text = 'Facebook';
        if ($attibs['text']) {
            $text = $attibs['text'];
        }
        if ($attibs['img']) {
            $text = $attibs['img'];
        }
        $onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
        $html    = '<a class="btn socialBtn socialbtnFacebook socialbtnFacebookShare" onclick="' . $onclick . '" href="http://www.facebook.com/share.php?u=' . $url . '">' . $text . '</a>';
        return $html;
    }
}


