/* Drop for table s_blog */
DROP TABLE IF EXISTS `s_blog`;
/* Create table s_blog */
CREATE TABLE `s_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `annotation` text,
  `text` longtext,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `date` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4),
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_blog */
TRUNCATE TABLE `s_blog`;
/* Data for table s_blog */

/* Drop for table s_brands */
DROP TABLE IF EXISTS `s_brands`;
/* Create table s_brands */
CREATE TABLE `s_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_brands */
TRUNCATE TABLE `s_brands`;
/* Data for table s_brands */

/* Drop for table s_cache_integer */
DROP TABLE IF EXISTS `s_cache_integer`;
/* Create table s_cache_integer */
CREATE TABLE `s_cache_integer` (
  `updated` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4) ON UPDATE CURRENT_TIMESTAMP(4),
  `keyhash` binary(16) NOT NULL,
  `value` mediumint(4) NOT NULL,
  PRIMARY KEY (`keyhash`) USING BTREE,
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_cache_integer */
TRUNCATE TABLE `s_cache_integer`;
/* Data for table s_cache_integer */
INSERT INTO `s_cache_integer` (`updated`, `keyhash`, `value`) VALUES
('2017-10-20 19:32:29.4661','�#�T6��1\0wY�Z�\Z','0');

/* Drop for table s_categories */
DROP TABLE IF EXISTS `s_categories`;
/* Create table s_categories */
CREATE TABLE `s_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `parent_id` (`parent_id`),
  KEY `position` (`position`),
  KEY `visible` (`visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_categories */
TRUNCATE TABLE `s_categories`;
/* Data for table s_categories */

/* Drop for table s_categories_features */
DROP TABLE IF EXISTS `s_categories_features`;
/* Create table s_categories_features */
CREATE TABLE `s_categories_features` (
  `category_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`feature_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_categories_features */
TRUNCATE TABLE `s_categories_features`;
/* Data for table s_categories_features */

/* Drop for table s_comments */
DROP TABLE IF EXISTS `s_comments`;
/* Create table s_comments */
CREATE TABLE `s_comments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4),
  `ip` varchar(20) DEFAULT NULL,
  `object_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  `type` enum('product','blog') DEFAULT 'blog',
  `approved` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`object_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_comments */
TRUNCATE TABLE `s_comments`;
/* Data for table s_comments */

/* Drop for table s_coupons */
DROP TABLE IF EXISTS `s_coupons`;
/* Create table s_coupons */
CREATE TABLE `s_coupons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL,
  `expire` timestamp(4) NULL DEFAULT NULL,
  `type` enum('absolute','percentage') NOT NULL DEFAULT 'absolute',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_order_price` decimal(10,2) DEFAULT NULL,
  `single` int(1) NOT NULL DEFAULT '0',
  `usages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_coupons */
TRUNCATE TABLE `s_coupons`;
/* Data for table s_coupons */

/* Drop for table s_currencies */
DROP TABLE IF EXISTS `s_currencies`;
/* Create table s_currencies */
CREATE TABLE `s_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '0',
  `sign` varchar(20) DEFAULT NULL,
  `code` char(3) DEFAULT NULL,
  `rate_from` decimal(10,2) NOT NULL DEFAULT '1.00',
  `rate_to` decimal(10,2) NOT NULL DEFAULT '1.00',
  `cents` int(1) NOT NULL DEFAULT '2',
  `position` int(11) NOT NULL DEFAULT '0',
  `enabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/* Clear table s_currencies */
TRUNCATE TABLE `s_currencies`;
/* Data for table s_currencies */
INSERT INTO `s_currencies` (`id`, `name`, `sign`, `code`, `rate_from`, `rate_to`, `cents`, `position`, `enabled`) VALUES
('1','RUR','RUR','RUR','1.00','1.00','2','1','1');

/* Drop for table s_delivery */
DROP TABLE IF EXISTS `s_delivery`;
/* Create table s_delivery */
CREATE TABLE `s_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `free_from` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `separate_payment` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_delivery */
TRUNCATE TABLE `s_delivery`;
/* Data for table s_delivery */

/* Drop for table s_delivery_payment */
DROP TABLE IF EXISTS `s_delivery_payment`;
/* Create table s_delivery_payment */
CREATE TABLE `s_delivery_payment` (
  `delivery_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  PRIMARY KEY (`delivery_id`,`payment_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Связка способом оплаты и способов доставки';
/* Clear table s_delivery_payment */
TRUNCATE TABLE `s_delivery_payment`;
/* Data for table s_delivery_payment */

/* Drop for table s_features */
DROP TABLE IF EXISTS `s_features`;
/* Create table s_features */
CREATE TABLE `s_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `uri` varchar(200) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `in_filter` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `in_filter` (`in_filter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_features */
TRUNCATE TABLE `s_features`;
/* Data for table s_features */

/* Drop for table s_feedbacks */
DROP TABLE IF EXISTS `s_feedbacks`;
/* Create table s_feedbacks */
CREATE TABLE `s_feedbacks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4),
  `ip` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_feedbacks */
TRUNCATE TABLE `s_feedbacks`;
/* Data for table s_feedbacks */

/* Drop for table s_groups */
DROP TABLE IF EXISTS `s_groups`;
/* Create table s_groups */
CREATE TABLE `s_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_groups */
TRUNCATE TABLE `s_groups`;
/* Data for table s_groups */

/* Drop for table s_images */
DROP TABLE IF EXISTS `s_images`;
/* Create table s_images */
CREATE TABLE `s_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `filename` (`filename`),
  KEY `product_id` (`product_id`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_images */
TRUNCATE TABLE `s_images`;
/* Data for table s_images */

/* Drop for table s_labels */
DROP TABLE IF EXISTS `s_labels`;
/* Create table s_labels */
CREATE TABLE `s_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_labels */
TRUNCATE TABLE `s_labels`;
/* Data for table s_labels */

/* Drop for table s_menu */
DROP TABLE IF EXISTS `s_menu`;
/* Create table s_menu */
CREATE TABLE `s_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Clear table s_menu */
TRUNCATE TABLE `s_menu`;
/* Data for table s_menu */
INSERT INTO `s_menu` (`id`, `name`, `position`) VALUES
('1','Основное меню','0'),
('2','Другие страницы','1');

/* Drop for table s_options */
DROP TABLE IF EXISTS `s_options`;
/* Create table s_options */
CREATE TABLE `s_options` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_options */
TRUNCATE TABLE `s_options`;
/* Data for table s_options */

/* Drop for table s_options_uniq */
DROP TABLE IF EXISTS `s_options_uniq`;
/* Create table s_options_uniq */
CREATE TABLE `s_options_uniq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `val` varchar(1024) NOT NULL,
  `md4` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `md4` (`md4`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_options_uniq */
TRUNCATE TABLE `s_options_uniq`;
/* Data for table s_options_uniq */

/* Drop for table s_orders */
DROP TABLE IF EXISTS `s_orders`;
/* Create table s_orders */
CREATE TABLE `s_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` int(11) DEFAULT NULL,
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method_id` int(11) DEFAULT NULL,
  `paid` int(1) NOT NULL DEFAULT '0',
  `payment_date` datetime DEFAULT NULL,
  `closed` tinyint(1) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `payment_details` text,
  `ip` varchar(15) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` varchar(1024) DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_code` varchar(255) DEFAULT NULL,
  `separate_delivery` int(1) NOT NULL DEFAULT '0',
  `modified` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4),
  PRIMARY KEY (`id`),
  KEY `login` (`user_id`),
  KEY `written_off` (`closed`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `code` (`url`),
  KEY `payment_status` (`paid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_orders */
TRUNCATE TABLE `s_orders`;
/* Data for table s_orders */

/* Drop for table s_orders_labels */
DROP TABLE IF EXISTS `s_orders_labels`;
/* Create table s_orders_labels */
CREATE TABLE `s_orders_labels` (
  `order_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`order_id`,`label_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_orders_labels */
TRUNCATE TABLE `s_orders_labels`;
/* Data for table s_orders_labels */

/* Drop for table s_pages */
DROP TABLE IF EXISTS `s_pages`;
/* Create table s_pages */
CREATE TABLE `s_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `meta_title` varchar(500) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `body` longtext,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `header` varchar(1024) DEFAULT NULL,
  `new_field` varchar(255) DEFAULT NULL,
  `new_field2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_num` (`position`),
  KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/* Clear table s_pages */
TRUNCATE TABLE `s_pages`;
/* Data for table s_pages */
INSERT INTO `s_pages` (`id`, `url`, `name`, `meta_title`, `meta_description`, `meta_keywords`, `body`, `menu_id`, `position`, `visible`, `header`, `new_field`, `new_field2`) VALUES
('1','','Главная','Хиты продаж','Этот магазин является демонстрацией скрипта интернет-магазина  Simpla . Все материалы на этом сайте присутствуют исключительно в демострационных целях.','Хиты продаж','<p>Этот магазин является демонстрацией скрипта интернет-магазина <a href=\"http://simplacms.ru\">Simpla</a>. Все материалы на этом сайте присутствуют исключительно в демострационных целях.</p>','1','1','1','О магазине',null,null),
('2','oplata','Оплата','Оплата','Оплата','Оплата','<h2><span>Наличными курьеру</span></h2><p>Вы можете оплатить заказ курьеру в гривнах непосредственно в момент доставки. Курьерская доставка осуществляется по Москве на следующий день после принятия заказа.</p><h2>Webmoney</h2><p>После оформления заказа вы сможете перейти на сайт webmoney для оплаты заказа, где сможете оплатить заказ в автоматическом режиме, а так же проверить наш сертификат продавца.</p><h2>Наличными в офисе Автолюкса</h2><p>При доставке заказа системой Автолюкс, вы сможете оплатить заказ в их офисе непосредственно в момент получения товаров.</p>','1','4','1','Способы оплаты',null,null),
('3','dostavka','Доставка','Доставка','Доставка','Доставка','<h2>Курьерская доставка по&nbsp;Москве</h2><p>Курьерская доставка осуществляется на следующий день после оформления заказа<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>если товар есть в&nbsp;наличии. Курьерская доставка осуществляется в&nbsp;пределах Томска и&nbsp;Северска ежедневно с&nbsp;10.00 до&nbsp;21.00. Заказ на&nbsp;сумму свыше 300 рублей доставляется бесплатно. <br /><br />Стоимость бесплатной доставки раcсчитывается от&nbsp;суммы заказа с&nbsp;учтенной скидкой. В&nbsp;случае если сумма заказа после применения скидки менее 300р<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>осуществляется платная доставка. <br /><br />При сумме заказа менее 300 рублей стоимость доставки составляет от 50 рублей.</p><h2>Самовывоз</h2><p>Удобный<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>бесплатный и быстрый способ получения заказа.<br />Адрес офиса: Москва<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>ул. Арбат<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>1/3<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>офис 419.</p><h2>Доставка с&nbsp;помощью предприятия<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo;</h2><p>Удобный и быстрый способ доставки в крупные города России. Посылка доставляется в офис<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo; в&nbsp;Вашем городе. Для получения необходимо предъявить паспорт и&nbsp;номер грузовой декларации<span style=\"margin-right: 0.3em;\"> </span><span style=\"margin-left: -0.3em;\">(</span>сообщит наш менеджер после отправки). Посылку желательно получить в&nbsp;течение 24 часов с&nbsp;момента прихода груза<span style=\"margin-right: -0.2em;\">,</span><span style=\"margin-left: 0.2em;\"> </span>иначе компания<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Автотрейдинг&raquo; может взыскать с Вас дополнительную оплату за хранение. Срок доставки и стоимость Вы можете рассчитать на сайте компании.</p><h2>Наложенным платежом</h2><p>При доставке заказа наложенным платежом с помощью<span style=\"margin-right: 0.44em;\"> </span><span style=\"margin-left: -0.44em;\">&laquo;</span>Почты России&raquo;, вы&nbsp;сможете оплатить заказ непосредственно в&nbsp;момент получения товаров.</p>','1','3','1','Способы доставки',null,null),
('4','blog','Блог','Блог','','Блог','','1','2','1','Блог',null,null),
('5','404','','Страница не найдена','Страница не найдена','Страница не найдена','<p>Страница не найдена</p>','2','5','1','Страница не найдена',null,null),
('6','contact','Контакты','Контакты','Контакты','Контакты','<p>Москва, шоссе Энтузиастов 45/31, офис 453.</p><p><a href=\"http://maps.yandex.ru/?text=%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D1%8F%2C%20%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0%2C%20%D0%AD%D0%BD%D1%82%D1%83%D0%B7%D0%B8%D0%B0%D1%81%D1%82%D0%BE%D0%B2%20%D1%88%D0%BE%D1%81%D1%81%D0%B5%2C%2051&amp;sll=37.823314%2C55.773034&amp;sspn=0.021955%2C0.009277&amp;ll=37.826161%2C55.77356&amp;spn=0.019637%2C0.006461&amp;l=map\">Посмотреть на&nbsp;Яндекс.Картах</a></p><p>Телефон 345-45-54</p>','1','6','1','Контакты',null,null),
('7','products','Все товары','Все товары','','Все товары','','2','7','1','Все товары',null,null);

/* Drop for table s_payment_methods */
DROP TABLE IF EXISTS `s_payment_methods`;
/* Create table s_payment_methods */
CREATE TABLE `s_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `currency_id` float DEFAULT NULL,
  `settings` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/* Clear table s_payment_methods */
TRUNCATE TABLE `s_payment_methods`;
/* Data for table s_payment_methods */
INSERT INTO `s_payment_methods` (`id`, `module`, `name`, `description`, `currency_id`, `settings`, `enabled`, `position`) VALUES
('1','Receipt','Квитанция','<p>Вы можете распечатать квитанцию и оплатить её в любом отделении банка.</p>','2','a:10:{s:9:\"recipient\";s:65:\"ООО \"Великолепный интернет-магазин\"\";s:3:\"inn\";s:5:\"12345\";s:7:\"account\";s:6:\"223456\";s:4:\"bank\";s:18:\"Альфабанк\";s:3:\"bik\";s:6:\"556677\";s:21:\"correspondent_account\";s:11:\"77777755555\";s:8:\"banknote\";s:7:\"руб.\";s:5:\"pense\";s:7:\"коп.\";s:5:\"purse\";s:2:\"ru\";s:10:\"secret_key\";s:0:\"\";}','1','2'),
('2','Webmoney','Webmoney wmz','<p><span></span></p><div><p>Оплата через платежную систему&nbsp;<a href=\"http://www.webmoney.ru\">WebMoney</a>. У вас должен быть счет в этой системе для того, чтобы произвести оплату. Сразу после оформления заказа вы будете перенаправлены на специальную страницу системы WebMoney, где сможете произвести платеж в титульных знаках WMZ.</p></div><p>&nbsp;</p>','3','a:10:{s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:7:\"руб.\";s:5:\"pense\";s:0:\"\";s:5:\"purse\";s:13:\"Z111111111111\";s:10:\"secret_key\";s:13:\"testsecretkey\";}','1','1'),
('3','Robokassa','Робокасса','<p><span>RBK Money &ndash; это электронная платежная система, с помощью которой Вы сможете совершать платежи с персонального компьютера, коммуникатора или мобильного телефона.</span></p>','3','a:14:{s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:0:\"\";s:5:\"pense\";s:0:\"\";s:5:\"login\";s:0:\"\";s:9:\"password1\";s:0:\"\";s:9:\"password2\";s:0:\"\";s:8:\"language\";s:2:\"ru\";s:5:\"purse\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";}','1','3'),
('4','Paypal','PayPal','<p>Совершайте покупки безопасно, без раскрытия информации о своей кредитной карте. PayPal защитит вас, если возникнут проблемы с покупкой</p>','1','a:16:{s:8:\"business\";s:0:\"\";s:4:\"mode\";s:7:\"sandbox\";s:9:\"recipient\";s:0:\"\";s:3:\"inn\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"bank\";s:0:\"\";s:3:\"bik\";s:0:\"\";s:21:\"correspondent_account\";s:0:\"\";s:8:\"banknote\";s:0:\"\";s:5:\"pense\";s:0:\"\";s:5:\"login\";s:0:\"\";s:9:\"password1\";s:0:\"\";s:9:\"password2\";s:0:\"\";s:8:\"language\";s:2:\"ru\";s:5:\"purse\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";}','1','4'),
('5','Interkassa','Оплата через Интеркассу','<p><span>Это удобный в использовании сервис, подключение к которому позволит Интернет-магазинам, веб-сайтам и прочим торговым площадкам принимать все возможные формы оплаты в максимально короткие сроки.</span></p>','2','a:2:{s:18:\"interkassa_shop_id\";s:3:\"123\";s:21:\"interkassa_secret_key\";s:3:\"123\";}','1','5'),
('6','Liqpay','Оплата картой через Liqpay.com','<p><span>Благодаря своей открытости и универсальности LiqPAY стремительно интегрируется со многими платежными системами и платформами и становится стандартом платежных операций.</span></p>','2','a:5:{s:9:\"liqpay_id\";s:3:\"123\";s:11:\"liqpay_sign\";s:3:\"123\";s:12:\"pay_way_card\";s:1:\"1\";s:14:\"pay_way_liqpay\";s:1:\"1\";s:15:\"pay_way_delayed\";s:1:\"1\";}','1','6'),
('7','Pay2Pay','Оплата через Pay2Pay','<p>Универсальный платежный сервис Pay2Pay призван облегчить и максимально упростить процесс приема электронных платежей на Вашем сайте. Мы открыты для всего нового и сверхсовременного.</p>','2','a:5:{s:18:\"pay2pay_merchantid\";s:3:\"123\";s:14:\"pay2pay_secret\";s:3:\"123\";s:14:\"pay2pay_hidden\";s:3:\"123\";s:15:\"pay2pay_paymode\";s:3:\"123\";s:16:\"pay2pay_testmode\";s:1:\"1\";}','1','7'),
('8','Qiwi','Оплатить через QIWI','<p><span>QIWI &mdash; удобный сервис для оплаты повседневных услуг</span></p>','2','a:2:{s:10:\"qiwi_login\";s:3:\"123\";s:13:\"qiwi_password\";s:3:\"123\";}','1','8');

/* Drop for table s_products */
DROP TABLE IF EXISTS `s_products`;
/* Create table s_products */
CREATE TABLE `s_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(500) NOT NULL,
  `annotation` text,
  `body` longtext,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL DEFAULT '0',
  `meta_title` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created` timestamp(4) NULL DEFAULT CURRENT_TIMESTAMP(4),
  `featured` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `brand_id` (`brand_id`),
  KEY `visible` (`visible`),
  KEY `position` (`position`),
  KEY `hit` (`featured`),
  KEY `name` (`name`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_products */
TRUNCATE TABLE `s_products`;
/* Data for table s_products */

/* Drop for table s_products_categories */
DROP TABLE IF EXISTS `s_products_categories`;
/* Create table s_products_categories */
CREATE TABLE `s_products_categories` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `position` (`position`),
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_products_categories */
TRUNCATE TABLE `s_products_categories`;
/* Data for table s_products_categories */

/* Drop for table s_purchases */
DROP TABLE IF EXISTS `s_purchases`;
/* Create table s_purchases */
CREATE TABLE `s_purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT '0',
  `variant_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` int(11) NOT NULL DEFAULT '0',
  `sku` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_purchases */
TRUNCATE TABLE `s_purchases`;
/* Data for table s_purchases */

/* Drop for table s_queue */
DROP TABLE IF EXISTS `s_queue`;
/* Create table s_queue */
CREATE TABLE `s_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(3000) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/* Clear table s_queue */
TRUNCATE TABLE `s_queue`;
/* Data for table s_queue */
INSERT INTO `s_queue` (`id`, `keyhash`, `method`, `task`) VALUES
('1','	h%qKr����a���','','$this->products->get_products(array (\n  \'featured\' => 1,\n  \'var\' => \'featured_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('2','7H�=K�,y��D�u�','','$this->products->get_products(array (\n  \'limit\' => 3,\n  \'sort\' => \'created\',\n  \'var\' => \'new_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('3','}�c^��ܗ$�o��','','$this->products->get_products(array (\n  \'discounted\' => 1,\n  \'limit\' => 9,\n  \'var\' => \'discounted_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('7','�}�����p�;�lm','','$this->products->get_products(array (\n  \'id\' => \n  array (\n  ),\n  \'force_no_cache\' => true,\n));'),
('8','O��SO��MN����','','$this->products->get_products(array (\n  \'id\' => \n  array (\n    0 => \'139\',\n    1 => \'14278\',\n    2 => \'15038\',\n    3 => \'15165\',\n  ),\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));');

/* Drop for table s_queue_full */
DROP TABLE IF EXISTS `s_queue_full`;
/* Create table s_queue_full */
CREATE TABLE `s_queue_full` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(3000) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/* Clear table s_queue_full */
TRUNCATE TABLE `s_queue_full`;
/* Data for table s_queue_full */
INSERT INTO `s_queue_full` (`id`, `keyhash`, `method`, `task`) VALUES
('1','	h%qKr����a���','','$this->products->get_products(array (\n  \'featured\' => 1,\n  \'var\' => \'featured_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('2','7H�=K�,y��D�u�','','$this->products->get_products(array (\n  \'limit\' => 3,\n  \'sort\' => \'created\',\n  \'var\' => \'new_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('3','}�c^��ܗ$�o��','','$this->products->get_products(array (\n  \'discounted\' => 1,\n  \'limit\' => 9,\n  \'var\' => \'discounted_products\',\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));'),
('4','�}�����p�;�lm','','$this->products->get_products(array (\n  \'id\' => \n  array (\n  ),\n  \'force_no_cache\' => true,\n));'),
('5','O��SO��MN����','','$this->products->get_products(array (\n  \'id\' => \n  array (\n    0 => \'139\',\n    1 => \'14278\',\n    2 => \'15038\',\n    3 => \'15165\',\n  ),\n  \'visible\' => 1,\n  \'force_no_cache\' => true,\n));');

/* Drop for table s_related_products */
DROP TABLE IF EXISTS `s_related_products`;
/* Create table s_related_products */
CREATE TABLE `s_related_products` (
  `product_id` int(11) NOT NULL,
  `related_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_related_products */
TRUNCATE TABLE `s_related_products`;
/* Data for table s_related_products */

/* Drop for table s_settings */
DROP TABLE IF EXISTS `s_settings`;
/* Create table s_settings */
CREATE TABLE `s_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/* Clear table s_settings */
TRUNCATE TABLE `s_settings`;
/* Data for table s_settings */
INSERT INTO `s_settings` (`setting_id`, `name`, `value`) VALUES
('1','theme','default'),
('2','site_name','site'),
('3','company_name','site'),
('4','date_format','d.m.Y'),
('5','admin_email','site@site.si'),
('6','order_email','site@site.si'),
('7','comment_email','site@site.si'),
('8','notify_from_email','site@site.si'),
('9','decimals_point','.'),
('10','thousands_separator',' '),
('11','products_num','24'),
('12','products_num_admin','24'),
('13','max_order_amount','100'),
('14','units','ед.');

/* Drop for table s_users */
DROP TABLE IF EXISTS `s_users`;
/* Create table s_users */
CREATE TABLE `s_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) DEFAULT NULL,
  `created` timestamp(4) NOT NULL DEFAULT CURRENT_TIMESTAMP(4),
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_users */
TRUNCATE TABLE `s_users`;
/* Data for table s_users */

/* Drop for table s_variants */
DROP TABLE IF EXISTS `s_variants`;
/* Create table s_variants */
CREATE TABLE `s_variants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `compare_price` decimal(14,2) DEFAULT NULL,
  `stock` mediumint(9) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `attachment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `sku` (`sku`),
  KEY `price` (`price`),
  KEY `stock` (`stock`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Clear table s_variants */
TRUNCATE TABLE `s_variants`;
/* Data for table s_variants */

