<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	
	pjUtil::printNotice(__('infoAddBookingTitle', true, false), __('infoAddBookingDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateBooking">
		<input type="hidden" name="booking_create" value="1" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails');?></a></li>
			</ul>
			<div id="tabs-1" class="bs-loader-outer">
				<div class="bs-loader"></div>
				<p>
					<label class="title">&nbsp;</label>
					<span class="inline_block t5">
						<label class="block float_left r20"><input type="radio" name="booking_type" value="from" checked="checked"/><?php __('lblTravellingFrom');?></label>
						<label class="block float_left r20"><input type="radio" name="booking_type" value="to"/><?php __('lblTravellingTo');?></label>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<span class="inline_block t5">
						<label class="block float_left r20"><input type="radio" name="booking_option" value="oneway" checked="checked"/><?php __('booking_options_ARRAY_oneway');?></label>
						<label class="block float_left r20"><input type="radio" name="booking_option" value="roundtrip"/><?php __('booking_options_ARRAY_roundtrip');?></label>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDateTime'); ?></label>
					<span class="block overflow">
						<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" name="booking_date" id="booking_date" class="pj-form-field pointer w120 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" data-msg-required="<?php __('tr_field_required'); ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
				<p style="display: none;" class="pjReturnDateTime">
					<label class="title"><?php __('lblBookingReturnDatetime'); ?></label>
					<span class="block overflow">
						<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" name="return_date" id="return_date" class="pj-form-field pointer w120 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
				
				<p>
					<label class="title"><?php __('lblLocation'); ?></label>
					<span class="inline_block">
						<select name="from_location_id" id="from_location_id" class="pj-form-field w250 required" data-msg-required="<?php __('tr_field_required'); ?>">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['from_location_arr'] as $k => $v)
							{
							    ?><option value="<?php echo $v['id'];?>" data-address="<?php echo pjSanitize::html($v['address']);?>"><?php echo pjSanitize::html($v['name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label id="pjTbDropoffTitle" class="title"><?php __('lblAvailableDropoffLocation'); ?></label>
					<label id="pjTbPickupTitle" class="title" style="display:none"><?php __('lblAvailablePickupLocation'); ?></label>
					<span id="pjTbToLocationContainer" class="inline_block">
						<select name="to_location_id" id="to_location_id" class="pj-form-field w250 required" data-msg-required="<?php __('tr_field_required'); ?>">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
						</select>
					</span>
				</p>
				
				<p>
					<label class="title"><?php __('lblDistance'); ?></label>
					<span class="inline_block">
						<input type="text" id="distance" name="distance" class="pj-form-field digits w100 required" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-digits="<?php __('pj_digits_validation');?>"/>
						&nbsp;km
					</span>
				</p>
				
				<p>
					<label class="title"><?php __('lblFleet'); ?></label>
					<span id="pjTbVehicleContainer" class="inline_block">
						<select name="fleet_id" id="fleet_id" class="pj-form-field w300 required" data-msg-required="<?php __('tr_field_required'); ?>">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['fleet_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>" data-luggage="<?php echo !empty($v['luggage']) ? $v['luggage'] : null; ?>"><?php echo $v['fleet'];?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPassengers'); ?></label>
					<span class="inline_block">
						<input type="text" id="passengers" name="passengers" class="pj-form-field field-int w80 required pj-positive-number" data-value="0" readonly="readonly"/>
						<span id="tr_max_passengers"></span>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblLuggage'); ?></label>
					<span class="inline_block">
						<input type="text" id="luggage" name="luggage" class="pj-form-field field-int w80 required pj-positive-number" data-value="0" readonly="readonly"/>
						<span  id="tr_max_luggage"></span>
					</span>
				</p>
				<div id="extraBox">
					
				</div>
				<p>
					<label class="title"><?php __('lblSubTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="sub_total" name="sub_total" class="pj-form-field number w108" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTax'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="tax" name="tax" class="pj-form-field number w108" readonly="readonly" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="total" name="total" class="pj-form-field number w108" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDeposit'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" readonly="readonly" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPaymentMethod');?></label>
					<span class="inline_block">
						<select name="payment_method" id="payment_method" class="pj-form-field w150 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('payment_methods', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCType'); ?></label>
					<span class="inline_block">
						<select name="cc_type" class="pj-form-field w150">
							<option value="">---</option>
							<?php
							foreach (__('cc_types', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCNum'); ?></label>
					<span class="inline_block">
						<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" />
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCExp'); ?></label>
					<span class="inline_block">
						<select name="cc_exp_month" class="pj-form-field">
							<option value="">---</option>
							<?php
							$month_arr = __('months', true, false);
							ksort($month_arr);
							foreach ($month_arr as $key => $val)
							{
								?><option value="<?php echo $key;?>"><?php echo $val;?></option><?php
							}
							?>
						</select>
						<select name="cc_exp_year" class="pj-form-field">
							<option value="">---</option>
							<?php
							$y = (int) date('Y');
							for ($i = $y; $i <= $y + 10; $i++)
							{
								?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none">
					<label class="title"><?php __('lblCCCode'); ?></label>
					<span class="inline_block">
						<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" />
					</span>
				</p>
				<div class="p">
					<label class="title"><?php __('lblStatus'); ?></label>
					<span class="inline_block">
						<select name="status" id="status" class="pj-form-field w150 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('booking_statuses', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</div>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				</p>
				<p style="display: none;" class="pjCheckDatetimeMsg">
					<label class="title">&nbsp;</label>
					<span class="block overflow red"><?php __('front_check_time_desc');?></span>
				</p>
			</div>
			
			<div id="tabs-2">
				<?php
				if(!empty($tpl['client_arr']))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblClient'); ?></label>
						<span class="inline_block">
							<span class="block float_left r5">
								<select name="client_id" id="client_id" class="pj-form-field w500">
									<option value="">-- <?php __('lblNewClient'); ?>--</option>
									<?php
									foreach ($tpl['client_arr'] as $v)
									{
										$name_arr = array();
										if(!empty($v['fname']))
										{
											$name_arr[] = stripslashes($v['fname']);
										}
										if(!empty($v['lname']))
										{
											$name_arr[] = stripslashes($v['lname']);
										}
										$email_phone = array();
										if(!empty($v['email']))
										{
											$email_phone[] = stripslashes($v['email']);
										}
										if(!empty($v['phone']))
										{
											$email_phone[] = stripslashes($v['phone']);
										}
										?><option value="<?php echo $v['id']; ?>"><?php echo join(" ", $name_arr); ?> (<?php echo join(" | ", $email_phone); ?>)</option><?php
									}
									?>
								</select>
							</span>
							<a id="pjFdEditClient" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&id={ID}" class="pj-edit-client" style="display:none;"></a>
						</span>
					</p>
					<?php
				} 
				?>
				<div id="pjSbNewClientWrapper">
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{  
						?>
						<p>
							<label class="title"><?php __('lblBookingTitle'); ?></label>
							<span class="inline_block">
								<select name="c_title" id="c_title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' clientRequired required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									$title_arr = pjUtil::getTitles();
									$name_titles = __('personal_titles', true, false);
									foreach ($title_arr as $v)
									{
										?><option value="<?php echo $v; ?>"><?php echo $name_titles[$v]; ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingFname'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_fname" id="c_fname" class="pj-form-field w250<?php echo $tpl['option_arr']['o_bf_include_fname'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingLname'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_lname" id="c_lname" class="pj-form-field w250<?php echo $tpl['option_arr']['o_bf_include_lname'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingPhone'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_phone" id="c_phone" class="pj-form-field w250<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingEmail'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_email" id="c_email" class="pj-form-field w250<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{ 
						?>	
						<p>
							<label class="title"><?php __('lblBookingCompany'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_company" id="c_company" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingAddress'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_address" id="c_address" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingCity'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_city" id="c_city" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' clientRequired required' : NULL; ?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingState'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_state" id="c_state" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingZip'); ?></label>
							<span class="inline_block">
								<input type="text" name="c_zip" id="c_zip" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' clientRequired required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblBookingCountry'); ?></label>
							<span class="inline_block">
								<select name="c_country" id="c_country" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' clientRequired required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ($tpl['country_arr'] as $v)
									{
										?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					?>
				</div>
				<?php
				if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblBookingNotes'); ?></label>
						<span class="inline_block">
							<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>"></textarea>
						</span>
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingAirlineCompany'); ?></label>
						<span class="inline_block">
							<input type="text" name="c_airline_company" id="c_airline_company" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_airline_company'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblArrivalFlightNumber'); ?></label>
						<span class="inline_block">
							<input type="text" name="c_flight_number" id="c_flight_number" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_flight_number'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblFlightArrivalTime'); ?></label>
						<span class="inline_block">
							<input type="text" name="c_flight_time" id="c_flight_time" class="pj-form-field w300 timepick<?php echo $tpl['option_arr']['o_bf_include_flight_time'] == 3 ? ' required' : NULL; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingTerminal'); ?></label>
						<span class="inline_block">
							<input type="text" name="c_terminal" id="c_terminal" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_terminal'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				} 
				?>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
				<p style="display: none;" class="pjCheckDatetimeMsg">
					<label class="title">&nbsp;</label>
					<span class="block overflow red"><?php __('front_check_time_desc');?></span>
				</p>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.maximum = '<?php echo __('lblMaximum', true, false)?>';
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	myLabel.max_number = "<?php __('lblMaxNumber'); ?>";
	myLabel.email_already_exist = "<?php __('lblBookingsEmailExist'); ?>";
	myLabel.loader = '<img src="<?php echo PJ_IMG_PATH;?>backend/pj-preloader.gif" />';
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery);
		</script>
		<?php
	}
}
?>