
START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `return_date` datetime DEFAULT NULL AFTER `booking_date`;

INSERT INTO `fields` VALUES (NULL, 'lblBookingReturnDatetime', 'backend', 'Label / Return date & time', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return date & time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_datetime', 'frontend', 'Label / Return date and time', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return date and time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_check_time_title', 'frontend', 'Label / Check date & time title', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_check_time_desc', 'frontend', 'Label / Check date & time desc', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return date & time must be greater than booking date & time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_roundtrip_datetime_desc', 'frontend', 'Label / Roundtrip date & time info', 'script', '2018-01-31 03:17:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Returning on %s at %s', 'script');



SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
UPDATE `multi_lang` SET `content` = 'There are three types of auto-responder messages you can send to both clients and admins. The first one will be triggered after a new enquiry is submitted via the software. The second one will be sent to confirm a successful payment and the third one - after a service has been canceled. You may enable or disable all auto-responders separately as well as personalize the message using the tokens below. <br/><br/><div class="float_left w200">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}</div><div class="float_left w250">{DateTime}<br/>{From}<br/>{To}<br/>{Vehicle}<br/>{Distance}<br/>{Passengers}<br/>{Luggage}<br/>{Extras}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}</div><div class="float_left w250">{Airline}<br/>{FlightNumber}<br/>{ArrivalTime}<br/>{Terminal}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}<br/>{IsRoundTrip}<br/>[Roundtrip]<br/>{ReturnDateTime}<br/>[/Roundtrip]</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three types of auto-responder messages you can send to both clients and admins. The first one will be triggered after a new enquiry is submitted via the software. The second one will be sent to confirm a successful payment and the third one - after a service has been canceled. You may enable or disable all auto-responders separately as well as personalize the message using the tokens below. <br/><br/><div class="float_left w200">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}</div><div class="float_left w250">{DateTime}<br/>{From}<br/>{To}<br/>{Vehicle}<br/>{Distance}<br/>{Passengers}<br/>{Luggage}<br/>{Extras}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}</div><div class="float_left w250">{Airline}<br/>{FlightNumber}<br/>{ArrivalTime}<br/>{Terminal}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}<br/>{IsRoundTrip}<br/>[Roundtrip]<br/>{ReturnDateTime}<br/>[/Roundtrip]</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";


COMMIT;