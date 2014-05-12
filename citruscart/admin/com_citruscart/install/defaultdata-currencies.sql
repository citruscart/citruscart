-- -----------------------------------------------------
-- Table `#__citruscart_currencies` default data
-- -----------------------------------------------------

INSERT IGNORE INTO `#__citruscart_currencies` (`currency_id`, `currency_name`, `currency_code`, `symbol_left`, `symbol_right`, `currency_decimals`, `decimal_separator`, `thousands_separator`, `created_date`, `modified_date`, `currency_enabled`) VALUES
(1, 'US Dollar', 'USD', '$', '', 2, '.', ',', NOW(), NOW(), 1),
(2, 'Japanese Yen', 'JPY', '¥', '', 3, '.', ',', NOW(), NOW(), 1),
(3, 'Euro', 'EUR', '€', '', 2, '.', ',', NOW(), NOW(), 1),
(4, 'British Pound', 'GBP', '£', '', 2, '.', ',', NOW(), NOW(), 1);
