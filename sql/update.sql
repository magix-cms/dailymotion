ALTER TABLE `mc_product_dailymotion` ADD `order_pdn` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `video_id_pdn`;
ALTER TABLE `mc_product_dailymotion` ADD `private_id` VARCHAR(30) NULL AFTER `video_id_pdn`;

/* 1. On ajoute la nouvelle colonne de visibilité par produit */
ALTER TABLE `mc_product_dailymotion`
    ADD `visibility_pdm` ENUM('public', 'private', 'draft') DEFAULT 'private' AFTER `private_id`;

/* 2. On peut maintenant supprimer l'ancien champ booléen is_private_pdn si vous l'aviez créé */
ALTER TABLE `mc_product_dailymotion` DROP COLUMN IF EXISTS `is_private_pdn`;

/* 3. On nettoie la table globale */
ALTER TABLE `mc_dailymotion` DROP COLUMN IF EXISTS `visibility_dm`;
