ALTER TABLE `mc_product_dailymotion` ADD `order_pdn` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `video_id_pdn`;
ALTER TABLE `mc_product_dailymotion` ADD `private_id` VARCHAR(30) NULL AFTER `video_id_pdn`;
