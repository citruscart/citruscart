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

abstract class DSCElement extends JObject
{
    public $element = array();
    public $name = null; // form field name
    public $id = null; // form field id
    public $value = null; // form field value
    public $asset = null; // component name by default

    public function __construct($config=array())
    {
    	$input= JFactory::getApplication()->input;
        $this->setProperties( $config );

        if (empty($this->asset))
        {
            $this->asset = $input->get('option');
        }
    }

    /**
     *
     * @return
     * @param object $name
     * @param object $value[optional]
     * @param object $node[optional]
     * @param object $control_name[optional]
     */
    abstract public function fetchElement($name, $value='', $attribs=array());
}