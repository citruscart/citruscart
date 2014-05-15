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
require_once JPATH_SITE .'/libraries/dioscouri/library/image.php';

require_once JPATH_SITE .'/libraries/dioscouri/library/parameter.php';

class CitruscartViewProducts extends CitruscartViewBase
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
            case "gallery":
            case "setquantities":
            case "selectcategories":
                $this->_default($tpl);
              break;
            case "view":
                $this->_form($tpl);
              break;
            case "form_relations":
            case "form":
            	$app->input->set('hidemainmenu', '1');
				DSCImage::loadUploadify();
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
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		parent::_form($tpl);

		$model = $this->getModel();
		$item = $model->getItem();


		if (empty($item->product_id))
		{
		    // this is a new product
		    $item = JTable::getInstance('Products', 'CitruscartTable');
            $item->product_parameters = new DSCParameter( $item->product_params );
            $this->assign( 'row', $item );
		}

		$results = $app->triggerEvent( 'onGetProductView', array( $model->getItem() ) );

		$shippingHtml = implode('<hr />', $results);

		$this->assign('shippingHtml', $shippingHtml);

		$elementArticleModel = JModelLegacy::getInstance( 'ElementArticle', 'CitruscartModel' );
		$this->assign( 'elementArticleModel', $elementArticleModel );
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/views/CitruscartViewBase#_defaultToolbar()
	 */
	function _defaultToolbar()
	{
		JToolBarHelper::publishList( 'product_enabled.enable' );
		JToolBarHelper::unpublishList( 'product_enabled.disable' );
		JToolBarHelper::divider();

		parent::_defaultToolbar();
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/views/CitruscartViewBase#_formToolbar($isNew)
	 */
    function _formToolbar( $isNew=null )
    {
    	$model = $this->getModel();
    	if ($model->getId())
    	{
    	    JToolBarHelper::custom( 'view', 'edit', 'edit', 'COM_CITRUSCART_DASHBOARD', false);
            JToolBarHelper::divider();
    	}
    	if (!$isNew)
    	{
        	JToolBarHelper::custom('save_as', 'refresh', 'refresh', 'COM_CITRUSCART_SAVE_AS', false);
    	}
        parent::_formToolbar($isNew);
    }

    /**
     * (non-PHPdoc)
     * @see Citruscart/admin/views/CitruscartViewBase#_viewToolbar($isNew)
     */
	function _viewToolbar( $isNew=null )
	{
        JToolBarHelper::custom( 'edit', 'edit', 'edit', 'COM_CITRUSCART_EDIT', false);
        JToolBarHelper::divider();
        parent::_viewToolbar($isNew);
	}

}
