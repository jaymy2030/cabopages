
START TRANSACTION;

ALTER TABLE `prices` ADD COLUMN `price_roundtrip` decimal(9,2) unsigned DEFAULT NULL;
ALTER TABLE `bookings` ADD COLUMN `booking_option` enum('oneway','roundtrip') DEFAULT 'oneway';

INSERT INTO `fields` VALUES (NULL, 'lblBookingType', 'backend', 'Label / Type', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblOneWayPrice', 'backend', 'Label / One way price', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One way price', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblRoundtripPrice', 'backend', 'Label / Roundtrip price', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Roundtrip price', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_options_ARRAY_oneway', 'arrays', 'booking_options_ARRAY_oneway', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One way', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_options_ARRAY_roundtrip', 'arrays', 'booking_options_ARRAY_roundtrip', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Roundtrip', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblIsRoundTrip', 'backend', 'Label / Is roundtrip?', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is roundtrip?', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_options_yesno_ARRAY_oneway', 'arrays', 'booking_options_yesno_ARRAY_oneway', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_options_yesno_ARRAY_roundtrip', 'arrays', 'booking_options_yesno_ARRAY_roundtrip', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');



SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
UPDATE `multi_lang` SET `content` = 'There are three types of auto-responder messages you can send to both clients and admins. The first one will be triggered after a new enquiry is submitted via the software. The second one will be sent to confirm a successful payment and the third one - after a service has been canceled. You may enable or disable all auto-responders separately as well as personalize the message using the tokens below. <br/><br/><div class="float_left w200">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}</div><div class="float_left w250">{DateTime}<br/>{From}<br/>{To}<br/>{Vehicle}<br/>{Distance}<br/>{Passengers}<br/>{Luggage}<br/>{Extras}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}</div><div class="float_left w250">{Airline}<br/>{FlightNumber}<br/>{ArrivalTime}<br/>{Terminal}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}<br/>{IsRoundTrip}</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three types of auto-responder messages you can send to both clients and admins. The first one will be triggered after a new enquiry is submitted via the software. The second one will be sent to confirm a successful payment and the third one - after a service has been canceled. You may enable or disable all auto-responders separately as well as personalize the message using the tokens below. <br/><br/><div class="float_left w200">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}</div><div class="float_left w250">{DateTime}<br/>{From}<br/>{To}<br/>{Vehicle}<br/>{Distance}<br/>{Passengers}<br/>{Luggage}<br/>{Extras}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}</div><div class="float_left w250">{Airline}<br/>{FlightNumber}<br/>{ArrivalTime}<br/>{Terminal}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}<br/>{IsRoundTrip}</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";





COMMIT;