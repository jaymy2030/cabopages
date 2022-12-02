
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_google_signin_client_id', 1, '', NULL, 'string', 17, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_google_signin_client_id', 'backend', 'Options / Google Signin Client ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google Signin Client ID', 'script');

COMMIT;