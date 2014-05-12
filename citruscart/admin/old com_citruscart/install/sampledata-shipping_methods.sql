-- -----------------------------------------------------
-- Table `#__citruscart_shippingmethods` sample data
-- -----------------------------------------------------

INSERT INTO `#__citruscart_shippingmethods` (`shipping_method_id`, `shipping_method_name`, `tax_class_id`, `shipping_method_enabled`, `shipping_method_type`, `subtotal_minimum`) VALUES
(1, 'Flat Rate', 2, 1, 1, 0.00000),
(2, 'Ground', 2, 1, 0, 0.00000),
(3, 'Overnight', 2, 1, 0, 0.00000),
(4, 'Air', 2, 1, 0, 0.00000),
(5, 'Free Shipping', 2, 1, 2, 99.99000);