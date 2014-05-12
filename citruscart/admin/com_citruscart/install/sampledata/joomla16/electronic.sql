# ELECTRONIC STORE SAMPLE DATA
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#
--
-- Dumping data for Table `#__citruscart_manufacturers`
--

INSERT IGNORE INTO `#__citruscart_manufacturers` (`manufacturer_id`, `manufacturer_name`, `manufacturer_image`, `manufacturer_enabled`, `created_date`, `modified_date`) VALUES
(1, 'HTC', '', 1, NOW(), NOW()),
(2, 'Acer', '', 1, NOW(), NOW()),
(3, 'AMD', '', 1, NOW(), NOW()),
(4, 'Apple', '', 1, NOW(), NOW()),
(5, 'AT&T', '', 1, NOW(), NOW()),
(6, 'Canon', '', 1, NOW(), NOW()),
(7, 'Dell', '', 1, NOW(), NOW()),
(8, 'Gateway', '', 1, NOW(), NOW());


--
-- DELETE the category 1
--
DELETE FROM `#__citruscart_categories` WHERE `#__citruscart_categories`.`category_id` = 1 LIMIT 1;


--
-- Dumping data for Table `#__citruscart_categories`
--

INSERT IGNORE INTO `#__citruscart_categories` (`category_id`, `category_name`, `category_description`, `category_thumb_image`, `category_full_image`, `created_date`, `modified_date`, `lft`, `rgt`, `parent_id`, `category_enabled`, `isroot`) VALUES
(1, 'All Categories', '', NULL, NULL, NOW(), NOW(), 1, 30, 0, 1, 1),
(2, 'Laptops & Notebooks', '', '', NULL, NOW(), NOW(), 22, 23, 1, 1, 0),
(3, 'Phones & PDAs', '', '', NULL, NOW(), NOW(), 26, 27, 1, 1, 0),
(4, 'Components', '', '', NULL, NOW(), NOW(), 4, 15, 1, 1, 0),
(5, 'Software', '', '', NULL, NOW(), NOW(), 28, 29, 1, 1, 0),
(6, 'Cameras', '', '', NULL, NOW(), NOW(), 2, 3, 1, 1, 0),
(7, 'MP3 Players', '', '', NULL, NOW(), NOW(), 24, 25, 1, 1, 0),
(8, 'Desktops', '', '', NULL, NOW(), NOW(), 16, 21, 1, 1, 0),
(9, 'PC', '', '', NULL, NOW(), NOW(), 17, 18, 8, 1, 0),
(10, 'Mac', '', '', NULL, NOW(), NOW(), 19, 20, 8, 1, 0),
(11, 'Monitors', '', '', NULL, NOW(), NOW(), 5, 6, 4, 1, 0),
(12, 'Mice and Trackballs', '', '', NULL, NOW(), NOW(), 7, 8, 4, 1, 0),
(13, 'Printers', '', '', NULL, NOW(), NOW(), 9, 10, 4, 1, 0),
(14, 'Scanners', '', '', NULL, NOW(), NOW(), 11, 12, 4, 1, 0),
(15, 'Web Cameras', '', '', NULL, NOW(), NOW(), 13, 14, 4, 1, 0);

--
-- Dumping data for Table `#__citruscart_products`
--

INSERT IGNORE INTO `#__citruscart_products` (`product_id`, `vendor_id`, `manufacturer_id`, `product_description`, `product_description_short`, `product_full_image`, `product_weight`, `product_length`, `product_width`, `product_height`, `product_url`, `product_sku`, `product_model`, `product_check_inventory`, `product_ships`, `ordering`, `created_date`, `modified_date`, `publish_date`, `unpublish_date`, `product_name`, `product_alias`, `tax_class_id`, `product_enabled`, `product_notforsale`, `quantity_restriction`, `quantity_min`, `quantity_max`, `quantity_step`, `product_images_path`, `product_files_path`, `product_recurs`, `recurring_payments`, `recurring_period_interval`, `recurring_period_unit`, `recurring_trial`, `recurring_trial_period_interval`, `recurring_trial_period_unit`, `recurring_trial_price`, `product_params`, `product_layout`, `product_subscription`, `subscription_lifetime`, `subscription_period_interval`, `subscription_period_unit`, `product_sql`, `product_listprice`, `product_listprice_enabled`, `product_rating`, `product_comments`) VALUES 
('1', '0', '1', 'A Ferrari is not just about speed. It is an expression of beauty, power, excitement and pleasure. A Ferrari exudes excellence from the original concept to the finished product, and the Ferrari One 200 offers the same sensations down to the finest detail. Race to your destination with this attractive, less-than-1-inch-thin netbook that delivers superb performance!', 'A Ferrari is not just about speed. It is an expression of beauty, power, excitement and pleasure. A Ferrari exudes excellence from the original concept to the finished product, and the Ferrari One 200 offers the same sensations down to the finest detail. Race to your destination with this attractive, less-than-1-inch-thin netbook that delivers superb performance!', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), '2020-12-09 22:21:31', 'Ferrari One 200', 'electronics-laptop', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
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
('2', '0', '2', 'Desciption of product.', 'Short description of the product.', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), '2020-12-09 22:21:31', 'Nokia', 'electronics-samsung', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
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
('3', '0', '3', 'Desciption of product.', 'Short description of the product.', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, NULL, NULL, '0', '0', '1', NOW(), NOW(), NOW(), '2020-12-09 22:21:31', 'Motorola', 'electronics-samsung', NULL, '1', '0', '0', NULL, NULL, NULL, '', '', '0', '0', '0', 'D', '0', '0', 'D', '0.00000', 'amigos_commission_override=
billets_ticket_limit_increase=
billets_ticket_limit_exclusion=
juga_group_csv_add=
juga_group_csv_remove=
core_user_change_gid=0
core_user_new_gid=
billets_hour_limit_increase=
billets_hour_limit_exclusion=
juga_group_csv_add_expiration=
juga_group_csv_remove_expiration=', '', '0', '0', '0', 'D', '', '0.00000', '0', '0.00000', '0');


--
-- Dumping data for Table `#__citruscart_productprices`
--
INSERT IGNORE INTO `#__citruscart_productprices` (`product_price_id`, `product_id`, `product_price`, `product_price_startdate`, `product_price_enddate`, `created_date`, `modified_date`, `group_id`, `price_quantity_start`, `price_quantity_end`) VALUES
(NULL, '1', '8.74000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '2', '6.24000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0'),
(NULL, '3', '8.74000', NOW(), DATE_ADD(NOW(),INTERVAL 5 YEAR), NOW(), NOW(), '1', '0', '0');

--
-- Dumping data for table `jos_Citruscart_orders`
--

INSERT IGNORE INTO`#__citruscart_orders` (`order_id`, `user_id`, `shipping_method_id`, `order_number`, `order_total`, `order_subtotal`, `order_tax`, `order_shipping`, `order_shipping_tax`, `order_discount`, `order_currency`, `currency_id`, `order_state_id`, `created_date`, `modified_date`, `customer_note`, `ip_address`, `order_ships`, `order_recurs`, `recurring_amount`, `recurring_payments`, `recurring_period_interval`, `recurring_period_unit`, `recurring_trial`, `recurring_trial_period_interval`, `recurring_trial_period_unit`, `recurring_trial_price`, `completed_tasks`, `quantities_updated`) VALUES
(NULL, 25, 0, '', 4248.88000, 5399.00000, 647.88, 190.00, 12.00, 2000.00, 'currency_name=US Dollar\ncurrency_code=USD\nsymbol_left=$\nsymbol_right=\ncurrency_decimals=2\ndecimal_separator=.\nthousands_separator=,\nexchange_rate=1.00000000\nupdated_date=2010-11-12 08:35:39\n\n', 1, 15, '2010-12-15 12:17:21', '2010-12-15 12:17:21', '', '::1', 1, 0, 0.00000, 0, 0, '', 0, 0, '', 0.00000, 0, 0);


INSERT IGNORE INTO`#__citruscart_productcategoryxref` (`category_id`, `product_id`) VALUES
('2', '1'),
('5', '2'),
('8', '3');