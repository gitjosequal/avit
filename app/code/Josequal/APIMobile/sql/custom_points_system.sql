-- Custom Points System Database Schema
-- إنشاء جدول النقاط المخصص

CREATE TABLE IF NOT EXISTS `custom_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL,
  `description` text,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول إجمالي النقاط للعملاء
CREATE TABLE IF NOT EXISTS `custom_points_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `used_points` int(11) NOT NULL DEFAULT 0,
  `available_points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول استخدام النقاط في السلة
CREATE TABLE IF NOT EXISTS `custom_points_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `points_used` int(11) NOT NULL DEFAULT 0,
  `discount_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_id` (`quote_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج بيانات تجريبية
INSERT INTO `custom_points` (`customer_id`, `points`, `action`, `description`, `created_at`) VALUES
(1, 100, 'signup', 'Welcome bonus for new customer', NOW()),
(1, 50, 'review', 'Product review bonus', NOW()),
(1, 200, 'order', 'Order completion bonus', NOW()),
(2, 150, 'signup', 'Welcome bonus for new customer', NOW()),
(2, 75, 'order', 'Order completion bonus', NOW());

-- تحديث إجمالي النقاط للعملاء
INSERT INTO `custom_points_balance` (`customer_id`, `total_points`, `used_points`, `available_points`) VALUES
(1, 350, 0, 350),
(2, 225, 0, 225)
ON DUPLICATE KEY UPDATE
`total_points` = VALUES(`total_points`),
`available_points` = VALUES(`available_points`);
