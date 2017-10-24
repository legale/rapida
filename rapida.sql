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
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_blog */
;
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/* Data for table s_brands */
INSERT INTO `s_brands` (`id`,`name`,`url`,`meta_title`,`meta_keywords`,`meta_description`,`description`,`image`) VALUES
(1, 'Sonex', 'sonex', 'Sonex', 'Sonex', 'Sonex', '', ''),
(2, 'Arte Lamp', 'arte_lamp', 'Arte Lamp', 'Arte Lamp', 'Arte Lamp', '', ''),
(3, 'Ideal Lux', 'ideal_lux', 'Ideal Lux', 'Ideal Lux', 'Ideal Lux', '', ''),
(4, 'Citilux', 'citilux', 'Citilux', 'Citilux', 'Citilux', '', ''),
(5, 'Lightstar', 'lightstar', 'Lightstar', 'Lightstar', 'Lightstar', '', ''),
(6, 'St Luce', 'st_luce', 'St Luce', 'St Luce', 'St Luce', '', ''),
(7, 'IDLamp', 'idlamp', 'IDLamp', 'IDLamp', 'IDLamp', '', ''),
(8, 'Eurosvet', 'eurosvet', 'Eurosvet', 'Eurosvet', 'Eurosvet', '', '');
/* Drop for table s_cache_integer */
DROP TABLE IF EXISTS `s_cache_integer`;
/* Create table s_cache_integer */
CREATE TABLE `s_cache_integer` (
  `updated` date DEFAULT NULL,
  `keyhash` binary(16) NOT NULL,
  `value` mediumint(4) NOT NULL,
  PRIMARY KEY (`keyhash`) USING BTREE,
  UNIQUE KEY `keyhash_value` (`keyhash`,`value`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_cache_integer */
INSERT INTO `s_cache_integer` (`updated`,`keyhash`,`value`) VALUES
('2017-10-24', 0xcc23865436abc431007759e15a11991a, 10);
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
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/* Data for table s_categories */
INSERT INTO `s_categories` (`id`,`parent_id`,`name`,`meta_title`,`meta_keywords`,`meta_description`,`description`,`url`,`image`,`position`,`visible`) VALUES
(1, 0, 'Люстры', 'Люстры', 'Люстры', 'Люстры', '', 'lyustry', '', 1, 1),
(2, 1, 'Люстры  по цене', 'Люстры  по цене', 'Люстры  по цене', 'Люстры  по цене', '', 'lyustry-po-tsene', '', 2, 1),
(3, 2, 'Недорогие подвесные люстры', 'Недорогие подвесные люстры', 'Недорогие подвесные люстры', 'Недорогие подвесные люстры', '', 'nedorogie-podvesnye-lyustry', '', 3, 1),
(4, 0, 'Светильники', 'Светильники', 'Светильники', 'Светильники', '', 'svetilniki', '', 4, 1),
(5, 4, 'Светильники по типу', 'Светильники по типу', 'Светильники по типу', 'Светильники по типу', '', 'svetilniki-po-tipu', '', 5, 1),
(6, 5, 'Недорогие светильники', 'Недорогие светильники', 'Недорогие светильники', 'Недорогие светильники', '', 'nedorogie-svetilniki', '', 6, 1),
(7, 6, 'Потолочные недорогие светильники', 'Потолочные недорогие светильники', 'Потолочные недорогие светильники', 'Потолочные недорогие светильники', '', 'potolochnye-nedorogie-svetilniki', '', 7, 1),
(8, 1, 'Люстры по стране', 'Люстры по стране', 'Люстры по стране', 'Люстры по стране', '', 'lyustry-po-strane', '', 8, 1),
(9, 8, 'Люстры Италия', 'Люстры Италия', 'Люстры Италия', 'Люстры Италия', '', 'lyustry-italiya', '', 9, 1),
(10, 1, 'Люстры по месту', 'Люстры по месту', 'Люстры по месту', 'Люстры по месту', '', 'lyustry-po-mestu', '', 10, 1),
(11, 10, 'Люстры для спальни', 'Люстры для спальни', 'Люстры для спальни', 'Люстры для спальни', '', 'lyustry-dlya-spalni', '', 11, 1),
(12, 1, 'Люстры по стилю', 'Люстры по стилю', 'Люстры по стилю', 'Люстры по стилю', '', 'lyustry-po-stilyu', '', 12, 1),
(13, 12, 'Современные люстры', 'Современные люстры', 'Современные люстры', 'Современные люстры', '', 'sovremennye-lyustry', '', 13, 1),
(14, 4, 'Светильники по стилю', 'Светильники по стилю', 'Светильники по стилю', 'Светильники по стилю', '', 'svetilniki-po-stilyu', '', 14, 1),
(15, 14, 'Светильники хай-тек', 'Светильники хай-тек', 'Светильники хай-тек', 'Светильники хай-тек', '', 'svetilniki-haj-tek', '', 15, 1),
(16, 2, 'Недорогие потолочные люстры', 'Недорогие потолочные люстры', 'Недорогие потолочные люстры', 'Недорогие потолочные люстры', '', 'nedorogie-potolochnye-lyustry', '', 16, 1),
(17, 0, 'Бра', 'Бра', 'Бра', 'Бра', '', 'bra', '', 17, 1),
(18, 17, 'Бра по стилю', 'Бра по стилю', 'Бра по стилю', 'Бра по стилю', '', 'bra-po-stilyu', '', 18, 1),
(19, 18, 'Бра современные', 'Бра современные', 'Бра современные', 'Бра современные', '', 'bra-sovremennye', '', 19, 1),
(20, 4, 'Светильники по форме', 'Светильники по форме', 'Светильники по форме', 'Светильники по форме', '', 'svetilniki-po-forme', '', 20, 1),
(21, 20, 'Светильники круглые', 'Светильники круглые', 'Светильники круглые', 'Светильники круглые', '', 'svetilniki-kruglye', '', 21, 1),
(22, 0, 'Споты', 'Споты', 'Споты', 'Споты', '', 'spoty', '', 22, 1),
(23, 22, 'Споты по стилю', 'Споты по стилю', 'Споты по стилю', 'Споты по стилю', '', 'spoty-po-stilyu', '', 23, 1),
(24, 23, 'Споты хай-тек', 'Споты хай-тек', 'Споты хай-тек', 'Споты хай-тек', '', 'spoty-haj-tek', '', 24, 1);
/* Drop for table s_categories_features */
DROP TABLE IF EXISTS `s_categories_features`;
/* Create table s_categories_features */
CREATE TABLE `s_categories_features` (
  `category_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`feature_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_categories_features */
INSERT INTO `s_categories_features` (`category_id`,`feature_id`) VALUES
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(3, 37),
(3, 38),
(3, 39),
(3, 40),
(3, 41),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7),
(7, 8),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14),
(7, 15),
(7, 16),
(7, 17),
(7, 18),
(7, 19),
(7, 20),
(7, 21),
(7, 22),
(7, 23),
(7, 24),
(7, 25),
(7, 27),
(7, 28),
(7, 29),
(7, 30),
(7, 31),
(7, 32),
(7, 33),
(7, 34),
(7, 35),
(7, 36),
(7, 37),
(7, 38),
(7, 39),
(7, 40),
(7, 41),
(7, 42),
(7, 43),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),
(9, 7),
(9, 8),
(9, 9),
(9, 10),
(9, 11),
(9, 12),
(9, 13),
(9, 14),
(9, 15),
(9, 16),
(9, 17),
(9, 18),
(9, 19),
(9, 20),
(9, 21),
(9, 22),
(9, 23),
(9, 24),
(9, 25),
(9, 26),
(9, 27),
(9, 28),
(9, 29),
(9, 30),
(9, 31),
(9, 32),
(9, 33),
(9, 34),
(9, 35),
(9, 36),
(9, 37),
(9, 38),
(9, 39),
(9, 40),
(9, 41),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 6),
(11, 7),
(11, 8),
(11, 9),
(11, 10),
(11, 11),
(11, 12),
(11, 13),
(11, 14),
(11, 15),
(11, 16),
(11, 17),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(11, 25),
(11, 26),
(11, 27),
(11, 28),
(11, 29),
(11, 30),
(11, 31),
(11, 32),
(11, 33),
(11, 34),
(11, 35),
(11, 36),
(11, 37),
(11, 38),
(11, 39),
(11, 40),
(11, 41),
(11, 42),
(11, 43),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(13, 6),
(13, 7),
(13, 8),
(13, 9),
(13, 10),
(13, 11),
(13, 12),
(13, 13),
(13, 14),
(13, 15),
(13, 16),
(13, 17),
(13, 18),
(13, 19),
(13, 20),
(13, 21),
(13, 22),
(13, 23),
(13, 24),
(13, 25),
(13, 27),
(13, 28),
(13, 29),
(13, 30),
(13, 31),
(13, 32),
(13, 34),
(13, 35),
(13, 36),
(13, 37),
(13, 38),
(13, 39),
(13, 40),
(13, 41),
(13, 42),
(13, 43),
(13, 44),
(15, 1),
(15, 2),
(15, 3),
(15, 4),
(15, 5),
(15, 6),
(15, 7),
(15, 8),
(15, 9),
(15, 10),
(15, 11),
(15, 12),
(15, 13),
(15, 14),
(15, 15),
(15, 16),
(15, 17),
(15, 18),
(15, 19),
(15, 20),
(15, 21),
(15, 22),
(15, 23),
(15, 24),
(15, 25),
(15, 26),
(15, 27),
(15, 28),
(15, 29),
(15, 30),
(15, 31),
(15, 32),
(15, 33),
(15, 34),
(15, 35),
(15, 36),
(15, 37),
(15, 38),
(15, 39),
(15, 40),
(15, 41),
(15, 42),
(15, 43),
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 5),
(16, 6),
(16, 7),
(16, 8),
(16, 9),
(16, 10),
(16, 11),
(16, 12),
(16, 13),
(16, 14),
(16, 15),
(16, 16),
(16, 17),
(16, 18),
(16, 19),
(16, 20),
(16, 21),
(16, 22),
(16, 23),
(16, 24),
(16, 25),
(16, 26),
(16, 27),
(16, 28),
(16, 29),
(16, 30),
(16, 31),
(16, 32),
(16, 33),
(16, 34),
(16, 35),
(16, 36),
(16, 37),
(16, 38),
(16, 39),
(16, 40),
(16, 41),
(16, 42),
(16, 43),
(19, 1),
(19, 2),
(19, 3),
(19, 4),
(19, 5),
(19, 6),
(19, 7),
(19, 8),
(19, 9),
(19, 11),
(19, 12),
(19, 14),
(19, 15),
(19, 16),
(19, 17),
(19, 18),
(19, 19),
(19, 20),
(19, 21),
(19, 22),
(19, 23),
(19, 24),
(19, 25),
(19, 27),
(19, 28),
(19, 29),
(19, 30),
(19, 31),
(19, 32),
(19, 33),
(19, 34),
(19, 35),
(19, 36),
(19, 37),
(19, 38),
(19, 39),
(19, 40),
(19, 41),
(19, 42),
(19, 43),
(19, 45),
(21, 1),
(21, 2),
(21, 3),
(21, 4),
(21, 5),
(21, 6),
(21, 7),
(21, 8),
(21, 9),
(21, 10),
(21, 11),
(21, 12),
(21, 13),
(21, 14),
(21, 15),
(21, 16),
(21, 17),
(21, 18),
(21, 19),
(21, 20),
(21, 21),
(21, 22),
(21, 23),
(21, 24),
(21, 25),
(21, 26),
(21, 27),
(21, 28),
(21, 29),
(21, 30),
(21, 31),
(21, 32),
(21, 33),
(21, 34),
(21, 35),
(21, 36),
(21, 37),
(21, 38),
(21, 39),
(21, 40),
(21, 41),
(21, 42),
(21, 43),
(21, 44),
(24, 1),
(24, 2),
(24, 3),
(24, 4),
(24, 5),
(24, 6),
(24, 7),
(24, 8),
(24, 9),
(24, 11),
(24, 12),
(24, 13),
(24, 14),
(24, 15),
(24, 16),
(24, 17),
(24, 18),
(24, 19),
(24, 20),
(24, 21),
(24, 22),
(24, 23),
(24, 24),
(24, 25),
(24, 27),
(24, 28),
(24, 29),
(24, 30),
(24, 31),
(24, 32),
(24, 33),
(24, 34),
(24, 35),
(24, 36),
(24, 37),
(24, 38),
(24, 39),
(24, 40),
(24, 41),
(24, 42),
(24, 43),
(24, 44),
(24, 45);
/* Drop for table s_comments */
DROP TABLE IF EXISTS `s_comments`;
/* Create table s_comments */
CREATE TABLE `s_comments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/* Data for table s_comments */
;
/* Drop for table s_coupons */
DROP TABLE IF EXISTS `s_coupons`;
/* Create table s_coupons */
CREATE TABLE `s_coupons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL,
  `expire` timestamp NULL DEFAULT NULL,
  `type` enum('absolute','percentage') NOT NULL DEFAULT 'absolute',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_order_price` decimal(10,2) DEFAULT NULL,
  `single` int(1) NOT NULL DEFAULT '0',
  `usages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_coupons */
;
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
/* Data for table s_currencies */
INSERT INTO `s_currencies` (`id`,`name`,`sign`,`code`,`rate_from`,`rate_to`,`cents`,`position`,`enabled`) VALUES
(1, 'RUR', 'RUR', 'RUR', 1.00, 1.00, 2, 1, 1);
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
/* Data for table s_delivery */
;
/* Drop for table s_delivery_payment */
DROP TABLE IF EXISTS `s_delivery_payment`;
/* Create table s_delivery_payment */
CREATE TABLE `s_delivery_payment` (
  `delivery_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  PRIMARY KEY (`delivery_id`,`payment_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Связка способом оплаты и способов доставки';
/* Data for table s_delivery_payment */
;
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
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/* Data for table s_features */
INSERT INTO `s_features` (`id`,`name`,`uri`,`position`,`in_filter`) VALUES
(1, '#url#', 'url', 0, 0),
(2, 'currencyId', 'currencyid', 1, 0),
(3, 'store', 'store', 2, 0),
(4, 'pickup', 'pickup', 3, 0),
(5, 'delivery', 'delivery', 4, 0),
(6, 'vendorCode', 'vendorcode', 5, 0),
(7, 'manufacturer_warranty', 'manufacturer_warranty', 6, 0),
(8, 'seller_warranty', 'seller_warranty', 7, 0),
(9, 'Тип', 'tip', 8, 0),
(10, 'Подтип', 'podtip', 9, 0),
(11, 'Стиль', 'stil', 10, 0),
(12, 'Интерьер', 'interer', 11, 0),
(13, 'Цвет', 'cvet', 12, 0),
(14, 'Виды материалов', 'vidy_materialov', 13, 0),
(15, 'Количество ламп', 'kolichestvo_lamp', 14, 0),
(16, 'Площадь освещения, м2', 'ploschad_osvescheniya_m2', 15, 0),
(17, 'Общая мощность, W', 'obschaya_moschnost_w', 16, 0),
(18, 'Цвет арматуры', 'cvet_armatury', 17, 0),
(19, 'Форма плафона', 'forma_plafona', 18, 0),
(20, 'Материал арматуры', 'material_armatury', 19, 0),
(21, 'Тип лампочки (основной)', 'tip_lampochki_osnovnoj', 20, 0),
(22, 'Мощность лампы, W', 'moschnost_lampy_w', 21, 0),
(23, 'Пульт ДУ', 'pult_du', 22, 0),
(24, 'Тип цоколя', 'tip_cokolya', 23, 0),
(25, 'Степень защиты, IP', 'stepen_zaschity_ip', 24, 0),
(26, 'Диаметр, мм', 'diametr_mm', 25, 0),
(27, 'Высота, мм', 'vysota_mm', 26, 0),
(28, 'Длина, мм', 'dlina_mm', 27, 0),
(29, 'Ширина, мм', 'shirina_mm', 28, 0),
(30, 'Напряжение, V', 'napryazhenie_v', 29, 0),
(31, 'Артикул производителя', 'artikul_proizvoditelya', 30, 0),
(32, 'Коллекция', 'kollekciya', 31, 0),
(33, 'Группа товаров', 'gruppa_tovarov', 32, 0),
(34, 'Гарантия', 'garantiya', 33, 0),
(35, 'Место установки', 'mesto_ustanovki', 34, 0),
(36, 'питание', 'pitanie', 35, 0),
(37, 'street', 'street', 36, 0),
(38, 'LED', 'led', 37, 0),
(39, 'Страна', 'strana', 38, 0),
(40, 'Производитель', 'proizvoditel', 39, 0),
(41, 'currency', 'currency', 40, 0),
(42, 'Цвет плафонов', 'cvet_plafonov', 41, 0),
(43, 'Материал плафонов', 'material_plafonov', 42, 0),
(44, 'Лампы в комплекте', 'lampy_v_komplekte', 43, 0),
(45, 'Глубина, мм', 'glubina_mm', 44, 0);
/* Drop for table s_feedbacks */
DROP TABLE IF EXISTS `s_feedbacks`;
/* Create table s_feedbacks */
CREATE TABLE `s_feedbacks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_feedbacks */
;
/* Drop for table s_groups */
DROP TABLE IF EXISTS `s_groups`;
/* Create table s_groups */
CREATE TABLE `s_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_groups */
;
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
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/* Data for table s_images */
INSERT INTO `s_images` (`id`,`name`,`product_id`,`filename`,`position`) VALUES
(1, '', 3, 'avrora_101385l.png', 1),
(2, '', 3, 'http://sevenlight.ru/files/originals/avrora_101385l-1.png', 2),
(3, '', 4, 'sonex_1234.png', 1),
(4, '', 4, 'http://sevenlight.ru/files/originals/sonex_1234-1.png', 2),
(5, '', 4, 'http://sevenlight.ru/files/originals/sonex_1234-2.png', 3),
(6, '', 5, 'artelamp_a9520lm6br.png', 1),
(7, '', 5, 'http://sevenlight.ru/files/originals/artelamp_a9520lm6br-1.png', 2),
(8, '', 6, 'ideallux_pavonesp6.png', 1),
(9, '', 6, 'http://sevenlight.ru/files/originals/ideallux_pavonesp6-1.png', 2),
(10, '', 7, 'citilux_el331c1201.png', 1),
(11, '', 7, 'http://sevenlight.ru/files/originals/citilux_el331c1201-1.png', 2),
(12, '', 8, 'lightstar_214436.png', 1),
(13, '', 8, 'http://sevenlight.ru/files/originals/lightstar_214436-1.png', 2),
(14, '', 9, 'stluce_sl48309203.png', 1),
(15, '', 9, 'http://sevenlight.ru/files/originals/stluce_sl48309203-1.png', 2),
(16, '', 10, 'idlamp_2371aoldbronze.png', 1),
(17, '', 10, 'http://sevenlight.ru/files/originals/idlamp_2371aoldbronze-1.png', 2),
(18, '', 11, 'citilux_cl70342r.png', 1),
(19, '', 11, 'http://sevenlight.ru/files/originals/citilux_cl70342r-1.png', 2),
(20, '', 12, 'eurosvet_200331hrom.png', 1),
(21, '', 12, 'http://sevenlight.ru/files/originals/eurosvet_200331hrom-1.png', 2);
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
/* Data for table s_labels */
;
/* Drop for table s_menu */
DROP TABLE IF EXISTS `s_menu`;
/* Create table s_menu */
CREATE TABLE `s_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/* Data for table s_menu */
INSERT INTO `s_menu` (`id`,`name`,`position`) VALUES
(1, 'Основное меню', 0),
(2, 'Другие страницы', 1);
/* Drop for table s_options */
DROP TABLE IF EXISTS `s_options`;
/* Create table s_options */
CREATE TABLE `s_options` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `1` mediumint(9) DEFAULT NULL,
  `2` mediumint(9) DEFAULT NULL,
  `3` mediumint(9) DEFAULT NULL,
  `4` mediumint(9) DEFAULT NULL,
  `5` mediumint(9) DEFAULT NULL,
  `6` mediumint(9) DEFAULT NULL,
  `7` mediumint(9) DEFAULT NULL,
  `8` mediumint(9) DEFAULT NULL,
  `9` mediumint(9) DEFAULT NULL,
  `10` mediumint(9) DEFAULT NULL,
  `11` mediumint(9) DEFAULT NULL,
  `12` mediumint(9) DEFAULT NULL,
  `13` mediumint(9) DEFAULT NULL,
  `14` mediumint(9) DEFAULT NULL,
  `15` mediumint(9) DEFAULT NULL,
  `16` mediumint(9) DEFAULT NULL,
  `17` mediumint(9) DEFAULT NULL,
  `18` mediumint(9) DEFAULT NULL,
  `19` mediumint(9) DEFAULT NULL,
  `20` mediumint(9) DEFAULT NULL,
  `21` mediumint(9) DEFAULT NULL,
  `22` mediumint(9) DEFAULT NULL,
  `23` mediumint(9) DEFAULT NULL,
  `24` mediumint(9) DEFAULT NULL,
  `25` mediumint(9) DEFAULT NULL,
  `26` mediumint(9) DEFAULT NULL,
  `27` mediumint(9) DEFAULT NULL,
  `28` mediumint(9) DEFAULT NULL,
  `29` mediumint(9) DEFAULT NULL,
  `30` mediumint(9) DEFAULT NULL,
  `31` mediumint(9) DEFAULT NULL,
  `32` mediumint(9) DEFAULT NULL,
  `33` mediumint(9) DEFAULT NULL,
  `34` mediumint(9) DEFAULT NULL,
  `35` mediumint(9) DEFAULT NULL,
  `36` mediumint(9) DEFAULT NULL,
  `37` mediumint(9) DEFAULT NULL,
  `38` mediumint(9) DEFAULT NULL,
  `39` mediumint(9) DEFAULT NULL,
  `40` mediumint(9) DEFAULT NULL,
  `41` mediumint(9) DEFAULT NULL,
  `42` mediumint(9) DEFAULT NULL,
  `43` mediumint(9) DEFAULT NULL,
  `44` mediumint(9) DEFAULT NULL,
  `45` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/* Data for table s_options */
INSERT INTO `s_options` (`product_id`,`1`,`2`,`3`,`4`,`5`,`6`,`7`,`8`,`9`,`10`,`11`,`12`,`13`,`14`,`15`,`16`,`17`,`18`,`19`,`20`,`21`,`22`,`23`,`24`,`25`,`26`,`27`,`28`,`29`,`30`,`31`,`32`,`33`,`34`,`35`,`36`,`37`,`38`,`39`,`40`,`41`,`42`,`43`,`44`,`45`) VALUES
(3, 1, 2, 3, 3, 4, 5, 4, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 10, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 24, 25, 26, 27, 28, 29, 30, 25, 31, 32, 33, 34, 35, , , , ),
(4, 36, 2, 3, 3, 4, 37, 4, 4, 38, 39, 40, 41, 42, 43, 35, 44, 18, 45, 46, 16, 17, 18, 19, 47, 21, , 48, 14, 14, 49, 50, 51, 28, 29, 30, 49, 31, 32, 33, 52, 35, 53, 54, , ),
(5, 55, 2, 3, 3, 4, 56, 4, 4, 6, 7, 57, 58, 59, 11, 60, 21, 61, 59, 15, 16, 17, 18, 19, 20, 21, 62, 63, 24, 24, 49, 64, 65, 28, 29, 30, 49, 31, 32, 66, 67, 35, , , , ),
(6, 68, 2, 3, 3, 4, 69, 4, 4, 6, 7, 70, 9, 45, 43, 60, 71, 72, 45, 73, 16, 17, 74, 19, 20, 21, 62, 75, 24, 24, 25, 76, 77, 28, 29, 30, 25, 31, 32, 66, 78, 35, 79, 54, , ),
(7, 80, 2, 3, 3, 4, 81, 4, 4, 6, 82, 83, 84, 45, 85, 48, 18, 48, 45, 46, 16, 86, 35, 87, 88, 21, , 49, 89, 89, 49, 90, 91, , 29, 30, 49, 31, 32, 92, 93, 35, 94, 95, 96, ),
(8, 97, 2, 3, 3, 4, 98, 4, 4, 38, 39, 99, 100, 101, 11, 35, 102, 103, 101, 104, 16, 105, 103, 19, 106, 21, 18, 107, 24, 24, 49, 108, 109, 28, 29, 30, 49, 31, 32, 66, 110, 35, 101, 16, , ),
(9, 111, 2, 3, 3, 4, 112, 4, 4, 6, 82, 113, 114, 42, 115, 116, 117, 118, 45, 104, 16, 17, 18, 19, 47, 21, 119, 120, 24, 24, 49, 121, 122, 28, 29, 30, 49, 31, 32, 66, 123, 35, 101, 54, , ),
(10, 124, 2, 3, 3, 4, 125, 4, 4, 126, , 83, 9, , 43, 35, 44, 18, 101, 127, 16, 17, 18, 19, 47, 21, , 118, 24, 48, 25, 128, 129, 28, 29, 130, 25, 31, 32, 66, 131, 35, 132, 54, , 118),
(11, 133, 2, 3, 3, 4, 134, 4, 4, 38, 39, 83, 135, 101, 43, 74, 21, 74, 136, 127, 16, 86, 35, 87, 88, 21, 137, 138, 24, 24, 25, 139, 140, 28, 29, 30, 25, 31, 32, 92, 93, 35, 101, 141, 96, ),
(12, 142, 2, 3, 3, 4, 143, 4, 4, 144, , 99, 114, 45, 43, 35, 145, 74, 45, 146, 16, 17, 74, 19, 20, 21, , 147, 24, 148, 25, 149, 150, 28, 29, 130, 25, 31, 32, 151, 152, 35, 45, 54, 31, 147);
/* Drop for table s_options_uniq */
DROP TABLE IF EXISTS `s_options_uniq`;
/* Create table s_options_uniq */
CREATE TABLE `s_options_uniq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `val` varchar(1024) NOT NULL,
  `md4` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `md4` (`md4`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=utf8;
/* Data for table s_options_uniq */
INSERT INTO `s_options_uniq` (`id`,`val`,`md4`) VALUES
(1, 'http://sevenlight.ru/products/podvesnaya-lyustra-avrora-amsterdam-10138-5l?utm_source=yandex_market&\nutm_medium=cpc&utm_term=3', 0x0850c7f9ce005a426f507d44a5fe4b9d),
(2, 'RUR', 0xedf127c2fc33c03187adca509c2f3e54),
(3, 'false', 0xf953c23587a74b35e4f5c5ee03a993df),
(4, 'true', 0x528da3040e6d56dfcc3835fa075bdc14),
(5, 'S000002', 0x7dc119d8342bd3f6cad65fec104c078c),
(6, 'люстра', 0x11719df690be40b1cc8c7f266ff032a4),
(7, 'подвесная', 0x80bda8ad6b6eb5b20ee65e146d9d0a25),
(8, 'Замковый', 0xcbfb8d641538ed7c0013da369f10ff29),
(9, 'для спальни', 0xb26bf13a265d6340a3d70d2ca45b8636),
(10, 'черный', 0x1d9774b0c6fc441fc553e2bc7ff3dc9c),
(11, 'металлические', 0x8af5b26756bc6b499ae925cf97e7326a),
(12, '5', 0x4d27bb8bed8b0a0f955dbd1a1739def0),
(13, '16.7', 0x97cd2ed55a2a52e8c6ea5ea7ef3254a0),
(14, '300', 0x87965c144b26b40fb919da0cb293fe47),
(15, 'без плафона', 0x436afe6d66dc3a7a54294878cc057f3d),
(16, 'металл', 0x947b5d856bd5d9f5d747eb7e2cf80135),
(17, 'накаливания', 0x9941ec1a817ca530246ed6fcb6253228),
(18, '60', 0x7bfb8d0bfa7367a2458b44a463da48c7),
(19, 'без пульта', 0x45ff86c44fa06da896284a34e6111884),
(20, 'E14', 0xc9f0b38f7b9f00a2f6fa101152597052),
(21, '20', 0xa0bb95c4a388c305bb564937594fdf07),
(22, '544', 0x3bee6e559d50b89338d279a0dae8c36d),
(23, '470', 0x41e9b1319e2300ec235eb556a4c9813c),
(24, '0', 0x342c311a21c700473059171b4d4c23f9),
(25, '230', 0x4adafda15ed09a2f686ef2ecfeee9d3e),
(26, '10138-5L', 0x5b26c91731bd9ebd26dc1366eca0e68c),
(27, 'Амстердам', 0xd2d534036d92ff6d1f27905b9679ca40),
(28, 'основной товар', 0x2920b5d5867321597f9a5501cfd9a3d0),
(29, '18 месяцев', 0xb52c1661b3b6b19ef732440a5c88479b),
(30, 'на потолок', 0xaa2fd0664705908556541eebffc1c032),
(31, 'нет', 0x7d5802e657f87d26fd5bceb59e8bfb61),
(32, 'НЕТ', 0x3c6893d6fcb2e55f3aa7da4493ea3ba8),
(33, 'Россия', 0xcc31f0fde719936757c4870eda7476d7),
(34, 'Аврора', 0x013d6c322db25acf2dd02a2865d53111),
(35, '1', 0x8de361cf60f36e3fdc3d2294f6fec983),
(36, 'http://sevenlight.ru/products/potolochnyj-svetilnik-sonex-treza-1234?utm_source=yandex_market&\nutm_medium=cpc&utm_term=4', 0x7a7828d000993dfdd00323ff1095ae14),
(37, 'S000003', 0xd475b6152483c5d92eff7f744ca5aabc),
(38, 'светильник', 0x83f910b5c5d57e29a02f8413a7fcc92f),
(39, 'потолочный', 0xccec081a33e0f8dad0e1bba2ad27e797),
(40, 'Прованс', 0x2a3df17394f0cc01b2d116a546a51d3e),
(41, 'для прихожей', 0xb4d085eac395ba52ed3fed909cd013e0),
(42, 'желтый', 0x385545a04e17dcb50f8b5a34c52fa029),
(43, 'стеклянные', 0x4b2d8be68250b4948ba38441bb6b1da9),
(44, '3.3', 0x19dbb1746184dcc1006be028e8869034),
(45, 'хром', 0xbf24d61d9d12dfcbd6e513c42ccc039e),
(46, 'квадратный', 0x7161d68287f622674d20c28f8559aaa1),
(47, 'E27', 0x96894b4382765504ac0d2614c8a455a5),
(48, '120', 0xdc5c6cf7f225291754ac04920b421da1),
(49, '220', 0xb56aa1497785226fcbc77f595f251822),
(50, '1234', 0x7179435477cb531cd342379958b3d57c),
(51, 'Treza', 0x07e834dc67a201ea297001c4c7c607c4),
(52, 'Sonex', 0xf48cad7c82ff9cb0882778ffc9d8d962),
(53, 'разноцветный', 0xfa26e44f50111a9a07ff62d73c0f9e52),
(54, 'стекло', 0xba3e82e2bf73c1c3cca89a9382fd3bcd),
(55, 'http://sevenlight.ru/products/podvesnaya-lyustra-arte-lamp-taverna-a9520lm-6br?utm_source=yandex_market&\nutm_medium=cpc&utm_term=5', 0xc72db7c5708b183773c90758699ff0dc),
(56, 'S000004', 0xaa4186d9e1f0a009eeb273f88cccdd23),
(57, 'В морском стиле', 0xe84f7217491de215995c353c80e71d44),
(58, 'для кафе и ресторанов', 0xbac7664eb2440320c5355dbe5aaef016),
(59, 'коричневый', 0x567151351c174f8a364199a12dcd99df),
(60, '6', 0xa16aee1a69fd2e335cc06f1bd291232a),
(61, '360', 0xae9f11a142e9886d2fa00f640a1d6f81),
(62, '560', 0x614d7b263173b2245133e2ad5e24a20a),
(63, '350', 0xa00723203c28593278dc56a4be0113db),
(64, 'A9520LM-6BR', 0x56d476dca693a7292587dd55a726f3f5),
(65, 'Taverna', 0x7c1237c8f6d10bfa20fbd67b74592395),
(66, 'Италия', 0x49dc21474a1c6e24872fe37d827db24e),
(67, 'Arte Lamp', 0x9cb364ca63ffa117102da4825cc3db10),
(68, 'http://sevenlight.ru/products/podvesnaya-lyustra-ideal-lux-pavone-sp6?utm_source=yandex_market&\nutm_medium=cpc&utm_term=6', 0x1a5b30504aff6667a2854a5396984bce),
(69, 'S000005', 0x42a07d5eeda578366c838db426d07f0d),
(70, 'Арт-деко', 0x8b9c3ffe30c3c2304b16c6fd40d37970),
(71, '13.3', 0x7c2eaedff3d2a79cb7ee561f95527751),
(72, '240', 0x7b41a24987b180a125d42f1606189f43),
(73, 'декоративный', 0x80f293b453070f6f70ec6de6e1da0a3b),
(74, '40', 0xe2e93ef4bd38a46ed3899b4d463b6cb9),
(75, '1100', 0x3c6fa88bd8b5d42b8123202759611419),
(76, 'Pavone SP6', 0x357eb35ee1f94695aacf8718c27247b1),
(77, 'Pavone', 0xe148e88e7657c2701eab9f6324516e0f),
(78, 'Ideal Lux', 0x08e21e710b01d0cfa931857e37434f46),
(79, 'бежевый', 0xfd0a106025632c69a8a9b2afa87cbfff),
(80, 'http://sevenlight.ru/products/potolochnaya-lyustra-s-pultom-du-citilux-lavita-el331c1201?utm_source=yandex_market&\nutm_medium=cpc&utm_term=8', 0x4536b40737fd66f8f8ba1b5f285f84d8),
(81, 'S000007', 0xb8561bc1e813e453683d78d08db43015),
(82, 'потолочная', 0x6195967f60148cc639d0329d45e226b3),
(83, 'Современный', 0x884b7228081c6675d5b04eb02c415c4b),
(84, 'для больших залов', 0x871964690479caa066d31a4431956545),
(85, 'хрустальные', 0xb0a245cd1fbc60bc2ad248811b7c7272),
(86, 'светодиодная', 0xdc1284201537018e6127df60c62c8d3e),
(87, 'с пультом', 0x9cf28fdfdeb2de53d786244befea0e0b),
(88, 'LED', 0xf1fd097249f9cccf156a1550b0bf591c),
(89, '710', 0x19bc895385e5249f8d1eef9463598156),
(90, 'EL331C120.1', 0x365f1a503d57f5378bae8ddb67f2d06f),
(91, 'Lavita', 0x749e7592e88d02d02d2c7309b8e064ae),
(92, 'Дания', 0xd09644249de739c57ce31d6080819edb),
(93, 'Citilux', 0x7c62c73ec2c4abddb196fa04481f9fb3),
(94, 'прозрачный', 0xb31b244e3c9fb7d4d733ea5c6ee232e5),
(95, 'хрусталь', 0x2669018e08785dc6c438476e42083508),
(96, 'да', 0x654c9d4b3c393fb7d083863f5ad0f480),
(97, 'http://sevenlight.ru/products/potolochnyj-svetilnik-lightstar-rullo-214436?utm_source=yandex_market&\nutm_medium=cpc&utm_term=10', 0x3b3748c8018f4d4a682edc6b08e6778b),
(98, 'S000009', 0x577a818487a3ccff32f4b8ee2afa3752),
(99, 'Хай-тек', 0x75f7c85e2bdf6c06231febb87bb24bda),
(100, 'для магазина', 0x6819e34f5e347e6c708ad74019a80d3c),
(101, 'белый', 0x647cc67949508190069d22abe902ae74),
(102, '2.8', 0x1ce5a0f0f2b5f34e586a11907a8b3555),
(103, '50', 0x51771c1cffdeff48ce0a39d6ff28f2ec),
(104, 'цилиндр', 0x0ad21f9792621e642d9611bdcb23be23),
(105, 'галогеновая', 0x741f7828ddc5fbe30fcb2f0b85bf5e0f),
(106, 'GU10', 0x279f7f3fadd198ffb1828154a82468b0),
(107, '102', 0xa5ef82947fab54db6648cb9924db0556),
(108, '214436', 0xbfc8fd6b3ae18f4ecfb13fb0efb73ba1),
(109, 'Rullo', 0x78db8a0eab705088835836776b0fe3b0),
(110, 'Lightstar', 0x5453517c8e3c446918f6e667131e9996),
(111, 'http://sevenlight.ru/products/potolochnaya-lyustra-st-luce-foresta-sl48309203?utm_source=yandex_market&\nutm_medium=cpc&utm_term=11', 0x8baa0ce9e354b5259564a27adc585fb7),
(112, 'S000010', 0x949155b4c39dae0da9fa77286521ab4b),
(113, 'Ретро', 0x619e367d5ddc4d42ab7ab3ef210f5197),
(114, 'для кухни', 0x2cbce945088f2baa5ae5f52e116d3f95),
(115, 'пластиковые', 0xcee0879ff31d04d268a7199f24af0eaa),
(116, '3', 0xb74ca4914133f4c4ce5bb4d86fe81fd4),
(117, '10', 0xd17aa7b33bc58fbb51b94a8a12e9b7ae),
(118, '180', 0x13d0cd9cf9417d89dbd387488c2335d3),
(119, '600', 0xcd3aaadd81a7f7d74fb6941903ea5994),
(120, '200', 0x866c9c6c3a3777fe75fe0e64a16703b7),
(121, 'SL483.092.03', 0x29b535d85711d568c08d5b7941a58f6a),
(122, 'Foresta', 0xcdd82f6c3e8b97e339a837936043d9c7),
(123, 'ST Luce', 0x77fcad32cb99031785eaf56d10930e19),
(124, 'http://sevenlight.ru/products/bra-idlamp-brunilda-2371a-oldbronze?utm_source=yandex_market&\nutm_medium=cpc&utm_term=14', 0xda24000bca206882bb2c6a6970f78a98),
(125, 'S000013', 0x364cfdb9cea98249aed5020240183f27),
(126, 'бра', 0x3cc871e75f74256e6fcf9a16a7749dc4),
(127, 'круглый', 0xe5d4c16ef81d49ec20200db3fa203486),
(128, '237/1A-Oldbronze', 0x757941b076b3e55e6b42590410455062),
(129, 'Brunilda', 0xd4c6b58032a1fe2fd563c640f451f66c),
(130, 'на стену', 0x8724d35bf3c89706e0e3c95819aa58f1),
(131, 'IDLamp', 0x4e4aadab5536d65f8806610f5eb3bcd6),
(132, 'бронза', 0xb6aadb2b0b107d423ead7782eff1fb7f),
(133, 'http://sevenlight.ru/products/potolochnyj-svetilnik-s-pultom-du-citilux-starlajt-cl70342r?utm_source=yandex_market&\nutm_medium=cpc&utm_term=16', 0x707d92c31330a779b07026ffd8719507),
(134, 'S000015', 0xe3d2f7abc7eacf7bdf2ebcb236610f27),
(135, 'для гостиной', 0xdfee0ffaf3b4beb0aeae4758f8787a4f),
(136, 'Золотой', 0x2c27815b284d98c6b82d11e5b4648f9e),
(137, '490', 0xec6301019023da19b3487d40031a4868),
(138, '90', 0xd04702094346f55083c9e44ccec5af8b),
(139, 'CL70342R', 0xa7fb2effde24863c37320d89570d57f2),
(140, 'СтарЛайт', 0x57cc00a8d6f75c3f31e8ad9d1294bc5c),
(141, 'пластик', 0x374a57d82b928e02df954c5b502ccb3f),
(142, 'http://sevenlight.ru/products/spot-eurosvet-200331-hrom?utm_source=yandex_market&\nutm_medium=cpc&utm_term=18', 0x1ae0bf593bcf20f54f20647677b6fa0a),
(143, 'S000017', 0xa68408649a0e599b8aa7efbeedacad58),
(144, 'спот', 0xd00728a37528e8b4e87e0687f9557526),
(145, '2.2', 0x31c56e372022283138ae17f7b95f7617),
(146, 'овал', 0x6cdd58d5d6b46e94cd6218f5ff4e4f78),
(147, '160', 0x6b5043ec5a1a4fa21fb5d5a6dfb794af),
(148, '80', 0x38d0c01edd92f368716872871b61fa12),
(149, '20033/1 хром', 0x4a869b51ea59e6fa39104aee28d2de75),
(150, '20033', 0x4041d0b5613eee21be1c978a95e0121c),
(151, 'Китай', 0x87b24a5bf4c91d1edd3b637f3dd92353),
(152, 'Eurosvet', 0xb68f54d747922357e9f0a7eecb3383f4);
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
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `login` (`user_id`),
  KEY `written_off` (`closed`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `code` (`url`),
  KEY `payment_status` (`paid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_orders */
;
/* Drop for table s_orders_labels */
DROP TABLE IF EXISTS `s_orders_labels`;
/* Create table s_orders_labels */
CREATE TABLE `s_orders_labels` (
  `order_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`order_id`,`label_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_orders_labels */
;
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
/* Data for table s_pages */
INSERT INTO `s_pages` (`id`,`url`,`name`,`meta_title`,`meta_description`,`meta_keywords`,`body`,`menu_id`,`position`,`visible`,`header`,`new_field`,`new_field2`) VALUES
(1, '', 'Главная', 'Хиты продаж', 'Этот магазин является демонстрацией скрипта интернет-магазина  Simpla . Все материалы на этом сайте присутствуют исключительно в демострационных целях.', 'Хиты продаж', '<p>Этот магазин является демонстрацией скрипта интернет-магазина <a href=\"http://simplacms.ru\">Simpla</a>. Все материалы на этом сайте присутствуют исключительно в демострационных целях.</p>', 1, 1, 1, 'О магазине', '', ''),
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
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `featured` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `brand_id` (`brand_id`),
  KEY `visible` (`visible`),
  KEY `position` (`position`),
  KEY `hit` (`featured`),
  KEY `name` (`name`(333))
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/* Data for table s_products */
INSERT INTO `s_products` (`id`,`url`,`image`,`brand_id`,`name`,`annotation`,`body`,`visible`,`position`,`meta_title`,`meta_keywords`,`meta_description`,`created`,`featured`) VALUES
(3, 'lyustra-podvesnaya-avrora-10138-5l', '', 0, 'люстра подвесная Аврора 10138-5L', '', '- хорошее решение для интерьеров с небольшими потолками. подвесная люстра прекрасно впишется не только в коридор или спальню, но также и в остальные помещения вашего интерьера. могут быть большими по размеру и быть подходящими и для больших помещений с низкими потолками. Отличным вариантом будет подвесная люстра 10138-5L Амстердам бренда Аврора, Россия. Данный бренд зарекомендовал себя как надежный изготовитель люстр и светильников, пользующийся хорошим спросом среди российских покупателей. Интересный внешний вид изделия подвесная люстра 10138-5L сочетается с надежным качеством и практичностью. Эта модель прекрасно впишется в замковый интерьер, и не только. Цвет каркаса изделия - черный, а материал изготовления - металл, плафоны имеют цвет. Гармоничная форма и продуманная функциональность данной модели несомненно придаст теплую и радостную атмосферу, независимо от того куда вы собираетесь ее установить. подвесная люстра 10138-5L подойдет для помещения площадью 16.7 кв. м, это устройство оснащено лампочками в колличестве - 5 шт. Номинальная мощность составляет 300 ватт. Степень пылевлагозащищенности по международной классификации (Ingress Protection Rating) - IP20. В нашем интернет магазине Sevenlight.ru вы можете купить подвесная люстра Аврора 10138-5L по цене с доставкой по Москве и всей России прямо сейчас! В нашем интернет магазине вы имеете уникальную возможность купить данный товар по самой низкой цене в интернете!!! Чтобы получить скидку, используйте код купона REGION при оформлении заказа.', 1, 3, '', '', '', '2017-10-24 22:58:21', 0),
(4, 'svetilnik-potolochnyj-sonex-1234', '', 1, 'светильник потолочный Sonex 1234', '', '- хорошее решение для интерьеров с небольшими потолками. потолочный светильник прекрасно впишется не только в коридор или спальню, но также и в остальные помещения вашего интерьера. могут быть большими по размеру и быть подходящими и для больших помещений с низкими потолками. Отличным вариантом будет потолочный светильник 1234 Treza бренда Sonex, Россия. Данный бренд зарекомендовал себя как надежный изготовитель люстр и светильников, пользующийся хорошим спросом среди российских покупателей. Интересный внешний вид изделия потолочный светильник 1234 сочетается с надежным качеством и практичностью. Эта модель прекрасно впишется в прованс интерьер, и не только. Цвет каркаса изделия - хром, а материал изготовления - металл, плафоны имеют разноцветный цвет. Гармоничная форма и продуманная функциональность данной модели несомненно придаст теплую и радостную атмосферу, независимо от того куда вы собираетесь ее установить. потолочный светильник 1234 подойдет для помещения площадью 3.3 кв. м, это устройство оснащено лампочками в колличестве - 1 шт. Номинальная мощность составляет 60 ватт. Степень пылевлагозащищенности по международной классификации (Ingress Protection Rating) - IP20. В нашем интернет магазине Sevenlight.ru вы можете купить потолочный светильник Sonex 1234 по цене с доставкой по Москве и всей России прямо сейчас! В нашем интернет магазине вы имеете уникальную возможность купить данный товар по самой низкой цене в интернете!!! Чтобы получить скидку, используйте код купона REGION при оформлении заказа.', 1, 4, '', '', '', '2017-10-24 22:58:22', 0),
(5, 'lyustra-podvesnaya-arte-lamp-a9520lm-6br', '', 2, 'люстра подвесная Arte Lamp A9520LM-6BR', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет подвесная люстра Arte Lamp A9520LM-6BR(Италия).Важно обратить внимание, люстра A9520LM-6BR Arte Lamp соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь люстра A9520LM-6BR Taverna &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика Arte Lamp очень тщательно следит за уровнем качества выпускаемой продукции. люстра Arte Lamp A9520LM-6BR серии Taverna имеет соответствующий дизайн в стиле - замковый. Материал, используемый в модели Arte Lamp Taverna &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики Arte Lamp, из которых состоит люстра A9520LM-6BR &mdash; это коричневый,. подвесная люстра Arte Lamp Taverna отлично подойдет как для кафе. ресторанов, так и для других помещений, исполненных в стиле замковый. люстра A9520LM-6BR в среднем освещает помещение площадью 20 кв. м. Лампочек данная модель имеет 6xE14, общая мощность которых - 360 ватт. Показатель защиты от влаги - IP20. подвесная люстра Arte LampA9520LM-6BR удачно сочетает в себе внешний вид, выполненный в стиле замковый и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель люстра A9520LM-6BR производителя Arte Lamp за', 1, 5, '', '', '', '2017-10-24 22:58:22', 0),
(6, 'lyustra-podvesnaya-ideal-lux-pavone-sp6', '', 3, 'люстра подвесная Ideal Lux Pavone SP6', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет подвесная люстра Ideal Lux Pavone SP6(Италия).Важно обратить внимание, люстра Pavone SP6 Ideal Lux соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь люстра Pavone SP6 Pavone &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика Ideal Lux очень тщательно следит за уровнем качества выпускаемой продукции. люстра Ideal Lux Pavone SP6 серии Pavone имеет соответствующий дизайн в стиле - арт-деко. Материал, используемый в модели Ideal Lux Pavone &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики Ideal Lux, из которых состоит люстра Pavone SP6 &mdash; это хром, бежевый. подвесная люстра Ideal Lux Pavone отлично подойдет как для спальни, так и для других помещений, исполненных в стиле арт-деко. люстра Pavone SP6 в среднем освещает помещение площадью 13.3 кв. м. Лампочек данная модель имеет 6xE14, общая мощность которых - 240 ватт. Показатель защиты от влаги - IP20. подвесная люстра Ideal LuxPavone SP6 удачно сочетает в себе внешний вид, выполненный в стиле арт-деко и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель люстра Pavone SP6 производителя Ideal Lux за', 1, 6, '', '', '', '2017-10-24 22:58:22', 0),
(7, 'lyustra-potolochnaya-citilux-el331c1201', '', 4, 'люстра потолочная Citilux EL331C120.1', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет потолочная люстра Citilux EL331C120.1(Дания).Важно обратить внимание, люстра EL331C120.1 Citilux соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь люстра EL331C120.1 Lavita &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика Citilux очень тщательно следит за уровнем качества выпускаемой продукции. люстра Citilux EL331C120.1 серии Lavita имеет соответствующий дизайн в стиле - современный. Материал, используемый в модели Citilux Lavita &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики Citilux, из которых состоит люстра EL331C120.1 &mdash; это хром, прозрачный. потолочная люстра Citilux Lavita отлично подойдет как для больших залов, так и для других помещений, исполненных в стиле современный. люстра EL331C120.1 в среднем освещает помещение площадью 60 кв. м. Лампочек данная модель имеет 120xLED, общая мощность которых - 120 ватт. Показатель защиты от влаги - IP20. потолочная люстра CitiluxEL331C120.1 удачно сочетает в себе внешний вид, выполненный в стиле современный и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель люстра EL331C120.1 производителя Citilux за', 1, 7, '', '', '', '2017-10-24 22:58:22', 0),
(8, 'svetilnik-potolochnyj-lightstar-214436', '', 5, 'светильник потолочный Lightstar 214436', '', '- хорошее решение для интерьеров с небольшими потолками. потолочный светильник прекрасно впишется не только в коридор или спальню, но также и в остальные помещения вашего интерьера. могут быть большими по размеру и быть подходящими и для больших помещений с низкими потолками. Отличным вариантом будет потолочный светильник 214436 Rullo бренда Lightstar, Италия. Данный бренд зарекомендовал себя как надежный изготовитель люстр и светильников, пользующийся хорошим спросом среди российских покупателей. Интересный внешний вид изделия потолочный светильник 214436 сочетается с надежным качеством и практичностью. Эта модель прекрасно впишется в современный интерьер, и не только. Цвет каркаса изделия - белый, а материал изготовления - металл, плафоны имеют белый цвет. Гармоничная форма и продуманная функциональность данной модели несомненно придаст теплую и радостную атмосферу, независимо от того куда вы собираетесь ее установить. потолочный светильник 214436 подойдет для помещения площадью 2.8 кв. м, это устройство оснащено лампочками в колличестве - 1 шт. Номинальная мощность составляет 50 ватт. Степень пылевлагозащищенности по международной классификации (Ingress Protection Rating) - IP20. В нашем интернет магазине Sevenlight.ru вы можете купить потолочный светильник Lightstar 214436 по цене с доставкой по Москве и всей России прямо сейчас! В нашем интернет магазине вы имеете уникальную возможность купить данный товар по самой низкой цене в интернете!!! Чтобы получить скидку, используйте код купона REGION при оформлении заказа.', 1, 8, '', '', '', '2017-10-24 22:58:22', 0),
(9, 'lyustra-potolochnaya-st-luce-sl48309203', '', 6, 'люстра потолочная ST Luce SL483.092.03', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет потолочная люстра ST Luce SL483.092.03(Италия).Важно обратить внимание, люстра SL483.092.03 ST Luce соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь люстра SL483.092.03 Foresta &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика ST Luce очень тщательно следит за уровнем качества выпускаемой продукции. люстра ST Luce SL483.092.03 серии Foresta имеет соответствующий дизайн в стиле - современный. Материал, используемый в модели ST Luce Foresta &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики ST Luce, из которых состоит люстра SL483.092.03 &mdash; это хром, белый. потолочная люстра ST Luce Foresta отлично подойдет как для кухни, так и для других помещений, исполненных в стиле современный. люстра SL483.092.03 в среднем освещает помещение площадью 10 кв. м. Лампочек данная модель имеет 3xE27, общая мощность которых - 180 ватт. Показатель защиты от влаги - IP20. потолочная люстра ST LuceSL483.092.03 удачно сочетает в себе внешний вид, выполненный в стиле современный и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель люстра SL483.092.03 производителя ST Luce за', 1, 9, '', '', '', '2017-10-24 22:58:23', 0),
(10, 'bra-idlamp-2371a-oldbronze', '', 7, 'бра IDLamp 237/1A-Oldbronze', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет бра IDLamp 237/1A-Oldbronze(Италия).Важно обратить внимание, бра 237/1A-Oldbronze IDLamp соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь бра 237/1A-Oldbronze Brunilda &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика IDLamp очень тщательно следит за уровнем качества выпускаемой продукции. бра IDLamp 237/1A-Oldbronze серии Brunilda имеет соответствующий дизайн в стиле - современный. Материал, используемый в модели IDLamp Brunilda &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики IDLamp, из которых состоит бра 237/1A-Oldbronze &mdash; это белый, бронза. бра IDLamp Brunilda отлично подойдет как для спальни, так и для других помещений, исполненных в стиле современный. бра 237/1A-Oldbronze в среднем освещает помещение площадью 3.3 кв. м. Лампочек данная модель имеет 1xE27, общая мощность которых - 60 ватт. Показатель защиты от влаги - IP20. бра IDLamp237/1A-Oldbronze удачно сочетает в себе внешний вид, выполненный в стиле современный и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель бра 237/1A-Oldbronze производителя IDLamp за', 1, 10, '', '', '', '2017-10-24 22:58:23', 0),
(11, 'svetilnik-potolochnyj-citilux-cl70342r', '', 4, 'светильник потолочный Citilux CL70342R', '', 'имеют большую популярность и предназначаются для множества интерьеров, главное правильно выбрать подходящий вариант из категории. Если Вам нужно купить надежные и привлекательные. Хорошим выбором для обустройства вашего жилища будет потолочный светильник Citilux CL70342R(Дания).Важно обратить внимание, светильник CL70342R Citilux соответствуем всем стандартам качества, это значит, вам не стоит переживать за надежность данного изделия, ведь светильник CL70342R СтарЛайт &mdash; сертифицированный продукт, прошедший все надлежащие проверки. Фабрика Citilux очень тщательно следит за уровнем качества выпускаемой продукции. светильник Citilux CL70342R серии СтарЛайт имеет соответствующий дизайн в стиле - современный. Материал, используемый в модели Citilux СтарЛайт &mdash; это металл, а гармонично подобранные цвета дизайнерами фабрики Citilux, из которых состоит светильник CL70342R &mdash; это золото, белый. потолочный светильник Citilux СтарЛайт отлично подойдет как для гостиной, так и для других помещений, исполненных в стиле современный. светильник CL70342R в среднем освещает помещение площадью 20 кв. м. Лампочек данная модель имеет 40xLED, общая мощность которых - 40 ватт. Показатель защиты от влаги - IP20. потолочный светильник CitiluxCL70342R удачно сочетает в себе внешний вид, выполненный в стиле современный и функциональные данные. Что несомненно является преимуществом приятной покупки. Купить модель светильник CL70342R производителя Citilux за', 1, 11, '', '', '', '2017-10-24 22:58:23', 0),
(12, 'spot-eurosvet-200331-hrom', '', 8, 'спот Eurosvet 20033/1 хром', '', '- хорошее решение для интерьеров с небольшими потолками. спот прекрасно впишется не только в коридор или спальню, но также и в остальные помещения вашего интерьера. могут быть большими по размеру и быть подходящими и для больших помещений с низкими потолками. Отличным вариантом будет спот 20033/1 хром 20033 бренда Eurosvet, Китай. Данный бренд зарекомендовал себя как надежный изготовитель люстр и светильников, пользующийся хорошим спросом среди российских покупателей. Интересный внешний вид изделия спот 20033/1 хром сочетается с надежным качеством и практичностью. Эта модель прекрасно впишется в хай-тек интерьер, и не только. Цвет каркаса изделия - хром, а материал изготовления - металл, плафоны имеют хром цвет. Гармоничная форма и продуманная функциональность данной модели несомненно придаст теплую и радостную атмосферу, независимо от того куда вы собираетесь ее установить. спот 20033/1 хром подойдет для помещения площадью 2.2 кв. м, это устройство оснащено лампочками в колличестве - 1 шт. Номинальная мощность составляет 40 ватт. Степень пылевлагозащищенности по международной классификации (Ingress Protection Rating) - IP20. В нашем интернет магазине Sevenlight.ru вы можете купить спот Eurosvet 20033/1 хром по цене с доставкой по Москве и всей России прямо сейчас! В нашем интернет магазине вы имеете уникальную возможность купить данный товар по самой низкой цене в интернете!!! Чтобы получить скидку, используйте код купона REGION при оформлении заказа.', 1, 12, '', '', '', '2017-10-24 22:58:23', 0);
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
/* Data for table s_products_categories */
INSERT INTO `s_products_categories` (`product_id`,`category_id`,`position`) VALUES
(3, 3, 0),
(4, 7, 0),
(5, 9, 0),
(6, 11, 0),
(7, 13, 0),
(8, 15, 0),
(9, 16, 0),
(10, 19, 0),
(11, 21, 0),
(12, 24, 0);
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
/* Data for table s_purchases */
;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/* Data for table s_queue */
;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/* Data for table s_queue_full */
INSERT INTO `s_queue_full` (`id`,`keyhash`,`method`,`task`) VALUES
(6, 0x69b9ccea57c797580393264e4507c0b3, '', '$this->features->get_options(array (\n  \'product_id\' => \'1\',\n  \'force_no_cache\' => true,\n));'),
(8, 0xd95769c498165d54aca5ddacf81bc8ae, '', '$this->features->get_options(array (\n  \'product_id\' => \'2\',\n  \'force_no_cache\' => true,\n));'),
(9, 0xc546a67375570b65c0d690f66fc16cb8, '', '$this->products->get_products(array (\n  \'limit\' => 10,\n  \'page\' => 1,\n  \'force_no_cache\' => true,\n));'),
(10, 0xcc23865436abc431007759e15a11991a, '', '$this->products->count_products(array (\n  \'force_no_cache\' => true,\n));');
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
/* Data for table s_related_products */
;
/* Drop for table s_settings */
DROP TABLE IF EXISTS `s_settings`;
/* Create table s_settings */
CREATE TABLE `s_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Data for table s_users */
;
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/* Data for table s_variants */
INSERT INTO `s_variants` (`id`,`product_id`,`sku`,`name`,`price`,`compare_price`,`stock`,`position`,`attachment`) VALUES
(1, 3, '3', '', 5190.00, 7550.00, 11, 1, ''),
(2, 4, '4', '', 1617.00, 0.00, 150, 1, ''),
(3, 5, '5', '', 10900.00, 14950.00, 66, 1, ''),
(4, 6, '6', '', 26110.00, 0.00, 1, 1, ''),
(5, 7, '8', '', 126750.00, 0.00, 1, 1, ''),
(6, 8, '10', '', 745.00, 866.00, 20, 1, ''),
(7, 9, '11', '', 6847.00, 7914.00, 21, 1, ''),
(8, 10, '14', '', 2490.00, 2990.00, 20, 1, ''),
(9, 11, '16', '', 6990.00, 7700.00, 40, 1, ''),
(10, 12, '18', '', 1076.00, 0.00, 11, 1, '');
