CREATE TABLE IF NOT EXISTS `orders` (
  `ono` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `order_date` DATE NOT NULL,
  `shipping_status` VARCHAR(50) NOT NULL DEFAULT 'Not yet shipped',
  PRIMARY KEY (`ono`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE
) 