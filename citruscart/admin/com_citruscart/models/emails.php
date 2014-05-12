<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/libraries/dioscouri/library/parameter.php');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelEmails extends CitruscartModelBase
{
    // The prefix of the email language constants that we should fetch
    var $email_prefix = 'COM_CITRUSCART_EMAIL_';

    function getTable($name='Config', $prefix='CitruscartTable', $options = array())
    {
        return parent::getTable( $name, $prefix, $options );
    }

    public function getList($refresh = false){

        jimport('joomla.language.helper');

        $list = JLanguageHelper::createLanguageList(JLanguageHelper::detectLanguage());
        
        foreach($list as $l){
            $l['link'] = "index.php?option=com_citruscart&view=emails&task=edit&id=".$l['value'];
            $item = new JObject();
            			            
            foreach($l as $k => $v){
                $item->$k = $v;
            }
            $result[] = $item;
        }
		return $result;
    }

    public function getItem( $id='en-GB', $refresh=false, $emptyState=true )
    {
        if (empty( $this->_item ))
        {
             
            $lang = JLanguage::getInstance($id);
            // Load only site language (Email language costants should be only there)
            $lang->load('com_citruscart', JPATH_ADMINISTRATOR, $id, true);

            $temp_paths = $lang->getPaths('com_citruscart');
            foreach($temp_paths as $p => $k){
                $path = $p;
            }

            $result = new JObject();
            $result->name = $lang->getName();
            $result->code = $lang->getTag();
            $result->path = $path;

            $result->strings = array();

            // Load File and Take only the constants that contains "EMAIL_"
            $file = new DSCParameter();
            $file->loadFile($path);
            $strings = $file->toArray();
            $result_strings = array();
            foreach($strings as $k =>$v)
            {
                // Only if it is a prefix!
                if(stripos( $k, $this->email_prefix) === 0)
                    $result_strings[$k] = $v;
            }
            $result->strings = array('file' => $path,
                    'strings' => $result_strings);

            
            JFactory::getApplication()->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix'), array( &$result ) );

            $this->_item = $result;
        }

        return $this->_item;

    }
}
