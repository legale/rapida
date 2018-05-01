/* Drop for table s_blog */
DROP TABLE IF EXISTS `s_blog`;
/* Create table s_blog */
CREATE TABLE `s_blog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `trans` varchar(255) DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  `meta_title` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `annotation` text,
  `text` longtext,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_blog */
;
/* Drop for table s_brands */
DROP TABLE IF EXISTS `s_brands`;
/* Create table s_brands */
CREATE TABLE `s_brands` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trans` varchar(255) CHARACTER SET ascii NOT NULL,
  `trans2` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `description` text,
  `image` varchar(255) DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  UNIQUE KEY `trans` (`trans`) USING BTREE,
  KEY `id_name` (`id`,`name`),
  KEY `trans2` (`trans2`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_brands */
INSERT INTO `s_brands` (`id`,`name`,`trans`,`trans2`,`meta_title`,`meta_keywords`,`meta_description`,`description`,`image`,`image_id`) VALUES
(1, 'brand', 'brandddd', '', 'brand', 'brand', 'brand', '', '', 0);
/* Drop for table s_cache_integer */
DROP TABLE IF EXISTS `s_cache_integer`;
/* Create table s_cache_integer */
CREATE TABLE `s_cache_integer` (
  `updated` date DEFAULT '1000-01-01',
  `keyhash` binary(16) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`keyhash`) USING BTREE,
  UNIQUE KEY `keyhash_value` (`keyhash`,`value`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* $skipdata is true. Data skipped s_cache_integer */
/* Drop for table s_categories */
DROP TABLE IF EXISTS `s_categories`;
/* Create table s_categories */
CREATE TABLE `s_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT '',
  `meta_keywords` varchar(255) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `description` text,
  `trans` varchar(255) DEFAULT '',
  `trans2` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `url` (`trans`),
  KEY `parent_id` (`parent_id`),
  KEY `pos` (`pos`),
  KEY `visible` (`visible`),
  KEY `url2` (`trans2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_categories */
;
/* Drop for table s_categories_features */
DROP TABLE IF EXISTS `s_categories_features`;
/* Create table s_categories_features */
CREATE TABLE `s_categories_features` (
  `category_id` smallint(5) unsigned NOT NULL,
  `feature_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`feature_id`,`category_id`),
  UNIQUE KEY `cid_fid` (`category_id`,`feature_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_categories_features */
INSERT INTO `s_categories_features` (`category_id`,`feature_id`) VALUES
(1, 1);
/* Drop for table s_comments */
DROP TABLE IF EXISTS `s_comments`;
/* Create table s_comments */
CREATE TABLE `s_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) DEFAULT '',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `text` text,
  `type` enum('product','blog') DEFAULT 'blog',
  `approved` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`object_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_comments */
;
/* Drop for table s_coupons */
DROP TABLE IF EXISTS `s_coupons`;
/* Create table s_coupons */
CREATE TABLE `s_coupons` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('absolute','percentage') NOT NULL DEFAULT 'absolute',
  `value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `min_order_price` decimal(10,2) unsigned DEFAULT '0.00',
  `single` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `usages` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_coupons */
;
/* Drop for table s_currencies */
DROP TABLE IF EXISTS `s_currencies`;
/* Create table s_currencies */
CREATE TABLE `s_currencies` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '0',
  `sign` varchar(20) DEFAULT '',
  `code` char(3) DEFAULT '',
  `rate` float unsigned NOT NULL DEFAULT '1',
  `cents` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pos` (`pos`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/* Data for table s_currencies */
INSERT INTO `s_currencies` (`id`,`name`,`sign`,`code`,`rate`,`cents`,`pos`,`enabled`) VALUES
(1, 'RUR', 'RUR', 'RUR', '1', 0, 0, 1),
(2, 'USD', 'USD', 'USD', '0.0166667', 2, 1, 1),
(3, 'EUR', 'EUR', 'EUR', '0.0142857', 2, 2, 1);
/* Drop for table s_delivery */
DROP TABLE IF EXISTS `s_delivery`;
/* Create table s_delivery */
CREATE TABLE `s_delivery` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `free_from` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pos` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `separate_payment` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pos` (`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_delivery */
;
/* Drop for table s_delivery_payment */
DROP TABLE IF EXISTS `s_delivery_payment`;
/* Create table s_delivery_payment */
CREATE TABLE `s_delivery_payment` (
  `delivery_id` int(10) unsigned NOT NULL,
  `payment_method_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`delivery_id`,`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка способом оплаты и способов доставки';
/* Data for table s_delivery_payment */
;
/* Drop for table s_features */
DROP TABLE IF EXISTS `s_features`;
/* Create table s_features */
CREATE TABLE `s_features` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `trans` varchar(200) CHARACTER SET ascii DEFAULT '',
  `trans2` varchar(200) NOT NULL DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  `in_filter` tinyint(1) DEFAULT '0',
  `tpl` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `trans` (`trans`),
  KEY `pos` (`pos`),
  KEY `in_filter` (`in_filter`),
  KEY `trans2` (`trans2`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_features */
INSERT INTO `s_features` (`id`,`name`,`gid`,`trans`,`trans2`,`pos`,`in_filter`,`tpl`,`visible`) VALUES
(1, 'тестовое', 0, 'testovoe', '', 0, 0, 0, 0);
/* Drop for table s_feedback */
DROP TABLE IF EXISTS `s_feedback`;
/* Create table s_feedback */
CREATE TABLE `s_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_feedback */
;
/* Drop for table s_groups */
DROP TABLE IF EXISTS `s_groups`;
/* Create table s_groups */
CREATE TABLE `s_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_groups */
;
/* Drop for table s_img_blog */
DROP TABLE IF EXISTS `s_img_blog`;
/* Create table s_img_blog */
CREATE TABLE `s_img_blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`),
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_img_blog */
;
/* Drop for table s_img_categories */
DROP TABLE IF EXISTS `s_img_categories`;
/* Create table s_img_categories */
CREATE TABLE `s_img_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`),
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/* Data for table s_img_categories */
INSERT INTO `s_img_categories` (`id`,`item_id`,`basename`,`pos`) VALUES
(1, 193, '815183d053fc018be591f0e28cff9c04.jpg', 0),
(7, 2, '9fccd9155ac877d6b975ab5266c74e01.jpg', 0),
(8, 2, '815183d053fc018be591f0e28cff9c04.jpg', 1),
(9, 9, '815183d053fc018be591f0e28cff9c04.jpg', 0);
/* Drop for table s_img_products */
DROP TABLE IF EXISTS `s_img_products`;
/* Create table s_img_products */
CREATE TABLE `s_img_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`) USING BTREE,
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Data for table s_img_products */
INSERT INTO `s_img_products` (`id`,`item_id`,`basename`,`pos`) VALUES
(2, 1, '2ff0198a81469a6f381425ae75f979c8.jpg', 0);
/* Drop for table s_labels */
DROP TABLE IF EXISTS `s_labels`;
/* Create table s_labels */
CREATE TABLE `s_labels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `color` varchar(6) DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_labels */
INSERT INTO `s_labels` (`id`,`name`,`color`,`pos`) VALUES
(1, 'новый', '3247ad', 1);
/* Drop for table s_menu */
DROP TABLE IF EXISTS `s_menu`;
/* Create table s_menu */
CREATE TABLE `s_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Data for table s_menu */
INSERT INTO `s_menu` (`id`,`name`,`pos`) VALUES
(1, 'Основное меню', 0),
(2, 'Другие страницы', 1);
/* Drop for table s_options */
DROP TABLE IF EXISTS `s_options`;
/* Create table s_options */
CREATE TABLE `s_options` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `2` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `3` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `4` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `5` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `6` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `7` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `8` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `9` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `10` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `11` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `12` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `13` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `14` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `15` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `20` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `21` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `22` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `23` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `24` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `25` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `26` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `27` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `28` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `29` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `30` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `31` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `32` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `33` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `34` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `35` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `36` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `37` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `38` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `39` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `40` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `41` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `42` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `43` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `44` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `45` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `46` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `47` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `48` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `49` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `50` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `51` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `52` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `53` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `54` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `55` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `56` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `57` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `58` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `59` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `60` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `61` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `62` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `63` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `64` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `65` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `66` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `67` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `68` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `69` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `70` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `71` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `72` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `73` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `74` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `75` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `76` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `77` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `78` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `79` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `80` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `81` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `82` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `83` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `84` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `85` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `86` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `1` mediumint(9) DEFAULT NULL,
  `16` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `17` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `18` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `19` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `87` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `88` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `89` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `90` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `91` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `19` (`product_id`),
  KEY `21` (`21`,`product_id`),
  KEY `17` (`17`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_options */
;
/* Drop for table s_options_groups */
DROP TABLE IF EXISTS `s_options_groups`;
/* Create table s_options_groups */
CREATE TABLE `s_options_groups` (
  `id` tinyint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `pos` tinyint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Data for table s_options_groups */
INSERT INTO `s_options_groups` (`id`,`name`,`pos`) VALUES
(1, 'основные ', 1),
(2, 'второстепенные ', 2);
/* Drop for table s_options_uniq */
DROP TABLE IF EXISTS `s_options_uniq`;
/* Create table s_options_uniq */
CREATE TABLE `s_options_uniq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `val` varchar(512) NOT NULL DEFAULT '',
  `trans` varchar(512) CHARACTER SET ascii NOT NULL DEFAULT '',
  `trans2` varchar(512) CHARACTER SET ascii NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `trans` (`trans`) USING BTREE,
  KEY `trans2` (`trans2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_options_uniq */
;
/* Drop for table s_orders */
DROP TABLE IF EXISTS `s_orders`;
/* Create table s_orders */
CREATE TABLE `s_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_date` timestamp NOT NULL DEFAULT '1970-01-02 03:00:00',
  `closed` tinyint(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `phone` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `comment` varchar(1024) DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `trans` varchar(255) DEFAULT '',
  `payment_details` text,
  `ip` varchar(15) DEFAULT '',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` varchar(1024) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_code` varchar(255) DEFAULT '',
  `separate_delivery` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT '1970-01-02 03:00:00',
  PRIMARY KEY (`id`),
  KEY `login` (`user_id`),
  KEY `written_off` (`closed`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `code` (`trans`),
  KEY `payment_status` (`paid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_orders */
;
/* Drop for table s_orders_labels */
DROP TABLE IF EXISTS `s_orders_labels`;
/* Create table s_orders_labels */
CREATE TABLE `s_orders_labels` (
  `order_id` smallint(5) unsigned NOT NULL,
  `label_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`order_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_orders_labels */
;
/* Drop for table s_pages */
DROP TABLE IF EXISTS `s_pages`;
/* Create table s_pages */
CREATE TABLE `s_pages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `trans` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `body` longtext,
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `header` varchar(1024) DEFAULT '',
  `new_field` varchar(255) DEFAULT '',
  `new_field2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `order_num` (`pos`),
  KEY `url` (`trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_pages */
;
/* Drop for table s_payment_methods */
DROP TABLE IF EXISTS `s_payment_methods`;
/* Create table s_payment_methods */
CREATE TABLE `s_payment_methods` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `description` text,
  `currency_id` float DEFAULT '0',
  `settings` text,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pos` (`pos`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/* Data for table s_payment_methods */
INSERT INTO `s_payment_methods` (`id`,`module`,`name`,`description`,`currency_id`,`settings`,`enabled`,`pos`) VALUES
(1, 'Receipt', 'Квитанция', '<p>Вы можете распечатать квитанцию и оплатить её в любом отделении банка.</p>', 2, 'a:10:{s:9:\"recipient\";s:65:\"ООО \"Великолепный интернет-магазин\"\";s:3:\"inn\";s:5:\"12345\";s:7:\"account\";s:6:\"223456\";s:4:\"bank\";s:18:\"Альфабанк\";s:3:\"bik\";s:6:\"556677\";s:21:\"correspondent_account\";s:11:\"77777755555\";s:8:\"banknote\";s:7:\"руб.\";s:5:\"pense\";s:7:\"коп.\";s:5:\"purse\";s:2:\"ru\";s:10:\"secret_key\";s:0:\"\";}', 1, 2),
(2, 'Webmoney', 'Webmoney wmz', '<p><span></span></p><div><p>Оплата через платежную систему&nbsp;<a href=\"http://www.webmoney.ru\">WebMoney</a>. У вас должен быть счет в этой системе для того, чтобы произвести оплату. Сразу после оформления заказа вы будете перенаправлены на специальную страницу системы WebMoney, где сможете произвести платеж в титульных знаках WMZ.</p></div><p>&nbsp;</p>', 3, 'a:10:{s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:7:\"руб.\";s:5:\"pense\";s:0:\"\";s:5:\"purse\";s:13:\"Z111111111111\";s:10:\"secret_key\";s:13:\"testsecretkey\";}', 1, 1),
(3, 'Robokassa', 'Робокасса', '<p><span>RBK Money &ndash; это электронная платежная система, с помощью которой Вы сможете совершать платежи с персонального компьютера, коммуникатора или мобильного телефона.</span></p>', 3, 'a:14:{s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:0:\"\";s:5:\"pense\";s:0:\"\";s:5:\"login\";s:0:\"\";s:9:\"password1\";s:0:\"\";s:9:\"password2\";s:0:\"\";s:8:\"language\";s:2:\"ru\";s:5:\"purse\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";}', 1, 3),
(4, 'Paypal', 'PayPal', '<p>Совершайте покупки безопасно, без раскрытия информации о своей кредитной карте. PayPal защитит вас, если возникнут проблемы с покупкой</p>', 1, 'a:16:{s:8:\"business\";s:0:\"\";s:4:\"mode\";s:7:\"sandbox\";s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:0:\"\";s:5:\"pense\";s:0:\"\";s:5:\"login\";s:0:\"\";s:9:\"password1\";s:0:\"\";s:9:\"password2\";s:0:\"\";s:8:\"language\";s:2:\"ru\";s:5:\"purse\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";}', 1, 4),
(5, 'Interkassa', 'Оплата через Интеркассу', '<p><span>Это удобный в использовании сервис, подключение к которому позволит Интернет-магазинам, веб-сайтам и прочим торговым площадкам принимать все возможные формы оплаты в максимально короткие сроки.</span></p>', 2, 'a:2:{s:18:\"interkassa_shop_id\";s:3:\"123\";s:21:\"interkassa_secret_key\";s:3:\"123\";}', 1, 5),
(6, 'Liqpay', 'Оплата картой через Liqpay.com', '<p><span>Благодаря своей открытости и универсальности LiqPAY стремительно интегрируется со многими платежными системами и платформами и становится стандартом платежных операций.</span></p>', 2, 'a:5:{s:9:\"liqpay_id\";s:3:\"123\";s:11:\"liqpay_sign\";s:3:\"123\";s:12:\"pay_way_card\";s:1:\"1\";s:14:\"pay_way_liqpay\";s:1:\"1\";s:15:\"pay_way_delayed\";s:1:\"1\";}', 1, 6),
(7, 'Pay2Pay', 'Оплата через Pay2Pay', '<p>Универсальный платежный сервис Pay2Pay призван облегчить и максимально упростить процесс приема электронных платежей на Вашем сайте. Мы открыты для всего нового и сверхсовременного.</p>', 2, 'a:5:{s:18:\"pay2pay_merchantid\";s:3:\"123\";s:14:\"pay2pay_secret\";s:3:\"123\";s:14:\"pay2pay_hidden\";s:3:\"123\";s:15:\"pay2pay_paymode\";s:3:\"123\";s:16:\"pay2pay_testmode\";s:1:\"1\";}', 1, 7),
(8, 'Qiwi', 'Оплатить через QIWI', '<p><span>QIWI &mdash; удобный сервис для оплаты повседневных услуг</span></p>', 2, 'a:2:{s:10:\"qiwi_login\";s:3:\"123\";s:13:\"qiwi_password\";s:3:\"123\";}', 1, 8);
/* Drop for table s_products */
DROP TABLE IF EXISTS `s_products`;
/* Create table s_products */
CREATE TABLE `s_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trans` varchar(255) NOT NULL,
  `trans2` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(500) NOT NULL,
  `annotation` text NOT NULL,
  `body` longtext NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  `meta_title` varchar(500) NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) NOT NULL DEFAULT '',
  `meta_description` varchar(500) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `featured` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url` (`trans`),
  KEY `brand_id` (`brand_id`),
  KEY `pos` (`pos`),
  KEY `hit` (`featured`),
  KEY `name` (`name`(255)),
  KEY `visible` (`visible`) USING BTREE,
  KEY `url2` (`trans2`),
  KEY `stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_products */
;
/* Drop for table s_products_categories */
DROP TABLE IF EXISTS `s_products_categories`;
/* Create table s_products_categories */
CREATE TABLE `s_products_categories` (
  `product_id` int(10) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`category_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_products_categories */
INSERT INTO `s_products_categories` (`product_id`,`category_id`) VALUES
(1, 1);
/* Drop for table s_purchases */
DROP TABLE IF EXISTS `s_purchases`;
/* Create table s_purchases */
CREATE TABLE `s_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned DEFAULT '0',
  `variant_id` int(10) unsigned DEFAULT '0',
  `product_name` varchar(255) DEFAULT '',
  `variant_name` varchar(255) DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `amount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_purchases */
INSERT INTO `s_purchases` (`id`,`order_id`,`product_id`,`variant_id`,`product_name`,`variant_name`,`price`,`amount`,`sku`) VALUES
(1, 1, 1, 1, 'test', '', 4354.00, 3, '1');
/* Drop for table s_queue */
DROP TABLE IF EXISTS `s_queue`;
/* Create table s_queue */
CREATE TABLE `s_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(5000) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* $skipdata is true. Data skipped s_queue */
/* Drop for table s_queue_full */
DROP TABLE IF EXISTS `s_queue_full`;
/* Create table s_queue_full */
CREATE TABLE `s_queue_full` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(5000) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* $skipdata is true. Data skipped s_queue_full */
/* Drop for table s_related_products */
DROP TABLE IF EXISTS `s_related_products`;
/* Create table s_related_products */
CREATE TABLE `s_related_products` (
  `product_id` int(10) unsigned NOT NULL,
  `related_id` int(10) unsigned NOT NULL,
  `pos` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`),
  KEY `pos` (`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_related_products */
;
/* Drop for table s_settings */
DROP TABLE IF EXISTS `s_settings`;
/* Create table s_settings */
CREATE TABLE `s_settings` (
  `setting_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/* Data for table s_settings */
INSERT INTO `s_settings` (`setting_id`,`name`,`value`) VALUES
(1, 'theme', 'default'),
(2, 'site_name', 'site'),
(3, 'company_name', 'site'),
(4, 'date_format', 'd.m.Y'),
(5, 'admin_email', 'site@site.si'),
(6, 'order_email', 'site@site.si'),
(7, 'comment_email', 'site@site.si'),
(8, 'notify_from_email', 'site@site.si'),
(9, 'decimals_point', '.'),
(10, 'thousands_separator', ' '),
(11, 'products_num', '24'),
(12, 'products_num_admin', '24'),
(13, 'max_order_amount', '100'),
(14, 'units', 'ед.'),
(15, 'phone', '');
/* Drop for table s_slider */
DROP TABLE IF EXISTS `s_slider`;
/* Create table s_slider */
CREATE TABLE `s_slider` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `basename` varchar(500) NOT NULL,
  `trans` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(512) NOT NULL DEFAULT '',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pos` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_slider */
;
/* Drop for table s_text_options */
DROP TABLE IF EXISTS `s_text_options`;
/* Create table s_text_options */
CREATE TABLE `s_text_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `product_id` (`product_id`,`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_text_options */
;
/* Drop for table s_users */
DROP TABLE IF EXISTS `s_users`;
/* Create table s_users */
CREATE TABLE `s_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `admin` tinyint(1) DEFAULT '0',
  `perm` varchar(200) CHARACTER SET ascii DEFAULT '',
  `last_ip` varchar(15) DEFAULT '',
  `last_login` timestamp NOT NULL DEFAULT '1970-01-02 01:00:00',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `admin` (`perm`),
  KEY `perm` (`admin`),
  KEY `created` (`created`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/* Data for table s_users */
INSERT INTO `s_users` (`id`,`email`,`password`,`name`,`group_id`,`enabled`,`admin`,`perm`,`last_ip`,`last_login`,`created`) VALUES
(1, 'admin@admin.ad', '5f6b179e0034e20383dfe8942f59cda6', 'admin@admin.ad', 0, 1, 1, '0:1:2:3:4:5:6:7:8:9:10:11:12:13:14:15:16:17:18:19:20:21:22:23', '151.66.102.24', '2018-03-26 20:20:35', '2017-11-12 06:08:40');
/* Drop for table s_variants */
DROP TABLE IF EXISTS `s_variants`;
/* Create table s_variants */
CREATE TABLE `s_variants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sku` varchar(255) CHARACTER SET ascii NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price3` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `old_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `stock` mediumint(9) unsigned DEFAULT NULL,
  `preorder` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`) USING BTREE,
  KEY `product_id` (`product_id`),
  KEY `price` (`price`),
  KEY `stock` (`stock`),
  KEY `pos` (`pos`),
  KEY `preorder` (`preorder`),
  KEY `price1` (`price1`),
  KEY `price2` (`price2`),
  KEY `price3` (`price3`),
  KEY `old_price` (`old_price`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_variants */
INSERT INTO `s_variants` (`id`,`product_id`,`sku`,`name`,`price`,`price1`,`price2`,`price3`,`old_price`,`stock`,`preorder`,`pos`) VALUES
(1, 1, '1', '', 4354.00, 354.00, 5.00, 2.00, 200.00, 999, 0, 0);
