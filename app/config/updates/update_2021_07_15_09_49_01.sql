
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'menuLocations2', 'backend', 'Menu / Locations', 'script', '2021-07-14 07:18:38');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Locations', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ALO01', 'arrays', 'error_bodies_ARRAY_ALO01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes made to the location have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ALO03', 'arrays', 'error_bodies_ARRAY_ALO03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New location has been added into the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ALO04', 'arrays', 'error_bodies_ARRAY_ALO04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry that new location could not bee added successfully.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ALO08', 'arrays', 'error_bodies_ARRAY_ALO08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry that the location you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ALO01', 'arrays', 'error_titles_ARRAY_ALO01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ALO03', 'arrays', 'error_titles_ARRAY_ALO03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ALO04', 'arrays', 'error_titles_ARRAY_ALO04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location not added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ALO08', 'arrays', 'error_titles_ARRAY_ALO08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location not found!', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationTitle', 'backend', 'Label / Title', 'script', '2021-07-14 07:26:06');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationType', 'backend', 'Label / Type', 'script', '2021-07-14 07:26:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

COMMIT;