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

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgQuickiconCitruscartIcon extends JPlugin
{

    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onGetIcons($context)
    {
        if (
            $context == $this->params->get('context', 'mod_quickicon')
            && JFactory::getUser()->authorise('core.manage', 'com_content')
        ){
            return array(array(
                'link' => 'index.php?option=com_citruscart',
                'image' => JURI::root().'/media/citruscart/images/citruscart.png',
                'text' => 'Citruscart Dashboard',
                'id' => 'plg_quickicon_citruscarticon'
            ));
        } else return;
    }
}
