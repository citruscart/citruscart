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
class CitruscartControllerProducts extends CitruscartController
{
	private $_callback_js = ''; // callback jS that should be returned with the current AJAX request

    /**
     * constructor
     */
    function __construct( )
    {
        parent::__construct( );

        $this->set( 'suffix', 'products' );
        $this->itemid = JRequest::getInt('Itemid');
    }

    /**
     * Gets the view's namespace for state variables.
     * Overriding here in order to make namespace specific to Itemid
     *
     * @return string
     */
    function getNamespace()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );

        $ns = $app->getName().'::'.'com.'.$this->get('com').'.model.'.$model->getTable()->get('_suffix').".".$this->itemid;
        return $ns;


    }

    /**
     * Sets the model's state
     *
     * @return array()
     */
    function _setModelState( )
    {
        $state = parent::_setModelState( );
        $app = JFactory::getApplication( );
        $model = $this->getModel( $this->get( 'suffix' ) );
        $ns = $this->getNamespace( );

        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
        $user_id = JFactory::getUser( )->id;
        $state['filter_group'] = CitruscartHelperUser::getUserGroup( $user_id );

        $date = JFactory::getDate( );

        if ($this->defines->get('disable_changing_list_limit')) {
            $state['limit']  	= $this->defines->get('default_list_limit') ? $this->defines->get('default_list_limit') : $app->getCfg('list_limit');
        } else {
            $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $this->defines->get('default_list_limit', $app->getCfg('list_limit')), 'int');
        }

        $state['order'] = 'tbl.ordering';
        $state['direction'] = 'ASC';
        $state['filter_published'] = 1;
        $state['filter_published_date'] = gmdate('Y-m-d H:i:00');
        $state['filter_enabled'] = 1;
        $state['filter_category'] = $app->getUserStateFromRequest( $ns . '.category', 'filter_category', '', 'int' );
        $prev_cat_id = $app->getUserState( $ns . 'prev_cat_id' );
        if( $prev_cat_id && $prev_cat_id != $state['filter_category'] ) // drop all filters
        {
            $app->setUserState( $ns . 'price_from', 0 );
            $app->setUserState( $ns . 'price_to', '' );
            $app->setUserState( $ns . 'attribute', '' );
            $app->setUserState( $ns . 'manufacturer', 0 );
            $app->setUserState( $ns . 'manufacturer_set', '' );
            $app->setUserState( $ns . 'attributeoptionname', array( ) );
            $app->setUserState( $ns . 'rating', '' );
        }

        $state['search'] = $app->getUserStateFromRequest( $ns . '.search', 'search', '', '' );
        $state['search_type'] = $app->getUserStateFromRequest( $ns . '.search_type', 'search_type', '', '' );
        $state['filter_price_from'] = $app->getUserStateFromRequest( $ns . 'price_from', 'filter_price_from', '0', 'int' );
        $state['filter_price_to'] = $app->getUserStateFromRequest( $ns . 'price_to', 'filter_price_to', '', '' );
        $state['filter_attribute_set'] = $app->getUserStateFromRequest( $ns . 'attribute', 'filter_attribute_set', '', '' );
        $state['filter_manufacturer'] = $app->getUserStateFromRequest( $ns . 'manufacturer', 'filter_manufacturer', '', 'int' );
        $state['filter_manufacturer_set'] = $app->getUserStateFromRequest( $ns . 'manufacturer_set', 'filter_manufacturer_set', '', '' );
        $state['filter_attributeoptionname'] = $app->getUserStateFromRequest( $ns . 'attributeoptionname', 'filter_attributeoptionname', array( ), 'array' );
        $state['filter_rating'] = $app->getUserStateFromRequest( $ns . 'rating', 'filter_rating', '', '' );
        $state['filter_sortby'] = $app->getUserStateFromRequest( $ns . 'sortby', 'filter_sortby', '', '' );
        $state['filter_dir'] = $app->getUserStateFromRequest( $ns . 'dir', 'filter_dir', 'asc', '' );

        if ( !$this->defines->get( 'display_out_of_stock' ) )
        {
            $state['filter_quantity_from'] = 1;
        }

        // search filters reset
        $state['filter'] = '';
        $state['filter_name'] = '';
        $state['filter_namedescription'] = '';
        $state['filter_sku'] = '';
        $state['filter_model'] = '';
        $state['filter_pao_names'] = array();
        $state['filter_pao_ids'] = array();
        $state['filter_pao_id_groups'] = array();

        // resettable filter
        $state['filter_pao_names'] = $app->getUserStateFromRequest( $ns . 'filter_pao_names', 'filter_pao_names', array(), 'array' );
        $state['filter_pao_ids'] = $app->getUserStateFromRequest( $ns . 'filter_pao_ids', 'filter_pao_ids', array(), 'array' );
        $state['filter_pao_id_groups'] = $app->getUserStateFromRequest( $ns . 'filter_pao_id_groups', 'filter_pao_id_groups', array(), 'array' );
        if ($app->input->getInt('reset') == 1) {
            $state['filter_pao_names'] = array();
            $app->setUserState( $ns . 'filter_pao_names', '' );
            $state['filter_pao_ids'] = array();
            $app->setUserState( $ns . 'filter_pao_ids', '' );
            $state['filter_pao_id_groups'] = array();
            $app->setUserState( $ns . 'filter_pao_id_groups', '' );
        }

        if ( strlen( $state['filter_sortby'] ) && Citruscart::getInstance( )->get( 'display_sort_by', '1' ) )
        {
            $state['order'] = $state['filter_sortby'];
            $state['direction'] = strtoupper($state['filter_dir']);
        }

        if ( $state['search'] )
        {
            $filter = $state['filter'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );

            // apply additional 'AND' filter if requested by module and unset filter state
            switch ( $state['search_type'] )
            {
                case "4":
                    $state['filter_name'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
                    $state['filter'] = '';
                    break;
                case "3":
                    $state['filter_namedescription'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
                    $state['filter'] = '';
                    break;
                case "2":
                    $state['filter_sku'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
                    $state['filter'] = '';
                    break;
                case "1":
                    $state['filter_model'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
                    $state['filter'] = '';
                    break;
                case "0":
                default:
                    break;
            }
        }
        else
        {
            $state['filter'] = '';
        }

        if ( $state['filter_category'] )
        {
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $cmodel = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
            $cmodel->setId( $state['filter_category'] );
            if ( $item = $cmodel->getItem( ) )
            {
                $state['category_name'] = $item->category_name;
            }

        }
        elseif ( !$state['search'] )
        {
            $state['filter_category'] = '0';
        }

        if ( $state['search'] && $state['filter_category'] == '1' )
        {
            $state['filter_category'] = '';
        }

        foreach ( @$state as $key => $value )
        {
            $model->setState( $key, $value );
        }

        return $state;
    }

    /**
     * Displays a product category
     *
     * (non-PHPdoc)
     * @see Citruscart/admin/CitruscartController#display($cachable)
     */
    function display($cachable=false, $urlparams = false )
    {
    	$input = JFactory::getApplication()->input;
        $input->set( 'view', $this->get( 'suffix' ) );
        $input->set( 'search', false );
        $view = $this->getView( $this->get( 'suffix' ), JFactory::getDocument( )->getType( ) );
        $model = $this->getModel( $this->get( 'suffix' ) );
        $state = $this->_setModelState();

        $session = JFactory::getSession();
        $app = JFactory::getApplication();
        $ns = $app->getName().'::'.'com.citruscart.products.state.'.$this->itemid;
        $session->set( $ns, $state );

        $app = JFactory::getApplication();
        $ns_general = $app->getName().'::'.'com.citruscart.products.state';
        $session->set( $ns_general, $state );

        // get the category we're looking at
        $filter_category = $model->getState( 'filter_category', $input->getString( 'filter_category' ) );
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $cmodel = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
        $cat = $cmodel->getTable( );
        $cat->load( $filter_category );

        // set the title based on the selected category
        $title = ( empty( $cat->category_name ) ) ? JText::_('COM_CITRUSCART_ALL_CATEGORIES') : JText::_( $cat->category_name );
        $level = ( !empty( $filter_category ) ) ? $filter_category : '1';

        // breadcrumb support
        $app = JFactory::getApplication( );
        $pathway = $app->getPathway( );

        // does this item have its own itemid?  if so, let joomla handle the breadcrumb,
        // otherwise, help it out a little bit
        $category_itemid = $this->router->category( $filter_category, true );
        if (!$category_itemid)
        {
            $category_itemid = $input->getInt( 'Itemid' );
            $items = Citruscart::getClass( "CitruscartHelperCategory", 'helpers.category' )->getPathName( $filter_category, 'array' );
            if ( !empty( $items ) )
            {
                // add the categories to the pathway
                Citruscart::getClass( "CitruscartHelperPathway", 'helpers.pathway' )->insertCategories( $items, $category_itemid );
            }
            // add the item being viewed to the pathway
            $pathway_values = $pathway->getPathway( );
            $pathway_names = Citruscart::getClass( "CitruscartHelperBase", 'helpers._base' )->getColumn( $pathway_values, 'name' );
            $pathway_links = Citruscart::getClass( "CitruscartHelperBase", 'helpers._base' )->getColumn( $pathway_values, 'link' );
            $cat_url = "index.php?Itemid=$category_itemid";
            if ( !in_array( $cat->category_name, $pathway_names ) )
            {
                $pathway->addItem( $title );
            }
        }
        $cat->itemid = $category_itemid;

        // get the category's sub categories
        $cmodel->setState( 'filter_level', $level );
        $cmodel->setState( 'filter_enabled', '1' );
        $cmodel->setState( 'order', 'tbl.lft' );
        $cmodel->setState( 'direction', 'ASC' );
        if ( $citems = $cmodel->getList( ) )
        {
            foreach ( $citems as $item )
            {
                $item->itemid_string = null;
                $item->itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $item->category_id, true );
                if (!empty($item->itemid)) {
                    $item->itemid_string = "&Itemid=".$item->itemid;
                }
            }
        }

        $this->_list = true; // if you want to display a slightly differen add-to-cart area for list view, check this boolean
        // get the products to be displayed in this category
        if ( $items = $model->getList( ) )
        {
           $input->set( 'page', 'category' ); // for "getCartButton"
            $this->display_cartbutton = Citruscart::getInstance( )->get( 'display_category_cartbuttons', '1' );
            foreach ( $items as $item )
            {
                $item->itemid_string = null;
                $item->itemid = (int) Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, null, true );
                if (!empty($item->itemid)) {
                    $item->itemid_string = "&Itemid=".$item->itemid;
                }
            }
        }

        if ( ( $model->getState( 'filter_price_from' ) > '0' ) || ( $model->getState( 'filter_price_to' ) > '0' ) )
        {
            $url = "index.php?option=com_citruscart&view=products&filter_category=$filter_category&filter_price_from=&filter_price_to=";
            $from = CitruscartHelperBase::currency( $model->getState( 'filter_price_from' ) );
            $to = ( $model->getState( 'filter_price_to' ) > 0 ) ? CitruscartHelperBase::currency( $model->getState( 'filter_price_to' ) )
            : JText::_('COM_CITRUSCART_MAXIMUM_PRICE');
            $view->assign( 'remove_pricefilter_url', $url );
            $view->assign( 'pricefilter_applied', true );
            $view->assign( 'filterprice_from', $from );
            $view->assign( 'filterprice_to', $to );
        }

        if(Citruscart::getInstance()->get('enable_product_compare', '1'))
        {
            Citruscart::load( "CitruscartHelperProductCompare", 'helpers.productcompare' );
            $compareitems = CitruscartHelperProductCompare::getComparedProducts();
            $view->assign( 'compareitems',  $compareitems);
        }

        $view->assign( 'level', $level );
        $view->assign( 'title', $title );
        $view->assign( 'cat', $cat );
        $view->assign( 'citems', $citems );
        $view->assign( 'items', $items );
        $view->set( '_doTask', true );
        $view->setModel( $model, true );

        // add the media/templates folder as a valid path for templates
        $view->addTemplatePath( Citruscart::getPath( 'categories_templates' ) );
        // but add back the template overrides folder to give it priority
        $template_overrides = JPATH_BASE . '/templates/' . $app->getTemplate( ) . '/html/com_citruscart/' . $view->getName( );
        $view->addTemplatePath( $template_overrides );

        // using a helper file, we determine the category's layout
        $layout = Citruscart::getClass( 'CitruscartHelperCategory', 'helpers.category' )->getLayout( $cat->category_id );
        $view->setLayout( $layout );

        $view->display( );
        $this->footer( );
        return;
    }

    /**
     * Displays a single product
     * (non-PHPdoc)
     * @see Citruscart/site/CitruscartController#view()
     */
    function view( )
    {
    	$session = JFactory::getSession();
    	$app = JFactory::getApplication();

    	$input = JFactory::getApplication()->input;
        $this->display_cartbutton = true;

        $input->set( 'view', $this->get( 'suffix' ) );
        $model = $this->getModel( $this->get( 'suffix' ) );
        $model->getId( );
        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );

        $user_id = JFactory::getUser( )->id;

        $filter_group = CitruscartHelperUser::getUserGroup( $user_id, $model->getId( ) );

        $model->setState( 'filter_group', $filter_group );
		$model->setState( 'product.qty', 1);
		$model->setState( 'user.id', $user_id );
        $row = $model->getItem( false, false, false ); // use the state

        $filter_category = $model->getState( 'filter_category', $input->getString( 'filter_category' ) );
        if ( empty( $filter_category ) )
        {
            $categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $row->product_id );
            if ( !empty( $categories ) )
            {
                $filter_category = $categories[0];
            }
        }
        $unpublished = false;



        if( $row->unpublish_date != JFactory::getDbo()->getNullDate() )
        {
            $unpublished = strtotime( $row->unpublish_date ) < time();
        }
        if( !$unpublished && $row->publish_date != JFactory::getDbo()->getNullDate() )
        {
            $unpublished = strtotime( $row->publish_date ) > time();
        }

      	if ( empty( $row->product_enabled ) || $unpublished )
        {
            $redirect = "index.php?option=com_citruscart&view=products&task=display&filter_category=" . $filter_category;
            $redirect = JRoute::_( $redirect, false );
            $this->message = JText::_('COM_CITRUSCART_CANNOT_VIEW_DISABLED_PRODUCT');
            $this->messagetype = 'notice';
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        Citruscart::load( 'CitruscartArticle', 'library.article' );
        $product_description = CitruscartArticle::fromString( $row->product_description );
        $product_description_short = CitruscartArticle::fromString( $row->product_description_short );

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $cmodel = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
        $cat = $cmodel->getTable( );
        $cat->load( $filter_category );

        // if product browsing enabled on detail pages, get surrounding items based on browsing state

        $ns = $app->getName().'::'.'com.citruscart.products.state';
        $session_state = $session->get( $ns );

        $surrounding = array();
        // Only do this if product browsing is enabled on product detail pages
        if ($this->defines->get('enable_product_detail_nav') && $session_state)
        {
            $products_model = $this->getModel( $this->get( 'suffix' ) );
            $products_model->emptyState();
            foreach ((array)$session_state as $key => $value )
            {
                $products_model->setState( $key, $value );
            }
            $surrounding = $products_model->getSurrounding( $model->getId() );
        }

        $view = $this->getView( $this->get( 'suffix' ), JFactory::getDocument( )->getType( ) );
        $view->set( '_doTask', true );
        $view->assign( 'row', $row );
        $view->assign( 'surrounding', $surrounding );

        // breadcrumb support
        $pathway = $app->getPathway( );

        // does this item have its own itemid?  if so, let joomla handle the breadcrumb,
        // otherwise, help it out a little bit
        $category_itemid = $input->getInt( 'Itemid', Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $filter_category, true ) );
        if (!$product_itemid = $this->router->findItemid(array('view'=>'products', 'task'=>'view', 'id'=>$row->product_id)))
        {
            $items = Citruscart::getClass( "CitruscartHelperCategory", 'helpers.category' )->getPathName( $filter_category, 'array' );
            if ( !empty( $items ) )
            {
                // add the categories to the pathway
                Citruscart::getClass( "CitruscartHelperPathway", 'helpers.pathway' )->insertCategories( $items, $category_itemid );
            }
            // add the item being viewed to the pathway
            $pathway->addItem( $row->product_name );
        }
        $cat->itemid = $category_itemid;
        $view->assign( 'cat', $cat );

        // Check If the inventroy is set then it will go for the inventory product quantities
        if ( $row->product_check_inventory )
        {
            $inventoryList = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getProductQuantities( $row->product_id );

            if ( !Citruscart::getInstance( )->get( 'display_out_of_stock' ) && empty( $inventoryList ) )
            {
                // redirect
                $redirect = "index.php?option=com_citruscart&view=products&task=display&filter_category=" . $filter_category;
                $redirect = JRoute::_( $redirect, false );
                $this->message = JText::_('COM_CITRUSCART_CANNOT_VIEW_PRODUCT');
                $this->messagetype = 'notice';
                $this->setRedirect( $redirect, $this->message, $this->messagetype );
                return;
            }

            // if there is no entry of product in the productquantities
            if ( count( $inventoryList ) == 0 )
            {
                $inventoryList[''] = '0';
            }
            $view->assign( 'inventoryList', $inventoryList );
        }

        $view->product_comments = $this->getComments( $view, $row->product_id );
        $view->files = $this->getFiles( $view, $row->product_id );
        $view->product_relations = $this->getRelationshipsHtml( $view, $row->product_id, 'relates' );
        $view->product_children = $this->getRelationshipsHtml( $view, $row->product_id, 'parent' );
        $view->product_requirements = $this->getRelationshipsHtml( $view, $row->product_id, 'requires' );
        $view->product_description = $product_description;
        $view->product_description_short = $product_description_short;
        $view->setModel( $model, true );

        // we know the product, set the meta info
        $doc = JFactory::getDocument();
        $doc->setTitle( str_replace( array("&apos;", "&amp;"), array("'", "&"), htmlspecialchars_decode( $row->product_name ) ) );
        $doc->setDescription( htmlspecialchars_decode( $product_description ) );

        // add the media/templates folder as a valid path for templates
        $view->addTemplatePath( Citruscart::getPath( 'products_templates' ) );
        // but add back the template overrides folder to give it priority
        $template_overrides = JPATH_BASE . '/templates/' . $app->getTemplate( ) . '/html/com_citruscart/' . $view->getName( );
        $view->addTemplatePath( $template_overrides );

        // using a helper file, we determine the product's layout
        $layout = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )
        ->getLayout( $row->product_id, array(
                'category_id' => $cat->category_id
        ) );
        $view->setLayout( $layout );

        JPluginHelper::importPlugin('citruscart');

        ob_start( );
        $app->triggerEvent( 'onBeforeDisplayProduct', array(
               $row->product_id
        ) );

        $view->assign( 'onBeforeDisplayProduct', ob_get_contents( ) );
        ob_end_clean( );

        ob_start( );
        $app->triggerEvent('onBeforeDisplayProductDescription',array());
        $view->assign( 'onBeforeDisplayProductDescription',  ob_get_contents( ));
        ob_end_clean( );

        ob_start( );
       	$app->triggerEvent( 'onAfterDisplayProduct', array(
                $row->product_id
        ) );
	    $view->assign( 'onAfterDisplayProduct', ob_get_contents( ) );
        ob_end_clean( );

        ob_start( );
        $html = $app->triggerEvent('onAfterDisplayProductDescription',array());
       	$view->assign( 'onAfterDisplayProductDescription',  $html[0]);
        ob_end_clean( );

        $view->display( );
        $this->footer( );
        return;
    }

    /**
     * Gets a product's add to cart section
     * formatted for display
     *
     * @param int $address_id
     * @return string html
     */
    function getAddToCart( $product_id, $values = array( ) )
    {
    	$input = JFactory::getApplication()->input;
        $layout = 'product_buy';

        Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
        if( isset( $values['layout'] ) ) {
            $layout = $values['layout'];
        }

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance('Products', 'CitruscartModel');
        $model->setId( $product_id );
		$user_id = JFactory::getUser()->id;

        $filter_group = CitruscartHelperUser::getUserGroup( $user_id, $product_id );
		$qty = $input->getInt( 'product_qty', 1 );
        $model->setState( 'filter_group', $filter_group );
		$model->setState( 'product.qty', $qty );
		$model->setState( 'user.id', $user_id );

        $row = $model->getItem( false, false, false );
        $buy_layout_override = $row->product_parameters->get('product_buy_layout_override');
        if (!empty($buy_layout_override))
        {
            $layout = $buy_layout_override;
        }

        $html = CitruscartHelperProduct::getCartButton( $product_id, $layout, $values, $this->_callback_js );

        return $html;
    }

    /**
     * Used whenever an attribute selection is changed,
     * to update the price and/or attribute selectlists
     *
     * @return unknown_type
     */
    function updateAddToCart( )
    {
    	$app = JFactory::getApplication();
    	$input = JFactory::getApplication()->input;
        $response = array( );
        $response['msg'] = '';
        $response['error'] = '';

        // get elements from post
        $elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $input->getString( 'elements', '') ) );

        // convert elements to array that can be binded
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
        $values = $helper->elementsToArray( $elements );

        // merge current elements with post
        $request_arr = $input->getArray($_REQUEST);

        unset($request_arr['elements'] );


       $input->set( 'elements', null );

        $values = array_merge( $values, $request_arr );

        $input->set(  'POST' ,$values);

        if ( empty( $values['product_id'] ) )
        {
            $values['product_id'] = $input->getInt( 'product_id', 0 );
        }

        // now get the summary
        $this->display_cartbutton = true;

        $html = $this->getAddToCart( $values['product_id'], $values );

		if( !empty( $this->_callback_js ) ) {
			$response['callback'] = '';
			if( is_array( $this->_callback_js ) ) {
				// add all calbacks and wrap them into eval function
				foreach( $this->_callback_js as $js ) {
					$response['callback'] .= 'eval( \''.$js.'\');';
				}
			} else { // only one string
				$response['callback'] = 'eval( \''.$this->_callback_js.'\');';
			}
		}

        $response['msg'] = $html;

        $paov_items = array();
        $paov_model = Citruscart::getClass('CitruscartModelProductAttributeOptionValues', 'models.productattributeoptionvalues');
        $paov_model->setState('filter_product', (int) $values['product_id'] );
        if (!empty($values['changed_attr'])) {
            $key = 'attribute_' . (int) $values['changed_attr'];
            $paov_model->setState('filter_option', (int) $values[$key] );
        }
        $paov_items = $paov_model->getList();

        $response['paov_items'] = $paov_items;
        $response['product_id'] = (int) $values['product_id'];

        // encode and echo (need to echo to send back to browser)
        echo json_encode( $response );
        $app->close();
    }

    /**
     * Gets a product's files list
     * formatted for display
     *
     * @param int $address_id
     * @return string html
     */
    function getFiles( $view, $product_id )
    {
        $html = '';

        // get the product's files
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductFiles', 'CitruscartModel' );
        $model->setState( 'filter_product', $product_id );
        $model->setState( 'filter_enabled', 1 );
        //$model->setState( 'filter_purchaserequired', 1 );
        $items = $model->getList( );

        // get the user's active subscriptions to this product, if possible
        $submodel = JModelLegacy::getInstance( 'Subscriptions', 'CitruscartModel' );
        $submodel->setState( 'filter_userid', JFactory::getUser( )->id );
        $submodel->setState( 'filter_productid', $product_id );
        $subs = $submodel->getList( );

        if ( !empty( $items ) )
        {
            // reconcile the list of files to the date the sub's files were last checked
            Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
            $subhelper = new CitruscartHelperSubscription( );
            $subhelper->reconcileFiles( $subs );

            Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
            $helper = CitruscartHelperBase::getInstance( 'ProductDownload', 'CitruscartHelper' );
            $filtered_items = $helper->filterRestricted( $items, JFactory::getUser( )->id );

            $view->setModel( $model, true );
            $product_file_data = new stdClass;
            $product_file_data->downloadItems = $filtered_items[0];
            $product_file_data->nondownloadItems = $filtered_items[1];
            $product_file_data->product_id = $product_id;
            $lyt = $view->getLayout();
            $view->setLayout( 'product_files' );
            $view->product_file_data = $product_file_data;

            ob_start( );
            echo $view->loadTemplate( null );
            $html = ob_get_contents( );
            ob_end_clean( );
            $view->setLayout( $lyt );
            unset( $view->product_file_data );
        }

        return $html;
    }

    /**
     * Gets a product's related items
     * formatted for display
     *
     * @param int $address_id
     * @return string html
     */
    function getRelationshipsHtml( $view, $product_id, $relation_type = 'relates' )
    {
    	$input=JFactory::getApplication()->input;
        $html = '';
        $validation = "";

        // get the list
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
        $model->setState( 'filter_relation', $relation_type );
        $user = JFactory::getUser( );
        $model->setState( 'filter_group', $relation_type );

        switch ( $relation_type )
        {
            case "requires":
                $model->setState( 'filter_product_from', $product_id );
                $check_quantity = false;
                $layout = 'product_requirements';
                break;
            case "parent":
            case "child":
            case "children":
                $model->setState( 'filter_product_from', $product_id );
                $check_quantity = true;
                $validation = "index.php?option=com_citruscart&view=products&task=validateChildren&format=raw";
                $layout = 'product_children';
                break;
            case "relates":
                $model->setState( 'filter_product', $product_id );
                $check_quantity = false;
                $layout = 'product_relations';
                break;
            default:
                return $html;
                break;
        }
        $query = $model->getQuery();
        $query->order( 'p_from.ordering ASC, p_to.ordering ASC' );

        if ( $items = $model->getList( ) )
        {
            $filter_category = $model->getState( 'filter_category', $input->getString( 'filter_category' ) );
            if ( empty( $filter_category ) )
            {
                $categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $product_id );
                if ( !empty( $categories ) )
                {
                    $filter_category = $categories[0];
                }
            }
            $userId = JFactory::getUser( )->id;
            $config = Citruscart::getInstance( );
            $show_tax = $config->get( 'display_prices_with_tax' );
            Citruscart::load('CitruscartHelperTax', 'helpers.tax');
            if ( $show_tax )
                $taxes = CitruscartHelperTax::calculateTax( $items, 2 );

            foreach ( $items as $key => $item )
            {
                if ( $check_quantity )
                {
                    // TODO Unset $items[$key] if
                    // this is out of stock &&
                    // check_inventory &&
                    // item for sale
                }

                if ( $item->product_id_from == $product_id )
                {
                    // display the _product_to
                    $item->product_id = $item->product_id_to;
                    $item->product_name = $item->product_name_to;
                    $item->product_model = $item->product_model_to;
                    $item->product_sku = $item->product_sku_to;
                    $item->product_price = $item->product_price_to;
                }
                else
                {
                    // display the _product_from
                    $item->product_id = $item->product_id_from;
                    $item->product_name = $item->product_name_from;
                    $item->product_model = $item->product_model_from;
                    $item->product_sku = $item->product_sku_from;
                    $item->product_price = $item->product_price_from;
                }

                $itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, $filter_category, true );
                $item->itemid = $input->getInt( 'Itemid', $itemid );
                $item->tax = 0;
                if ( $show_tax )
                {
                    $tax = $taxes->product_taxes[$item->product_id];
                    $item->taxtotal = $tax;
                    $item->tax = $tax;
                }
            }
        }
        else
        {
            return '';
        }
        $view->setModel( $model, true );
        $lyt = $view->getLayout();
        $view->setLayout( $layout );
        $product_relations = new stdClass;
        $product_relations->items = $items;
        $product_relations->product_id = $product_id;
        $product_relations->show = $product_id;
        $product_relations->filter_category = $filter_category;
        $product_relations->validation = $validation;
        $product_relations->show_tax = $show_tax;
        $view->product_relations_data = $product_relations;

        ob_start( );
        $view->display( );
        $html = ob_get_contents( );
        ob_end_clean( );
        unset( $view->product_relations_data );
        $view->setLayout( $lyt );

        return $html;
    }

    /**
     * downloads a file
     *
     * @return void
     */
    function downloadFile( )
    {
        $input= JFactory::getApplication()->input;
    	$user = JFactory::getUser( );
        $productfile_id = intval( $input->getInt( 'id',0) );
        $product_id = intval( $input->getInt( 'product_id',0) );
        $link = 'index.php?option=com_citruscart&controller=products&view=products&task=view&id=' . $product_id;

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance( 'ProductDownload', 'CitruscartHelper' );

        if ( !$canView = $helper->canDownload( $productfile_id, JFactory::getUser( )->id ) )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_NOT_AUTHORIZED_TO_DOWNLOAD_FILE');
            $this->setRedirect( $link, $this->message, $this->messagetype );
            return false;
        }
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        $productfile = JTable::getInstance( 'ProductFiles', 'CitruscartTable' );
        $productfile->load( $productfile_id );
        if ( empty( $productfile->productfile_id ) )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_INVALID_FILE');
            $this->setRedirect( $link, $this->message, $this->messagetype );
            return false;
        }

        // log and download
        Citruscart::load( 'CitruscartFile', 'library.file' );

        // Log the download
        $productfile->logDownload( $user->id );

        // After download complete it will update the productdownloads on the basis of the user

        // geting the ProductDownloadId to updated for which productdownload_max  is greater then 0
        $productToDownload = $helper->getProductDownloadInfo( $productfile->productfile_id, $user->id );
        ;

        if ( !empty( $productToDownload ) )
        {
            $productDownload = JTable::getInstance( 'ProductDownloads', 'CitruscartTable' );
            $productDownload->load( $productToDownload->productdownload_id );
            $productDownload->productdownload_max = $productDownload->productdownload_max - 1;
            if ( !$productDownload->save( ) )
            {
                // TODO in case product Download is not updating properly .
            }
        }

        if ( $downloadFile = CitruscartFile::download( $productfile->productfile_path ) )
        {
            $link = JRoute::_( $link, false );
            $this->setRedirect( $link );
        }
    }

    /**
     *
     * @return void
     */
    function search( )
    {
    	$input = JFactory::getApplication()->input;
    	$input->set( 'view', $this->get( 'suffix' ) );
        $input->set( 'layout', 'search' );
        $input->set( 'search', true );



        $model = $this->getModel( $this->get( 'suffix' ) );
        $this->_setModelState( );

        if ( !Citruscart::getInstance( )->get( 'display_out_of_stock' ) )
        {
            $model->setState( 'filter_quantity_from', '1' );
        }
        parent::display( );

        // TODO In the future, make "Redirect to Advanced Search from Search Module?" an option in Citruscart Config

        //        $query = array();
        //        // now that we have it, let's clean the post and redirect to the advanced search page
        //        // use the itemid from the request, so the user stays on the same menu item as they previously were on
        //
        //        $query['Itemid'] = JRequest::getInt('Itemid');
        //        if (empty($query['Itemid']))
        //        {
        //            // TODO Use Citruscart Router to get the item_id for a Citruscart shop link
            //            //$item_id = 0;
            //            //$query['Itemid'] = $item_id;
            //        }
            //
        //        $badchars = array('#','>','<','\\');
        //        $filter = trim(str_replace($badchars, '', JRequest::getString('filter', null, 'post')));
        //        $query['filter'] = $filter;
        //
        //        $query['view'] = 'search';
        //
        //	    $uri = JURI::getInstance();
        //        $uri->setQuery($query);
        //        $uri->setVar('option', 'com_citruscart');
        //
        //        $this->setRedirect(JRoute::_('index.php'.$uri->__toString(array('query', 'fragment')), false));
    }

    /**
     * Verifies the fields in a submitted form.  Uses the table's check() method.
     * Will often be overridden. Is expected to be called via Ajax
     *
     * @return unknown_type
     */
    function validate( )
    {
    	$input = JFactory::getApplication()->input;
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = new CitruscartHelperBase( );

        $response = array( );
        $response['msg'] = '';
        $response['error'] = '';

        // get elements from post
        $elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $input->getString( 'elements') ) );

        // validate it using table's ->check() method
        if ( empty( $elements ) )
        {
            // if it fails check, return message
            $response['error'] = '1';
            $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_COULD_NOT_PROCESS_FORM') );
            echo ( json_encode( $response ) );
            return;
        }

        if ( !Citruscart::getInstance( )->get( 'shop_enabled', '1' ) )
        {
            $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_SHOP_DISABLED') );
            $response['error'] = '1';
            echo ( json_encode( $response ) );
            return false;
        }

        // convert elements to array that can be binded
        $values = $helper->elementsToArray( $elements );
        $product_id = !empty( $values['product_id'] ) ? ( int ) $values['product_id'] : $input->getInt( 'product_id' );
        $product_qty = !empty( $values['product_qty'] ) ? ( int ) $values['product_qty'] : '1';

        $attributes = array( );
        foreach ( $values as $key => $value )
        {
            if ( substr( $key, 0, 10 ) == 'attribute_' )
            {
                $attributes[] = $value;
                if(  !( int )$value )
                {
                    $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_ALL_PRODUCT_ATTRIBUTES_REQUIRED') );
                    $response['error'] = '1';
                    echo ( json_encode( $response ) );
                    return false;
                }
            }
        }
        sort( $attributes );
        $attributes_csv = implode( ',', $attributes );

        // Integrity checks on quantity being added
        if ( $product_qty < 0 )
        {
            $product_qty = '1';
        }

        // using a helper file to determine the product's information related to inventory
        $availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity( $product_id, $attributes_csv );
        if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
        {
            $response['msg'] = $helper->generateMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
            $response['error'] = '1';
            echo ( json_encode( $response ) );
            return false;
        }

        $product = JTable::getInstance( 'Products', 'CitruscartTable' );
        $product->load( array(
                'product_id' => $product_id
        ) );

        // if product notforsale, fail
        if ( $product->product_notforsale )
        {
            $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE') );
            $response['error'] = '1';
            echo ( json_encode( $response ) );
            return false;
        }

        $user = JFactory::getUser( );
        $keynames = array( );
        $keynames['user_id'] = $user->id;
        if ( empty( $user->id ) )
        {
            $session = JFactory::getSession( );
            $keynames['session_id'] = $session->getId( );
        }
        $keynames['product_id'] = $product_id;

        $cartitem = JTable::getInstance( 'Carts', 'CitruscartTable' );
        $cartitem->load( $keynames );

        if ( $product->quantity_restriction )
        {
            $error = false;
            $min = $product->quantity_min;
            $max = $product->quantity_max;

            if ( $max )
            {
                $remaining = $max - $cartitem->product_qty;
                if ( $product_qty > $remaining )
                {
                    $error = true;
                    $msg = $helper
                    ->generateMessage(
                            JText::_('COM_CITRUSCART_YOU_HAVE_REACHED_THE_MAXIMUM_QUANTITY_YOU_CAN_ORDER_ANOTHER') . " " . $remaining );
                }
            }

            if ( $min )
            {
                if ( $product_qty < $min )
                {
                    $error = true;
                    $msg = $helper
                    ->generateMessage(
                            JText::_('COM_CITRUSCART_YOU_HAVE_NOT_REACHED_THE_MIMINUM_QUANTITY_YOU_HAVE_TO_ORDER_AT_LEAST') . " "
                            . $min );
                }
            }

            $remainder = 0;
            if (!empty($product->quantity_step)) {
                $remainder = ($product_qty % $product->quantity_step);
            }

            if (!empty($product->quantity_step) && !empty($remainder))
            {
                $error = true;
                $msg = $helper
                ->generateMessage(
                        JText::sprintf('COM_CITRUSCART_QUANTITY_MUST_BE_IN_INCREMENTS_OF_X_FOR_PRODUCT_Y', $product->quantity_step, $product->product_name)
                        );
            }

            if ( $error )
            {
                $response['msg'] = $msg;
                $response['error'] = '1';
                echo ( json_encode( $response ) );
                return false;
            }
        }

        // create cart object out of item properties
        $item = new JObject;
        $item->user_id = JFactory::getUser( )->id;
        $item->product_id = ( int ) $product_id;
        $item->product_qty = ( int ) $product_qty;
        $item->product_attributes = $attributes_csv;
        $item->vendor_id = '0'; // vendors only in enterprise version

        // no matter what, fire this validation plugin event for plugins that extend the checkout workflow
        $results = array( );
        $dispatcher = JDispatcher::getInstance( );
        $results = JFactory::getApplication()->triggerEvent( "onValidateAddToCart", array(
                $item, $values
        ) );

        for ( $i = 0; $i < count( $results ); $i++ )
        {
            $result = $results[$i];
            if ( !empty( $result->error ) )
            {
                Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
                $helper = CitruscartHelperBase::getInstance( );
                $response['msg'] = $helper->generateMessage( $result->message );
                $response['error'] = '1';
                echo ( json_encode( $response ) );
                return;
            }
            else
            {
                // if here, all is OK
                $response['error'] = '0';
            }
        }
        echo ( json_encode( $response ) );
        return;
    }

    /**
     * Verifies the fields in a submitted form.
     * Then adds the item to the users cart
     *
     * @return unknown_type
     */
    function addToCart( )
    {
    	$input  = JFactory::getApplication()->input;
        JSession::checkToken( ) or jexit( 'Invalid Token' );
        $product_id = $input->getInt( 'product_id' );
        $product_qty = $input->getInt( 'product_qty' );
        $filter_category = $input->getInt( 'filter_category' );

        Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
        $router = new CitruscartHelperRoute( );
        if ( !$itemid = $router->product( $product_id, $filter_category, true ) )
        {
            $itemid = $router->category( 1, true );
            if( !$itemid )
                $itemid = $input->getInt( 'Itemid', 0 );
        }

        // set the default redirect URL
        $redirect = "index.php?option=com_citruscart&view=products&task=view&id=$product_id&filter_category=$filter_category&Itemid=" . $itemid;
        $redirect = JRoute::_( $redirect, false );

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance( );
        if ( !Citruscart::getInstance( )->get( 'shop_enabled', '1' ) )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_SHOP_DISABLED');
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        // convert elements to array that can be binded
        $values = $input->getArray($_POST);
        if( isset( $values['elements'] ) ) // ajax call! -> decode elements and merge them with the request array
        {
            $elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $values['elements'] ) );
            unset( $values['elements'] );
            // convert elements to array that can be binded
            $values = array_merge( CitruscartHelperBase::elementsToArray( $elements ), $values );
            $intpu->set( $values, 'POST' );
        }

        $files = $input->files->get( 'files' );

        $attributes = array( );
        foreach ( $values as $key => $value )
        {
            if ( substr( $key, 0, 10 ) == 'attribute_' )
            {
                $attributes[] = $value;
            }
        }
        sort( $attributes );
        $attributes_csv = implode( ',', $attributes );

        // Integrity checks on quantity being added
        if ( $product_qty < 0 )
        {
            $product_qty = '1';
        }

        // using a helper file to determine the product's information related to inventory
        $availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity( $product_id, $attributes_csv );
        if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        // do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
        $product = JTable::getInstance( 'Products', 'CitruscartTable' );
        $product->load( array(
                'product_id' => $product_id
        ), true, false );

        // if product notforsale, fail
        if ( $product->product_notforsale )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE');
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        $user = JFactory::getUser( );
        $cart_id = $user->id;
        $id_type = "user_id";
        if ( empty( $user->id ) )
        {
            $session = JFactory::getSession( );
            $cart_id = $session->getId( );
            $id_type = "session";
        }

        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $carthelper = new CitruscartHelperCarts( );

        $cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );
        if ( $product->product_recurs && $cart_recurs )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_CART_ALREADY_RECURS');
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        if ( $product->product_recurs )
        {
            $product_qty = '1';
        }

        // create cart object out of item properties
        $item = new JObject;
        $item->user_id = JFactory::getUser( )->id;
        $item->product_id = ( int ) $product_id;
        $item->product_qty = ( int ) $product_qty;
        $item->product_attributes = $attributes_csv;
        $item->vendor_id = '0'; // vendors only in enterprise version

        // if ther is another product_url, put it into the cartitem_params, to allow custom redirect
        if ( array_key_exists( 'product_url', $values ) )
        {
            $params = new DSCParameter(trim(@$item->cartitem_params));
            $params->set( 'product_url', $values['product_url'] );
            $item->cartitem_params = trim( $params->__toString( ) );
        }

        // onAfterCreateItemForAddToCart: plugin can add values to the item before it is being validated /added
        // once the extra field(s) have been set, they will get automatically saved
        $dispatcher = JDispatcher::getInstance( );
        $results = JFactory::getApplication()->triggerEvent( "onAfterCreateItemForAddToCart", array(
                $item, $values, $files
        ) );
        foreach ( $results as $result )
        {
            foreach ( $result as $key => $value )
            {
                $item->set( $key, $value );
            }
        }

        // does the user/cart match all dependencies?
        $canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
        if ( !$canAddToCart )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError( );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        // no matter what, fire this validation plugin event for plugins that extend the checkout workflow
        $results = array( );
        $dispatcher = JDispatcher::getInstance( );
        $results = JFactory::getApplication()->triggerEvent( "onBeforeAddToCart", array(
                &$item, $values
        ) );

        for ( $i = 0; $i < count( $results ); $i++ )
        {
            $result = $results[$i];
            if ( !empty( $result->error ) )
            {
                $this->messagetype = 'notice';
                $this->message = $result->message;
                $this->setRedirect( $redirect, $this->message, $this->messagetype );
                return;
            }
        }

        // if here, add to cart

        // After login, session_id is changed by Joomla, so store this for reference
        $session = JFactory::getSession( );
        $session->set( 'old_sessionid', $session->getId( ) );

        // add the item to the cart
        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $cart_helper = new CitruscartHelperCarts( );
        $cartitem = $cart_helper->addItem( $item );

        // fire plugin event
        $dispatcher = JDispatcher::getInstance( );
        JFactory::getApplication()->triggerEvent( 'onAfterAddToCart', array(
                $cartitem, $values
        ) );

        // get the 'success' redirect url
        switch ( Citruscart::getInstance( )->get( 'addtocartaction', 'redirect' ) )
        {
            case "checkout":
                // if a base64_encoded url is present as return, use that as the return url
                // otherwise return == the product view page
                $returnUrl = base64_encode( $redirect );
                if ( $return_url = $input->getBase64( 'return') )
                {
                    $return_url = base64_decode( $return_url );
                    if ( JURI::isInternal( $return_url ) )
                    {
                        $returnUrl = base64_encode( $return_url );
                    }
                }
                // if a base64_encoded url is present as redirect, redirect there,
                // otherwise redirect to the checkout
                $itemid_checkout = $router->findItemid( array(
                        'view' => 'checkout'
                ) );

                $itemid_opc = $router->findItemid( array(
                        'view' => 'opc'
                ) );

                $checkout_view = "checkout";
                $itemid = null;
                if ($itemid_opc) {
                    $itemid = $itemid_opc;
                    $checkout_view = "opc";
                } elseif ($itemid_checkout) {
                    $itemid = $itemid_checkout;
                }

                if( !$itemid ) {
                    $itemid = $input->getInt( 'Itemid', 0 );
                }

                $redirect = JRoute::_( "index.php?option=com_citruscart&view=" . $checkout_view . "&Itemid=" . $itemid, false );
                if ( $redirect_url = $input->getBase64( 'redirect'))
                {
                    $redirect_url = base64_decode( $redirect_url );
                    if ( JURI::isInternal( $redirect_url ) )
                    {
                        $redirect = $redirect_url;
                    }
                }

                if ( strpos( $redirect, '?' ) === false )
                {
                    $redirect .= "?return=" . $returnUrl;
                }
                else
                {
                    $redirect .= "&return=" . $returnUrl;
                }

                break;
            case "0":
            case "none":
                // redirects back to product page
                break;
            case "samepage":
                // redirects back to the page it came from (category, content, etc)
                // Take only the url without the base domain (index.php?option.....)

                if ( $return_url = $input->getBase64( 'return') )
                {
                    $return_url = base64_decode( $return_url );
                    $uri = JURI::getInstance( );
                    $uri->parse( $return_url );
                    $redirect = $uri->__toString( array(
                            'path', 'query', 'fragment'
                    ) );
                    $redirect = JRoute::_( $redirect, false );
                }
                break;
            case "lightbox":
            case "redirect":
            default:
                // if a base64_encoded url is present as return, use that as the return url
                // otherwise return == the product view page
                $returnUrl = base64_encode( $redirect );
                if ( $return_url = $input->getBase64('return') )
                {
                    $return_url = base64_decode( $return_url );
                    if ( JURI::isInternal( $return_url ) )
                    {
                        $returnUrl = base64_encode( $return_url );
                    }
                }
                // if a base64_encoded url is present as redirect, redirect there,
                // otherwise redirect to the cart
                $itemid = $router->findItemid( array(
                        'view' => 'carts'
                ) );

                if( !$itemid )
                    $itemid = $input->getInt( 'Itemid', 0 );
                $redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=" . $itemid, false );
                if ( $redirect_url = $input->getBase64( 'redirect') )
                {
                    $redirect_url = base64_decode( $redirect_url );
                    if ( JURI::isInternal( $redirect_url ) )
                    {
                        $redirect = $redirect_url;
                    }
                }

                //$returnUrl = base64_encode( $redirect );
                //$itemid = $router->findItemid( array('view'=>'checkout') );
                //$redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$itemid, false );
                if ( strpos( $redirect, '?' ) === false )
                {
                    $redirect .= "?return=" . $returnUrl;
                }
                else
                {
                    $redirect .= "&return=" . $returnUrl;
                }

                break;
        }

        $this->messagetype = 'message';
        $this->message = JText::_('COM_CITRUSCART_ITEM_ADDED_TO_YOUR_CART');
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
        return;
    }

    /**
     * Gets all the product's user reviews
     * @param $product_id
     * @return unknown_type
     */
    function getComments( $view, $product_id )
    {
    	$input = JFactory::getApplication()->input;
        $html = '';

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'productcomments', 'CitruscartModel' );
        $selectsort = $input->getString( 'default_selectsort', '' );
        $model->setstate( 'order', $selectsort );
        $limitstart = $input->getInt( 'limitstart', 0 );
        $model->setId( $product_id );
        $model->setstate( 'limitstart', $limitstart );
        $model->setstate( 'filter_product', $product_id );
        $model->setstate( 'filter_enabled', '1' );
        $reviews = $model->getList( );

        $count = count( $reviews );

        $lyt = $view->getLayout();
        $view->setLayout( 'product_comments' );
        $view->setModel( $model, true );
        $comments_data = new stdClass;
        $comments_data->product_id = $product_id;
        $comments_data->count = $count;
        $comments_data->reviews = $reviews;           

        $user_id = JFactory::getUser( )->id;
        $productreview = CitruscartHelperProduct::getUserAndProductIdForReview( $product_id, $user_id );
        $purchase_enable = Citruscart::getInstance( )->get( 'purchase_leave_review_enable', '0' );
        $login_enable = Citruscart::getInstance( )->get( 'login_review_enable', '0' );
        $product_review_enable = Citruscart::getInstance( )->get( 'product_review_enable', '0' );

        $result = 1;
        if ( $product_review_enable == '1' )
        {
            $review_enable = 1;
        }
        else
        {
            $review_enable = 0;
        }
        if ( ( $login_enable == '1' ) )
        {
            if ( $user_id )
            {
                $order_enable = '1';

                if ( $purchase_enable == '1' )
                {
                    $orderexist = CitruscartHelperProduct::getOrders( $product_id );
                    if ( !$orderexist )
                    {
                        $order_enable = '0';

                    }
                }

                if ( ( $order_enable != '1' ) || !empty( $productreview ) )
                {
                    $result = 0;
                }
            }
            else
            {
                $result = 0;
            }
        }

        $comments_data->review_enable = $review_enable;
        $comments_data->result = $result;
        $comments_data->click = 'index.php?option=com_citruscart&controller=products&view=products&task=addReview';
        $comments_data->selectsort = $selectsort;
        $view->comments_data = $comments_data;
        $task = $model->getState( 'task' );
        $model->setState( 'task', 'product_comments' );
        ob_start( );
        $view->display( null );
        $html = ob_get_contents( );
        ob_end_clean( );
        $model->setState( 'task', $task );
        $view->setLayout( $lyt );

        return $html;
    }

    /**
     * Verifies the fields in a submitted form.  Uses the table's check() method.
     * Will often be overridden. Is expected to be called via Ajax
     *
     * @return unknown_type
     */
    function validateChildren( )
    {
        $input = JFactory::getApplication()->input;
    	$response = array( );
        $response['msg'] = '';
        $response['error'] = '';

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance( );

        // get elements from post
        $elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $input->getString( 'elements') ) );

        // validate it using table's ->check() method
        if ( empty( $elements ) )
        {
            // if it fails check, return message
            $response['error'] = '1';
            $response['msg'] = $helper->generateMessage( "Could not process form" );
            echo ( json_encode( $response ) );
            return;
        }

        if ( !Citruscart::getInstance( )->get( 'shop_enabled', '1' ) )
        {
            $response['msg'] = $helper->generateMessage( "Shop Disabled" );
            $response['error'] = '1';
            echo ( json_encode( $response ) );
            return false;
        }

        // convert elements to array that can be binded
        $values = $helper->elementsToArray( $elements );
        $attributes_csv = '';
        $product_id = !empty( $values['product_id'] ) ? ( int ) $values['product_id'] : $input->getInt( 'product_id' );
        $quantities = !empty( $values['quantities'] ) ? $values['quantities'] : array( );

        $items = array( ); // this will collect the items to add to the cart
        $attributes_csv = '';

        $user = JFactory::getUser( );
        $cart_id = $user->id;
        $id_type = "user_id";
        if ( empty( $user->id ) )
        {
            $session = JFactory::getSession( );
            $cart_id = $session->getId( );
            $id_type = "session";
        }

        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $carthelper = new CitruscartHelperCarts( );

        $cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );

        // TODO get the children
        // loop thru each child,
        // get the list
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
        $model->setState( 'filter_product', $product_id );
        $model->setState( 'filter_relation', 'parent' );
        if ( $children = $model->getList( ) )
        {
            foreach ( $children as $child )
            {
                $product_qty = $quantities[$child->product_id_to];

                // Integrity checks on quantity being added
                if ( $product_qty < 0 )
                {
                    $product_qty = '1';
                }

                // using a helper file to determine the product's information related to inventory
                $availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )
                ->getAvailableQuantity( $child->product_id_to, $attributes_csv );
                if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
                {
                    $response['msg'] = $helper
                    ->generateMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
                    $response['error'] = '1';
                    echo ( json_encode( $response ) );
                    return false;
                }

                // do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
                $product = JTable::getInstance( 'Products', 'CitruscartTable' );
                $product->load( array(
                        'product_id' => $child->product_id_to
                ) );

                // if product notforsale, fail
                if ( $product->product_notforsale )
                {
                    $response['msg'] = $helper->generateMessage( "Product Not For Sale" );
                    $response['error'] = '1';
                    echo ( json_encode( $response ) );
                    return false;
                }

                if ( $product->product_recurs && $cart_recurs )
                {
                    $response['msg'] = $helper->generateMessage( "Cart Already Recurs" );
                    $response['error'] = '1';
                    echo ( json_encode( $response ) );
                    return false;
                }

                if ( $product->product_recurs )
                {
                    $product_qty = '1';
                }

                if ( $product->quantity_restriction )
                {
                    $min = $product->quantity_min;
                    $max = $product->quantity_max;

                    if ( $max )
                    {
                        $user = JFactory::getUser( );
                        $keynames = array( );
                        $keynames['user_id'] = $user->id;
                        if ( empty( $user->id ) )
                        {
                            $session = JFactory::getSession( );
                            $keynames['session_id'] = $session->getId( );
                        }
                        $keynames['product_id'] = $product_id;

                        $cartitem = JTable::getInstance( 'Carts', 'CitruscartTable' );
                        $cartitem->load( $keynames );

                        $remaining = $max - $cartitem->product_qty;
                        if ( $product_qty > $remaining )
                        {
                            $response['error'] = '1';
                            $response['msg'] = $helper
                            ->generateMessage(
                                    JText::_('COM_CITRUSCART_YOU_HAVE_REACHED_THE_MAXIMUM_QUANTITY_YOU_CAN_ORDER_ANOTHER') . " " . $remaining );
                            echo ( json_encode( $response ) );
                            return false;
                        }
                    }

                    if ( $min )
                    {
                        if ( $product_qty < $min )
                        {
                            $response['error'] = '1';
                            $response['msg'] = $helper
                            ->generateMessage(
                                    JText::_('COM_CITRUSCART_YOU_HAVE_NOT_REACHED_THE_MIMINUM_QUANTITY_YOU_HAVE_TO_ORDER_AT_LEAST') . " "
                                    . $min );
                            echo ( json_encode( $response ) );
                            return false;
                        }
                    }

                    $remainder = 0;
                    if (!empty($product->quantity_step)) {
                        $remainder = ($product_qty % $product->quantity_step);
                    }

                    if (!empty($product->quantity_step) && !empty($remainder))
                    {
                        $response['error'] = '1';
                        $response['msg'] = $helper
                        ->generateMessage(
                                JText::sprintf('COM_CITRUSCART_QUANTITY_MUST_BE_IN_INCREMENTS_OF_X_FOR_PRODUCT_Y', $product->quantity_step, $product->product_name)
                        );
                        echo ( json_encode( $response ) );
                        return false;
                    }
                }

                // create cart object out of item properties
                $item = new JObject;
                $item->user_id = JFactory::getUser( )->id;
                $item->product_id = ( int ) $child->product_id_to;
                $item->product_qty = ( int ) $product_qty;
                $item->product_attributes = $attributes_csv;
                $item->vendor_id = '0'; // vendors only in enterprise version

                // does the user/cart match all dependencies?
                $canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
                if ( !$canAddToCart )
                {
                    $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError( ) );
                    $response['error'] = '1';
                    echo ( json_encode( $response ) );
                    return false;
                }

                // no matter what, fire this validation plugin event for plugins that extend the checkout workflow
                $results = array( );
                $dispatcher = JDispatcher::getInstance( );
                $results = JFactory::getApplication()->triggerEvent( "onValidateAddToCart", array(
                        $item, $values
                ) );

                for ( $i = 0; $i < count( $results ); $i++ )
                {
                    $result = $results[$i];
                    if ( !empty( $result->error ) )
                    {
                        $response['msg'] = $helper->generateMessage( $result->message );
                        $response['error'] = '1';
                        echo ( json_encode( $response ) );
                        return false;
                    }
                }

                // if here, add to cart
                $items[] = $item;
            }
        }

        if ( !empty( $items ) )
        {
            $response['error'] = '0';
        }
        else
        {
            $response['msg'] = $helper->generateMessage( "No Items Passed Validity Check" );
            $response['error'] = '1';
        }

        echo ( json_encode( $response ) );
        return;
    }

    /**
     * Verifies the fields in a submitted form.
     * Then adds the item to the users cart
     *
     * @return unknown_type
     */
    function addChildrenToCart( )
    {
    	$input = JFactory::getApplication()->input;
        JSession::checkToken( ) or jexit( 'Invalid Token' );
        $product_id = $input->getInt( 'product_id' );
        $quantities = $input->get( 'quantities', array(
                0
        ), 'request', 'array' );
        $filter_category = $input->getInt( 'filter_category' );

        Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
        $router = new CitruscartHelperRoute( );
        if ( !$itemid = $router->product( $product_id, $filter_category, true ) )
        {
            $itemid = $router->category( 1, true );
        }

        // set the default redirect URL
        $redirect = "index.php?option=com_citruscart&view=products&task=view&id=$product_id&filter_category=$filter_category&Itemid=" . $itemid;
        $redirect = JRoute::_( $redirect, false );

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance( );
        if ( !Citruscart::getInstance( )->get( 'shop_enabled', '1' ) )
        {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_CITRUSCART_SHOP_DISABLED');
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        $items = array( ); // this will collect the items to add to the cart

        // convert elements to array that can be binded
        $values = $input->getArray( $_POST);
        $attributes_csv = '';

        $user = JFactory::getUser( );
        $cart_id = $user->id;
        $id_type = "user_id";
        if ( empty( $user->id ) )
        {
            $session = JFactory::getSession( );
            $cart_id = $session->getId( );
            $id_type = "session";
        }

        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $carthelper = new CitruscartHelperCarts( );

        $cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );

        // TODO get the children
        // loop thru each child,
        // get the list
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
        $model->setState( 'filter_product', $product_id );
        $model->setState( 'filter_relation', 'parent' );
        if ( $children = $model->getList( ) )
        {
            foreach ( $children as $child )
            {
                $product_qty = $quantities[$child->product_id_to];

                // Integrity checks on quantity being added
                if ( $product_qty < 0 )
                {
                    $product_qty = '1';
                }

                if ( !$product_qty ) // product quantity is zero -> skip this product
                    continue;

                // using a helper file to determine the product's information related to inventory
                $availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )
                ->getAvailableQuantity( $child->product_id_to, $attributes_csv );
                if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
                {
                    $this->messagetype = 'notice';
                    $this->message = JText::_( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
                    $this->setRedirect( $redirect, $this->message, $this->messagetype );
                    return;
                }

                // do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
                $product = JTable::getInstance( 'Products', 'CitruscartTable' );
                $product->load( array(
                        'product_id' => $child->product_id_to
                ) );

                // if product notforsale, fail
                if ( $product->product_notforsale )
                {
                    $this->messagetype = 'notice';
                    $this->message = JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE');
                    $this->setRedirect( $redirect, $this->message, $this->messagetype );
                    return;
                }

                if ( $product->product_recurs && $cart_recurs )
                {
                    $this->messagetype = 'notice';
                    $this->message = JText::_('COM_CITRUSCART_CART_ALREADY_RECURS');
                    $this->setRedirect( $redirect, $this->message, $this->messagetype );
                    return;
                }

                if ( $product->product_recurs )
                {
                    $product_qty = '1';
                }

                // create cart object out of item properties
                $item = new JObject;
                $item->user_id = JFactory::getUser( )->id;
                $item->product_id = ( int ) $child->product_id_to;
                $item->product_qty = ( int ) $product_qty;
                $item->product_attributes = $attributes_csv;
                $item->vendor_id = '0'; // vendors only in enterprise version

                // does the user/cart match all dependencies?
                $canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
                if ( !$canAddToCart )
                {
                    $this->messagetype = 'notice';
                    $this->message = JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError( );
                    $this->setRedirect( $redirect, $this->message, $this->messagetype );
                    return;
                }

                // no matter what, fire this validation plugin event for plugins that extend the checkout workflow
                $results = array( );
                $dispatcher = JDispatcher::getInstance( );
                $results = JFactory::getApplication()->triggerEvent( "onBeforeAddToCart", array(
                        $item, $values
                ) );

                for ( $i = 0; $i < count( $results ); $i++ )
                {
                    $result = $results[$i];
                    if ( !empty( $result->error ) )
                    {
                        $this->messagetype = 'notice';
                        $this->message = $result->message;
                        $this->setRedirect( $redirect, $this->message, $this->messagetype );
                        return;
                    }
                }

                // if here, add to cart
                $items[] = $item;
            }
        }

        if ( !empty( $items ) )
        {
            // add the items to the cart
            Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
            CitruscartHelperCarts::updateCart( $items );

            // fire plugin event
            $dispatcher = JDispatcher::getInstance( );
            JFactory::getApplication()->triggerEvent( 'onAfterAddToCart', array(
                    $items, $values
            ) );

            $this->messagetype = 'message';
            $this->message = JText::_('COM_CITRUSCART_ITEMS_ADDED_TO_YOUR_CART');
        }

        // After login, session_id is changed by Joomla, so store this for reference
        $session = JFactory::getSession( );
        $session->set( 'old_sessionid', $session->getId( ) );

        // get the 'success' redirect url
        // TODO Enable redirect via base64_encoded urls?
        switch ( Citruscart::getInstance( )->get( 'addtocartaction', 'redirect' ) )
        {
            case "redirect":
                $returnUrl = base64_encode( $redirect );
                $itemid = $router->findItemid( array(
                        'view' => 'checkout'
                ) );
                $redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=" . $itemid, false );
                if ( strpos( $redirect, '?' ) === false )
                {
                    $redirect .= "?return=" . $returnUrl;
                }
                else
                {
                    $redirect .= "&return=" . $returnUrl;
                }
                break;
            case "0":
            case "none":
                break;
            case "lightbox":
            default:
                // TODO Figure out how to get the lightbox to display even after a redirect
                break;
        }

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
        return;
    }

    /**
     * Verifies the fields in a submitted form. Is expected to be called via Ajax
     *
     * @return unknown_type
     */
    function validateReview( )
    {
    	$input = JFactory::getApplication()->input;
        $response = array( );
        $response['msg'] = '';
        $response['error'] = '';
        $errors = array( );

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
        $helper = CitruscartHelperBase::getInstance( );
        $user = JFactory::getUser( );

        // get elements from post
        $elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $input->getString( 'elements') ) );

        // validate it using table's ->check() method
        if ( empty( $elements ) )
        {
            // if it fails check, return message
            $response['error'] = '1';
            $response['msg'] = $helper->generateMessage( "Could not process form" );
            echo ( json_encode( $response ) );
            return;
        }

        if ( !Citruscart::getInstance( )->get( 'shop_enabled', '1' ) )
        {
            $response['msg'] = $helper->generateMessage( "Shop Disabled" );
            $response['error'] = '1';
            echo ( json_encode( $response ) );
            return false;
        }

        // convert elements to array that can be binded
        $values = CitruscartHelperBase::elementsToArray( $elements );
        if ( !$user->id )
        {
            if ( empty( $values['user_name'] ) )
            {
                $errors[] = '<li>' . JText::_('COM_CITRUSCART_NAME_FIELD_IS_REQUIRED') . '</li>';
            }

            jimport( 'joomla.mail.helper' );
            if ( !JMailHelper::isEmailAddress( $values['user_email'] ) )
            {
                $errors[] = '<li>' . JText::_('COM_CITRUSCART_PLEASE_ENTER_A_CORRECT_EMAIL_ADDRESS') . '</li>';
            }

            if ( in_array( $values['user_email'], CitruscartHelperProduct::getUserEmailForReview( $values['product_id'] ) ) )
            {
                $errors[] = '<li>' . JText::_('COM_CITRUSCART_YOU_ALREADY_SUBMITTED_A_REVIEW_CAN_ONLY_SUBMIT_REVIEW_ONCE') . '</li>';
            }
        }
        else
        {
            if ( in_array( $user->email, CitruscartHelperProduct::getUserEmailForReview( $values['product_id'] ) ) )
            {
                $errors[] = '<li>' . JText::_('COM_CITRUSCART_YOU_ALREADY_SUBMITTED_A_REVIEW_CAN_ONLY_SUBMIT_REVIEW_ONCE') . '</li>';
            }
        }

        if ( count( $errors ) ) // there were errors => stop here
        {
            $response['error'] = 1;
            $response['msg'] = $helper->generateMessage( implode( "\n", $errors ), false );
            echo ( json_encode( $response ) );
            return;
        }

        if ( empty( $values['productcomment_rating'] ) )
        {
            $errors[] = '<li>' . JText::_('COM_CITRUSCART_RATING_IS_REQUIRED') . '</li>';
        }

        if ( empty( $values['productcomment_text'] ) )
        {
            $errors[] = '<li>' . JText::_('COM_CITRUSCART_COMMENT_FIELD_IS_REQUIRED') . '</li>';
        }

        if ( count( $errors ) ) // there were erros
        {
            $response['error'] = 1;
            $response['msg'] = $helper->generateMessage( implode( "\n", $errors ), false );
        }

        echo ( json_encode( $response ) );
        return;
    }

    /**
     * Add review
     *
     */
    function addReview( )
    {
    	$input = JFactory::getApplication()->input;
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
        $productreviews = JTable::getInstance( 'productcomments', 'CitruscartTable' );
        $post = $input->getArray( $_POST );
                      
        $product_id = $post['product_id'];
        $Itemid = $post['Itemid'];
        $user = JFactory::getUser( );
        $valid = true;
        $this->messagetype = 'message';
        
        $productcomment = CitruscartHelperProduct::strip_html_tags($post['productcomment_text']);

        //set in case validation fails
        $linkAdd = '';
        $linkAdd .= '&rn=' . base64_encode( $post['user_name'] );
        $linkAdd .= '&re=' . base64_encode( $post['user_email'] );
        $linkAdd .= '&rc=' . base64_encode( $productcomment );
                
        if ( !$user->id )
        {
            if ( empty( $post['user_name'] ) && $valid )
            {
                $valid = false;
                $this->message = JText::_('COM_CITRUSCART_NAME_FIELD_IS_REQUIRED');
                $this->messagetype = 'notice';
            }

            jimport( 'joomla.mail.helper' );
            if ( !JMailHelper::isEmailAddress( $post['user_email'] ) && $valid )
            {
                $valid = false;
                $this->message = JText::_('COM_CITRUSCART_PLEASE_ENTER_A_CORRECT_EMAIL_ADDRESS');
                $this->messagetype = 'notice';
            }

            if ( in_array( $post['user_email'], CitruscartHelperProduct::getUserEmailForReview( $post['product_id'] ) ) && $valid )
            {
                $valid = false;
                $this->message = JText::_('COM_CITRUSCART_YOU_ALREADY_SUBMITTED_A_REVIEW_CAN_ONLY_SUBMIT_REVIEW_ONCE');
                $this->messagetype = 'notice';
            }
        }
        else
        {
            if ( in_array( $user->email, CitruscartHelperProduct::getUserEmailForReview( $post['product_id'] ) ) && $valid )
            {
                $valid = false;
                $this->message = JText::_('COM_CITRUSCART_YOU_ALREADY_SUBMITTED_A_REVIEW_CAN_ONLY_SUBMIT_REVIEW_ONCE');
                $this->messagetype = 'notice';
            }
        }

        if ( empty( $post['productcomment_rating'] ) && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_RATING_IS_REQUIRED');
            $this->messagetype = 'notice';
        }

        if ( empty( $productcomment ) && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_COMMENT_FIELD_IS_REQUIRED');
            $this->messagetype = 'notice';
        }

        $captcha = true;
        if ( Citruscart::getInstance( )->get( 'use_captcha', '0' ) && $valid )
        {
            $privatekey = "6LcAcbwSAAAAANZOTZWYzYWRULBU_S--368ld2Fb";
            $captcha = false;

            if ( $_POST["recaptcha_response_field"] )
            {
                Citruscart::load( 'CitruscartRecaptcha', 'library.recaptcha' );
                $recaptcha = new CitruscartRecaptcha( );
                $resp = $recaptcha
                ->recaptcha_check_answer( $privatekey, $_SERVER["REMOTE_ADDR"], $post['recaptcha_challenge_field'],
                        $post['recaptcha_response_field'] );
                if ( $resp->is_valid )
                {
                    $captcha = true;
                }
            }
        }

        if ( !$captcha && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_INCORRECT_CAPTCHA');
            $this->messagetype = 'notice';
        }

        if ( $valid )
        {
            $date = JFactory::getDate( );
                       
            $productreviews->bind( $post );
            $productreviews->created_date = $date->toSql( );
            $productreviews->productcomment_enabled = Citruscart::getInstance( )->get( 'product_reviews_autoapprove', '0' );

            if ( !$productreviews->save( ) )
            {
                $this->message = JText::_('COM_CITRUSCART_UNABLE_TO_SAVE_REVIEW') . " :: " . $productreviews->getError( );
                $this->messagetype = 'notice';
            }
            else
            {
                $dispatcher = JDispatcher::getInstance( );
                JFactory::getApplication()->triggerEvent( 'onAfterSaveProductComments', array(
                        $productreviews
                ) );
                $this->message = JText::_('COM_CITRUSCART_SUCCESSFULLY_SUBMITTED_REVIEW');

                //successful
                $linkAdd = '';
            }
        }
        $redirect = "index.php?option=com_citruscart&view=products&task=view&id=" . $product_id . $linkAdd . "&Itemid=" . $Itemid;
        $redirect = JRoute::_( $redirect );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    /**
     * Adding helpfulness of review
     *
     */
    function reviewHelpfullness( )
    {
    	$input = JFactory::getApplication()->input;
        $user_id = JFactory::getUser( )->id;
        $Itemid = $input->getInt( 'Itemid', 0);
        $id = $input->getInt( 'product_id', 0 );
        $url = "index.php?option=com_citruscart&view=products&task=view&Itemid=" . $Itemid . "&id=" . $id;

        if ( $user_id )
        {
            $productcomment_id = $input->getInt( 'productcomment_id', 0 );
            Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
            $producthelper = new CitruscartHelperProduct( );
            JTable::addIncludePath( JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_citruscart' .DIRECTORY_SEPARATOR. 'tables' );
            $productcomment = JTable::getInstance( 'productcomments', 'CitruscartTable' );
            $productcomment->load( $productcomment_id );

            $helpful_votes_total = $productcomment->helpful_votes_total;
            $helpful_votes_total = $helpful_votes_total + 1;
            $helpfulness = $input->getInt( 'helpfulness', '' );
            if ( $helpfulness == 1 )
            {
                $helpful_vote = $productcomment->helpful_votes;
                $helpful_vote_new = $helpful_vote + 1;
                $productcomment->helpful_votes = $helpful_vote_new;
            }
            $productcomment->helpful_votes_total = $helpful_votes_total;

            $report = $input->getInt( 'report', '' );
            if ( $report == 1 )
            {
                $productcomment->reported_count = $productcomment->reported_count + 1;
            }

            $help = array( );
            $help['productcomment_id'] = $productcomment_id;
            $help['helpful'] = $helpfulness;
            $help['user_id'] = $user_id;
            $help['reported'] = $report;
            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $reviewhelpfulness = JTable::getInstance( 'ProductCommentsHelpfulness', 'CitruscartTable' );
            $reviewhelpfulness->load( array(
                    'user_id' => $user_id, 'productcomment_id' => $productcomment_id
            ) );

            $application = JFactory::getApplication( );
            if ( $report == 1 && !empty( $reviewhelpfulness->productcommentshelpfulness_id ) && empty( $reviewhelpfulness->reported ) )
            {
                $reviewhelpfulness->reported = 1;
                $reviewhelpfulness->save( );

                $productcomment->save( );
                $application->enqueueMessage( JText::sprintf( "COM_CITRUSCART_THANKS_FOR_REPORTING_THIS_REVIEW" ) );
                $application->redirect( $url );
                return;
            }

            if ( $report )
            {
                $application->enqueueMessage( JText::sprintf( "COM_CITRUSCART_YOU_ALREADY_REPORTED_THIS_REVIEW" ) );
                $application->redirect( $url );
            }

            $reviewhelpfulness->bind( $help );
            if ( !empty( $reviewhelpfulness->productcommentshelpfulness_id ) )
            {
                $application->enqueueMessage( JText::sprintf( "COM_CITRUSCART_YOU_HAVE_ALREADY_GIVEN_YOUR_FEEDBACK_ON_THIS_REVIEW" ) );
                $application->redirect( $url );
                return;
            }
            else
            {
                $reviewhelpfulness->save( );
                $productcomment->save( );
                $application->enqueueMessage( JText::sprintf( "COM_CITRUSCART_THANKS_FOR_YOUR_FEEDBACK_ON_THIS_COMMENT" ) );
                $application->redirect( $url );
                return;
            }
        }
        else
        {
            $redirect = "index.php?option=com_user&view=login&return=" . base64_encode( $url );
            $redirect = JRoute::_( $redirect, false );
            JFactory::getApplication( )->redirect( $redirect );
            return;
        }
    }

    /**
     * Displays a ask question form
     * (non-PHPdoc)
     * @see Citruscart/site/CitruscartController#askQuestion()
     */
    function askQuestion( )
    {
    	$input = JFactory::getApplication()->input;
        $input->set( 'view', $this->get( 'suffix' ) );
        $model = $this->getModel( $this->get( 'suffix' ) );
        $view = $this->getView( $this->get( 'suffix' ), JFactory::getDocument( )->getType( ) );
        $view->set( '_doTask', true );

        $view->setModel( $model, true );
        $view->setLayout( 'form_askquestion' );
        $view->display( );
        $this->footer( );
        return;
    }

    function sendAskedQuestion( )
    {
    	$input = JFactory::getApplication()->input;
        $config = Citruscart::getInstance( );
        $post = $input->getArray($_POST);
        $valid = true;
        $this->messagetype = 'message';
        $this->message = '';
        $add_link = '';

        $json=array();
        if (empty( $post['sender_name'] ) && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_NAME_FIELD_IS_REQUIRED');
            $this->messagetype = 'notice';
            $json['error']['sender_name'] = JText::_('COM_CITRUSCART_NAME_FIELD_IS_REQUIRED');
        }

        jimport( 'joomla.mail.helper' );
        if ( !$json && !JMailHelper::isEmailAddress( $post['sender_mail'] ) && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_PLEASE_ENTER_A_CORRECT_EMAIL_ADDRESS');
            $this->messagetype = 'notice';
            $json['error']['sender_mail'] = JText::_('COM_CITRUSCART_PLEASE_ENTER_A_CORRECT_EMAIL_ADDRESS');
            $add_link .= "&sender_name={$post['sender_name']}";
            $add_link .= !empty( $post['sender_message'] ) ? "&sender_message={$post['sender_message']}" : '';

        }

        if (!$json && empty( $post['sender_message'] ) && $valid )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_MESSAGE_FIELD_IS_REQUIRED');
            $this->messagetype = 'notice';

           // $json['error']['message'] = JText::_('COM_CITRUSCART_MESSAGE_FIELD_IS_REQUIRED');
            $add_link .= "&sender_name={$post['sender_name']}&sender_mail={$post['sender_mail']}";
        }

        //captcha checking
        $captcha = true;
        if ( ( $config->get( 'ask_question_showcaptcha', '1' ) == 1 ) && $valid )
        {
            $privatekey = "6LcAcbwSAAAAANZOTZWYzYWRULBU_S--368ld2Fb";
            $captcha = false;

            if ( $_POST["recaptcha_response_field"] )
            {
                Citruscart::load( 'CitruscartRecaptcha', 'library.recaptcha' );
                $recaptcha = new CitruscartRecaptcha( );
                $resp = $recaptcha
                ->recaptcha_check_answer( $privatekey, $_SERVER["REMOTE_ADDR"], $post['recaptcha_challenge_field'],
                        $post['recaptcha_response_field'] );
                if ( $resp->is_valid )
                {
                    $captcha = true;
                }
            }

        }
        if ( !$json &&  !$captcha )
        {
            $valid = false;
            $this->message = JText::_('COM_CITRUSCART_INCORRECT_CAPTCHA');
            $json['error']['captcha'] =JText::_('COM_CITRUSCART_INCORRECT_CAPTCHA');
            $this->messagetype = 'notice';
            $add_link .= "&sender_name={$post['sender_name']}&sender_mail={$post['sender_mail']}&sender_message={$post['sender_message']}";
        }

        if ( $valid )
        {
            $mainframe = JFactory::getApplication( );
            $sendObject = new JObject( );
            $sendObject->mailfrom = $post['sender_mail'];
            $sendObject->namefrom = $post['sender_name'];
            $sendObject->mailto = $config->get( 'emails_defaultemail', $mainframe->getCfg( 'mailfrom' ) );
            $sendObject->body = $post['sender_message'];

            //get product info
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            $model->setId( $post['product_id'] );
            $sendObject->item = $model->getItem( );

            Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
            $helper = CitruscartHelperBase::getInstance( 'Email' );
            if ( $send = $helper->sendEmailToAskQuestionOnProduct( $sendObject ) )
            {
                $this->message = JText::_('COM_CITRUSCART_MESSAGE_SUCCESSFULLY_SENT');
            }
            else
            {
                $this->message = JText::_('COM_CITRUSCART_ERROR_IN_SENDING_MESSAGE');
                $this->messagetype = 'notice';
            }
            if ( Citruscart::getInstance( )->get( 'ask_question_modal', '1' ) )
            {
                $url = "index.php?option=com_citruscart&view=products&task=askquestion&id={$post['product_id']}&tmpl=component&return=" . $post['return']
                . $add_link . "&success=1";
                $redirect = JRoute::_( $url );
            }
            else
            {
                $redirect = JRoute::_( base64_decode( $post['return'] ) );
            }

        }
        else
        {
            $url = "index.php?option=com_citruscart&view=products&task=askquestion&id={$post['product_id']}&tmpl=component&return=" . $post['return']
            . $add_link;
            $redirect = JRoute::_( $url );
        }
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    public function addToWishlist()
    {

    	$app = JFactory::getApplication();
    	$input= $app->input;
        $response = new stdClass();
        $response->html = '';
        $response->error = false;

        // verify form submitted by user
        JSession::checkToken( ) or jexit( 'Invalid Token' );

        $product_id = $input->getInt( 'product_id' ,0);

        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        $product = JTable::getInstance( 'Products', 'CitruscartTable' );
        $product->load( $product_id, true, false );

        if (empty($product->product_id))
        {
            $response->html = JText::_('COM_CITRUSCART_INVALID_PRODUCT');
            $response->error = true;
            echo json_encode((array) $response);
           return;
        }

        $values = $input->getArray($_POST);

        $attributes = array( );
        foreach ( $values as $key => $value )
        {
            if ( substr( $key, 0, 10 ) == 'attribute_' )
            {
                $attributes[] = $value;
            }
        }
        sort( $attributes );
        $attributes_csv = implode( ',', $attributes );
        $values['product_attributes'] = $attributes_csv;

        // use the wishlist model to add the item to the wishlist, let the model handle all logic
        $session = JFactory::getSession();
        $session_id = $session->getId();
        $session->set( 'old_sessionid', $session_id );

        $user_id = JFactory::getUser()->id;
        $values['user_id'] = $user_id;
        $values['session_id'] = $session_id;

        $model = $this->getModel('wishlists');

        if (!$model->addItem($values)) {
            $response->html = JText::_('COM_CITRUSCART_COULD_NOT_ADD_TO_WISHLIST');
        } else {
            $url = "index.php?option=com_citruscart&view=wishlists&Itemid=" . $this->router->findItemid( array('view'=>'wishlists') );
            $response->html = JText::sprintf( JText::_('COM_CITRUSCART_ADDED_TO_WISHLIST'), addslashes(JRoute::_( $url )) );
        }

        echo json_encode((array) $response);
		//$url ='index.php?option=com_citruscart&controller=products&view=products&task=view&id='. $product_id;
        $app->redirect($url,$response->html);
        
    }  
   
}

