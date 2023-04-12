CREATE TABLE IF NOT EXISTS `mc_dailymotion` (
    `id_dm` smallint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `apikey_dm` varchar(150) NOT NULL,
    `apisecret_dm` varchar(150) NOT NULL,
    `username_dm` varchar(150) NOT NULL,
    `password_dm` varchar(50) NOT NULL,
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_dm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_product_dailymotion` (
    `id_pdn` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_product` int(11) UNSIGNED NOT NULL,
    `name_pdn` varchar(150) NULL,
    `video_id_pdn` varchar(30) NULL,
    `private_id` varchar(30) NULL,
    `thumbnail_360_url` varchar(180) NULL,
    `thumbnail_720_url` varchar(180) NULL,
    `order_pdn` int(11) UNSIGNED NOT NULL DEFAULT '0',
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pdn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;