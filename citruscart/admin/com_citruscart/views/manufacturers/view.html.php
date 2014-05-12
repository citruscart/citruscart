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

Citruscart::load( 'CitruscartViewBase', 'views._base' );
require_once(JPATH_SITE.'/libraries/dioscouri/library/parameter.php');
class CitruscartViewManufacturers extends CitruscartViewBase
{
    /**
     *
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null)
    {
    	$app = JFactory::getApplication();
        $layout = $this->getLayout();
        $this->renderSubmenu();
        switch(strtolower($layout))
        {
            case "view":
                $this->_form($tpl);
              break;
            case "form":
                $app->input->set('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_catalog' );
                $this->_default($tpl);
              break;
        }
    }

    function _form($tpl = null){
    	parent::_form($tpl);

    	$model = $this->getModel();



    	$this->row = $model->getItem();

    	if (empty($this->row->manufacturer_id))
    	{
    		// this is a new product
    		$item = JTable::getInstance('Manufacturers', 'CitruscartTable');
    		$item->manufacturer_params = new DSCParameter( $item->manufacturer_params );
    		$this->assign( 'row', $item );
    	}


    }
	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/views/CitruscartViewBase#_defaultToolbar()
	 */
	function _defaultToolbar()
	{
		JToolBarHelper::publishList( 'manufacturer_enabled.enable' );
		JToolBarHelper::unpublishList( 'manufacturer_enabled.disable' );
		JToolBarHelper::divider();
		parent::_defaultToolbar();
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/views/CitruscartViewBase#_formToolbar($isNew)
	 */
    function _formToolbar( $isNew=null )
    {
    	if (!$isNew)
    	{
        	JToolBarHelper::custom('save_as', 'refresh', 'refresh', 'COM_CITRUSCART_SAVE_AS', false);
    	}
        parent::_formToolbar($isNew);
    }
}
