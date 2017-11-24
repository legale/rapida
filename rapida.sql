/* Drop for table s_blog */
DROP TABLE IF EXISTS `s_blog`;
/* Create table s_blog */
CREATE TABLE `s_blog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `url` varchar(255) DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `annotation` text DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_blog */
;
/* Drop for table s_brands */
DROP TABLE IF EXISTS `s_brands`;
/* Create table s_brands */
CREATE TABLE `s_brands` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) CHARACTER SET ascii DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `url` (`url`),
  KEY `id_name` (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_brands */
;
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
/* Data for table s_cache_integer */
;
/* Drop for table s_categories */
DROP TABLE IF EXISTS `s_categories`;
/* Create table s_categories */
CREATE TABLE `s_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT '',
  `meta_keywords` varchar(255) DEFAULT '',
  `meta_description` varchar(255) DEFAULT '',
  `description` text DEFAULT NULL,
  `url` varchar(255) DEFAULT '',
  `image` varchar(255) DEFAULT '',
  `position` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `parent_id` (`parent_id`),
  KEY `position` (`position`),
  KEY `visible` (`visible`)
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
;
/* Drop for table s_comments */
DROP TABLE IF EXISTS `s_comments`;
/* Create table s_comments */
CREATE TABLE `s_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(20) DEFAULT '',
  `object_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT '',
  `text` text DEFAULT NULL,
  `type` enum('product','blog') DEFAULT 'blog',
  `approved` int(1) NOT NULL DEFAULT 0,
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
  `expire` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('absolute','percentage') NOT NULL DEFAULT 'absolute',
  `value` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `min_order_price` decimal(10,2) unsigned DEFAULT 0.00,
  `single` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `usages` mediumint(8) unsigned NOT NULL DEFAULT 0,
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
  `rate_from` decimal(10,2) unsigned NOT NULL DEFAULT 1.00,
  `rate_to` decimal(10,2) unsigned NOT NULL DEFAULT 1.00,
  `cents` tinyint(1) unsigned NOT NULL DEFAULT 2,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  `enabled` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `position` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Data for table s_currencies */
INSERT INTO `s_currencies` (`id`,`name`,`sign`,`code`,`rate_from`,`rate_to`,`cents`,`position`,`enabled`) VALUES
(1, 'RUR', 'RUR', 'RUR', 1.00, 1.00, 2, 1, 1);
/* Drop for table s_delivery */
DROP TABLE IF EXISTS `s_delivery`;
/* Create table s_delivery */
CREATE TABLE `s_delivery` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `free_from` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `position` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `separate_payment` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
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
  `trans` varchar(200) CHARACTER SET ascii DEFAULT '',
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  `in_filter` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `position` (`position`),
  KEY `in_filter` (`in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_features */
;
/* Drop for table s_feedbacks */
DROP TABLE IF EXISTS `s_feedbacks`;
/* Create table s_feedbacks */
CREATE TABLE `s_feedbacks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(20) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_feedbacks */
;
/* Drop for table s_groups */
DROP TABLE IF EXISTS `s_groups`;
/* Create table s_groups */
CREATE TABLE `s_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_groups */
;
/* Drop for table s_images */
DROP TABLE IF EXISTS `s_images`;
/* Create table s_images */
CREATE TABLE `s_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `filename` (`filename`),
  KEY `product_id` (`product_id`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_images */
;
/* Drop for table s_labels */
DROP TABLE IF EXISTS `s_labels`;
/* Create table s_labels */
CREATE TABLE `s_labels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `color` varchar(6) DEFAULT '',
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_labels */
;
/* Drop for table s_menu */
DROP TABLE IF EXISTS `s_menu`;
/* Create table s_menu */
CREATE TABLE `s_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Data for table s_menu */
INSERT INTO `s_menu` (`id`,`name`,`position`) VALUES
(1, 'Основное меню', 0),
(2, 'Другие страницы', 1);
/* Drop for table s_options */
DROP TABLE IF EXISTS `s_options`;
/* Create table s_options */
CREATE TABLE `s_options` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `1` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `2` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `3` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `4` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `5` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `6` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `7` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `8` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `9` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `10` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `11` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `12` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `13` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `14` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `15` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `16` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `17` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `18` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `19` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `20` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `21` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `22` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `23` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `24` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `25` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `26` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `27` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `28` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `29` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `30` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `31` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `32` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `33` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `34` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `35` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `36` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `37` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `38` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `39` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `40` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `41` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `42` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `43` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `44` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `45` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `46` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `47` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `48` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `49` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `50` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `51` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `52` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `53` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_id`),
  KEY `12` (`12`,`product_id`),
  KEY `13` (`13`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_options */
;
/* Drop for table s_options_uniq */
DROP TABLE IF EXISTS `s_options_uniq`;
/* Create table s_options_uniq */
CREATE TABLE `s_options_uniq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `val` varchar(512) DEFAULT '',
  `trans` varchar(512) CHARACTER SET ascii DEFAULT '',
  `md4` binary(16) DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `md4_id` (`md4`,`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_options_uniq */
;
/* Drop for table s_orders */
DROP TABLE IF EXISTS `s_orders`;
/* Create table s_orders */
CREATE TABLE `s_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` int(11) DEFAULT 0,
  `delivery_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` int(11) DEFAULT 0,
  `paid` int(1) NOT NULL DEFAULT 0,
  `payment_date` timestamp NOT NULL DEFAULT '1970-01-01 23:00:00',
  `closed` tinyint(1) DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT 0,
  `name` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `phone` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `comment` varchar(1024) DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT 0,
  `url` varchar(255) DEFAULT '',
  `payment_details` text DEFAULT NULL,
  `ip` varchar(15) DEFAULT '',
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` varchar(1024) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` varchar(255) DEFAULT '',
  `separate_delivery` int(1) NOT NULL DEFAULT 0,
  `modified` timestamp NOT NULL DEFAULT '1970-01-01 23:00:00',
  PRIMARY KEY (`id`),
  KEY `login` (`user_id`),
  KEY `written_off` (`closed`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `code` (`url`),
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
  `url` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `body` longtext DEFAULT NULL,
  `menu_id` int(10) unsigned NOT NULL DEFAULT 0,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `header` varchar(1024) DEFAULT '',
  `new_field` varchar(255) DEFAULT '',
  `new_field2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `order_num` (`position`),
  KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/* Data for table s_pages */
INSERT INTO `s_pages` (`id`,`url`,`name`,`meta_title`,`meta_description`,`meta_keywords`,`body`,`menu_id`,`position`,`visible`,`header`,`new_field`,`new_field2`) VALUES
(1, '', 'Главная', 'Хиты продаж', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'Хиты продаж', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas accumsan libero fermentum nisl auctor dapibus. Sed sed gravida enim. In non pharetra lacus. Donec sed erat a felis hendrerit maximus. Vivamus congue turpis nec risus bibendum, at euismod odio rutrum. Aliquam pulvinar sapien vitae justo volutpat tristique. Donec ut accumsan urna. Quisque accumsan scelerisque metus eget pretium. Cras tincidunt volutpat dui ac maximus. Morbi congue ligula rutrum, interdum lectus id, cursus velit. Nunc interdum sagittis erat, at mattis erat molestie ut. Praesent ut rhoncus mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p><p>Nullam et scelerisque odio. Maecenas ut ante erat. Suspendisse in cursus lacus, ac blandit odio. Cras quis nulla eu mauris sagittis luctus vel ut est. Praesent placerat tincidunt justo. Fusce iaculis sem a tortor ullamcorper vehicula. Sed hendrerit rutrum fringilla.</p><p>Duis eleifend, metus ut varius aliquet, tortor nibh gravida nulla, nec maximus diam diam non ligula. Sed eu vestibulum nisl, nec elementum orci. Nam pellentesque arcu ligula, in luctus mauris sollicitudin in. Quisque fermentum pretium rhoncus. Donec mauris purus, sodales vitae dictum nec, volutpat a erat. Etiam a fringilla arcu. Sed fringilla eu libero viverra viverra. Nullam dui enim, varius sit amet risus ut, suscipit malesuada eros. Ut a arcu in leo tempus euismod. Nam tempor purus nisi, a finibus ante ultricies eget. Sed consectetur justo ut vulputate cursus. Mauris ullamcorper, urna euismod rhoncus rhoncus, tortor metus dapibus libero, sit amet suscipit purus magna in nisi. Vestibulum et bibendum magna, ac pellentesque orci. In blandit odio efficitur ligula vehicula euismod. Ut congue ligula eu diam rhoncus, sed varius ipsum euismod. Cras placerat nunc non velit blandit, vitae consequat enim fermentum.</p><p>Vivamus ultricies sollicitudin nisl, eu placerat sem sollicitudin at. Vestibulum luctus neque vel volutpat tempor. Cras laoreet lorem a neque interdum, ut placerat ligula eleifend. Nullam a varius dui. Donec vel ligula at magna tempus sagittis. In cursus laoreet ultrices. Mauris sodales sem non nibh imperdiet luctus. Nullam malesuada aliquam egestas. Sed suscipit, ipsum non tempus porttitor, nulla justo tincidunt nisl, in auctor ligula nunc eu lacus. Fusce lorem risus, venenatis vel sodales ut, auctor et lacus. Fusce sagittis efficitur neque eu viverra. Etiam tortor odio, ultrices vulputate nunc eget, iaculis rutrum justo. Vivamus rutrum, mauris vel tempor placerat, neque nulla volutpat urna, ac hendrerit mauris sapien ut nisi.</p>', 1, 1, 1, 'О магазине', '', ''),
(2, 'oplata', 'Оплата', 'Оплата', 'Оплата', 'Оплата', '<h2><span>Наличными курьеру</span></h2><p>Вы можете оплатить заказ курьеру в гривнах непосредственно в момент доставки. Курьерская доставка осуществляется по Москве на следующий день после принятия заказа.</p><h2>Webmoney</h2><p>После оформления заказа вы сможете перейти на сайт webmoney для оплаты заказа, где сможете оплатить заказ в автоматическом режиме, а так же проверить наш сертификат продавца.</p><h2>Наличными в офисе Автолюкса</h2><p>При доставке заказа системой Автолюкс, вы сможете оплатить заказ в их офисе непосредственно в момент получения товаров.</p>', 1, 4, 1, 'Способы оплаты', '', ''),
(3, 'dostavka', 'Доставка', 'Доставка', 'Доставка', 'Доставка', '<h2>Курьерская доставка по&nbsp;Москве</h2><p>Курьерская доставка осуществляется на следующий день после оформления заказа<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>если товар есть в&nbsp;наличии. Курьерская доставка осуществляется в&nbsp;пределах Томска и&nbsp;Северска ежедневно с&nbsp;10.00 до&nbsp;21.00. Заказ на&nbsp;сумму свыше 300 рублей доставляется бесплатно. <br /><br />Стоимость бесплатной доставки раcсчитывается от&nbsp;суммы заказа с&nbsp;учтенной скидкой. В&nbsp;случае если сумма заказа после применения скидки менее 300р<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>осуществляется платная доставка. <br /><br />При сумме заказа менее 300 рублей стоимость доставки составляет от 50 рублей.</p><h2>Самовывоз</h2><p>Удобный<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>бесплатный и быстрый способ получения заказа.<br />Адрес офиса: Москва<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>ул. Арбат<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>1/3<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>офис 419.</p><h2>Доставка с&nbsp;помощью предприятия<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo;</h2><p>Удобный и быстрый способ доставки в крупные города России. Посылка доставляется в офис<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo; в&nbsp;Вашем городе. Для получения необходимо предъявить паспорт и&nbsp;номер грузовой декларации<span style=\"margin-right: 0.3em;\"> </span><span style=\"margin-left: -0.3em;\">(</span>сообщит наш менеджер после отправки). Посылку желательно получить в&nbsp;течение 24 часов с&nbsp;момента прихода груза<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>иначе компания<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo; может взыскать с Вас дополнительную оплату за хранение. Срок доставки и стоимость Вы можете рассчитать на сайте компании.</p><h2>Наложенным платежом</h2><p>При доставке заказа наложенным платежом с помощью<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Почты России&raquo;, вы&nbsp;сможете оплатить заказ непосредственно в&nbsp;момент получения товаров.</p>', 1, 3, 1, 'Способы доставки', '', ''),
(4, 'blog', 'Блог', 'Блог', '', 'Блог', '', 1, 2, 1, 'Блог', '', ''),
(5, '404', '', 'Страница не найдена', 'Страница не найдена', 'Страница не найдена', '<p>Страница не найдена</p>', 2, 5, 1, 'Страница не найдена', '', ''),
(6, 'contact', 'Контакты', 'Контакты', 'Контакты', 'Контакты', '<p>Москва, шоссе Энтузиастов 45/31, офис 453.</p><p><a href=\"http://maps.yandex.ru/?text=%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D1%8F%2C%20%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0%2C%20%D0%AD%D0%BD%D1%82%D1%83%D0%B7%D0%B8%D0%B0%D1%81%D1%82%D0%BE%D0%B2%20%D1%88%D0%BE%D1%81%D1%81%D0%B5%2C%2051&amp;sll=37.823314%2C55.773034&amp;sspn=0.021955%2C0.009277&amp;ll=37.826161%2C55.77356&amp;spn=0.019637%2C0.006461&amp;l=map\">Посмотреть на&nbsp;Яндекс.Картах</a></p><p>Телефон 345-45-54</p>', 1, 6, 1, 'Контакты', '', ''),
(7, 'products', 'Все товары', 'Все товары', '', 'Все товары', '', 2, 7, 1, 'Все товары', '', '');
/* Drop for table s_payment_methods */
DROP TABLE IF EXISTS `s_payment_methods`;
/* Create table s_payment_methods */
CREATE TABLE `s_payment_methods` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `description` text DEFAULT NULL,
  `currency_id` float DEFAULT 0,
  `settings` text DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/* Data for table s_payment_methods */
INSERT INTO `s_payment_methods` (`id`,`module`,`name`,`description`,`currency_id`,`settings`,`enabled`,`position`) VALUES
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
  `url` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(500) NOT NULL,
  `annotation` text NOT NULL,
  `body` longtext NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  `meta_title` varchar(500) NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) NOT NULL DEFAULT '',
  `meta_description` varchar(500) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `featured` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `brand_id` (`brand_id`),
  KEY `position` (`position`),
  KEY `hit` (`featured`),
  KEY `name` (`name`(333)),
  KEY `visible` (`visible`) USING BTREE
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
;
/* Drop for table s_purchases */
DROP TABLE IF EXISTS `s_purchases`;
/* Create table s_purchases */
CREATE TABLE `s_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned DEFAULT 0,
  `variant_id` int(10) unsigned DEFAULT 0,
  `product_name` varchar(255) DEFAULT '',
  `variant_name` varchar(255) DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `amount` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `sku` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_purchases */
;
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
/* Data for table s_queue */
;
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
/* Data for table s_queue_full */
;
/* Drop for table s_related_products */
DROP TABLE IF EXISTS `s_related_products`;
/* Create table s_related_products */
CREATE TABLE `s_related_products` (
  `product_id` int(10) unsigned NOT NULL,
  `related_id` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`),
  KEY `position` (`position`)
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
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
(14, 'units', 'ед.');
/* Drop for table s_users */
DROP TABLE IF EXISTS `s_users`;
/* Create table s_users */
CREATE TABLE `s_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `group_id` int(10) unsigned NOT NULL DEFAULT 0,
  `enabled` tinyint(1) DEFAULT 0,
  `admin` tinyint(1) DEFAULT 0,
  `perm` varchar(200) CHARACTER SET ascii DEFAULT '',
  `last_ip` varchar(15) DEFAULT '',
  `last_login` timestamp NOT NULL DEFAULT '1970-01-01 23:00:00',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `admin` (`perm`),
  KEY `perm` (`admin`),
  KEY `created` (`created`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/* Data for table s_users */
INSERT INTO `s_users` (`id`,`email`,`password`,`name`,`group_id`,`enabled`,`admin`,`perm`,`last_ip`,`last_login`,`created`) VALUES
(1, 'admin@admin.ad', '5f6b179e0034e20383dfe8942f59cda6', 'admin@admin.ad', 0, 1, 1, '0:1:2:3:4:5:6:7:8:9:10:11:12:13:14:15:16:17:18:19:20:21:22:23', '127.0.0.1', '2017-11-12 04:14:23', '2017-11-12 04:08:40');
/* Drop for table s_variants */
DROP TABLE IF EXISTS `s_variants`;
/* Create table s_variants */
CREATE TABLE `s_variants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sku` varchar(255) CHARACTER SET ascii NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `price1` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `price2` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `price3` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `old_price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `stock` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `preorder` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`) USING BTREE,
  KEY `product_id` (`product_id`),
  KEY `price` (`price`),
  KEY `stock` (`stock`),
  KEY `position` (`position`),
  KEY `preorder` (`preorder`),
  KEY `price1` (`price1`),
  KEY `price2` (`price2`),
  KEY `price3` (`price3`),
  KEY `old_price` (`old_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* Data for table s_variants */
;
