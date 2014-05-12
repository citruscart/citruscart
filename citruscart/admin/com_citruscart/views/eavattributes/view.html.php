<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewEavAttributes extends CitruscartViewBase 
{
    /**
     * 
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null) 
    {
        $layout = $this->getLayout();
        
        /* Get the application  */
        $app = JFactory::getApplication();
        
        switch(strtolower($layout))
        {
        	
        	 case "selectproducts":
        	 	
        	 	$app->input->set('hidemainmenu', '1');
        	 	//JRequest::setVar('hidemainmenu', '1');
                $this->_default($tpl);
              break;
            case "view":
                $this->_form($tpl);
              break;
            case "form":
            	$app->input->set('hidemainmenu', '1');
                //JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_customfields' );
                $this->_default($tpl);
              break;
        }
    }
    
    function _form($tpl=null)
    {      	
    	    /* Get the application */
    	    $app = JFactory::getApplication();
    	    
    	    /* Get the id values */
    	    $id = $app->input->get('id', '');
    	    //$id = JRequest::getVar('id', '');
    		    	    
    		$model = $this->getModel();
    	   							    		
			$item = $model->getItem($id);
		
			$items = JArrayHelper::fromObject($item);	
			
			switch($items['eaventity_type'])
			{
				case 'products':
					// Products
					$productModel 	= JModelLegacy::getInstance( 'ElementProduct', 'CitruscartModel' );
		         	// terms
		         	$product = JTable::getInstance('Products', 'CitruscartTable');
		         	$product->load($items['eaventity_id']);
					$elementArticle_product 		= $productModel->fetchElement( 'eaventity_id',$product->product_name) ;
					$resetArticle_product		= $productModel->clearElement( 'eaventity_id', '0' );
					$this->assign('elementproduct', $elementArticle_product);
					$this->assign('resetproduct', $resetArticle_product);
			}   
						
			parent::_form($tpl);
    }
}
