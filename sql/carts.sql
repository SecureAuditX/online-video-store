CREATE TABLE IF NOT EXISTS `cart` (
  `customer_id` INT(11) NOT NULL,
  `pno` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  PRIMARY KEY (`customer_id`, `pno`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`pno`) REFERENCES `products`(`pno`) ON DELETE CASCADE
)