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
	$statuses = __('booking_statuses', true, false);
	$booking_options_yesno = __('booking_options_yesno', true, false);
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	pjUtil::printNotice(__('infoReservationListTitle', true, false), __('infoReservationListDesc', true, false)); 
	?>
	
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddEnquiry'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch', false, true); ?>" />
		</form>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $statuses['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $statuses['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $statuses['cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['fleet_id']) && (int) $_GET['fleet_id'] > 0)
	{
		?>pjGrid.queryString += "&fleet_id=<?php echo (int) $_GET['fleet_id']; ?>";<?php
	}
	if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
	{
		?>pjGrid.queryString += "&client_id=<?php echo (int) $_GET['client_id']; ?>";<?php
	}
	if (isset($_GET['date']))
	{
		?>pjGrid.queryString += "&date=<?php echo $_GET['date']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.client = "<?php __('lblClient', false, true); ?>";
	myLabel.fleet = "<?php __('lblFleet', false, true); ?>";
	myLabel.distance = "<?php __('lblDistance', false, true); ?>";
	myLabel.is_roundtrip = "<?php __('lblIsRoundTrip', false, true); ?>";
	myLabel.date_time = "<?php __('lblDateTime', false, false); ?>";
	myLabel.email = "<?php __('email', false, true); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.print = "<?php __('lblPrint', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.pending = "<?php echo $statuses['pending']; ?>";
	myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	myLabel.yes = "<?php echo $booking_options_yesno['roundtrip']; ?>";
	myLabel.no = "<?php echo $booking_options_yesno['oneway']; ?>";
	</script>
	<?php
}
?>