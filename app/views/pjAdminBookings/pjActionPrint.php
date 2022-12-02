<div style="margin-bottom: 14px; margin-top: 10px; font-weight: bold; font-size: 16px;">
<?php
if(!isset($_GET['id']))
{ 
	if (isset($_GET['record']) && $_GET['record'] != '')
	{ 
		__('lblReservationPrintList');
	}else{
		__('lblTodayTransfers');
	}
}else{
	__('lblReservationPrint', false, false);
	if(!empty($tpl['transfer_arr']))
	{
		echo '<br/>' . __('lblID', true, false) . ': ' . $tpl['transfer_arr'][0]['uuid'];
	}
} 
?>
</div>
<?php
if(!isset($_GET['id']))
{ 
	include PJ_VIEWS_PATH . 'pjAdminBookings/elements/list.php'; 
}else{
	$pickup_arr = $tpl['transfer_arr'][0];
	include PJ_VIEWS_PATH . 'pjAdminBookings/elements/single.php'; 
} 
?>