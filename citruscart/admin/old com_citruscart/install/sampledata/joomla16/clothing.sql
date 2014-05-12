# CLOTHING STORE SAMPLE DATA
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
# 
--
-- Dumping data for Table `#__citruscart_manufacturers`
--

INSERT IGNORE INTO `#__citruscart_manufacturers` (`manufacturer_id`, `manufacturer_name`, `manufacturer_image`, `manufacturer_enabled`, `created_date`, `modified_date`) VALUES
(1, 'Esprit', '', 1, NOW(), NOW()),
(2, 'Gap', '', 1, NOW(), NOW()),
(3, 'Guess', '', 1, NOW(), NOW()),
(4, 'Levis', '', 1, NOW(), NOW()),
(5, 'Nike', '', 1, NOW(), NOW()),
(6, 'Ralph Lauren', '', 1, NOW(), NOW()),
(7, 'Tommy Hilfiger', '', 1, NOW(), NOW());


--
-- DELETE the category 1
--
DELETE FROM `#__citruscart_categories` WHERE `#__citruscart_categories`.`category_id` = 1 LIMIT 1;

--
-- Dumping data for Table `#__citruscart_categories`
--

INSERT IGNORE INTO `#__citruscart_categories` (`category_id`, `category_name`, `category_description`, `category_thumb_image`, `category_full_image`, `created_date`, `modified_date`, `lft`, `rgt`, `parent_id`, `category_enabled`, `isroot`) VALUES
(1, 'All Categories', '', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 14, 0, 1, 1),
(2, 'Women\'s Clothing', '', '', NULL, NOW(), NOW(), 2, 3, 1, 1, 0),
(3, 'Men\'s Clothing', '', '', NULL, NOW(), NOW(), 4, 5, 1, 1, 0),
(4, 'Girls Clothing', '', '', NULL, NOW(), NOW(), 6, 7, 1, 1, 0),
(5, 'Boys Clothing', '', '', NULL, NOW(), NOW(), 8, 9, 1, 1, 0),
(6, 'Infants & Toddlers Clothing', '', '', NULL, NOW(), NOW(), 10, 11, 1, 1, 0),
(7, 'Swimwear', '', '', NULL, NOW(), NOW(), 12, 13, 1, 1, 0);


--
-- Dumping data for Table `#__citruscart_products`
--

INSERT IGNORE INTO `#__citruscart_products` (`product_id`, `vendor_id`, `manufacturer_id`, `product_description`, `product_description_short`, `product_full_image`, `product_weight`, `product_length`, `product_width`, `product_height`, `product_url`, `product_sku`, `product_model`, `product_check_inventory`, `product_ships`, `ordering`, `created_date`, `modified_date`, `publish_date`, `unpublish_date`, `product_name`, `product_alias`, `tax_class_id`, `product_enabled`, `product_notforsale`, `quantity_restriction`, `quantity_min`, `quantity_max`, `quantity_step`, `product_images_path`, `product_files_path`, `product_recurs`, `recurring_payments`, `recurring_period_interval`, `recurring_period_unit`, `recurring_trial`, `recurring_trial_period_interval`, `recurring_trial_period_unit`, `recurring_trial_price`, `product_params`, `product_layout`, `product_subscription`, `subscription_lifetime`, `subscription_period_interval`, `subscription_period_unit`, `product_sql`, `product_listprice`, `product_listprice_enabled`, `product_rating`, `product_comments`) VALUES 
('1', '0', '2', 'A basic long sleev shirt with blue orange and red floral patterns. The nekline has a bow at the middle of it. Great for everyday where. In used condition - wash fade.', 'A basic long sleev shirt with blue orange and red floral patterns. The nekline has a bow at the middle of it. Great for everyday where. In used condition - wash fade.', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), 'GAP Black Long Sleved Floral Patterned Shirt', 'clothing-girls', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '0.00000', '0', '0.00000', '0'),
('2', '0', '1', 'Quality basic by GYMBOREE! Looks great with ESPRIT checkered pants! ', 'Quality basic by GYMBOREE! Looks great with ESPRIT checkered pants! ', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), 'GYMBOREE Short Sleeve T-Shirt Polo Top - 24 mos', 'clothing-toddler', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '24.99000', '1', '0.00000', '0'),
('3', '0', '3', 'SEXY, CLASSIC beaded string bikini bottoms! Going to Europe or Brazil soon...or maybe just your local beach or pool! Check out our coordinating separates! ', 'SEXY, CLASSIC beaded string bikini bottoms! Going to Europe or Brazil soon...or maybe just your local beach or pool! Check out our coordinating separates! ', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), 'GUESS Swimsuit Separates - BOTTOMS - Large - BRAND NEW!', 'clothing-swimwear', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '45.00000', '1', '0.00000', '0'),
('4', '0', '4', 'Wonderful LEVIS fitted sweater! Comfy cotton looks great with jeans or a pair of black dress pants, under a jacket. What versatility...and quality you know by LEVIS! ', 'Wonderful LEVIS fitted sweater! Comfy cotton looks great with jeans or a pair of black dress pants, under a jacket. What versatility...and quality you know by LEVIS! ', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), 'LEVIS RED TAB Cotton Variegated Blue Sweater - Juniors Small', 'clothing-women', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '68.75000', '1', '0.00000', '0'),
('5', '0', '7', 'Trendy city style in this shirt by TOMMY HILFIGER! Very light weight breezy plaid fabric will be great for any spring or summer day! BRAND NEW for a fraction of retail!', 'Trendy city style in this shirt by TOMMY HILFIGER! Very light weight breezy plaid fabric will be great for any spring or summer day! BRAND NEW for a fraction of retail!', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), 'TOMMY HILFIGER Short Sleeve Button Front Shirt - Mens XLarge - BRAND NEW!', 'clothing-men', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '75.00000', '1', '0.00000', '0');

--
-- Dumping data for Table `#__citruscart_productprices`
--
INSERT IGNORE INTO `#__citruscart_productprices` (`product_price_id`, `product_id`, `product_price`, `product_price_startdate`, `product_price_enddate`, `created_date`, `modified_date`, `group_id`, `price_quantity_start`, `price_quantity_end`) VALUES
(NULL, '1', '8.74000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '2', '6.24000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '3', '8.74000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '4', '23.74000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '5', '37.50000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0');


--
-- Dumping data for table `jos_Citruscart_orders`
--

INSERT IGNORE INTO `#__citruscart_orders` (`order_id`, `user_id`, `shipping_method_id`, `order_number`, `order_total`, `order_subtotal`, `order_tax`, `order_shipping`, `order_shipping_tax`, `order_discount`, `order_currency`, `currency_id`, `order_state_id`, `created_date`, `modified_date`, `customer_note`, `ip_address`, `order_ships`, `order_recurs`, `recurring_amount`, `recurring_payments`, `recurring_period_interval`, `recurring_period_unit`, `recurring_trial`, `recurring_trial_period_interval`, `recurring_trial_period_unit`, `recurring_trial_price`, `completed_tasks`, `quantities_updated`) VALUES
(NULL, 25, 0, '', 4248.88000, 5399.00000, 647.88, 190.00, 12.00, 2000.00, 'currency_name=US Dollar\ncurrency_code=USD\nsymbol_left=$\nsymbol_right=\ncurrency_decimals=2\ndecimal_separator=.\nthousands_separator=,\nexchange_rate=1.00000000\nupdated_date=2010-11-12 08:35:39\n\n', 1, 15, '2010-12-15 12:17:21', '2010-12-15 12:17:21', '', '::1', 1, 0, 0.00000, 0, 0, '', 0, 0, '', 0.00000, 0, 0);


INSERT IGNORE INTO`#__citruscart_productcategoryxref` (`category_id`, `product_id`) VALUES
('1', '1'),
('6', '2'),
('7', '3'),
('2', '4'),
('3', '5');
