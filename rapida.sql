/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_blog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `url` varchar(255) DEFAULT '',
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
  KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_brands` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trans` varchar(255) CHARACTER SET ascii DEFAULT '',
  `trans2` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(500) DEFAULT '',
  `meta_keywords` varchar(500) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `description` text,
  `image` varchar(255) DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `id_name` (`id`,`name`),
  KEY `trans2` (`trans2`),
  KEY `trans` (`trans`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_cache_integer` (
  `updated` date DEFAULT '1000-01-01',
  `keyhash` binary(16) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`keyhash`) USING BTREE,
  UNIQUE KEY `keyhash_value` (`keyhash`,`value`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT '',
  `meta_keywords` varchar(255) DEFAULT '',
  `meta_description` varchar(500) DEFAULT '',
  `description` text,
  `url` varchar(255) DEFAULT '',
  `url2` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) DEFAULT '',
  `image_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `parent_id` (`parent_id`),
  KEY `pos` (`pos`),
  KEY `visible` (`visible`),
  KEY `url2` (`url2`)
) ENGINE=InnoDB AUTO_INCREMENT=641 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_categories_features` (
  `category_id` smallint(5) unsigned NOT NULL,
  `feature_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`feature_id`,`category_id`),
  UNIQUE KEY `cid_fid` (`category_id`,`feature_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_delivery_payment` (
  `delivery_id` int(10) unsigned NOT NULL,
  `payment_method_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`delivery_id`,`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка способом оплаты и способов доставки';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_features` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `trans` varchar(200) CHARACTER SET ascii DEFAULT '',
  `trans2` varchar(200) NOT NULL DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  `in_filter` tinyint(1) DEFAULT '0',
  `tpl` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `pos` (`pos`),
  KEY `in_filter` (`in_filter`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_img_blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`),
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_img_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`),
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_img_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `basename` varchar(255) NOT NULL,
  `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`item_id`) USING BTREE,
  KEY `pos` (`pos`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `basename` (`basename`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=419348 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_labels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `color` varchar(6) DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `pos` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_options` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85149 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_options_groups` (
  `id` tinyint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `pos` tinyint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_options_uniq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `val` varchar(512) DEFAULT '',
  `trans` varchar(512) CHARACTER SET ascii DEFAULT '',
  `trans2` varchar(512) CHARACTER SET ascii NOT NULL DEFAULT '',
  `md4` binary(16) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `md42` binary(16) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `md4_id` (`md4`,`id`) USING BTREE,
  UNIQUE KEY `val` (`val`),
  KEY `md42` (`md42`,`md4`,`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=454966 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_date` timestamp NOT NULL DEFAULT '1970-01-02 04:00:00',
  `closed` tinyint(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `phone` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `comment` varchar(1024) DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT '',
  `payment_details` text,
  `ip` varchar(15) DEFAULT '',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` varchar(1024) DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_code` varchar(255) DEFAULT '',
  `separate_delivery` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT '1970-01-02 04:00:00',
  PRIMARY KEY (`id`),
  KEY `login` (`user_id`),
  KEY `written_off` (`closed`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `code` (`url`),
  KEY `payment_status` (`paid`)
) ENGINE=InnoDB AUTO_INCREMENT=1013 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_orders_labels` (
  `order_id` smallint(5) unsigned NOT NULL,
  `label_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`order_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_pages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT '',
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
  KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `url2` varchar(255) NOT NULL DEFAULT '',
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
  KEY `url` (`url`),
  KEY `brand_id` (`brand_id`),
  KEY `pos` (`pos`),
  KEY `hit` (`featured`),
  KEY `name` (`name`(255)),
  KEY `visible` (`visible`) USING BTREE,
  KEY `url2` (`url2`),
  KEY `stock` (`stock`)
) ENGINE=InnoDB AUTO_INCREMENT=85149 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_products_categories` (
  `product_id` int(10) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`category_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1905 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(5000) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8469024 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_queue_full` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyhash` binary(16) NOT NULL,
  `method` varchar(15) CHARACTER SET ascii NOT NULL,
  `task` varchar(5000) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyhash` (`keyhash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3644821 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_related_products` (
  `product_id` int(10) unsigned NOT NULL,
  `related_id` int(10) unsigned NOT NULL,
  `pos` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`),
  KEY `pos` (`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_settings` (
  `setting_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_slider` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `basename` varchar(500) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(512) NOT NULL DEFAULT '',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pos` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `enabled` (`visible`),
  KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_login` timestamp NOT NULL DEFAULT '1970-01-02 02:00:00',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `admin` (`perm`),
  KEY `perm` (`admin`),
  KEY `created` (`created`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=85142 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
