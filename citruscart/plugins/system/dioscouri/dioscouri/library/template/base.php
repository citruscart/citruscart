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

class DSCTemplateBase
{

    public $name = 'template';
    public $layout;

    // access to the helper classes
    public $API;
    public $bootstrap;
    public $less;

    public $page_suffix;
    public $pageclass = '';
    public $doc;
    public $footerScripts = array();

	function __construct($template) {

        $this->API = new DSCTemplateAPI($template);
        $this->name = $this->API->templateName();
        $this->bootstrap = new DSCTemplateBootstrap($this);
        $this->layout = $this->API->get('layout', 'default.php');
        $this->pageclass = $this->API->get('pageclass', '');

	}

    function prepareDoc () {

        $this->doc = JFactory::getDocument();
        $this->doc->setGenerator( $this->API->get('generator', 'Dioscouri Design'));
        $this->API->addFavicon();

    }

    function returnLayout() {

        if (JFile::exists( $this->API->getTemplateLayoutPath($this->layout))) {

        return $this->API->getTemplateLayoutPath($this->layout);
        }
    }

    function prepareFooterScripts() {

    }

}