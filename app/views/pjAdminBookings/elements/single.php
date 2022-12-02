<div style="overflow: hidden; margin-bottom: 16px;">
	<div style="width: 48%;margin-right: 4%;float:left;">
		<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;margin-bottom: 40px;">
			<thead>
				<tr>
					<th colspan="2"><?php __('lblClientDetails');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$statuses = __('booking_statuses', true, false);
				$payment_methods = __('payment_methods', true, false);
				$name_titles = __('personal_titles', true, false);
				$booking_options = __('booking_options', true);
				$booking_options_yesno = __('booking_options_yesno', true, false);
				$client_name_arr = array();
				if(!empty($pickup_arr['c_title']) || !empty($pickup_arr['title']))
				{
					$client_name_arr[] = !empty($pickup_arr['client_id']) ? $name_titles[$pickup_arr['title']] : $name_titles[$pickup_arr['c_title']];
				}
				if(!empty($pickup_arr['c_fname']) || !empty($pickup_arr['fname']))
				{
					$client_name_arr[] = !empty($pickup_arr['client_id']) ? pjSanitize::clean($pickup_arr['fname']) : pjSanitize::clean($pickup_arr['c_fname']);
				}
				if(!empty($pickup_arr['c_lname']) || !empty($pickup_arr['lname']))
				{
					$client_name_arr[] = !empty($pickup_arr['client_id']) ? pjSanitize::clean($pickup_arr['lname']) : pjSanitize::clean($pickup_arr['c_lname']);
				}
				if(!empty($client_name_arr))
				{
					?>
					<tr class="bold">
						<td style="width:40%;"><?php __('lblName', false, false);?></td>
						<td style="width:60%;"><?php echo join(' ', $client_name_arr);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['phone']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingPhone', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['phone']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['email']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingEmail', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['email']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['company']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingCompany', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['company']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['address']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingAddress', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['address']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['city']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingCity', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['city']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['state']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingState', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['state']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['zip']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingZip', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['zip']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['country_id']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingCountry', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['country']);?></td>
					</tr>
					<?php
				} 
				if(!empty($pickup_arr['c_notes']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingNotes', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_notes']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_airline_company']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingAirlineCompany', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_airline_company']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_flight_number']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingFlightNumber', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_flight_number']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_flight_time']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblFlightTime', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_flight_time']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_destination_address']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingDestAddress', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_destination_address']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_cruise_ship']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingCruiseShip', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_cruise_ship']);?></td>
					</tr>
					<?php
				}
				if(!empty($pickup_arr['c_terminal']))
				{
					?>
					<tr>
						<td style="width:40%;"><?php __('lblBookingTerminal', false, false);?></td>
						<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_terminal']);?></td>
					</tr>
					<?php
				} 
				?>
			</tbody>
		</table>
	</div>
	<div style="width: 48%;float:right;">
		<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 40px;">
			<thead>
				<tr>
					<th colspan="2"><?php __('lblEnquiryDetails');?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblDateAndTime', false, false);?></td>
					<td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($pickup_arr['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($pickup_arr['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
				</tr>
				<?php if ($pickup_arr['booking_option'] == 'roundtrip') { ?>
					<tr class="bold">
						<td style="width:40%;"><?php __('lblBookingReturnDatetime', false, false);?></td>
						<td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($pickup_arr['return_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($pickup_arr['return_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
					</tr>
				<?php } ?>
				<tr>
					<td style="width:40%;"><?php __('lblPickupAddress', false, false);?></td>
					<td style="width:60%;"><?php echo pjAppController::getLocation($pickup_arr['booking_type'] == 'from' ? $pickup_arr['from_location_id'] : $pickup_arr['to_location_id'], $controller->getLocaleId());?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblDropoffAddress', false, false);?></td>
					<td style="width:60%;"><?php echo pjAppController::getLocation($pickup_arr['booking_type'] == 'from' ? $pickup_arr['to_location_id'] : $pickup_arr['from_location_id'], $controller->getLocaleId());?></td>
				</tr>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblVehicle', false, false);?></td>
					<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['fleet']);?></td>
				</tr>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblDistance', false, false);?></td>
					<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['distance']);?> km</td>
				</tr>
				<?php
				if(isset($tpl['extras']))
				{ 
					?>
					<tr class="bold">
						<td style="width:40%;"><?php __('lblExtras', false, false);?></td>
						<td style="width:60%;"><?php echo $tpl['extras'];?></td>
					</tr>
					<?php
				} 
				?>
				<tr>
					<td style="width:40%;"><?php __('lblPassengers', false, false);?></td>
					<td style="width:60%;"><?php echo $pickup_arr['passengers'];?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblLuggage', false, false);?></td>
					<td style="width:60%;"><?php echo $pickup_arr['luggage'];?></td>
				</tr>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblIsRoundTrip', false, false);?></td>
					<td style="width:60%;"><?php echo @$booking_options_yesno[$pickup_arr['booking_option']];?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblPayment', false, false);?></td>
					<td style="width:60%;"><?php echo pjUtil::formatCurrencySign($pickup_arr['total'], $tpl['option_arr']['o_currency']);?> / <?php echo $payment_methods[$pickup_arr['payment_method']];?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblStatus', false, false);?></td>
					<td style="width:60%;"><?php echo $statuses[$pickup_arr['status']];?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>