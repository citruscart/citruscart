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
require_once(JPATH_SITE .'/libraries/dioscouri/library/parameter.php');
class CitruscartViewCategories extends CitruscartViewBase
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
            case "selectproducts":
                $this->_default($tpl);
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

	function _form($tpl=null)
	{
		parent::_form($tpl);

		$model = $this->getModel();

		$item = $model->getItem();


		$results = JFactory::getApplication()->triggerEvent( 'onGetCategoryView', array( $item  ) );

		if (empty($item->category_id))
		{
			// this is a new product
			$item = JTable::getInstance('Categories', 'CitruscartTable');
			$item->category_params = new DSCParameter( $item->category_params );
			$this->assign( 'row', $item );
		}


		$shippingHtml = implode('<hr />', $results);
		if( !isset($this->row) )
			$this->row = new stdClass();

		if( !isset( $this->row->display_name_subcategory ) )
			$this->row->display_name_subcategory = 1;
		if( !isset( $this->row->display_name_category ) )
			$this->row->display_name_category = 1;

		$this->assign('shippingHtml', $shippingHtml);
	}

    /**
     * (non-PHPdoc)
     * @see Citruscart/admin/views/CitruscartViewBase#_defaultToolbar()
     */
	function _defaultToolbar()
	{
		JToolBarHelper::custom('rebuild', 'refresh', 'refresh', 'COM_CITRUSCART_REBUILD_TREE' , false);
		JToolBarHelper::publishList( 'category_enabled.enable' );
		JToolBarHelper::unpublishList( 'category_enabled.disable' );
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
        	JToolBarHelper::custom('save_as', 'refresh', 'refresh', 'COM_CITRUSCART_SAVE_AS' , false);
    	}
        parent::_formToolbar($isNew);
    }

}
