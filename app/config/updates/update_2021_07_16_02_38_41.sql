
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'infoUpdateLocationTitle2', 'backend', 'Infobox / Update location', 'script', '2021-07-16 02:12:52');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update location', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateLocationDesc2', 'backend', 'Infobox / Update location', 'script', '2021-07-16 02:13:43');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click on the Save button to edit location information.', 'script');

COMMIT;