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
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pdn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;