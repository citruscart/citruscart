<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewProductComments extends CitruscartViewBase
{

    /**
     *
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null)
    {
        $layout = $this->getLayout();
        $this->renderSubmenu();
        switch(strtolower($layout))
        {
            case "form":
            	/* Get the application */
                $app = JFactory::getApplication();
            	$app->input->set('hidemainmenu', '1');
                //JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_catalog' );
                $this->_default($tpl);
              break;
        }
    }
    /**
     *
     *
     */

	function _default($tpl=null)
	{
		Citruscart::load( 'CitruscartUrl', 'library.url' );
		$model = $this->getModel();
		parent::_default($tpl);
	}
	/**
	 *
	 * @param unknown_type $tpl
	 */
	function _form($tpl=null)
	{
			$model = $this->getModel();
			$item = $model->getItem();
			$this->assign( 'item', $item);
		    // Products
			$productModel 	= JModelLegacy::getInstance( 'ElementProduct', 'CitruscartModel' );
         	// terms
			$elementArticle_product 		= $productModel->fetchElement( 'product_id',$item->product_id) ;
			$resetArticle_product		= $productModel->clearElement( 'product_id', '0' );
			$this->assign('elementArticle_product', $elementArticle_product);
			$this->assign('resetArticle_product', $resetArticle_product);
			$userModel 	= JModelLegacy::getInstance( 'ElementUser', 'CitruscartModel' );
         	// terms
			$elementUser_product 		= $userModel->fetchElement( 'user_id',$item->user_id ) ;
			$resetUser_product		= $userModel->clearElement( 'user_id','0' );
			$this->assign('elementUser_product',$elementUser_product);
			$this->assign('resetUser_product', $resetUser_product);

		parent::_form($tpl);


	}

	function _defaultToolbar()
	{
		JToolBarHelper::publishList( 'productcomment_enabled.enable' );
		JToolBarHelper::unpublishList( 'productcomment_enabled.disable' );
		JToolBarHelper::divider();
//		parent::_defaultToolbar();
		JToolBarHelper::deleteList( 'COM_CITRUSCART_VALID_DELETE_ITEMS' );
	}
}
