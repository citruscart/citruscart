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
if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( 'CitruscartModelEav', 'models._baseeav' );

require_once(JPATH_SITE.'/libraries/dioscouri/library/parameter.php');
class CitruscartModelProducts extends CitruscartModelEav
{
    protected function _buildQueryWhere( &$query )
    {
        $filter = $this->getState( 'filter' );
        $filter_id_from = $this->getState( 'filter_id_from' );
        $filter_id_to = $this->getState( 'filter_id_to' );
        $filter_id_set = $this->getState( 'filter_id_set' );
        $filter_name = $this->getState( 'filter_name', '', 'string' );
        $filter_enabled = $this->getState( 'filter_enabled' );
        $filter_quantity_from = $this->getState( 'filter_quantity_from' );
        $filter_quantity_to = $this->getState( 'filter_quantity_to' );
        $filter_category = $this->getState( 'filter_category' );
        $filter_multicategory = $this->getState( 'filter_multicategory' );
        $filter_multicategoryoperator = $this->getState( 'filter_multicategoryoperator' );
        $filter_sku = $this->getState( 'filter_sku', '', 'string' );
        $filter_price_from = $this->getState( 'filter_price_from' );
        $filter_price_to = $this->getState( 'filter_price_to' );
        $filter_taxclass = $this->getState( 'filter_taxclass' );
        $filter_ships = $this->getState( 'filter_ships' );
        $filter_date_from = $this->getState( 'filter_date_from' );
        $filter_date_to = $this->getState( 'filter_date_to' );
        $filter_datetype = $this->getState( 'filter_datetype', '', 'word' );
        $filter_published = $this->getState( 'filter_published' );
        $filter_published_date = $this->getState( 'filter_published_date' );
        $filter_manufacturer = $this->getState( 'filter_manufacturer' );
        $filter_manufacturer_set = $this->getState( 'filter_manufacturer_set' );
        $filter_multicategory = $this->getState( 'filter_multicategory' );
        $filter_description = $this->getState( 'filter_description' );
        $filter_description_short = $this->getState( 'filter_description_short' );
        $filter_namedescription = $this->getState( 'filter_namedescription' );
        $filter_attribute_set = $this->getState( 'filter_attribute_set' );
        $filter_rating = $this->getState( 'filter_rating' );
        $filter_pao_names = $this->getState( 'filter_pao_names' );
        $filter_pao_ids = $this->getState( 'filter_pao_ids' );
        $filter_pao_id_groups = $this->getState( 'filter_pao_id_groups' );
        $filter_all = $this->getState('filter_all');
        $filter_any = $this->getState('filter_any');

        if ( $filter )
        {
            $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter ) ) ) . '%' );

            $where = array( );
            $where[] = 'LOWER(tbl.product_id) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_name) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_description) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_description_short) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_sku) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_model) LIKE ' . $key;
            $where[] = 'LOWER(m.manufacturer_name) LIKE ' . $key;
            $where[] = 'LOWER(c.category_name) LIKE ' . $key;

            $query->where( '(' . implode( ' OR ', $where ) . ')' );
        }

        if ( $filter_namedescription )
        {
            $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter_namedescription ) ) ) . '%' );
            $where = array( );
            $where[] = 'LOWER(tbl.product_name) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_description) LIKE ' . $key;
            $where[] = 'LOWER(tbl.product_description_short) LIKE ' . $key;
            $query->where( '(' . implode( ' OR ', $where ) . ')' );
        }

        if ( $filter_all )
        {
            $words = explode( ' ', $filter_all );
            foreach ($words as $word)
            {
                $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
                $where = array( );
                $where[] = 'LOWER(tbl.product_id) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_name) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_description) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_description_short) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_sku) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_model) LIKE ' . $key;
                $where[] = 'LOWER(m.manufacturer_name) LIKE ' . $key;
                $where[] = 'LOWER(c.category_name) LIKE ' . $key;

                $query->where( '(' . implode( ' OR ', $where ) . ')' );
            }
        }

        if ( $filter_any )
        {
            $words = explode( ' ', $filter_any );
            $wheres = array( );
            foreach ($words as $word)
            {
                $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
                $where = array( );
                $where[] = 'LOWER(tbl.product_id) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_name) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_description) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_description_short) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_sku) LIKE ' . $key;
                $where[] = 'LOWER(tbl.product_model) LIKE ' . $key;
                $where[] = 'LOWER(m.manufacturer_name) LIKE ' . $key;
                $where[] = 'LOWER(c.category_name) LIKE ' . $key;

                $wheres[] = '(' . implode( ' OR ', $where ) . ')';
            }

            if (!empty($wheres))
            {
                $stmt = '(' . implode( ' OR ', $wheres ) . ')';
                $query->where($stmt);
            }
        }

        if ( strlen( $filter_enabled ) )
        {
            $query->where( 'tbl.product_enabled = ' . $this->_db->q( $filter_enabled ) );
        }
        if ( strlen( $filter_id_from ) )
        {
            if ( strlen( $filter_id_to ) )
            {
                $query->where( 'tbl.product_id >= ' . ( int ) $filter_id_from );
            }
            else
            {
                $query->where( 'tbl.product_id = ' . ( int ) $filter_id_from );
            }
        }

        if ( $filter_rating )
        {
            $query->where( 'tbl.product_rating >= ' . ( float ) $filter_rating );
        }

        if ( strlen( $filter_id_to ) )
        {
            $query->where( 'tbl.product_id <= ' . ( int ) $filter_id_to );
        }
        if ( strlen( $filter_id_set ) )
        {
            $query->where( 'tbl.product_id IN (' . $filter_id_set . ')' );
        }
        if ( strlen( $filter_name ) )
        {
            $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter_name ) ) ) . '%' );
            $query->where( 'LOWER(tbl.product_name) LIKE ' . $key );
        }
        if ( strlen( $filter_quantity_from ) )
        {
            $query
            ->where(
                    "
                    (
                    tbl.product_check_inventory = '0' OR
                    (
                    (
                    SELECT
                    SUM(quantities.quantity)
                    FROM
                    #__citruscart_productquantities AS quantities
                    WHERE
                    quantities.product_id = tbl.product_id
                    AND quantities.vendor_id = 0
            ) >= '" . ( int ) $filter_quantity_from
                    . "'
                    AND
                    tbl.product_check_inventory = '1'
            )
            )
                    " );
        }
        if ( strlen( $filter_quantity_to ) )
        {
            $query
            ->where(
                    '(
                    SELECT
                    SUM(quantities.quantity)
                    FROM
                    #__citruscart_productquantities AS quantities
                    WHERE
                    quantities.product_id = tbl.product_id
                    AND quantities.vendor_id = 0
            ) <= ' . ( int ) $filter_quantity_to );
        }
        if ( strlen( $filter_sku ) )
        {
            $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter_sku ) ) ) . '%' );

            // Check also the pao for codes
            $where = array( );
            $where[] = 'LOWER(tbl.product_sku) LIKE ' . $key;
            $where[] = 'LOWER(pao.productattributeoption_code) LIKE ' . $key;
            $query->where( '(' . implode( ' OR ', $where ) . ')' );
        }

        if ( strlen( $filter_attribute_set ) )
        {
            $query->where( 'pa.productattribute_id IN(' . $filter_attribute_set . ')' );
        }
        if(count($filter_multicategory)) {
                //AND FILTER
                    if($filter_multicategoryoperator == 'AND') {
                        $count = COUNT($filter_multicategory);
                        $query->where( ' tbl.product_id in (  SELECT DISTINCT(product_id) FROM mytable WHERE cnt = '.  $this->_db->q($count ) .')');
                     }

                //ALL FILTERS
                if($filter_multicategoryoperator == 'OR') {
                    // Creating the in clause for the case of the multiple category filter
                $in_category_clause = "";
                foreach ( ( ( array ) $filter_multicategory ) as $category )
                {
                    if ( strlen( $category ) )
                    {
                        $in_category_clause = $in_category_clause . $category . ",";

                    }
                }
                if ( $in_category_clause != "" )
                {
                    $in_category_clause = substr( $in_category_clause, 0, -1 );
                    $query->where( 'p2c.category_id IN(' . $in_category_clause . ')' );
                }
         }
        } else {
        if ( $filter_category == 'none' )
        {
            $query->where( "NOT EXISTS (SELECT * FROM #__citruscart_productcategoryxref AS p2c WHERE tbl.product_id = p2c.product_id)" );
        }
        elseif ( strlen( $filter_category ) )
        {
            $query->where( 'p2c.category_id = ' . ( int ) $filter_category );
        }
        }

        if ( strlen( $filter_price_from ) )
        {
            $query->having( "price >= '" . ( int ) $filter_price_from . "'" );
        }
        if ( strlen( $filter_price_to ) )
        {
            $query->having( "price <= '" . ( int ) $filter_price_to . "'" );
        }
        if ( strlen( $filter_taxclass ) )
        {
            $query->where( 'tbl.tax_class_id = ' . ( int ) $filter_taxclass );
        }
        if ( strlen( $filter_ships ) )
        {
            $query->where( 'tbl.product_ships = ' . ( int ) $filter_ships );
        }
        if ( strlen( $filter_date_from ) )
        {
            switch ( $filter_datetype )
            {
                case "modified":
                    $query->where( "tbl.modified_date >= '" . $filter_date_from . "'" );
                    break;
                case "created":
                default:
                    $query->where( "tbl.created_date >= '" . $filter_date_from . "'" );
                    break;
            }
        }

        if ( strlen( $filter_date_to ) )
        {
            switch ( $filter_datetype )
            {
                case "modified":
                    $query->where( "tbl.modified_date <= '" . $filter_date_to . "'" );
                    break;
                case "created":
                default:
                    $query->where( "tbl.created_date <= '" . $filter_date_to . "'" );
                    break;
            }
        }

        if ( $filter_manufacturer )
        {
            $query->where( 'tbl.manufacturer_id = ' . ( int ) $filter_manufacturer );
        }

        if ( strlen( $filter_manufacturer_set ) )
        {
            $query->where( 'tbl.manufacturer_id IN(' . $filter_manufacturer_set . ')' );
        }


        if ( strlen( $filter_published ) )
        {
            // TODO Add this after updating the products form to add publish/unpublish date fields
            $query
            ->where(
                    "(tbl.publish_date <= '" . $filter_published_date . "' AND (tbl.unpublish_date > '" . $filter_published_date
                    . "' OR tbl.unpublish_date = '0000-00-00' ) )", 'AND' );
        }


        if ( strlen( $filter_description ) )
        {
            $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter_description ) ) ) . '%' );
            $query->where( 'LOWER(tbl.product_description) LIKE ' . $key );
        }

        if (!empty($filter_pao_names) && is_array($filter_pao_names))
        {
    	    $filter_id_set = implode("', '", $filter_pao_names);

            // only return products who have one of the selected pao names
            $subquery = "SELECT sq_pa.product_id FROM #__citruscart_productattributes AS sq_pa WHERE sq_pa.productattribute_id IN (
                SELECT sq_pao.productattribute_id FROM #__citruscart_productattributeoptions AS sq_pao WHERE sq_pao.productattributeoption_name IN ('" . $filter_id_set . "')
            )";

            $query->where( 'tbl.product_id IN (' . $subquery . ')' );
        }

        if (!empty($filter_pao_ids) && is_array($filter_pao_ids))
        {
            $filter_id_set = implode("', '", $filter_pao_ids);

            // only return products who have one of the selected pao ids
            $subquery = "SELECT sq_pa.product_id FROM #__citruscart_productattributes AS sq_pa WHERE sq_pa.productattribute_id IN (
            SELECT sq_pao.productattribute_id FROM #__citruscart_productattributeoptions AS sq_pao WHERE sq_pao.productattributeoption_id IN ('" . $filter_id_set . "')
            )";

            $query->where( 'tbl.product_id IN (' . $subquery . ')' );
        }

        if (!empty($filter_pao_id_groups) && is_array($filter_pao_id_groups))
        {
            foreach ($filter_pao_id_groups as $filter_pao_id_group)
            {
                if (!empty($filter_pao_id_group) && is_array($filter_pao_id_group))
                {
                    $filter_id_set = implode("', '", $filter_pao_id_group);

                    if (!empty($filter_id_set))
                    {
                        // only return products who have one of the selected pao ids
                        $subquery = "SELECT sq_pa.product_id FROM #__citruscart_productattributes AS sq_pa WHERE sq_pa.productattribute_id IN (
                        SELECT sq_pao.productattribute_id FROM #__citruscart_productattributeoptions AS sq_pao WHERE sq_pao.productattributeoption_id IN ('" . $filter_id_set . "')
                        )";

                        $query->where( 'tbl.product_id IN (' . $subquery . ')' );
                    }
                }
            }
        }

    }

    protected function _buildQueryJoins( &$query )
    {
        $query->join( 'LEFT', '#__citruscart_productcategoryxref AS p2c ON p2c.product_id = tbl.product_id' );
        $query->join( 'LEFT', '#__citruscart_categories AS c ON p2c.category_id = c.category_id' );
        $query->join( 'LEFT', '#__citruscart_manufacturers AS m ON m.manufacturer_id = tbl.manufacturer_id' );

        $filter_sku = $this->getState( 'filter_sku', '', 'string' );
        $filter_attribute_set = $this->getState( 'filter_attribute_set' );
        if ( strlen( $filter_sku ) || strlen($filter_attribute_set) )
        {
            // Check also the pao for codes
            $query->join( 'LEFT', '#__citruscart_productattributes AS pa ON pa.product_id = tbl.product_id' );

            if(strlen( $filter_sku ))
            {
                $query->join( 'LEFT', '#__citruscart_productattributeoptions AS pao ON pa.productattribute_id = pao.productattribute_id' );
            }
        }
    }

    protected function _buildTempTables( ) {
        $cats = $this->getState( 'filter_multicategory' );
        $cats = implode(',', $cats);
        if(count($cats) > 0 &&  !empty($cats) && !empty($cats)) {
        $sql = " CREATE TEMPORARY TABLE mytable AS (SELECT a.product_id, (
        SELECT COUNT(b.product_id) FROM #__citruscart_productcategoryxref as b WHERE b.category_id IN ($cats) AND b.product_id = a.product_id
        ) as cnt FROM #__citruscart_productcategoryxref as a WHERE a.category_id IN ($cats))";
        $db = JFactory::getDBO();
        $db->setQuery('DROP TABLE IF EXISTS mytable');
        $db->query();
        $db->setQuery($sql);
        $db->query();
        }
    }

    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    protected function _buildQuery( $refresh=false )
    {
        if (!empty($this->_query) && !$refresh)
        {
            return $this->_query;
        }
        if($this->getState( 'filter_multicategory' )) {
           $this->_buildTempTables();
        }
        $query = new DSCQuery();

        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);


        return $query;
    }



    protected function _buildQueryFields( &$query )
    {
        $field = array( );
        if ( $this->getState( 'filter_category' ) )
        {
            $field[] = " c.category_name AS category_name ";
        }

        $field[] = " m.manufacturer_name AS manufacturer_name ";

        // This subquery returns the default price for the product and allows for sorting by price
        $date = JFactory::getDate( )->toSql( );

        $default_group = Citruscart::getInstance( )->get( 'default_user_group', '1' );
        $filter_group = ( int ) $this->getState( 'filter_group' );
        if ( empty( $filter_group ) )
        {
            $filter_group = $default_group;
        }

        $field[] = "
       (
        SELECT
        prices.product_price
        FROM
        #__citruscart_productprices AS prices
        WHERE
        prices.product_id = tbl.product_id
        AND prices.group_id = '$filter_group'
        AND prices.product_price_startdate <= '$date'
        AND (prices.product_price_enddate >= '$date' OR prices.product_price_enddate = '0000-00-00 00:00:00' )
        ORDER BY prices.price_quantity_start ASC
        LIMIT 1
        )
        AS price ";

        $field[] = "
        (
        SELECT
        SUM(quantities.quantity)
        FROM
        #__citruscart_productquantities AS quantities
        WHERE
        quantities.product_id = tbl.product_id
        AND quantities.vendor_id = '0'
        )
        AS product_quantity ";

        $query->select( $this->getState( 'select', 'tbl.*' ) );
        $query->select( $field );
    }

    protected function _buildQueryGroup( &$query )
    {
        $query->group( 'tbl.product_id' );
    }

    /**
     * Remove the normal order clause since on large db it makes it heavierfor nothing
     */
    protected function _buildQueryOrder(&$query)
    {
        $order      = $this->_db->escape( $this->getState('order') );
       	$direction  = $this->_db->escape( strtoupper( $this->getState('direction') ) );

        if ($order)
        {
            $query->order("$order $direction");
        }
    }

    /**
     * Builds a generic SELECT COUNT(*) query that takes group by into account
     */
    protected function _buildResultQuery( )
    {	JPluginHelper::importPlugin('citruscart');
        $app = JFactory::getApplication();
        $grouped_query = new DSCQuery( );
        $grouped_query->select( $this->getState( 'select', 'COUNT(tbl.product_id)' ) );

        $field = array( );
        $filter_quantity_from = $this->getState( 'filter_quantity_from' );
        $filter_quantity_to = $this->getState( 'filter_quantity_to' );
        $filter_price_from = $this->getState( 'filter_price_from' );
        $filter_price_to = $this->getState( 'filter_price_to' );

        if ( strlen( $filter_price_from ) || strlen( $filter_price_to ) )
        {
            // This subquery returns the default price for the product and allows for sorting by price
            $date = JFactory::getDate( )->toSql( );

            $default_group = Citruscart::getInstance( )->get( 'default_user_group', '1' );
            $filter_group = ( int ) $this->getState( 'filter_group' );
            if ( empty( $filter_group ) )
            {
                $filter_group = $default_group;
            }

            $field[] = "(
            SELECT
            prices.product_price
            FROM
            #__citruscart_productprices AS prices
            WHERE
            prices.product_id = tbl.product_id
            AND prices.group_id = '$filter_group'
            AND prices.product_price_startdate <= '$date'
            AND (prices.product_price_enddate >= '$date' OR prices.product_price_enddate = '0000-00-00 00:00:00' )
            ORDER BY prices.price_quantity_start ASC
            LIMIT 1
            )
            AS price";
        }

        if ( strlen( $filter_quantity_from ) || strlen( $filter_quantity_to ) )
        {
            $field[] = "
            (
            SELECT
            SUM(quantities.quantity)
            FROM
            #__citruscart_productquantities AS quantities
            WHERE
            quantities.product_id = tbl.product_id
            AND quantities.vendor_id = '0'
            )
            AS product_quantity ";
        }

        if ( count( $field ) )
        {
            $grouped_query->select( $field );
        }

        $this->_buildQueryFrom( $grouped_query );
        $this->_buildQueryJoins( $grouped_query );
        $this->_buildQueryWhere( $grouped_query );
        $this->_buildQueryGroup( $grouped_query );
        $this->_buildQueryHaving( $grouped_query );

        $query = new DSCQuery( );
        $query->select( 'COUNT(*)' );
        $query->from( '(' . $grouped_query . ') as grouped_count' );

        // Allow plugins to edit the query object
        $suffix = ucfirst( $this->getName( ) );
        //$dispatcher = JDispatcher::getInstance( );
        $app->triggerEvent( 'onAfterBuildResultQuery' . $suffix, array(
                &$query
        ) );

        return $query;
    }

    /**
     * Set basic properties for the item, whether in a list or a singleton
     *
     * @param unknown_type $item
     * @param unknown_type $key
     * @param unknown_type $refresh
     */
    protected function prepareItem( &$item, $key=0, $refresh=false )
    {
        Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
        Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
        $helper_product = new CitruscartHelperProduct();

        if ( !empty( $item->product_recurs ) )
        {
            $item->recurring_price = $item->price;
            if( $item->subscription_prorated )
            {
                Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
                $result = CitruscartHelperSubscription::calculateProRatedTrial( $item->subscription_prorated_date,
                        $item->subscription_prorated_term,
                        $item->recurring_period_unit,
                        $item->recurring_trial_price,
                        $item->subscription_prorated_charge
                );
                $item->price = $result['price'];
                $item->prorated_price = $result['price'];
                $item->prorated_interval = $result['interval'];
                $item->prorated_unit = $result['unit'];
                // $item->recurring_trial = $result['trial'];
            }
                else
            {
                if ( !empty( $item->recurring_trial ) )
                {
                    $item->price = $item->recurring_trial_price;
                }
            }
        }

		$user_id = $this->getState( 'user.id', 0 );
		$qty = $this->getState( 'product.qty', -1 );
		if( $qty > -1 ) {
	        $user_group = CitruscartHelperUser::getUserGroup( $user_id, $item->product_id );
			$price = CitruscartHelperProduct::getPrice($item->product_id, $qty, $user_group );
			$item->price = @$price->product_price;
		}

        $item->product_parameters = new DSCParameter( $item->product_params );
        $item->slug = $item->product_alias ? ":$item->product_alias" : "";
        $item->link = 'index.php?option=com_citruscart&view=products&task=view&id=' . $item->product_id;
        $item->link_edit = 'index.php?option=com_citruscart&view=products&task=edit&id=' . $item->product_id;
        $item->product_categories = $this->getCategories( $item->product_id );
        $item->default_attributes = $helper_product->getDefaultAttributes( $item->product_id );
        $item->product_classes = null;
        foreach ($item->product_categories as $cat) {
            $item->product_classes .= " " . $cat->category_alias;
        }
        if (!empty($item->product_class_suffix)) {
            $item->product_classes .= " " . $item->product_class_suffix;
        }
        $item->product_classes = trim($item->product_classes);

        parent::prepareItem( $item, $key, $refresh );
    }

	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCache()
	{
	    parent::clearCache();
	    self::clearCacheAuxiliary();
	}

	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCacheAuxiliary()
	{
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );

	    $model = DSCModel::getInstance('ProductCategories', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductAttributeOptions', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductAttributeOptionValues', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductAttributes', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductCompare', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductFiles', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductPrices', 'CitruscartModel');
	    $model->clearCache();

	    $model = DSCModel::getInstance('ProductRelations', 'CitruscartModel');
	    $model->clearCache();
	}

	public function getItemid( $id, $fallback=null, $allow_null=false )
	{

		$app = JFactory::getApplication();
		Citruscart::load( 'CitruscartHelperRoute', 'helpers.route' );

	    $return = CitruscartHelperRoute::findItemid(array('view'=>'products', 'task'=>'view', 'id'=>$id));
	    if (!$return) {
	        $return = CitruscartHelperRoute::findItemid(array('view'=>'products', 'task'=>'view'));
	        if (!$return) {
	            $return = CitruscartHelperRoute::findItemid(array('view'=>'products'));
	            if (!$return) {

	                if ($fallback) {
	                    $return = $fallback;
	                }

	                if (!$allow_null)
	                {
	                    if ($categories = $this->getCategories( $id ))
	                    {
	                        $count = count($categories);
	                        $cat_model = Citruscart::getClass('CitruscartModelCategories', 'models.categories');
	                        for ($i=0; !$return && $i < $count; $i++)
	                        {
	                            $category = $categories[$i];
	                            if ($cat_itemid = $cat_model->getItemid( $category->category_id, null, true )) {
	                                $return = $cat_itemid;
	                            }
	                        }
	                    }

	                    if (!$return)
	                    {
	                        $return = $app->input->getInt('Itemid');
	                    }

	                    if (!$return) {
	                        $menu	= $app->getMenu();
	                        if ($default = $menu->getDefault() && !empty($default->id))
	                        {
	                            $return = $default->id;
	                        }
	                    }
	                }
	            }
	        }
	    }

	    return $return;
	}

	public function getAlias($id, $refresh=false)
	{
	    $cache_key = $id;

	    $classname = strtolower( get_class($this) );
	    $cache = JFactory::getCache( $classname . '.alias', '' );
	    $cache->setCaching($this->cache_enabled);
	    $cache->setLifeTime($this->cache_lifetime);
	    $item = $cache->get($cache_key);

	    if (!$item || $refresh)
	    {
	        $item = $this->_getAlias( $id );
	        $cache->store($item, $cache_key);
	    }

	    return $item;
	}

	protected function _getAlias( $id )
	{
	    $db = JFactory::getDbo();
	    $query = $db->setQuery($db->getQuery(true)
	            ->select('product_alias')
	            ->from('#__citruscart_products')
	            ->where('product_id='.(int) $id)
	    );
	    $alias = $db->loadResult();

	    return $alias;
	}

	public function getCategories($id, $refresh=false)
	{
	    $model = Citruscart::getClass('CitruscartModelProductCategories', 'models.productcategories');
	    $model->setState('filter_product_id', $id);
	    $result = $model->getList($refresh);

	    return $result;
	}

	/**
	 * Gets the prev and next items in a list of products, based on the user's selected filters
	 * Useful when adding prev/next links to a product detail page
	 *
	 * @see DSCModel::getSurrounding()
	 */
	public function getSurrounding( $id, $refresh=false )
	{
	    $return = array();
	    $return["prev"] = '';
	    $return["next"] = '';

	    if (empty($id))
	    {
	        return $return;
	    }

	    $refresh = true;
	    $this->setState('limit', null);
	    $this->setState('limitstart', null);
	    $this->setState( 'id', $id );

	    $cache_key = base64_encode(serialize($this->getState())) . '.surrounding';
	    $classname = strtolower( get_class($this) );
	    $cache = JFactory::getCache( $classname . '.surrounding', '' );
	    $cache->setCaching($this->cache_enabled);
	    $cache->setLifeTime($this->cache_lifetime);
	    $list = $cache->get($cache_key);

	    if (empty($list) || $refresh)
	    {
	        $surrounding = $this->_getSurrounding($id);
	        if (!empty($surrounding["prev"]) || !empty($surrounding["next"]))
	        {
	            $cache->store($surrounding, $cache_key);
	            $return = $surrounding;
	        }
	    }

	    return $return;
	}

	protected function _getSurrounding( $id )
	{
	    $return = array();
	    $return["prev"] = '';
	    $return["next"] = '';

	    if (empty($id))
	    {
	        return $return;
	    }

	    $prev = $this->getState('prev');
	    $next = $this->getState('next');
	    if (strlen($prev) || strlen($next))
	    {
	        $return["prev"] = $prev;
	        $return["next"] = $next;
	        return $return;
	    }

	    $db = $this->getDBO();
	    $key = $this->getTable()->getKeyName();

	    $query = $this->getQuery( true );
	    $rowset_query = (string) $query;

	    $rownum_query = "SELECT orig.product_id, @rownum:=@rownum+1 AS rownum, orig.product_name
	    FROM (
	    $rowset_query
	    ) AS orig,
	    (SELECT @rownum:=0) r";

	    $q2 = "
	    SELECT x.rownum INTO @midpoint FROM (
	    $rownum_query
	    ) x WHERE x.$key = '$id';
	    ";
	    $db->setQuery( $q2 );
	    $db->query();

	    $q3 = "
	    SELECT x.* FROM (
	    $rownum_query
	    ) AS x
	    WHERE x.rownum BETWEEN @midpoint - 1 AND @midpoint + 1;
	    ";
	    $db->setQuery( $q3 );
	    $rowset = $db->loadObjectList();
	    $count = count($rowset);

	    $found = false;
	    $prev_id = '';
	    $next_id = '';

	    JArrayHelper::sortObjects( $rowset, 'rownum', 1 );

	    for ($i=0; $i < $count && empty($found); $i++)
	    {
	        $row = $rowset[$i];
	        if ($row->$key == $id)
	        {
	            $found = true;
	            $prev_num = $i - 1;
	            $next_num = $i + 1;
	            if (!empty($rowset[$prev_num]->$key)) {
	                $prev_id = $rowset[$prev_num]->$key;
	            }
	            if (!empty($rowset[$next_num]->$key)) {
	                $next_id = $rowset[$next_num]->$key;
	            }
	        }
	    }

	    $return["prev"] = $prev_id;
	    $return["next"] = $next_id;
	    return $return;
	}

	public function getPAOCategories( $category_ids=array() )
	{
	    // TODO Cache this

	    $filter_published_date = $this->getState( 'filter_published_date' );
	    $state = $this->getState();

	    $query = $this->getDBO()->getQuery(true);

	    $query->from( "#__citruscart_productattributes AS pa" );
	    $query->join( "INNER", "#__citruscart_productcategoryxref AS xref ON pa.product_id = xref.product_id" );
	    $query->join( "INNER", "#__citruscart_products AS tbl ON pa.product_id = tbl.product_id" );
	    $query->where( 'tbl.product_enabled = 1' );
	    $query->where(
	            "(tbl.publish_date <= '" . $filter_published_date . "' AND (tbl.unpublish_date > '" . $filter_published_date
	            . "' OR tbl.unpublish_date = '0000-00-00' ) )", 'AND' );

	    if (!empty($category_ids)) {
	        $category_ids = (array) $category_ids;
	        $filter_id_set = implode("', '", $category_ids);
	        $query->where( "xref.category_id IN ('" . $filter_id_set . "')" );
	    }

	    $id_query = clone( $query );

	    $query->select( "DISTINCT(productattribute_name)" );
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query );
	    if ($result = $db->loadObjectList())
	    {
	        jimport('joomla.utilities.arrayhelper');
	        JArrayHelper::sortObjects($result, 'productattribute_name');

	        foreach ($result as $category)
	        {
	            $this_query = clone( $id_query );
	            $this_query->select("pa.productattribute_id");
	            $this_query->where( "pa.productattribute_name = '$category->productattribute_name'" );
	            $db->setQuery( (string) $this_query );
                $category->productattribute_ids = $db->loadColumn();
	            $category->productattribute_options = $this->getPAOCategoryOptions( $category->productattribute_ids );
	        }
	    }

	    return $result;
	}

	public function getPAOCategoryOptions( $pa_ids )
	{
	    $return = array();

	    $query = $this->getDBO()->getQuery(true);

	    $pa_ids = (array) $pa_ids;
	    $filter_id_set = implode("', '", $pa_ids);


	    $query->from( "#__citruscart_productattributeoptions AS pao" );
	    $query->where( "pao.productattribute_id IN ('" . $filter_id_set . "')" );

	    $id_query = clone( $query );

	    $query->select( "DISTINCT(productattributeoption_name)" );
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query );
	    //$return = $db->loadColumn();
	    if ($return = $db->loadObjectList())
	    {
	        foreach ($return as $pao)
	        {
	            $this_query = clone( $id_query );
	            $this_query->select("pao.productattributeoption_id");
	            $this_query->where( "pao.productattributeoption_name = '$pao->productattributeoption_name'" );
	            $db->setQuery( (string) $this_query );
	            $pao->productattributeoption_ids = $db->loadColumn();
	        }
	    }

	    jimport('joomla.utilities.arrayhelper');
	    JArrayHelper::sortObjects($return, 'productattributeoption_name');

	    return $return;
	}

	/**
	 * Determines if a product is in a visitor's wishlist,
	 * whether they are logged in or not
	 *
	 * xref_type = 'user' and xref_id = user_id OR
	 * xref_type = 'session' anx xref_id = session_id
	 *
	 * @param unknown_type $product_id
	 * @param unknown_type $xref_id
	 * @param unknown_type $xref_type
	 */
	public function isInWishlist( $product_id, $xref_id, $xref_type='user', $attributes ='' )
	{
	    $query = new CitruscartQuery();
	    $query->select( "tbl.wishlistitem_id" );
	    $query->from( '#__citruscart_wishlistitems AS tbl' );
	    $query->where( "tbl.product_id = " . (int) $product_id );
	    if (strtolower($xref_type) == 'session') {
	        $query->where( "tbl.session_id = ".$this->_db->q($xref_id));
	    } else {
	        $query->where( "tbl.user_id = " . (int) $xref_id );
	    }
		if( !empty( $attributes ) ) {
			$query->where( "tbl.product_attributes = ".$this->_db->q( $attributes ) );
		}
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query );
	    if ($result = $db->loadResult())
	    {
	        return $result;
	    }

	    return false;
	}


	 /**
	  * Method to Getlist of items from the ProductAttributes table
	  * return  objectlist
	  * @see CitruscartModelEav::getList()
	 */
	 public function getList($refresh = false, $getEav = true, $options = array()){
	 	$db = JFactory::getDbo();
	 	$query = $db->getQuery(true);
	 	$query = $this->_buildQuery();
	 	$db->setQuery($query);
	 	//$list = $db->loadObjectList();
	 	$list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
	 	$this->_list = $list;
        foreach($list as &$item)
		 {
		 	$item->link = 'index.php?option=com_citruscart&controller=products&view=products&task=view&id='.$item->product_id;
	 		$item->link_edit = 'index.php?option=com_citruscart&controller=products&view=products&task=edit&id='.$item->product_id;
	 	 }
	 	return $list;
	    }
}
