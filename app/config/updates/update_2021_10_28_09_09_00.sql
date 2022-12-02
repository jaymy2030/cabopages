
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_enquiry_url', 2, 'http://domain.com', NULL, 'string', 31, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_enquiry_url', 'backend', 'Options / URL to the webpage where the main script is installed', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL to the webpage where the main script is installed', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInstallCodeSearchForm', 'backend', 'Label / Install code for search form', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install code for search form', 'script');


COMMIT;