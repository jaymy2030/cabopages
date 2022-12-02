START TRANSACTION;

  INSERT INTO `fields` VALUES (NULL, 'lblNoServicesAvailable', 'backend', 'Label / No services available', 'script', '2018-01-31 03:17:27');
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No services are available for that number of people/luggage', 'script');

COMMIT;