<?php
include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$FORM = @$_SESSION[$controller->defaultForm];
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
	<form id="pjTbsPreviewForm_<?php echo $_GET['index'];?>" action="#" method="post">
		<input type="hidden" name="tbs_preview" value="1" />
		<div class="pjTbs-box">
			<div class="pjTbs-box-title"><?php __('front_your_enquiry');?></div><!-- /.pjTbs-box-title -->
	
			<ul class="pjTbs-extras">
				<li>
					<div class="row">
						<div class="col-md-6 col-xs-12">
							<em><?php echo pjSanitize::clean($tpl['fleet_arr']['fleet']);?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-4 col-xs-8">
							<?php echo $SEARCH['booking_date']?>, <?php echo $SEARCH['booking_time']?><?php echo $roundtrip_info;?>
						</div><!-- /.col-md-4 -->
						<?php
						if ($booking_option == 'roundtrip') {
							$total_price = $tpl['fleet_arr']['start_fee'] + $SEARCH['passengers'] * $tpl['fleet_arr']['fee_per_person'] + $tpl['fleet_arr']['price_roundtrip'];
						} else {
							$total_price = $tpl['fleet_arr']['start_fee'] + $SEARCH['passengers'] * $tpl['fleet_arr']['fee_per_person'] + $tpl['fleet_arr']['price'];
						}
						?>
						<div class="col-md-2 col-xs-4 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($total_price, 2), $tpl['option_arr']['o_currency'])?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_extra_price');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['extra'], 2), $tpl['option_arr']['o_currency']);?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_subtotal');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['subtotal'], 2), $tpl['option_arr']['o_currency']);?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
				
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_tax');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']);?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
	
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_total');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $tpl['option_arr']['o_currency']);?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
	
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_deposit_required');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
			</ul>
		</div>
	
		<div class="pjTbs-box">
			<div class="pjTbs-box-title"><?php __('front_booking_details');?></div><!-- /.pjTbs-box-title -->
	
			<div class="pjTbs-personal-details">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_pickup_address');?></span>
	
							<strong><?php echo pjSanitize::html($from);?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_dropoff_address');?></span>
	
							<strong><?php echo pjSanitize::html($to);?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_passengers');?></span>
	
							<strong><?php echo $tpl['passengers'];?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<?php
					if((int) $SEARCH['luggage'] > 0) 
					?>
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_pieces_of_luggage');?></span>
	
							<strong><?php echo $SEARCH['luggage'];?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<?php
					if($tpl['option_arr']['o_payment_disable'] == 'No' && isset($FORM['payment_method']) && $FORM['payment_method'] != '')
					{
						?>
						<div class="<?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'bank' ? 'col-sm-6' : 'col-sm-12';?> col-xs-12">
							<p>
								<span><?php __('front_payment_medthod'); ?></span>
		
								<strong><?php $payment_methods = __('payment_methods', true, false); echo $payment_methods[$FORM['payment_method']];?></strong>
							</p>
						</div><!-- /.col-sm-6 -->
						
						<div style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_cc_type'); ?></span>
			
									<strong>
										<?php 
										$cc_types = __('cc_types', true, false);
										echo $cc_types[$FORM['cc_type']];
										?>
									</strong>
								</p>
							</div><!-- /.col-sm-6 -->
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_cc_num'); ?></span>
			
									<strong><?php echo isset($FORM['cc_num']) ? pjSanitize::clean($FORM['cc_num']) : null;?></strong>
								</p>
							</div><!-- /.col-sm-6 -->
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_cc_exp'); ?></span>
			
									<strong>
										<?php
										$month_arr = __('months', true, false);
										ksort($month_arr);
										echo $month_arr[(int) $FORM['cc_exp_month']] . '-' . $FORM['cc_exp_year'];
										?>
									</strong>
								</p>
							</div><!-- /.col-sm-6 -->
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_cc_code'); ?></span>
			
									<strong><?php echo isset($FORM['cc_code']) ? pjSanitize::clean($FORM['cc_code']) : null;?></strong>
								</p>
							</div><!-- /.col-sm-6 -->
						</div>
						<div style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'bank' ? 'block' : 'none'; ?>">
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_bank_account'); ?></span>
			
									<strong>
										<?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?>
									</strong>
								</p>
							</div><!-- /.col-sm-6 -->
						</div>
						<?php
					} 
					?>
				</div>
			</div>
	
			<br>
	
			<div class="pjTbs-box-title"><?php __('front_personal_details');?></div><!-- /.pjTbs-box-title -->
			
			<div class="pjTbs-personal-details">
				<div class="row">
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)) && isset($FORM['c_title']) && $FORM['c_title'] != '')
					{ 
						$title = NULL;
						$name_titles = __('personal_titles', true, false);
						if(isset($FORM['c_title']) && $FORM['c_title'] != '')
						{
							$title = $FORM['c_title'];
						}
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_title'); ?>:</span>
		
								<strong><?php echo $name_titles[$title];?></strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3)) && isset($FORM['c_fname']) && $FORM['c_fname'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_fname'); ?></span>
								<strong>
									<?php echo isset($FORM['c_fname']) ? pjSanitize::clean($FORM['c_fname']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3)) && isset($FORM['c_lname']) && $FORM['c_lname'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_lname'); ?></span>
								<strong>
									<?php echo isset($FORM['c_lname']) ? pjSanitize::clean($FORM['c_lname']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)) && isset($FORM['c_phone']) && $FORM['c_phone'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_phone'); ?></span>
								<strong>
									<?php echo isset($FORM['c_phone']) ? pjSanitize::clean($FORM['c_phone']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)) && isset($FORM['c_email']) && $FORM['c_email'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_email'); ?></span>
								<strong>
									<?php echo isset($FORM['c_email']) ? pjSanitize::clean($FORM['c_email']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)) && isset($FORM['c_company']) && $FORM['c_company'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_company'); ?></span>
								<strong>
									<?php echo isset($FORM['c_company']) ? pjSanitize::clean($FORM['c_company']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)) && isset($FORM['c_address']) && $FORM['c_address'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_address'); ?></span>
								<strong>
									<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)) && isset($FORM['c_city']) && $FORM['c_city'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_city'); ?></span>
								<strong>
									<?php echo isset($FORM['c_city']) ? pjSanitize::clean($FORM['c_city']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)) && isset($FORM['c_state']) && $FORM['c_state'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_state'); ?></span>
								<strong>
									<?php echo isset($FORM['c_state']) ? pjSanitize::clean($FORM['c_state']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)) && isset($FORM['c_country']) && $FORM['c_country'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_zip'); ?></span>
								<strong>
									<?php echo isset($FORM['c_zip']) ? pjSanitize::clean($FORM['c_zip']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)) && isset($FORM['c_country']) && $FORM['c_country'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_country'); ?></span>
								<strong>
									<?php echo !empty($tpl['country_arr']) ? $tpl['country_arr']['country_title'] : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					?>
				</div><!-- /.row -->
				<?php
				if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)) && isset($FORM['c_notes']) && $FORM['c_notes'] != ''){ 
					?>
					<p>
						<span><?php __('front_notes'); ?></span>
		
						<strong><?php echo isset($FORM['c_notes']) ? nl2br(pjSanitize::clean($FORM['c_notes'])) : null;?></strong>
					</p>
					<?php
				}
				if(in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_termial'], array(2, 3))
				){
					?>
					<div class="pjTbs-box-title"><?php __('front_flight_details');?></div><!-- /.pjTbs-box-title -->
			
					<div class="pjTbs-personal-details">
						<div class="row">
							<?php
							if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) && isset($FORM['c_airline_company']) && $FORM['c_airline_company'] != ''){
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_airline_company'); ?></span>
										<strong>
											<?php echo isset($FORM['c_airline_company']) ? pjSanitize::clean($FORM['c_airline_company']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) && isset($FORM['c_flight_number']) && $FORM['c_flight_number'] != ''){ 
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_flight_number'); ?></span>
										<strong>
											<?php echo isset($FORM['c_flight_number']) ? pjSanitize::clean($FORM['c_flight_number']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) && isset($FORM['c_flight_time']) && $FORM['c_flight_time'] != ''){ 
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_flight_time'); ?></span>
										<strong>
											<?php echo isset($FORM['c_flight_time']) ? pjSanitize::clean($FORM['c_flight_time']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)) && isset($FORM['c_terminal']) && $FORM['c_terminal'] != ''){
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_terminal'); ?></span>
										<strong>
											<?php echo isset($FORM['c_terminal']) ? pjSanitize::clean($FORM['c_terminal']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							} 
							?>
						</div>
					</div>
					<?php
				} 
				?>
				
			</div><!-- /.pjTbs-personal-details -->
		</div>
	
		<div class="pjTbs-body-actions">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadCheckout"><?php __('front_btn_back');?></a>
				</div><!-- /.col-sm-3 -->
	
				<div class="col-sm-3 col-sm-offset-6 col-xs-12">
					<input type="submit" value="<?php __('front_btn_confirm');?>" class="btn btn-primary btn-block" >
				</div><!-- /.col-sm-3 -->
			</div><!-- /.row -->
		</div><!-- /.pjTbs-body-actions -->
	</form>
</div><!-- /.pjTbs-body -->