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

/**
 * Query Element Class.
 *
 * @package     Joomla.Framework
 * @subpackage  Database
 * @since       1.6
 */
class DSCQueryElement extends JObject
{
    /** @var string The name of the element */
    protected $_name = null;

    /** @var array An array of elements */
    protected $_elements = null;

    /** @var string Glue piece */
    protected $_glue = null;

    /**
     * Constructor.
     *
     * @param   string  The name of the element.
     * @param   mixed   String or array.
     * @param   string  The glue for elements.
     */
    public function __construct($name, $elements, $glue=',')
    {
        $this->_elements    = array();
        $this->_name        = $name;
        $this->_glue        = $glue;

        $this->append($elements);
    }

    public function __toString()
    {
        return PHP_EOL.$this->_name.' '.implode($this->_glue, $this->_elements);
    }

    /**
     * Appends element parts to the internal list.
     *
     * @param   mixed   String or array.
     */
    public function append($elements)
    {
        if (is_array($elements)) {
            $this->_elements = array_unique(array_merge($this->_elements, $elements));
        } else {
            $this->_elements = array_unique(array_merge($this->_elements, array($elements)));
        }
    }
}