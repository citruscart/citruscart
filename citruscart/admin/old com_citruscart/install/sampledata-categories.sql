-- -----------------------------------------------------
-- Table `#__citruscart_categories` sample data
-- -----------------------------------------------------

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