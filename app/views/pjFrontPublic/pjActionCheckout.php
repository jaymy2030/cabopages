<?php
include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$FORM = @$_SESSION[$controller->defaultForm];
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;

$from = pjAppController::getLocation($SEARCH['from_location_id'], $controller->getLocaleId());
$to = pjAppController::getLocation($SEARCH['to_location_id'], $controller->getLocaleId());
if($SEARCH['booking_type'] == 'to')
{
    $to = pjAppController::getLocation($SEARCH['from_location_id'], $controller->getLocaleId());
    $from = pjAppController::getLocation($SEARCH['to_location_id'], $controller->getLocaleId());
}
$booking_option = isset($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
$booking_options = __('booking_options', true);
if ($booking_option == 'roundtrip') {
	$roundtrip_info = '<br/>'.__('front_return_on', true).' '.$SEARCH['return_date'].', '.date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['return_time']));
} else {
	$roundtrip_info = '';
}
?>
<div class="pjTbs-body">
	<form id="pjTbsCheckoutForm_<?php echo $_GET['index'];?>" action="#" method="post" class="pjTbsCheckoutForm">
		<input type="hidden" name="lbs_checkout" value="1" />
		<div id="pjTbsCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
		
		<div class="pjTbs-service-list">
			<div class="pjTbs-service-list-row">
				<div class="row">
					<div class="col-sm-5 col-xs-12">
						<p><?php __('front_pickup_address');?>:</p>

						<p><strong><?php echo $from;?> </strong></p>

						<p><small><?php echo $SEARCH['booking_date'];?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['booking_time']));?></small><small><?php echo $roundtrip_info;?></small></p>
					</div><!-- /.col-sm-5 -->

					<div class="col-sm-4 col-xs-12">
						<p><?php __('front_dropoff_address');?>:</p>
						
						<p><strong><?php echo $to;?></strong></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-3 col-xs-12">
						<p><?php __('front_distance');?>:</p>
						
						<p><strong><?php echo $SEARCH['distance'];?> km</strong></p>
					</div><!-- /.col-sm-3 -->
				</div><!-- /.row -->
			</div><!-- /.pjTbs-service-list-row -->
			<?php
			$with_str = (int) $SEARCH['luggage'] >= 1 ? __('front_with_desc', true) : __('front_with_desc_2', true);
			$with_str = str_replace("{PASSENGERS}", $SEARCH['passengers'], $with_str);
			$with_str = str_replace("{LUGGAGE}", $SEARCH['luggage'], $with_str);
			if ($booking_option == 'roundtrip') {
				$total_price = $tpl['fleet_arr']['start_fee'] + $SEARCH['passengers'] * $tpl['fleet_arr']['fee_per_person'] + $tpl['fleet_arr']['price_roundtrip'];
			} else {
				$total_price = $tpl['fleet_arr']['start_fee'] + $SEARCH['passengers'] * $tpl['fleet_arr']['fee_per_person'] + $tpl['fleet_arr']['price'];
			}
			?>
			<div class="pjTbs-service-list-row">
				<div class="row">
					<div class="col-sm-5 col-xs-12">
						<p><?php __('front_with');?>:</p>
						
						<p><em><?php echo $with_str;?> </em></p>
					</div><!-- /.col-sm-5 -->

					<div class="col-sm-4 col-xs-12">
						<p><?php __('front_ride');?>:</p>
						
						<p><em><?php echo pjSanitize::clean($tpl['fleet_arr']['fleet']);?></em></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-3 text-right">
						<p><?php __('front_price');?>:</p>
						
						<p><strong><?php echo pjUtil::formatCurrencySign(number_format($total_price, 2), $tpl['option_arr']['o_currency']);?></strong></p>
					</div><!-- /.col-sm-3 -->
				</div><!-- /.row -->
			</div><!-- /.pjTbs-service-list-row -->
		</div><!-- /.pjTbs-service-list -->
		
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="pjTbs-box">
					<div class="pjTbs-box-title"><?php __('front_personal_details');?></div><!-- /.pjTbs-box-title -->
					<?php
					if(!$controller->isFrontLogged())
					{
						$login_message = __('front_login_message', true);
						$login_message = str_replace("{STAG}", '<a href="#" class="pjCssLogin">', $login_message);
						$login_message = str_replace("{ETAG}", '</a>', $login_message);
						?>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group"><label><?php echo $login_message;?></label></div>
							</div>
						</div>
						<?php
					}else{
						$logout_message = __('front_logout_message', true);
						$logout_message = str_replace("{STAG}", '<a href="#" class="pjCssLogout">', $logout_message);
						$logout_message = str_replace("{ETAG}", '</a>', $logout_message);
						?>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group"><label><?php echo $logout_message;?></label></div>
							</div>
						</div>
						<?php
					}
					$CLIENT = $controller->isFrontLogged() ? $_SESSION[$controller->defaultFrontClient] : array();
					
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_title'); ?></label>
	
							<select name="c_title" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_title'] == 3) ? ' required' : NULL; ?>" data-msg-required="<?php __('front_required_field');?>">
								<option value="">----</option>
								<?php
								$title_arr = pjUtil::getTitles();
								$name_titles = __('personal_titles', true, false);
								foreach ($title_arr as $v)
								{
									?><option value="<?php echo $v; ?>"<?php echo isset($FORM['c_title']) && $FORM['c_title'] == $v ? ' selected="selected"' : (isset($CLIENT['title']) ? ($CLIENT['title'] == $v ? ' selected="selected"' : NULL ) : NULL); ?>><?php echo $name_titles[$v]; ?></option><?php
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_fname'); ?></label>
							
							<input type="text" name="c_fname" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_fname'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_fname']) ? pjSanitize::clean($FORM['c_fname']) : (isset($CLIENT['fname']) ? pjSanitize::clean($CLIENT['fname']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_lname'); ?></label>
							
							<input type="text" name="c_lname" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_lname'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_lname']) ? pjSanitize::clean($FORM['c_lname']) : (isset($CLIENT['lname']) ? pjSanitize::clean($CLIENT['lname']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_phone'); ?></label>
							
							<input type="text" name="c_phone" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_phone'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_phone']) ? pjSanitize::clean($FORM['c_phone']) : (isset($CLIENT['phone']) ? pjSanitize::clean($CLIENT['phone']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_email'); ?></label>
							
							<input type="text" name="c_email" class="form-control email<?php echo ($tpl['option_arr']['o_bf_include_email'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_email']) ? pjSanitize::clean($FORM['c_email']) : (isset($CLIENT['email']) ? pjSanitize::clean($CLIENT['email']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>" data-msg-email="<?php __('front_email_validation');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_company'); ?></label>
							
							<input type="text" name="c_company" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_company'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_company']) ? pjSanitize::clean($FORM['c_company']) : (isset($CLIENT['company']) ? pjSanitize::clean($CLIENT['company']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_address'); ?></label>
							
							<input type="text" name="c_address" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_address'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : (isset($CLIENT['address']) ? pjSanitize::clean($CLIENT['address']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_city'); ?></label>
							
							<input type="text" name="c_city" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_city'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_city']) ? pjSanitize::clean($FORM['c_city']) : (isset($CLIENT['city']) ? pjSanitize::clean($CLIENT['city']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_state'); ?></label>
							
							<input type="text" name="c_state" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_state'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_state']) ? pjSanitize::clean($FORM['c_state']) : (isset($CLIENT['state']) ? pjSanitize::clean($CLIENT['state']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_zip'); ?></label>
							
							<input type="text" name="c_zip" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_zip'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_zip']) ? pjSanitize::clean($FORM['c_zip']) : (isset($CLIENT['zip']) ? pjSanitize::clean($CLIENT['zip']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_country'); ?></label>
							
							<select name="c_country" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_country'] == 3) ? ' required' : NULL; ?>" data-msg-required="<?php __('front_required_field');?>">
								<option value="">----</option>
								<?php
								foreach ($tpl['country_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : NULL) : (isset($CLIENT['country_id']) ? ($CLIENT['country_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL) ; ?>><?php echo $v['country_title']; ?></option><?php
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label class="control-label"><?php __('front_notes');?></label>
	
							<textarea name="c_notes" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_notes'] == 3) ? ' required' : NULL; ?>" cols="30" rows="10" data-msg-required="<?php __('front_required_field');?>"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if($tpl['option_arr']['o_payment_disable'] == 'No')
					{
						?>
						<div class="form-group">
							<label><?php __('front_payment_medthod'); ?></label>
							
							<select id="trPaymentMethod_<?php echo $_GET['index'];?>" name="payment_method" class="form-control required" data-msg-required="<?php __('front_required_field');?>">
								<option value="">----</option>
								<?php
								foreach (__('payment_methods', true, false) as $k => $v)
								{
									if($tpl['option_arr']['o_allow_' . $k] == 'Yes')
									{
										?><option value="<?php echo $k; ?>"<?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
									}
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<div class="form-group pjTbsBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
							<label><?php __('front_bank_account')?></label>
							
							<div class="text-muted"><strong><?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?></strong></div>
						</div>
						<div class="pjTbsCcWrap" style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<div class="form-group">
								<label><?php __('front_cc_type')?></label>
								
								<select name="cc_type" class="form-control required" data-msg-required="<?php __('front_required_field'); ?>">
						    		<option value="">---</option>
						    		<?php
									foreach (__('cc_types', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>"<?php echo @$FORM['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
									}
									?>
						    	</select>
						    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
							<div class="form-group">
								<label><?php __('front_cc_num')?></label>
								
								<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_num']); ?>"  autocomplete="off" data-msg-required="<?php __('front_required_field'); ?>"/>
						    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
							<div class="form-group">
								<label><?php __('front_cc_code')?></label>
								
								<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_code']); ?>"  autocomplete="off" data-msg-required="<?php __('front_required_field'); ?>"/>
						    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
							<div class="form-group">
								<label><?php __('front_cc_exp')?></label>
								<div class="row">
									<div class="col-sm-7">
										<?php
										$rand = rand(1, 99999);
										$time = pjTime::factory()
											->attr('name', 'cc_exp_month')
											->attr('id', 'cc_exp_month_' . $rand)
											->attr('class', 'form-control required')
											->prop('format', 'F');
										if (isset($FORM['cc_exp_month']) && !is_null($FORM['cc_exp_month']))
										{
											$time->prop('selected', $FORM['cc_exp_month']);
										}
										echo $time->month();
										?>
									</div>
									<div class="col-sm-5">
										<?php
										$time = pjTime::factory()
											->attr('name', 'cc_exp_year')
											->attr('id', 'cc_exp_year_' . $rand)
											->attr('class', 'form-control required')
											->prop('left', 0)
											->prop('right', 10);
										if (isset($FORM['cc_exp_year']) && !is_null($FORM['cc_exp_year']))
										{
											$time->prop('selected', $FORM['cc_exp_year']);
										}
										echo $time->year();
										?>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_captcha'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_captcha'); ?></label>
	
							<div class="row">
								<div class="col-sm-6 col-xs-12">
									<div class="form-group">
										<input type="text" name="captcha" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_captcha'] == 3) ? ' required' : NULL; ?>" autocomplete="off" data-msg-required="<?php __('front_required_field'); ?>" data-msg-remote="<?php __('front_incorrect_captcha');?>"/>
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
	
								<div class="col-sm-4 col-xs-12">
									<img id="pjTbsImage_<?php echo $_GET['index']?>" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . $_GET['session_id'] : NULL;?>" alt="Captcha" style="vertical-align: middle; cursor: pointer;" />
								</div><!-- /.col-sm-6 -->
							</div><!-- /.row -->
						</div><!-- /.form-group -->
						<?php
					} 
					?>

					<div class="form-group">
						<div class="checkbox">
							<label><input type="checkbox" name="terms" class="required" data-msg-required="<?php __('front_required_field'); ?>"/>  <?php __('front_agree');?> <a href="#" class="pjTbModalTrigger" data-toggle="modal" data-target="#pjNcbTermModal" data-title="<?php __('front_terms_title');?>"><?php __('front_terms_conditions');?></a></label>
						</div>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div><!-- /.pjTbs-car -->							
			</div><!-- /.col-sm-6 -->

			<div class="col-sm-6 col-xs-12">
				<?php
				if(in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) || 
				   in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) ||
				   in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) ||
				   in_array($tpl['option_arr']['o_bf_include_termial'], array(2, 3))
				  ){
					?>
					<div class="pjTbs-box">
						<div class="pjTbs-box-title"><?php __('front_flight_details');?></div><!-- /.pjTbs-box-title -->
						<div class="form-group">
							<span><?php __('front_flight_details_desc');?></span>
						</div>
						<?php
						if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)))
						{
							?>
							<div class="form-group">
								<label><?php __('front_airline'); ?></label>
								
								<input type="text" name="c_airline_company" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_airline_company'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_airline_company']) ? pjSanitize::clean($FORM['c_airline_company']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
							<?php
						}
						if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)))
						{
							?>
							<div class="form-group">
								<label><?php __('front_flight_number'); ?></label>
								
								<input type="text" name="c_flight_number" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_flight_number'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_flight_number']) ? pjSanitize::clean($FORM['c_flight_number']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
							<?php
						}
						
						if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) || in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))
						{ 
							?>
							<div class="row">
								<?php
								if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)))
								{ 
									?>
									<div class="col-md-6 col-sm-7 col-xs-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_flight_time');?></label>
											<div class="input-group time-pick">
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
												</span>
			
												<input type="text" name="c_flight_time" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_flight_time'] == 3) ? ' required' : NULL; ?>" autocomplete="off" value="<?php echo isset($FORM['c_flight_time']) ? pjSanitize::clean($FORM['c_flight_time']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
											</div>
											<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<?php
								}
								if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))
								{ 
									?>
			
									<div class="col-md-6 col-sm-5 col-xs-12">
										<div class="form-group">
											<label><?php __('front_terminal'); ?></label>
											
											<input type="text" name="c_terminal" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_terminal'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_terminal']) ? pjSanitize::clean($FORM['c_terminal']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
											<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<?php
								} 
								?>
							</div><!-- /.row -->
							<?php
						}
					?>
					</div>
					<?php
				}
				?>
				<div id="pjTbsPriceBox" >
					<?php
					include_once dirname(__FILE__) . '/pjActionGetPrices.php';
					?>
				</div>
								
			</div><!-- /.col-sm-6 -->
		</div><!-- /.row -->
		<div class="pjTbs-body-actions">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadFleets"><?php __('front_btn_back');?></a>
				</div><!-- /.col-sm-3 -->

				<div class="col-sm-3 col-sm-offset-6 col-xs-12">
					<input type="submit" value="<?php __('front_btn_preview');?>" class="btn btn-primary btn-block" >
				</div><!-- /.col-sm-3 -->
			</div><!-- /.row -->
		</div><!-- /.pjTbs-body-actions -->
	</form>
</div><!-- /.pjTbs-body -->