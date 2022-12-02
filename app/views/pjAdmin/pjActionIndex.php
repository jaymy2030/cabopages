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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat enquiries">
				<div class="info">
					<abbr><?php echo $tpl['enquiries_received_today'];?></abbr>
					<label><?php $tpl['enquiries_received_today'] != 1 ? __('dash_enquiries_received_today') : __('dash_enquiry_received_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat enquiries">
				<div class="info">
					<abbr><?php echo $tpl['reservations_today'];?></abbr>
					<label><?php $tpl['reservations_today'] != 1 ? __('dash_reservations_today') : __('dash_reservation_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat enquiries">
				<div class="info">
					<abbr><?php echo $tpl['total_reservations'];?></abbr>
					<label><?php $tpl['total_reservations'] != 1 ? __('dash_total_reservations') : __('dash_reservation');?></label>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dash_latest_enquiries')?></div>
			<div class="dashboard_column_top"><?php __('dash_title_reservations_today')?></div>
			<div class="dashboard_column_top"><?php __('dash_quick_links')?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_enquiries']) > 0)
					{
						foreach($tpl['latest_enquiries'] as $v)
						{
							$client_name_arr = array();
							if(!empty($v['fname']))
							{
								$client_name_arr[] = pjSanitize::html($v['fname']);
							}
							if(!empty($v['lname']))
							{
								$client_name_arr[] = pjSanitize::html($v['lname']);
							}
							?>
							<div class="dashboard_row">							
								<label class="bold"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo join(' ', $client_name_arr)?></a></label>
								<label><?php echo pjSanitize::html($v['fleet'])?></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></label>
								
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('dash_no_enquiries');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['reservations_today_arr']) > 0)
					{
						foreach($tpl['reservations_today_arr'] as $v)
						{
							$client_name_arr = array();
							if(!empty($v['fname']))
							{
								$client_name_arr[] = pjSanitize::html($v['fname']);
							}
							if(!empty($v['lname']))
							{
								$client_name_arr[] = pjSanitize::html($v['lname']);
							}
							?>
							<div class="dashboard_row">							
								<label class="bold"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo join(' ', $client_name_arr)?></a></label>
								<label><?php echo pjSanitize::html($v['fleet'])?></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></label>
								
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('dash_no_enquiries');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<div class="dashboard_row">
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('dash_view_enquiries');?></a>
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&amp;action=pjActionIndex&amp;date=<?php echo date("Y-m-d");?>"><?php __('dash_link_reservations_today');?></a>
						<a class="block no-decor fs14 b20" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_enquiry');?></a>
						
						<a class="block no-decor fs14 b10" href="preview.php?theme=<?php echo $tpl['option_arr']['o_theme']?>"><?php __('dash_open_frontend');?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>