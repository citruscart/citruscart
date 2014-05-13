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
require_once(JPATH_SITE.'/libraries/dioscouri/library/url.php');

require_once(JPATH_SITE.'/libraries/dioscouri/library/browser.php');

class CitruscartUrl extends DSCUrl
{
	public static function popup( $url, $text, $options = array() )
	{
	    if (!empty($options['bootstrap'])) {
	        return self::popupbootstrap( $url, $text, $options );
	    }

		$html = "";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JUri::root().'media/citruscart/colorbox/colorbox.css');
		$doc->addScript(JUri::root().'media/com_citruscart/colorbox/colorbox.js');


		//JHTML::_('stylesheet', 'colorbox.css', 'media/com_citruscart/colorbox/');
		//JHTML::_( 'script', 'colorbox.js', 'media/com_citruscart/colorbox/' );

		$document = JFactory::getDocument();
		$js = "citruscartJQ(document).ready(function() { citruscartJQ('.citruscart-modal').colorbox({current: '', iframe: true, opacity: '0.6', width: '80%', height: '80%'}); });";
		$document->addScriptDeclaration( $js );

		if (!empty($options['update']))
		{
		    $onclose = 'onClose: function(){ Dsc.update(); },';
		}
            else
		{
		    $onclose = '';
		}

		// set the $handler_string based on the user's browser
        $handler_string = "{handler:'iframe', ". $onclose ." size:{x: window.innerWidth-80, y: window.innerHeight-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";
		require_once(JPATH_SITE.'/libraries/dioscouri/dioscouri.php');
        $browser = DSC::getClass( 'DSCBrowser', 'library.browser' );

        if ( $browser->getBrowser() == DSCBrowser::BROWSER_IE )
        {
            // if IE, use
            $handler_string = "{handler:'iframe', ". $onclose ." size:{x:window.getSize().scrollSize.x-80, y: window.getSize().size.y-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";
        }

		$handler = (!empty($options['img']))
		  ? "{handler:'image'}"
		  : $handler_string;

		if (!empty($options['width']))
		{
			if (empty($options['height']))
			{
				$options['height'] = 480;
			}
			$handler = "{handler: 'iframe', ". $onclose ." size: {x: ".$options['width'].", y: ".$options['height']. "}}";
		}

		$id = (!empty($options['id'])) ? $options['id'] : '';
		$class = (!empty($options['class'])) ? $options['class'] : '';

		$html	= "<a class=\"citruscart-modal\" href=\"$url\" rel=\"$handler\" >\n";
		$html 	.= "<span class=\"".$class."\" id=\"".$id."\" >\n";
        $html   .= "$text\n";
		$html 	.= "</span>\n";
		$html	.= "</a>\n";

		return $html;
	}

	/**
	 * TODO Push this upstream once tested
	 *
	 * @param unknown_type $url
	 * @param unknown_type $text
	 * @param unknown_type $options
	 */
	public static function popupbootstrap( $url, $text, $options = array() )
	{
	    $version = isset($options['version']) ? $options['version'] : 'default';
	    DSC::loadBootstrap();
	    JHTML::_( 'script', 'bootstrap-modal.js', 'media/citruscart/bootstrap/'.$version.'/js/' );

	    $time = time();
	    $modal_id = isset($options['modal_id']) ? $options['modal_id'] : 'modal-' . $time;
	    $button_class = isset($options['button_class']) ? $options['button_class'] : 'btn';
	    $label = 'label-' . $time;

	    $button = '<a href="'.$url.'" data-target="#'.$modal_id.'" role="button" class="'.$button_class.'" data-toggle="modal">'.$text.'</a>';

	    $modal = '';
        $modal .= '<div id="'.$modal_id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="'.$label.'" aria-hidden="true">';
        $modal .= '    <div class="modal-header">';
        $modal .= '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
        $modal .= '        <h3 id="'.$label.'">'.$text.'</h3>';
        $modal .= '    </div>';

        $modal .= '    <div class="modal-body">';
        $modal .= '    </div>';

        $modal .= '</div>';

        return $button.$modal;
	}

}
