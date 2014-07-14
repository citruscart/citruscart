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

class DSCTemplateAPI extends JObject
{
	private $API;

	function __construct($parentTpl) {
		$this->API = $parentTpl;
	}

    public function addCSS($url, $type = 'text/css', $media = null) {
        $this->API->addStyleSheet($url, $type, $media);
    }

    public function addJS($url) {
        $this->API->addScript($url);
    }

    public function addCSSRule($code) {
        $this->API->addStyleDeclaration($code);
    }

    public function addJSFragment($code) {
    	$this->API->addScriptDeclaration($code);
    }

    public function get($key, $default) {
        return $this->API->params->get($key, $default);
    }

    public function modules($rule) {
        return $this->API->countModules($rule);
    }

    public function URLbase() {
        return JURI::base();
    }

    public function URLtemplate() {
        return JURI::base() . "templates/" . $this->API->template;
    }

    public function URLpath() {
        return JPATH_SITE;
    }

    public function URLtemplatepath() {
        return $this->URLpath() . "/templates/" . $this->API->template;
    }
    public function getTemplateLayoutPath($layout) {
        return $this->URLpath() . "/templates/" . $this->API->template . '/layouts/' .$layout;
    }
    public function templateName() {
        return $this->API->template;
    }

    public function getPageName() {
        $config = new JConfig();
        return $config->sitename;
    }

    public function addFavicon() {
        $icon = $this->URLtemplatepath() . '/images/ico/favicon.ico';
    	return $this->API->addFavicon($icon);
    }
}

// EOF