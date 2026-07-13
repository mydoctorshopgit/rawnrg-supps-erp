A complete solution for E-commerce Business with exclusive features & super responsive layout


ALTER TABLE `products` ADD `thumbnail_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `thumbnail_img`;
ALTER TABLE `products` ADD `gallery_alt` LONGTEXT NULL DEFAULT NULL AFTER `photos`;

ALTER TABLE `product_stocks` ADD `thumbnail_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `thumbnail_img`, ADD `gallery_alt` LONGTEXT NULL DEFAULT NULL AFTER `thumbnail_alt`;


ALTER TABLE `categories` ADD `banner_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `cover_image`, ADD `icon_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `banner_alt`, ADD `cover_image_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `icon_alt`;

ALTER TABLE `brands` ADD `logo_alt` VARCHAR(255) NULL DEFAULT NULL AFTER `logo`;