<?php
include_once dirname(__FILE__) . '/elements/header_search_form.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;

$booking_type = 'from';
if(isset($SEARCH['booking_type']))
{
    $booking_type = $SEARCH['booking_type'];
}
$booking_option = isset($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
$o_enquiry_url = $tpl['option_arr']['o_enquiry_url'];
if (strpos($o_enquiry_url, '?') !== false) {
	$o_enquiry_url .= '&tbs_search=1';
} else {
	$o_enquiry_url .= '?tbs_search=1';
}
?>
<div class="pjTbs-body">
	<form id="pjTbsSearchForm_<?php echo $_GET['index'];?>" action="<?php echo $o_enquiry_url;?>" method="get" class="pjTbsSearchForm">
		<input type="hidden" name="tbs_search" value="1" />
		<div id="pjTbsCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
		<div class="pjTbs-box">
			<div class="row">
				<div class="col-sm-6 col-xs-12">
					<div>
						<label class="radio-inline">
	      					<input type="radio" name="booking_type" value="from"<?php echo $booking_type == 'from' ? ' checked="checked"' : NULL;?>/><?php __('front_travelling_from')?>
	    				</label>
	    				<label class="radio-inline">
	      					<input type="radio" name="booking_type" value="to"<?php echo $booking_type == 'to' ? ' checked="checked"' : NULL;?>/><?php __('front_travelling_to')?>
	    				</label>
					</div>
					
					<div>
						<label class="radio-inline">
	      					<input type="radio" name="booking_option" value="oneway"<?php echo $booking_option == 'oneway' ? ' checked="checked"' : NULL;?>/><?php __('booking_options_ARRAY_oneway')?>
	    				</label>
	    				<label class="radio-inline">
	      					<input type="radio" name="booking_option" value="roundtrip"<?php echo $booking_option == 'roundtrip' ? ' checked="checked"' : NULL;?>/><?php __('booking_options_ARRAY_roundtrip')?>
	    				</label>
					</div>
				</div>
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
						<select name="from_location_id" id="from_location_id_<?php echo $_GET['index'];?>" class="form-control required" data-msg-required="<?php __('tr_field_required'); ?>">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['from_location_arr'] as $k => $v)
							{
							    ?><option value="<?php echo $v['id'];?>"<?php echo $SEARCH['from_location_id'] == $v['id'] ? ' selected="selected"' : NULL;?> data-address="<?php echo pjSanitize::html($v['address']);?>"><?php echo pjSanitize::html($v['name']);?></option><?php
							} 
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label id="pjTbDropoffTitle" class="control-label"style="display:<?php echo $booking_type == 'to' ? 'none' : NULL;?>;"><?php __('lblAvailableDropoffLocation'); ?></label>
						<label id="pjTbPickupTitle" class="control-label" style="display:<?php echo $booking_type == 'from' ? 'none' : NULL;?>;"><?php __('lblAvailablePickupLocation'); ?></label>
						<div id="pjTbToLocationContainer_<?php echo $_GET['index'];?>">
    						<select name="to_location_id" id="to_location_id_<?php echo $_GET['index'];?>" class="form-control required" data-msg-required="<?php __('tr_field_required'); ?>">
                            	<option value="">-- <?php __('lblChoose'); ?>--</option>
                            	<?php
                            	if(isset($tpl['to_location_arr']))
                            	{
                                	foreach($tpl['to_location_arr'] as $k => $v)
                                	{
                                	    ?><option value="<?php echo $v['id'];?>"<?php echo $SEARCH['to_location_id'] == $v['id'] ? ' selected="selected"' : NULL;?> data-address="<?php echo pjSanitize::html($v['address']);?>"><?php echo pjSanitize::html($v['name']);?></option><?php
                                	}
                            	}
                            	?>
                            </select>
    						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div><!-- /.form-group -->
				</div>

				<div class="col-md-6 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-sm-6 col-xs-12">
        					<label><?php __('front_passengers');?></label>
        					
        					<div class="form-group">
        						<div class="input-group">
        							<span class="input-group-addon">
        								<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        							</span>
        					
        							<div class="btn-group pjTbs-spinner" role="group" aria-label="...">
        					            <button type="button" class="btn pjTbs-spinner pjTbs-spinner-down">-</button>
        					
        								<input type="text" name="passengers" class="pjTbs-spinner-result digits" maxlength="3" value="<?php echo isset($SEARCH['passengers']) ? $SEARCH['passengers'] : 1;?>" data-msg-digits="<?php __('front_digits_validation');?>">
        					
        								<button type="button" class="btn pjTbs-spinner pjTbs-spinner-up">+</button>
        							</div>
        						</div>
        						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
        					</div><!-- /.form-group --><!-- /.col-sm-6 -->
        				</div>
    					<div class="col-sm-6 col-xs-12">
        					<label><?php __('front_pieces_of_luggage');?></label>
        					
        					<div class="form-group">
        						<div class="input-group">
        							<span class="input-group-addon">
        								<span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>
        							</span>
        					
        							<div class="btn-group pjTbs-spinner" role="group" aria-label="...">
        					            <button type="button" class="btn pjTbs-spinner pjTbs-spinner-down">-</button>
        					
        								<input type="text" name="luggage" class="pjTbs-spinner-result digits" maxlength="3" value="<?php echo isset($SEARCH['luggage']) ? $SEARCH['luggage'] : NULL;?>" data-msg-digits="<?php __('front_digits_validation');?>">
        					
        								<button type="button" class="btn pjTbs-spinner pjTbs-spinner-up">+</button>
        							</div>
        						</div>
        						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
        					</div><!-- /.form-group --><!-- /.col-sm-6 --><!-- /.row -->
        				</div>
					</div>
				</div><!-- /.col-sm-6 -->
			</div>
			<div class="row row-flex">
				<div class="col-sm-6 col-xs-12">
					<label class="control-label"><?php __('front_date_time');?></label>
	
					<div class="row">
						<div class="col-md-6 col-sm-7 col-xs-12">
							<div class="form-group">
								<div class="input-group date-pick">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									</span>
			
									<input type="text" name="booking_date" value="<?php echo isset($SEARCH['booking_date']) ? $SEARCH['booking_date'] : NULL;?>" class="form-control pjTbsDateFrom required" readonly="readonly" data-msg-required="<?php __('front_required_field');?>"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
	
						<div class="col-md-6 col-sm-5 col-xs-12">
							<div class="form-group">
								<div class="input-group time-pick">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
									</span>
	
									<input type="text" name="booking_time" value="<?php echo isset($SEARCH['booking_time']) ? $SEARCH['booking_time'] : NULL;?>" class="form-control required" readonly data-msg-required="<?php __('front_required_field');?>"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
					</div><!-- /.row -->
				</div><!-- /.col-sm-6 -->
				
				<div class="col-sm-6 col-xs-12 pjTbsReturnDatetime" style="display: <?php echo $booking_option == 'roundtrip' ? '' : 'none';?>">
					<label class="control-label"><?php __('front_return_datetime');?></label>
	
					<div class="row">
						<div class="col-md-6 col-sm-7 col-xs-12">
							<div class="form-group">
								<div class="input-group date-pick">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									</span>
			
									<input type="text" name="return_date" value="<?php echo isset($SEARCH['return_date']) ? $SEARCH['return_date'] : NULL;?>" class="form-control pjTbsDateTo required" readonly="readonly" data-msg-required="<?php __('front_required_field');?>"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
	
						<div class="col-md-6 col-sm-5 col-xs-12">
							<div class="form-group">
								<div class="input-group time-pick">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
									</span>
	
									<input type="text" name="return_time" value="<?php echo isset($SEARCH['return_time']) ? $SEARCH['return_time'] : NULL;?>" class="form-control required" readonly data-msg-required="<?php __('front_required_field');?>"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
					</div><!-- /.row -->
				</div><!-- /.col-sm-6 -->

				<div class="col-sm-6 col-xs-12">
					<label class="control-label"><?php __('front_distance');?>:</label>

					<div class="pjTbs-distance">
						<input type="text" id="pjTbsDistanceFiled" name="distance" value="<?php echo isset($SEARCH['distance']) ? $SEARCH['distance'] : NULL;?>" class="number" data-msg-required="<?php __('front_required_field');?>" data-msg-number="<?php __('front_number_validation');?>" readonly="readonly"/> km</div>
					<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
				</div>
			</div><!-- /.row -->
	<!---code added 10/06/2021----->
			<!---<div class="pjTbs-body-actions">		
				<input value="<?php __('front_btn_book_a_taxi');?>" class="btn btn-primary" type="submit">
			</div>------>

			<div class="pjTbs-body-actions">
			<form action="https://caboanchor.com/anil/index.html">		
				<input value="<?php __('front_btn_book_a_taxi');?>" class="btn btn-primary" type="submit">
			 </form>

			</div>

	
			<div class="pjTbs-map" id="pjTbsMapCanvas"></div>
		</div><!-- /.pjTbs-box -->
	</form>
</div><!-- /.pjTbs-body -->