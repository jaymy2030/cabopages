
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'infoLocationsTitle2', 'backend', 'Infobox / Locations title', 'script', '2021-07-14 07:29:08');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of locations', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoLocationsDesc2', 'backend', 'Infobox / Locations description', 'script', '2021-07-14 07:30:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below is a list of locations you have added to the system. To edit the predefined location details, click the pencil icon corresponding to the row. To add new location, click on the button + Add new location', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddLocation', 'backend', 'Button / + Add location', 'script', '2021-07-14 07:30:36');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add location', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddLocationTitle2', 'backend', 'Infobox / Add new location', 'script', '2021-07-14 07:31:40');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new location', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddLocationDesc2', 'backend', 'Infobox / Add new location desc', 'script', '2021-07-14 07:32:21');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click on the Save button to add new location.', 'script');

INSERT INTO `fields` VALUES (NULL, 'location_types_ARRAY_da', 'arrays', 'location_types_ARRAY_da', 'script', '2021-07-14 07:34:47');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure/Arrival', 'script');

INSERT INTO `fields` VALUES (NULL, 'location_types_ARRAY_pd', 'arrays', 'location_types_ARRAY_pd', 'script', '2021-07-14 07:36:50');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up/Drop off', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDepartureArrivalLocation', 'backend', 'lblDepartureArrivalLocation', 'script', '2021-07-14 11:59:41');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure / Arrival location', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTravellingFrom', 'backend', 'lblTravellingFrom', 'script', '2021-07-15 06:30:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travelling from', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTravellingTo', 'backend', 'lblTravellingTo', 'script', '2021-07-15 06:30:54');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travelling to', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAvailableDropoffLocation', 'backend', 'lblAvailableDropoffLocation', 'script', '2021-07-15 06:40:29');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available dropoff location', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAvailablePickupLocation', 'backend', 'lblAvailablePickupLocation', 'script', '2021-07-15 06:41:54');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available pickup location', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_travelling_from', 'backend', 'front_travelling_from', 'script', '2021-07-15 08:06:04');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travelling from', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_travelling_to', 'backend', 'front_travelling_to', 'script', '2021-07-15 08:06:13');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travelling to', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_stripe', 'arrays', 'payment_methods_ARRAY_stripe', 'script', '2021-07-15 09:03:10');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stripe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_stripe', 'backend', 'Options / Allow payments with Stripe', 'script', '2021-07-15 09:04:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payments with Stripe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_stripe_public_key', 'backend', 'Options / Stripe public key', 'script', '2021-07-15 09:04:23');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stripe public key', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_stripe_secret_key', 'backend', 'Options / Stripe secret key', 'script', '2021-07-15 09:04:41');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stripe secret key', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_stripe_cancel_url', 'backend', 'Options / Stripe cancel URL', 'script', '2021-07-15 09:04:55');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stripe cancel URL', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_6', 'arrays', 'front_messages_ARRAY_6', 'script', '2021-07-15 09:13:32');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your reservation is saved. Redirecting to Stripe...', 'script');

COMMIT;