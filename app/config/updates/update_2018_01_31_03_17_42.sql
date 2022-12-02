
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_latitude', 1, '51.509865', NULL, 'string', 15, 1, NULL),
(1, 'o_longitude', 1, '-0.118092', NULL, 'string', 16, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_latitude', 'backend', 'Options / Default latitude', 'script', '2018-01-31 03:17:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default latitude', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_longitude', 'backend', 'Options / Default longitude', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default longitude', 'script');

COMMIT;