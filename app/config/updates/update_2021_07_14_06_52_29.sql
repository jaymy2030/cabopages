
START TRANSACTION;

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` text DEFAULT NULL,
  `type` enum('da','pd') DEFAULT 'da',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prices`;
CREATE TABLE IF NOT EXISTS `prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned DEFAULT NULL,
  `from_location_id` int(10) unsigned DEFAULT NULL,
  `to_location_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `booking_type` enum('from','to') DEFAULT 'from';
ALTER TABLE `bookings` ADD COLUMN `from_location_id` int(10) unsigned DEFAULT NULL;
ALTER TABLE `bookings` ADD COLUMN `to_location_id` int(10) unsigned DEFAULT NULL;

ALTER TABLE `bookings` CHANGE `payment_method` `payment_method` enum('paypal','authorize','creditcard','cash','bank', 'stripe') DEFAULT NULL;
ALTER TABLE `bookings_payments` CHANGE `payment_method` `payment_method` enum('paypal','authorize','creditcard','cash','bank', 'stripe') DEFAULT NULL;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_allow_stripe', 2, 'Yes|No::Yes', 'Yes|No', 'enum', 27, 1, NULL),
(1, 'o_stripe_public_key', 2, NULL, NULL, 'string', 28, 1, NULL),
(1, 'o_stripe_secret_key', 2, NULL, NULL, 'string', 29, 1, NULL),
(1, 'o_stripe_cancel_url', 2, NULL, NULL, 'string', 30, 1, NULL);

COMMIT;