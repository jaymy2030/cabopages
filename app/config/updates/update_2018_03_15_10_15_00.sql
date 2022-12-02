START TRANSACTION;

  INSERT INTO `fields` VALUES (NULL, 'lblBookingsEmailExist', 'backend', 'Label / Email address already exist', 'script', '2018-01-31 03:17:27');
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email address already exist', 'script');

COMMIT;