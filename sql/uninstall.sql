TRUNCATE TABLE `mc_product_dailymotion`;
DROP TABLE `mc_product_dailymotion`;
TRUNCATE TABLE `mc_dailymotion`;
DROP TABLE `mc_dailymotion`;

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'dailymotion'
);