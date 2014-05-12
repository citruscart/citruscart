-- -----------------------------------------------------
-- Dumping data for table `#__citruscart_orderstates`
-- -----------------------------------------------------
INSERT IGNORE INTO `#__citruscart_orderstates` (`order_state_id`, `order_state_name`) VALUES
(1, 'Pending'),
(2, 'Processing'),
(3, 'Shipped'),
(7, 'Canceled'),
(5, 'Complete'),
(8, 'Denied'),
(9, 'Canceled Reversal'),
(10, 'Failed'),
(11, 'Refunded'),
(12, 'Reversed'),
(13, 'Chargeback'),
(14, 'Unspecified Error'),
(15, 'Pre-payment'),
(16, 'Payment Scheduled'),
(17, 'Payment Received');
