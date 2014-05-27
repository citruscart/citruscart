<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableOrders extends CitruscartTable
{
    /** @var array An array of CitruscartTableOrderItems objects */
    protected $_items = array();

    /** @var array An array of CitruscartTableProductDownloads objects */
    protected $_downloads = array();

    /** @var array An array of vendor_ids */
    protected $_vendors = array();

    /** @var object An CitruscartTableAddresses() object for billing */
    protected $_billing_address = null;

    /** @var object An CitruscartTableAddresses() object for shipping */
    protected $_shipping_address = null;

    /** @var array      tax & shipping geozone objects */
    protected $_billing_geozones = array();
    protected $_shipping_geozones = array();

    /** @var array      The shipping totals JObjects */
    protected $_shipping_totals = array();

    /** @var boolean Has the recurring item been added to the order?
     * This is used exclusively during orderTotal calculation
     */
    protected $_recurringItemExists = false;

    /** @var object And OrderItem Object, only populated if the orderitem recurs
     */
    protected $_recurringItem = false;

    /** @var array An array of CitruscartTableTaxRates objects (the unique taxrates for this order) */
    protected $_taxrates = array();

    /** @var array An array of tax amounts, indexed by tax_rate_id */
    protected $_taxrate_amounts = array();

    /** @var array An array of CitruscartTableTaxRates objects (the unique taxclasses for this order) */
    protected $_taxclasses = array();

    /** @var array An array of tax amounts, indexed by tax_class_id */
    protected $_taxclass_amounts = array();

    /** @var array An array of CitruscartTableCoupons objects */
    protected $_coupons = array();

    /** @var array An array of CitruscartTableOrderCoupons objects */
    protected $_ordercoupons = array();

    /** @var array An array of CitruscartTableCoupons objects. Tracks user-submitted coupon(s) added to the order.  Duplicates some data that is in the _coupons array, but doesn't store it to the DB when the order is saved */
    protected $_usercoupons = array();

    protected $_weight = 0;

    protected $_anonymous = 0;
    /**
     * @param $db
     * @return unknown_type
     */
    function __construct( &$db ){
        $tbl_key    = 'order_id';
        $tbl_suffix = 'orders';
        $this->set( '_suffix', $tbl_suffix );
        $name       = 'citruscart';
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
    }

    /**
     * Loads the Order object with values from the DB tables
     * (non-PHPdoc)
     * @see Citruscart/admin/tables/CitruscartTable#load($oid, $reset)
     */
    function load( $oid=null, $reset=true )
    {
        if ($return = parent::load($oid, $reset))
        {
            // TODO populate the protected vars with the info from the db
        }
        return $return;
    }

    /**
     * Ensures integrity of the table object before storing to db
     *
     * @return unknown_type
     */
    function check()
    {
        $nullDate   = $this->_db->getNullDate();

        if (empty($this->created_date) || $this->created_date == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_date = $date->toSql();
        }

        if (empty($this->modified_date) || $this->modified_date == $nullDate)
        {
            $date = JFactory::getDate();
            $this->modified_date = $date->toSql();
        }

        return parent::check();
    }

  function store($updateNulls=false)
  {
      // add hash to orders which are placed by guests
      if( !$this->order_id && !strlen( $this->order_hash ) && $this->user_id < Citruscart::getGuestIdStart() )
      {
          Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
          $this->order_hash = CitruscartHelperOrder::getHashInvoice( $this );
      }

    return parent::store($updateNulls);
  }

    /**
     * Saves the order to the db table
     *
     * (non-PHPdoc)
     * @see Citruscart/admin/tables/CitruscartTable#save()
     */
    function save($src='', $orderingFilter = '', $ignore = '')
    {
        if ($return = parent::save($src, $orderingFilter, $ignore))
        {
            // create the order_number when the order is saved, since it is based on the auto-inc value
            $order_number_prefix = Citruscart::getInstance()->get('order_number_prefix');
            if (!empty($order_number_prefix) && empty($this->order_number) && !empty($this->order_id))
            {
                $this->order_number = $order_number_prefix.$this->order_id;
                $this->store();
            }

            // If the order_credit > 0.00, then save a usage in the order credits, with a - value of the credit amount
            if ($this->order_credit > '0.00' && $this->_adjustCredits)
            {
                $credit = JTable::getInstance( 'Credits', 'CitruscartTable');
                $credit->user_id = $this->user_id;
                $credit->order_id = $this->order_id;
                $credit->credittype_code = 'usage';
                $credit->credit_enabled = '1';
                $credit->credit_amount = 0 - $this->order_credit;
                $credit->save();
            }


            // TODO All of the protected vars information could be saved here instead, no?
        }
        return $return;
    }

    /**
     * Adds an item to the order object
     * $item can be a named array with a minimum of 'product_id' and 'orderitem_quantity' and 'orderitem_attributes' (as CSV of productattributeoptions_ids)
     * $item can be an object with minimum of 'product_id' and 'orderitem_quantity' and 'orderitem_attributes' properties
     * $item can be a 'product_id' string
     *
     * $this->_items['product_id'] = TableOrderItems() object;
     *
     * @param object    $item   TableOrderItem object
     * @return void
     */
    function addItem( $item )
    {
        Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
        $orderItem = JTable::getInstance('OrderItems', 'CitruscartTable');
        if (is_array($item))
        {
            $orderItem->bind( $item );
        }
        elseif (is_object($item) && is_a($item, 'CitruscartTableOrderItems'))
        {
            $orderItem = $item;
        }
        elseif (is_object($item))
        {
            $orderItem->product_id = $item->product_id;
            $orderItem->orderitem_quantity = $item->orderitem_quantity;
            $orderItem->vendor_id  = $item->vendor_id;
            $orderItem->orderitem_attributes = $item->orderitem_attributes;
        }
        else
        {
            $orderItem->product_id = $item;
            $orderItem->orderitem_quantity = '1';
            $orderItem->vendor_id  = '0';
            $orderItem->orderitem_attributes = '';
        }

        // check whether/not the item recurs
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
        $model->setId( $orderItem->product_id );
        $product = $model->getItem();
        $orderItem->subscription_prorated = $product->subscription_prorated;

        // flag the order as recurring
        if( $product->product_recurs )
            $this->order_recurs = true;

        if ( $orderItem->subscription_prorated )
        {
            // set the orderitem's recurring product values
            $orderItem->orderitem_recurs            = $product->product_recurs;
            $orderItem->recurring_price             = $product->recurring_price;
            $orderItem->recurring_payments          = $product->recurring_payments;
            $orderItem->recurring_period_interval   = $product->recurring_period_interval;
            $orderItem->recurring_period_unit       = $product->recurring_period_unit;
            $orderItem->recurring_trial_price       = $product->recurring_trial_price;

            if( $product->subscription_prorated ) // prorated subscription
            {
                $result = CitruscartHelperSubscription::calculateProRatedTrial( $product->subscription_prorated_date,
                    $product->subscription_prorated_term,
                    $product->recurring_period_unit,
                    $product->recurring_trial_price,
                    $product->subscription_prorated_charge
                );
                $orderItem->recurring_trial = $result['trial'];
                $orderItem->recurring_trial_period_interval = $result['interval'];
                $orderItem->recurring_trial_period_unit = $result['unit'];
                $orderItem->recurring_trial_price = $result['price'];
            }
            else
            {
                $orderItem->recurring_trial             = $product->recurring_trial;
                $orderItem->recurring_trial_period_interval = $product->recurring_trial_period_interval;
                $orderItem->recurring_trial_period_unit = $product->recurring_trial_period_unit;
            }
        }

        if (!empty($product->product_subscription))
        {
            // set the orderitem's subscription product values
            $orderItem->orderitem_subscription      = $product->product_subscription;
            $orderItem->subscription_lifetime       = $product->subscription_lifetime;
            $orderItem->subscription_period_interval= $product->subscription_period_interval;
            $orderItem->subscription_period_unit    = $product->subscription_period_unit;
        }

        // Use hash to separate items when customer is buying the same product from multiple vendors
        // and with different attribs
        $hash = intval($orderItem->product_id).".".intval($orderItem->vendor_id).".".$orderItem->orderitem_attributes;


                $results = JFactory::getApplication()->triggerEvent( "onGetAdditionalOrderitemKeyValues", array( $orderItem ) );
//              JFactory::getApplication()->enqueueMessage( 'orders.php - line 236 - '.Citruscart::dump( $results ) );
                foreach ($results as $result)
        {
            foreach($result as $key=>$value)
            {
                $hash = $hash.".".$value;
            }
        }
        if( isset( $orderItem->cart_id ) )
            unset( $orderItem->cart_id );
//              $orderItem->orderitem_id = null; // so it can create a new ordreitem, if needed

        if (!empty($this->_items[$hash]))
        {
            // merely update quantity if item already in list
            $this->_items[$hash]->orderitem_quantity += $orderItem->orderitem_quantity;
        }
            else
        {
            $this->_items[$hash] = $orderItem;
        }
        // add the vendor to the order
        $this->addVendor( $orderItem );

        // add productdownloads records to the order
        // not necessary yet
        // $this->addDownloads( $orderItem );
    }

    /**
     * Adds product downloads records to the order
     * based on the properties of the item being added
     *
     * @param object    $orderItem      a TableItems object
     * @return void
     */
    function addDownloads( $orderItem )
    {
        // if this orderItem product has productfiles that are enabled and only available when product is purchased
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductFiles', 'CitruscartModel' );
        $model->setState( 'filter_product', $orderItem->product_id );
        $model->setState( 'filter_enabled', 1 );
        $model->setState( 'filter_purchaserequired', 1 );
        if (!$items = $model->getList())
        {
            // TODO Is there any need to return anything here?
            return;
        }

        // then add them to the order as a productdownloads table object
        foreach ($items as $item)
        {
            $productDownload = JTable::getInstance('ProductDownloads', 'CitruscartTable');
            $productDownload->product_id = $orderItem->product_id;
            $productDownload->productfile_id = $item->productfile_id;
            $productDownload->productdownload_max = '-1'; // TODO For now, infinite. In the future, add a field to productfiles that allows admins to limit downloads per file per purchase
            // in the order object, download is identified by the productfile_id
            $this->_downloads[$item->productfile_id] = $productDownload;
        }
    }

    /**
     * Adds a vendor to the order based on the properties of the item being added
     *
     * @param object    $orderItem      a TableItems object
     * @return void
     */
    function addVendor( $orderItem )
    {
        // if this product is from a vendor other than store owner, track it
        if (!empty($orderItem->vendor_id) && empty($this->_vendors[$orderItem->vendor_id]))
        {
            $orderVendor = JTable::getInstance('OrderVendors', 'CitruscartTable');
            $orderVendor->vendor_id = $orderItem->vendor_id;
            $this->_vendors[$orderItem->vendor_id] = $orderVendor;
        }

        if (!empty($this->_vendors[$orderItem->vendor_id]))
        {
            // TODO update the order vendor's totals?
            // or just wait until the calculateTotals() method is executed?
        }
    }

    /**
     * Based on the items and addresses in the object,
     * calculates the totals
     *
     * @return void
     */
    function calculateTotals()
    {
        $this->order_discount = 0;

        // get the subtotal first.
        // if there are per_product coupons and coupons_before_tax, the orderitem_final_price will be adjusted
        // and ordercoupons created
        $this->calculateProductTotals();

        // then calculate the tax
        $this->calculateTaxTotals();

        // then calculate shipping total
        $this->calculateShippingTotals();

        // coupons
        $this->order_discount += $this->calculatePerOrderCouponValue($this->order_subtotal + $this->order_tax, 'price' );

        // this goes last, to be sure it gets the fully adjusted figures
        $this->calculateVendorTotals();

        // sum totals
        $subtotal =
            $this->order_subtotal
            + $this->order_tax
            + $this->order_shipping
            + $this->order_shipping_tax
            ;

        $discount_total =
            $this->order_discount
            + $this->order_credit
            ;

        if ($discount_total > $subtotal) {
            $discount_total = $subtotal;
        }

        $total = $subtotal - $discount_total;

        // set object properties
        $this->order_total      = $total;

        // We fire just a single plugin event here and pass the entire order object
        // so the plugins can override whatever they need to

        JFactory::getApplication()->triggerEvent( "onCalculateOrderTotals", array( $this ) );
    }

    /**
     * Calculates the product total (aka subtotal)
     * using the array of items in the order object
     *
     * @return unknown_type
     */
    function calculateProductTotals()
    {
        $subtotal = 0.00;

        // TODO Must decide what we want these methods to return; for now, null
        $items = $this->getItems();
        if (!is_array($items))
        {
            $this->order_subtotal = $subtotal;
            return;
        }

//        $coupons_before_tax = Citruscart::getInstance()->get('coupons_before_tax');
        // calculate product subtotal
        foreach ($items as $item)
        {
            // track the subtotal
            $item->orderitem_final_price = ( $item->orderitem_price + $item->orderitem_attributes_price ) * $item->orderitem_quantity;
//          if ($coupons_before_tax)
//            {
                $item->orderitem_final_price = $this->calculatePerProductCouponValue( $item->product_id, $item->orderitem_final_price, 'price' );
//              $item->price = $this->calculatePerProductCouponValue( $item->product_id, $item->price, 'price' );
//            }
            $subtotal += $item->orderitem_final_price;
        }

        // set object properties
        $this->order_subtotal   = $subtotal;

        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculateProductTotals", array( $this ) );
    }

    /**
     * Calculates the tax totals for the order
     * using the array of items in the order object
     *
     * @return unknown_type
     */
    function calculateTaxTotals()
    {
        $config = Citruscart::getInstance();
        $show_tax = $config->get('display_prices_with_tax');
        $calc_tax_shipping = $config->get('calc_tax_shipping');

        Citruscart::load( "CitruscartHelperTax", 'helpers.tax' );
        $this->_taxrate_amounts = array();
        $this->_taxclass_amounts = array();

        $items = $this->getItems();
        $shipping_address = $this->getShippingAddress();
        $billing_address = $this->getBillingAddress();
        if ($calc_tax_shipping) {
            $taxes = CitruscartHelperTax::calculateTax( $items, 1, $shipping_address, $shipping_address );
        } else {
            $taxes = CitruscartHelperTax::calculateTax( $items, 1, $billing_address, $shipping_address );
        }

        foreach( $items as $item )
        {
            $item->orderitem_tax = $taxes->product_taxes[ $item->product_id ];
            if( $show_tax ) {
                $item->orderitem_final_price += $item->orderitem_tax;
            }
        }


        foreach( $taxes->tax_rate_rates as $key => $value )
        {
            $applied_tax = $value->applied_tax;

            // add this as one of the taxrates applicable to this order
            if ( empty( $this->_taxrates[$key] ) ) {
                $this->_taxrates[$key] = $value;
            }

            // track the total amount of tax applied to this order for this taxrate
            if ( empty($this->_taxrate_amounts[$key] ) ) {
                $this->_taxrate_amounts[$key] = 0;
            }

            $this->_taxrate_amounts[$key] += $applied_tax;
        }

        foreach( $taxes->tax_class_rates as $key => $value )
        {
            $applied_tax = $value->applied_tax;

            // add this as one of the taxclasses applicable to this order
            if ( empty( $this->_taxclasses[$key] ) ) {
                $this->_taxclasses[$key] = $value;
            }

            // track the total amount of tax applied to this order for this taxclass
            if ( empty( $this->_taxclass_amounts[$key] ) ) {
                $this->_taxclass_amounts[$key] = 0;
            }

            $this->_taxclass_amounts[$key] += $applied_tax;
        }

        $this->order_tax = $taxes->tax_total;

        // Allow tax calculation to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculateTaxTotals", array( $this ) );
    }

    /**
     * Calculates the shipping totals for the order
     * using the array of items in the order object
     *
     * @return unknown_type
     */
    function calculateShippingTotals()
    {
        $order_shipping     = 0.00;
        $order_shipping_tax = 0.00;

        $items = $this->getItems();

        if (!is_array($items) || !$this->shipping)
        {
            $this->order_shipping       = $order_shipping;
            $this->order_shipping_tax   = $order_shipping_tax;
            return;
        }

        // This support multiple shipping geozones
        // For each item in $this->getShippingGeoZones, calculate the shipping total
        // and store the object for later user
        $shipping_totals = array();

        /*
        $geozones = $this->getShippingGeoZones();
        foreach ($geozones as $geozone)
        {
            $geozone_id = $geozone->geozone_id;
            // calculate shipping total by passing entire items array to helper
            Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );
            $shipping_total = CitruscartHelperShipping::getTotal( $this->shipping_method_id, $geozone_id, $items );

            $shipping_total = new stdClass();
            $shipping_total->shipping_rate_price = 0;
            $shipping_total->shipping_rate_total = 0;
            $shipping_total->shipping_rate_handling = 0;
            $shipping_total->shipping_tax_total = 0;

            $order_shipping       += $shipping_total->shipping_rate_price + $shipping_total->shipping_rate_handling;
            $order_shipping_tax   += $shipping_total->shipping_tax_total;

            $shipping_totals[] = $shipping_total;
        }
        */
        // store the shipping_totals objects
        //$this->_shipping_totals = $shipping_totals;

        // set object properties
        $this->order_shipping       = $this->shipping->shipping_price + $this->shipping->shipping_extra;
        $this->order_shipping_tax   = $this->shipping->shipping_tax;

        $order_shipping_discount = $this->calculatePerOrderCouponValue($this->order_shipping, 'shipping');
        $this->order_shipping = $this->order_shipping - $order_shipping_discount;
        if( $this->order_shipping < 0)
        {
            $this->order_shipping = 0.00;
        }
        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculateShippingTotals", array( $this ) );
    }

    /**
     * Calculates the per_order coupon discount for the order
     * and the total post-tax/shipping discount
     * and sets order->order_discount
     *
     * @return unknown_type
     */
    function calculateCouponTotals()
    {
        $total = 0.00;

        if (empty($this->_coupons['order_price']))
        {
            $this->order_discount = $total;
            return;
        }

        $coupons_before_tax = Citruscart::getInstance()->get('coupons_before_tax');

        // apply any after tax coupons (both shipping and tax coupons)
        // this will calculate the order_discount (sum of all per_order discounts)
        // and if !coupons_before_tax, order_discount will include all per_product discounts
        // but will not adjust the order_tax, order_shipping, and order_shipping_tax (necessary for record-keeping)

        foreach ($this->_coupons['order_price'] as $coupon)
        {
            // TODO if this is a per_product (coupon_type == 1), adjust the orderitem_final_price?
            // if this is a per_order (coupon_type == 0), calculate the value and add an ordercoupons object
            if (empty($coupon->coupon_type))
            {
                // get the value
                switch ($coupon->coupon_value_type)
                {
                    case "1": // percentage
                        $amount = ($coupon->coupon_value/100) * ($this->order_subtotal + $this->order_tax);
                        break;
                    case "0": // flat-rate
                        $amount = $coupon->coupon_value;
                        break;
                }
            }

            // update the total amount of the discount
            $total += $amount;

            // save the ordercoupons object
            $oc = JTable::getInstance('OrderCoupons', 'CitruscartTable');
            $oc->coupon_id = $coupon->coupon_id;
            $oc->ordercoupon_name = $coupon->coupon_name;
            $oc->ordercoupon_code = $coupon->coupon_code;
            $oc->ordercoupon_value = $coupon->coupon_value;
            $oc->ordercoupon_value_type = $coupon->coupon_value_type;
            $oc->ordercoupon_amount = $amount;

            $this->_ordercoupons[] = $oc;
        }

        // store the total amount of the discount
        //set the total as equal to the order_subtotal + order_tax if its greater than the sum of the two
        $this->order_discount = $total > ($this->order_subtotal + $this->order_tax) ? $this->order_subtotal + $this->order_tax : $total;

        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculateCouponTotals", array( $this ) );

    }

    /**
     *
     * @return unknown_type
     */
    function calculateVendorTotals()
    {
        if (empty($this->_vendors))
        {
            return null;
        }

        $items = $this->getItems();
        if (!is_array($items))
        {
            return;
        }

        $subtotal = 0.00;
        $tax = 0.00;

        // calculate product subtotal and taxes
        // calculate shipping total
        Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
        //Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );
        foreach ($items as $item)
        {
            if (!empty($item->vendor_id))
            {
                // orderitem calculations should have already been completed, so just sum the values for each vendor
                $this->_vendors[$item->vendor_id]->ordervendor_total       += $item->orderitem_final_price + $item->orderitem_tax;
                $this->_vendors[$item->vendor_id]->ordervendor_subtotal    += $item->orderitem_final_price;
                $this->_vendors[$item->vendor_id]->ordervendor_tax         += $item->orderitem_tax;
                // if the shipping method is NOT per-order, calculate the per-item shipping cost
                if (!empty($this->shipping_method_id) && $this->shipping_method_id != '2')
                {
                    $shipping_total = 0;//CitruscartHelperShipping::getTotal( $this->shipping_method_id, $this->getShippingGeoZone(), $item->product_id );
                    $this->_vendors[$item->vendor_id]->ordervendor_shipping     += $shipping_total->shipping_rate_price + $shipping_total->shipping_rate_handling;
                    $this->_vendors[$item->vendor_id]->ordervendor_shipping_tax += $shipping_total->shipping_tax_total;
                }
            }
        }

        // at this point, each vendor's TableOrderVendor object is populated

        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculateVendorTotals", array( $this ) );
    }

    /**
     * Gets the order items
     *
     * @return array of TableOrderItems objects
     */
    function getItems()
    {
        // TODO once all references use this getter, we can do fun things with this method, such as fire a plugin event
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        // if empty($items) && !empty($this->order_id), then this is an order from the db,
        // so we grab all the orderitems from the db
        if (empty($this->_items) && !empty($this->order_id))
        {
            // TODO Do this?  How will this impact Site::CitruscartControllerCheckout->saveOrderItems()?
            //retrieve the order's items
            $model = JModelLegacy::getInstance( 'OrderItems', 'CitruscartModel' );
            $model->setState( 'filter_orderid', $this->order_id);
            $model->setState( 'order', 'tbl.orderitem_name' );
            $model->setState( 'direction', 'ASC' );
            $orderitems = $model->getList();
            foreach ($orderitems as $orderitem)
            {
                unset($table);
                $table = JTable::getInstance( 'OrderItems', 'CitruscartTable' );
                $table->load( $orderitem->orderitem_id );
                $this->addItem( $table );
            }
        }

        $items = $this->_items;
        if (!is_array($items))
        {
            $items = array();
        }

        if (empty($this->_itemschecked))
        {
            // ensure that the items array only has one recurring item in it
            foreach ($items as $key=>$item)
            {
                $shipping = Citruscart::getClass( "CitruscartHelperProduct", 'helpers.product' )->isShippingEnabled($item->product_id);
                if ($shipping) { $this->order_ships = '1'; }

                if (empty($this->_recurringItemExists) && $item->orderitem_recurs)
                {
                    // Only one recurring item allowed per order.
                    // If the item is recurring,
                    // check if there already is a recurring item accounted for in the order
                    // if so, remove this one from the order but leave it in the cart and continue
                    // if not, add its properties
                    $this->_recurringItemExists = true;
                    $this->_recurringItem = $item;
                    $this->recurring_payments          = $item->recurring_payments;
                    $this->recurring_period_interval   = $item->recurring_period_interval;
                    $this->recurring_period_unit       = $item->recurring_period_unit;
                    $this->recurring_trial             = $item->recurring_trial;
                    $this->recurring_trial_period_interval = $item->recurring_trial_period_interval;
                    $this->recurring_trial_period_unit = $item->recurring_trial_period_unit;
                    $this->recurring_trial_price       = $item->recurring_trial_price;
                    $this->recurring_amount             = $item->recurring_price; // TODO Add tax?
                    //$this->recurring_amount            = $item->recurring_amount; // TODO Add tax?
                    // TODO Set some kind of _recurring_item property, so it is easy to get the recurring item later?
                }
                    elseif (!empty($this->_recurringItemExists) && $item->orderitem_recurs)
                {
                    // Only one recurring item allowed per order.
                    // If the item is recurring,
                    // check if there already is a recurring item accounted for in the order
                    // if so, remove this one from the order but leave it in the cart and continue
                    unset($items[$key]);
                }
            }
            $this->_itemschecked = true;
        }
        $this->_items = $items;
        return $this->_items;
    }

    /**
     * Gets the order downloads
     *
     * @return array of CitruscartTableProductDownloads objects
     */
    function getDownloads()
    {
        // TODO Attempt to set this property if it is empty
        return $this->_downloads;
    }

    /**
     * Gets the order vendors
     *
     * @return array of CitruscartTableOrderVendors objects
     */
    function getVendors()
    {
        // TODO Attempt to set this if it is empty
        return $this->_vendors;
    }

    /**
     * Gets the order's shipping total object
     *
     * @return object
     */
    function getShippingTotal( $refresh=false )
    {
        // TODO If not set, should calculate it
        return $this->_shipping_total;
    }

    /**
     * Gets one of the order's tax geozones
     *
     * @return unknown_type
     */
    function getBillingGeoZone()
    {
        $geozone_id = 0;

        $geozones = $this->getBillingGeoZones();
        if (!empty($geozones))
        {
            $geozone_id = $geozones[0]->geozone_id;
        }

        return $geozone_id;
    }

    /**
     * Gets one the order's shipping geozones
     *
     * @return unknown_type
     */
    function getShippingGeoZone()
    {
        $geozone_id = 0;

        $geozones = $this->getShippingGeoZones();
        if (!empty($geozones))
        {
            $geozone_id = $geozones[0]->geozone_id;
        }

        return $geozone_id;
    }

    /**
     * Gets the order's tax geozones
     *
     * @return unknown_type
     */
    function getBillingGeoZones()
    {
        // Set this if it isn't
        if (empty($this->_billing_geozones) && !empty($this->order_id))
        {
            $orderinfo = JTable::getInstance('OrderInfo', 'CitruscartTable');
            $orderinfo->load( array('order_id'=>$this->order_id) );
            $orderinfo->zone_id = $orderinfo->billing_zone_id;
            // TODO What to do about orders that exist from pre 0.5.0 without zone_id
            $this->setAddress( $orderinfo, 'billing' );
        }

        return $this->_billing_geozones;
    }

    /**
     * Gets the order's shipping geozones
     *
     * @return unknown_type
     */
    function getShippingGeoZones()
    {
        // Set this if it isn't
        if (empty($this->_shipping_geozones) && !empty($this->order_id))
        {
            $orderinfo = JTable::getInstance('OrderInfo', 'CitruscartTable');
            $orderinfo->load( array('order_id'=>$this->order_id) );
            $orderinfo->zone_id = $orderinfo->shipping_zone_id;
            // TODO What to do about orders that exist from pre 0.5.0 without zone_id
            $this->setAddress( $orderinfo, 'shipping' );
        }

        return $this->_shipping_geozones;
    }

    /**
     * Gets the order billing address
     * @return unknown_type
     */
    function getBillingAddress()
    {
    	$session = JFactory::getSession();

        // TODO If $this->_billing_address is null, attempt to populate it with the orderinfo fields, or using the billing_address_id (if present)
		/* if(empty($this->_billing_address)){

			if($session->has('citruscart.opc.billingAddress')){
			$new_address = unserialize($session->get('citruscart.opc.billingAddress'));

			print_r($new_address);

			$new_address = JArrayHelper::fromObject($new_address);

			$new_key=array();

			$key=array_keys($new_address );
			}
			for($a=0; $a<count($key); $a++){

				$new_key[$a]=$input_prefix.$key[$a];
			}

			$addressArray=array_combine($new_key,array_values($new_address ));

			$zone = JTable::getInstance('Zones', 'CitruscartTable');

			$zone->load( $new_address['zone_id'] );

			$addressArray['zone_name'] = $zone->zone_name;

			// set the country name
			$country = JTable::getInstance('Countries', 'CitruscartTable');

			$country->load( $new_address['country_id'] );

			$addressArray['country_name'] = $country->country_name;

			$this->_billing_address = $addressArray;

		} */

    	return $this->_billing_address;
    }

    /**
     * Gets the order shipping address
     * @return unknown_type
     */
    function getShippingAddress()
    {
    	$session =  JFactory::getApplication();

        // TODO If $this->_shipping_address is null, attempt to populate it with the orderinfo fields, or using the shipping_address_id (if present)
    	/* if(empty($this->_billing_address)){

    		$new_address = unserialize($session->get('citruscart.opc.shippingAddress'));
			$new_address = JArrayHelper::fromObject($new_address);
    		$new_key=array();

    		$key=array_keys($new_address );

    		for($a=0; $a<count($key); $a++){

    			$new_key[$a]=$input_prefix.$key[$a];
    		}

    		$addressArray=array_combine($new_key,array_values($new_address ));

    		$zone = JTable::getInstance('Zones', 'CitruscartTable');

    		$zone->load( $new_address['zone_id'] );

    		$addressArray['zone_name'] = $zone->zone_name;

    		// set the country name
    		$country = JTable::getInstance('Countries', 'CitruscartTable');

    		$country->load( $new_address['country_id'] );

    		$addressArray['country_name'] = $country->country_name;

    		$this->_shipping_address = $addressArray;

    	} */


    	return $this->_shipping_address;
    }

    /**
     * Generates a unique invoice number based on the order's properties
     *
     * @return string from $order_date-$order_time-$user_id
     */
    function getInvoiceNumber( $refresh=false )
    {
        if (empty($this->_order_number) || $refresh)
        {
            $nullDate   = $this->_db->getNullDate();
            if (empty($this->created_date) || $this->created_date == $nullDate)
            {
                $date = JFactory::getDate();
                $this->created_date = $date->toSql();
            }
            $order_date = JHTML::_('date', $this->created_date, '%Y%m%d');
            $order_time = JHTML::_('date', $this->created_date, '%H%M%S');
            $user_id = $this->user_id;
            $this->_order_number = $order_date.'-'.$order_time.'-'.$user_id;
        }

        return $this->_order_number;
    }

    /**
     * Gets the ordercoupons for this order
     * and returns an array of objects
     *
     * @return unknown_type
     */
    function getOrderCoupons()
    {
        // TODO Attempt to set this if it is empty
        return $this->_ordercoupons;
    }

    /**
     * Gets the tax rates applicable to this order
     * and returns an array of taxrate objects
     *
     * @return array    An array of objects
     */
    function getTaxRates()
    {
        if (empty($this->_taxrates))
        {
            $this->calculateTaxTotals();
        }

        return $this->_taxrates;
    }

    /**
     * Gets the order's tax amount for the specified tax rate
     *
     * @return float if taxrate applies to this order, null otherwise
     */
    function getTaxRateAmount( $taxrate_id )
    {
        $amount = null;

        if (empty($this->_taxrate_amounts))
        {
            $this->calculateTaxTotals();
        }

        if (!empty($this->_taxrate_amounts[$taxrate_id]))
        {
            $amount = $this->_taxrate_amounts[$taxrate_id];
        }

        return $amount;
    }

    /**
     * Gets the tax classes applicable to this order
     * and returns an array of taxclass objects
     *
     * @return array    An array of objects
     */
    function getTaxClasses()
    {
        if (empty($this->_taxclasses))
        {
            $this->calculateTaxTotals();
        }

        return $this->_taxclasses;
    }

    /**
     * Gets the order's tax amount for the specified tax class
     *
     * @return float if taxclass applies to this order, null otherwise
     */
    function getTaxClassAmount( $taxclass_id )
    {
        $amount = null;

        if (empty($this->_taxclass_amounts))
        {
            $this->calculateTaxTotals();
        }

        if (!empty($this->_taxclass_amounts[$taxclass_id]))
        {
            $amount = $this->_taxclass_amounts[$taxclass_id];
        }

        return $amount;
    }

    /**
     * Gets the total order weight
     *
     * @return number Total order weight
     */
    function getOrderWeight()
    {
        if (empty($this->_weight))
        {
            $this->calculateOrderWeight();
        }

        return $this->_weight;
    }


    function calculateOrderWeight()
    {
      $items = $this->getItems();
      $result = 0;
      foreach( $items as $item )
      {
        $result += $item->orderitem_weight * $item->orderitem_quantity ;
      }

      return $result;
    }

    /**
     * Sets the order's billing or shipping address
     *
     * @param $type     string      billing | shipping
     * @param $address  object      CitruscartAddresses() object
     * @return object
     */
    function setAddress( $address, $type='both'  )
    {

      switch (strtolower($type))
        {
            case "billing":
                $this->_billing_address = $address;
                break;
            case "shipping":
                $this->_shipping_address = $address;
                break;
            case "both":
            default:
                $this->_shipping_address = $address;
                $this->_billing_address = $address;
                break;
        }
        $this->setGeozones();
    }

    /**
     * Based on the object's addresses,
     * sets the shipping and billing geozones
     *
     * @return unknown_type
     */
    function setGeozones( $geozones=null, $type='billing' )
    {
		//print_r($geozones); exit;

        if (!empty($geozones))
        {
            switch ($type)
            {
                case "shipping":
                    $this->_shipping_geozones = $geozones;
                    break;
                case "billing":
                default:
                     $this->_billing_geozones = $geozones;
                    break;
            }
        }
            else
        {
            Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );

            if (!empty($this->_billing_address))
            {
            	$this->_billing_geozones = CitruscartHelperShipping::getGeoZones( $this->_billing_address->zone_id, '1', $this->_billing_address->postal_code  );
            }
            if (!empty($this->_shipping_address))
            {
                $this->_shipping_geozones = CitruscartHelperShipping::getGeoZones( $this->_shipping_address->zone_id, '2', $this->_shipping_address->postal_code );
            }
        }
    }

    /**
     * Checks whether an order is recurring
     * @return boolean
     */
    function isRecurring()
    {
        if (empty($this->order_id))
        {
            // check the $_recurringItemExists value
            return $this->_recurringItemExists;
        }

        return $this->order_recurs;
    }

    public function isPaymentRequired()
    {
        $return = true;

        if( $this->isRecurring() )
        {
            if( (float)$this->getRecurringItem()->recurring_price == (float)'0.00' )
            {
                $return = false;
            }
        }
        else {
            $this->calculateTotals();

            if ((float)$this->order_total == (float)'0.00')
            {
                $return = false;
            }
        }
        return $return;
    }

    public function isShippingRequired()
    {
        $items = $this->getItems();
        if (empty($this->order_ships)) {
            return false;
        }
        return true;
    }

    /**
     * Gets an order's recurring item, if it exists
     * @return boolean
     */
    function getRecurringItem()
    {
        $is_recurring = $this->isRecurring();

        if (empty($is_recurring))
        {
            return false;
        }

        if (empty($this->_recurringItem))
        {
            // get the item from the DB
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'OrderItems', 'CitruscartModel' );
            $model->setState( 'filter_orderid', $this->order_id );
            $model->setState( 'filter_recurs', '1' );
            if ($orderitems = $model->getList())
            {
                $this->_recurringItem = $orderitems[0];
            }
        }

        return $this->_recurringItem;
    }

    public function delete( $oid=null )
    {
        $delete = array();

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }

        // this should all be a transaction...
        // Delete all the orderitems, orderpayments, ordershipping, etc

        $delete['items'] = $this->deleteItems( 'items', $oid );
        $delete['payments'] = $this->deleteItems( 'payments', $oid );
        $delete['info'] = $this->deleteItems( 'info', $oid );
        $delete['shippings'] = $this->deleteItems( 'shippings', $oid );
        $delete['history'] = $this->deleteItems( 'history', $oid );
        $delete['taxclasses'] = $this->deleteItems( 'taxclasses', $oid );
        $delete['taxrates'] = $this->deleteItems( 'taxrates', $oid );
        $delete['coupons'] = $this->deleteItems( 'coupons', $oid );

        $delete['order'] = parent::delete( $oid );

        $this->deleteResults = $delete;

        return parent::check();
    }

    public function deleteItems( $type, $oid=null )
    {
        $failed = false;

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }

        DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = DSCModel::getInstance( 'Order'.$type, 'CitruscartModel' );
        $model->setState('filter_orderid', $this->$k );
        if ($items = $model->getList())
        {
            $table = $model->getTable();
            $table_pk = $table->getKeyName();
            foreach ($items as $item)
            {
                if (!$table->delete( $item->$table_pk ))
                {
                    $errors = $table->getErrors();
                    if (!empty($errors))
                    {
                        foreach ($errors as $key=>$error)
                        {
                            $error = trim( $error );
                            if (!empty($error))
                            {
                                $failed = true;
                                $this->setError($error);
                            }
                        }
                    }
                }
            }
        }

        if ($failed) {
            return false;
        }
        return true;
    }

     /**
     * Adds a credit amount to the order
     *
     * @param float
     * @return void
     */
    function addCredit( $amount )
    {
        $this->order_credit = (float) $amount;
    }

    /**
     * Adds a coupon to the order
     *
     * @param object    $coupon      a TableCoupons object
     * @return void
     * @enterprise
     */
    function addCoupon( $coupon )
    {
        switch ($coupon->coupon_group)
        {
            case 'shipping':
                // Only Per Order
                $this->addOrderCoupon( $coupon, 'shipping' );
                break;
            case 'price':
            default:
               switch ($coupon->coupon_type)
                {
                    case "1":
                        // per product
                         $this->addProductCoupon( $coupon );
                        break;
                    case "0":
                        // per order
                        $this->addOrderCoupon( $coupon, 'price' );
                        break;
                }
                break;
        }

        if (!empty($coupon->coupon_id) && $coupon->coupon_automatic != '1')
        {
            $this->_usercoupons[] = $coupon;
        }
    }

    /**
     * Adds a per_order coupon to the order
     *
     * @param object    $coupon      a TableCoupons object
     * @param type      $type        could be price, tax, shipping
     * @return void
     * @enterprise
     */
    function addOrderCoupon( $coupon, $type = 'price' )
    {
        switch($type)
        {
            case 'shipping':
                if (empty($this->_coupons['order_shipping']))
                {
                    $this->_coupons['order_shipping'] = array();
                }
                $this->_coupons['order_shipping'][$coupon->coupon_id] = $coupon;
                break;
            case 'price':
            default:
                if (empty($this->_coupons['order_price']))
                {
                    $this->_coupons['order_price'] = array();
                }
                $this->_coupons['order_price'][$coupon->coupon_id] = $coupon;
                break;
        }

    }

    /**
     * Adds a per_product coupon to the order
     *
     * @param object    $coupon      a TableCoupons object
     * @param type      $type        could be price, tax, shipping
     * @return void
     * @enterprise
     */
    function addProductCoupon( $coupon, $type = 'price' )
    {
        switch($type)
        {
            case 'shipping':
                if (empty($this->_coupons['product_shipping']))
                {
                    $this->_coupons['product_shipping'] = array();
                }
                $this->_coupons['product_shipping'][$coupon->coupon_id] = $coupon;
                break;
            case 'price':
            default:
                if (empty($this->_coupons['product_price']))
                {
                    $this->_coupons['product_price'] = array();
                }
                $this->_coupons['product_price'][$coupon->coupon_id] = $coupon;
                break;
        }

    }

    /**
     * Calculates the discounted value of a per_product coupon
     *
     * @param   $product_id     the product id
     * @param   $value          the original value on which calculate the discount
     * @param   $type           could be price, tax, shipping
     */
    function calculatePerProductCouponValue( $product_id, $value, $type = 'price' )
    {
        $total = 0.00;

        switch($type)
        {
            case 'shipping':
                $coupons = !empty($this->_coupons['product_shipping']) ? $this->_coupons['product_shipping'] : array();
                break;
            default:
            case 'price':
                $coupons = !empty($this->_coupons['product_price']) ? $this->_coupons['product_price'] : array();
                break;
        }

        Citruscart::load('CitruscartHelperCoupon', 'helpers.coupon');
        // If there are per_product coupons that apply to this product
        // adjust the orderitem_final_price here.
        // remember that the orderitem_final_price already == product_price * orderitem_quantity
        if( $coupons )
        {
            foreach ($coupons as $coupon)
            {
                $is_for_this_product = CitruscartHelperCoupon::checkByProductIds( $coupon->coupon_id, array($product_id) );

                if( $is_for_this_product )
                {
                    switch ($coupon->coupon_value_type)
                    {
                        case "1": // percentage
                            $amount = ($coupon->coupon_value/100) * ($value);
                            break;
                        case "0": // flat-rate
                            $amount = $coupon->coupon_value;
                            break;
                    }

                    // update the total amount of the discount
                    $total += $amount;

                    // save the ordercoupons object
                    $oc = JTable::getInstance('OrderCoupons', 'CitruscartTable');
                    $oc->coupon_id = $coupon->coupon_id;
                    $oc->ordercoupon_name = $coupon->coupon_name;
                    $oc->ordercoupon_code = $coupon->coupon_code;
                    $oc->ordercoupon_value = $coupon->coupon_value;
                    $oc->ordercoupon_value_type = $coupon->coupon_value_type;
                    $oc->ordercoupon_amount = $amount;

                    $this->_ordercoupons[] = $oc;
                }
            }
        }

        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculatePerProductCouponValue", array( $this ) );

        if( $total > $value )
        {
            return 0;
        }
        else
        {
            return $value - $total;
        }
    }

    /**
     * Calculates the per_order coupon discount for the order
     * and the total post-tax/shipping discount
     * and sets order->order_discount
     *
     * @return unknown_type
     * @enterprise
     */
    function calculatePerOrderCouponValue($value, $type = 'price')
    {
        $total = 0.00;

        switch($type)
        {
            case 'shipping':
                $coupons = !empty($this->_coupons['order_shipping']) ? $this->_coupons['order_shipping'] : array();
                break;
            default:
            case 'price':
                $coupons = !empty($this->_coupons['order_price']) ? $this->_coupons['order_price'] : array();
                break;
        }

        if( $coupons )
        {
            foreach ($coupons as $coupon)
            {
                switch ($coupon->coupon_value_type)
                {
                    case "1": // percentage
                        $amount = ($coupon->coupon_value/100) * ($value);
                        break;
                    case "0": // flat-rate
                        $amount = $coupon->coupon_value;
                        break;
                }

                // update the total amount of the discount
                $total += $amount;

                // save the ordercoupons object
                $oc = JTable::getInstance('OrderCoupons', 'CitruscartTable');
                $oc->coupon_id = $coupon->coupon_id;
                $oc->ordercoupon_name = $coupon->coupon_name;
                $oc->ordercoupon_code = $coupon->coupon_code;
                $oc->ordercoupon_value = $coupon->coupon_value;
                $oc->ordercoupon_value_type = $coupon->coupon_value_type;
                $oc->ordercoupon_amount = $amount;

                $this->_ordercoupons[] = $oc;
            }
        }

        // Allow this to be modified via plugins

        JFactory::getApplication()->triggerEvent( "onCalculatePerOrderCouponValue", array( $this ) );

       return $total;

    }

    /**
     * Gets the coupons for this order
     * and returns an array of objects
     *
     * @return unknown_type
     * @enterprise
     */
    function getCoupons()
    {
        // TODO Attempt to set this if it is empty
        if($this->_coupons)
            return $this->_coupons;
        else
            return array();
    }

    /**
     * Gets the coupons for this order
     * and returns an array of objects
     *
     * @return unknown_type
     * @enterprise
     */
    function makeAnonymous()
    {
       $this->_anonymous = 1;
    }
    /**
     * Gets the user-submitted coupons for this order
     * and returns an array of objects
     *
     * @return unknown_type
     * @enterprise
     */
    function getUserCoupons()
    {
        if (!empty($this->_usercoupons))
        {
            return $this->_usercoupons;
        }
        else
        {
            // TODO create the array from _coupons, if possible
            return array();
        }
    }

    /**
     * Gets the shipping rate object
     */
    public function getShippingRate()
    {
        if (empty($this->shipping))
        {
            $this->shipping = new JObject();
            $this->shipping->shipping_price    = '0';
            $this->shipping->shipping_extra    = '0';
            $this->shipping->shipping_code     = null;
            $this->shipping->shipping_name     = null;
            $this->shipping->shipping_tax      = '0';
            $this->shipping->shipping_type     = null;
        }
        return $this->shipping;
    }

    /**
     * Sets the shipping object for the order from a shipping_rate array,
     * a standard array created by all shipping plugins as a valid shipping rate option during checkout
     *
     * @param array $rate
     */
    public function setShippingRate( $rate )
    {
        $this->shipping = new JObject();
        $this->shipping->shipping_price    = (isset($rate['price'])) ? $rate['price']: "" ;
        $this->shipping->shipping_extra    = (isset($rate['extra'])) ? $rate['extra'] : "";
        $this->shipping->shipping_code     = (isset($rate['code'])) ? $rate['code'] : "";
        $this->shipping->shipping_name     = (isset($rate['name'])) ? $rate['name'] : "";
        $this->shipping->shipping_tax      = (isset($rate['tax'])) ? $rate['tax'] : "";
        $this->shipping->shipping_type     = (isset($rate['element'])) ? $rate['element'] : "";

    }
}
