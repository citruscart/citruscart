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

class plgSearchCitruscart extends JPlugin
{
    public $cache_enabled = true; // TODO Make this a param and/or make it depend on global setting
    public $cache_lifetime = '900';  // TODO Make this a param and/or make it depend on global setting

    public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    /**
     * Checks the extension is installed
     *
     * @return boolean
     */
    function _isInstalled()
    {
        $success = false;

        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_citruscart/defines.php'))
        {
            $success = true;
            if ( !class_exists('Citruscart') ) {
                JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
            }

            $this->defines = Citruscart::getInstance();
            Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
        }
        return $success;
    }

    private function doSearchAreas()
    {
        if (!$this->_isInstalled())
        {
            return array();
        }

        $areas = array(
            'Citruscart' => $this->params->get('title', "Citruscart"),
            'wishlist' => $this->params->get('title_wishlist', 'Wishlists')
        );

        return $areas;
    }

    /**
     * Tells the seach component what extentions are being searched
     *
     * @return unknown_type
     */
    function onSearchAreas()
    {
        return $this->doSearchAreas();
    }

	/**
	* @return array An array of search areas
	*/
	function onContentSearchAreas()
	{
		return $this->doSearchAreas();
	}

	private function doSearch( $keyword='', $match='', $ordering='', $areas=null )
	{
	    if (!$this->_isInstalled())
	    {
	        return array();
	    }

		$search_products = $search_wishlists = false;
	    if ( is_array( $areas ) )
	    {
	        if ( !array_intersect( $areas, array_keys( $this->doSearchAreas() ) ) )
	        {
	            return array();
	        } else {
	        	if( in_array('Citruscart', $areas ) !== false ) {
	        		$search_products = true;
	        	}

	        	if( in_array('wishlist', $areas ) !== false ) {
	        		$search_wishlists = true;
	        	}
	        }
	    } else {
	    	$search_products = $search_wishlists = true;
	    }

	    $keyword = trim( $keyword );
	    if (empty($keyword))
	    {
	        return array();
	    }
	    $cache_key = base64_encode(serialize($keyword) . serialize($match) . serialize($ordering) . serialize($areas)) . '.search-results' ;
	    $classname = strtolower( get_class($this) );
	    $cache = JFactory::getCache( $classname . '.search-results', '' );
	    $cache->setCaching($this->cache_enabled);
	    $cache->setLifeTime($this->cache_lifetime);
	    $list = $cache->get($cache_key);

	    if (empty($list))
	    {
	        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
	        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

			$list = array();
	        $match = strtolower($match);
			if( $search_products ) {
		        $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
		        $model->setState( 'filter_published', 1 );
		        $model->setState( 'filter_enabled', 1 );
		        $model->setState( 'filter_published_date', JFactory::getDate()->toSql() );

		        switch ($match)
		        {
		            case 'any':
		                $model->setState('filter_any', $keyword);
		                break;
		            case 'all':
		                $model->setState('filter_all', $keyword);
		                break;
		            case 'exact':
		            default:
		                $model->setState('filter', $keyword);
		                break;
		        }

		        // order the items according to the ordering selected in com_search
		        switch ( $ordering )
		        {
		            case 'newest':
		                $model->setState('order', 'tbl.created_date');
		                $model->setState('direction', 'DESC');
		                break;
		            case 'oldest':
		                $model->setState('order', 'tbl.created_date');
		                $model->setState('direction', 'ASC');
		                break;
		            case 'alpha':
		            case 'popular':
		            default:
		                $model->setState('order', 'tbl.product_name');
		                break;
		        }

		        // filter according to shopper group
		        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
		        $user_id = JFactory::getUser( )->id;
		        $model->setState('filter_group', CitruscartHelperUser::getUserGroup( $user_id ) );

		        //display_out_of_stock
		        if (!$this->defines->get('display_out_of_stock'))
		        {
		            $model->setState( 'filter_quantity_from', '1' );
		        }

		        $items = $model->getListRaw();

		        if (!empty($items)) {
			        // format the items array according to what com_search expects
			        foreach ($items as $key => $item)
			        {
			            $item->itemid_string = null;
			            $item->itemid = (int) Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, null, true );
			            if (!empty($item->itemid)) {
			                $item->itemid_string = "&Itemid=".$item->itemid;
			            }
			            $item->link = 'index.php?option=com_citruscart&view=products&task=view&id=' . $item->product_id;
			            $item->href = $item->link . $item->itemid_string;

			            $item->title        = $item->product_name;
			            $item->created      = $item->created_date;
			            $item->section      = $this->params->get('title', "Citruscart");
			            $item->text         = $item->product_description;
			            $item->browsernav   = $this->params->get('link_behaviour', "0");

			            $item->source 		= $classname;
						$item->type 		= 'product';
			        }

			        $list = array_merge( $list, $items );
		        }
			}

			if( $search_wishlists ) {
		        $model = JModelLegacy::getInstance( 'Wishlists', 'CitruscartModel' );
		        $model->setState( 'filter_accessible', 1 );
		        $model->setState( 'filter_user', JFactory::getUser()->id );
				$model_items = JModelLegacy::getInstance('WishlistItems', 'CitruscartModel' );
				if( !empty( JFactory::getUser()->id ) ) {
			        $model_items->setState( 'filter_user', JFactory::getUser()->id );
				}

		        switch ($match)
		        {
		            case 'any':
		                $model->setState('filter_any', $keyword);
						$model_items->setState( 'filter_search_any', $keyword );
		                break;
		            case 'all':
		                $model->setState('filter_all', $keyword);
						$model_items->setState( 'filter_search_all', $keyword );
		                break;
		            case 'exact':
		            default:
		                $model->setState('filter', $keyword);
						$model_items->setState( 'filter_search', $keyword );
		                break;
		        }

		        // order the items according to the ordering selected in com_search
		        switch ( $ordering )
		        {
		            case 'newest':
		                $model->setState('order', 'tbl.created_date');
		                $model->setState('direction', 'DESC');
		                break;
		            case 'oldest':
		                $model->setState('order', 'tbl.created_date');
		                $model->setState('direction', 'ASC');
		                break;
		            case 'alpha':
		            case 'popular':
		            default:
		                $model->setState('order', 'tbl.wishlist_name');
		                break;
		        }
		        $items = $model->getListRaw();

		        if (!empty($items)) {
			        // format the items array according to what com_search expects
			        foreach ($items as $key => $item)
			        {
			            $item->href = $item->link = 'index.php?option=com_citruscart&view=wishlists&task=view&id=' . $item->wishlist_id;

			            $item->title        = $item->wishlist_name;
			            $item->created      = $item->created_date;
			            $item->section      = $this->params->get('title_wishlist', "Citruscart");
			            $item->text         = '';
			            $item->browsernav   = $this->params->get('link_behaviour', "0");

			            $item->source 		= $classname;
						$item->type 		= 'wishlist';
			        }

			        $list = array_merge( $list, $items );
				}

				// now go through wishlist items
				$items = $model_items->getListRaw();
		        if (!empty($items)) {
			        // format the items array according to what com_search expects
			        foreach ($items as $key => $item)
			        {
						$item->customer 	= $item->first_name.' ';
						if( empty( $item->middle_name ) ) {
							$item->customer .= $item->middle_name.' ';
						}
						$item->customer		.= $item->last_name;
						if( !strlen( trim( $item->customer ) ) ) {
							$item->customer = $item->user_joomla_name;
						}
						$item->customer_link = 'index.php?option=com_users&view=profile&id='.$item->user_id;

			            $item->href = $item->link = 'index.php?option=com_citruscart&view=products&task=view&id=' . $item->product_id;
			            $item->title        = $item->product_name;
			            $item->created      = $item->created_date;
			            $item->section      = $this->params->get('title_wishlist', "Citruscart");

						if( strlen( $item->product_attributes ) ) {
							$attributes = explode( ',', $item->product_attributes );
				        	$tbl = JTable::getInstance('ProductAttributes', 'CitruscartTable');
							$tbl_opt = JTable::getInstance( 'ProductAttributeOptions', 'CitruscartTable' );
							$attr_list = array();
					        for( $i = 0, $c = count( $attributes ); $i < $c; $i++ )
					        {
					        	$tbl_opt->load( $attributes[$i] );
								$tbl->load( $tbl_opt->productattribute_id );
			        			$item->href .= '&attribute_'.$tbl_opt->productattribute_id.'='.$attributes[$i];
								$attr_list []= $tbl->productattribute_name.': '.$tbl_opt->productattributeoption_name;
					        }
							$item->text = implode( '<br>', $attr_list );
						} else {
				            $item->text = $item->product_name;
						}
			            $item->browsernav   = $this->params->get('link_behaviour', "0");

			            $item->source 		= $classname;
						$item->type 		= 'wishlistitem';
			        }

			        $list = array_merge( $list, $items );
				}

			}

	        $cache->store($list, $cache_key);
	    }

	    return $list;
	}

    /**
     * Content Search method
     * The sql must return the following fields that are used in a common display
     * routine: href, title, section, created, text, browsernav
     * @param string Target search string
     * @param string mathcing option, exact|any|all
     * @param string ordering option, newest|oldest|popular|alpha|category
     * @param mixed An array if the search it to be restricted to areas, null if search all
     */
    public function onContentSearch($keyword, $match='', $ordering='', $areas=null)
    {
        return $this->doSearch( $keyword, $match, $ordering, $areas );
    }


    /**
     * Performs the search
     *
     * @param string $keyword
     * @param string $match
     * @param unknown_type $ordering
     * @param unknown_type $areas
     * @return unknown_type
     */
    public function onSearch( $keyword='', $match='', $ordering='', $areas=null )
    {
        return $this->doSearch( $keyword, $match, $ordering, $areas );
    }
}
