<?php
if (pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminLocations' ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuLocations2'); ?></a></li>
		<?php
		if ($controller->isAdmin())
		{
			?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' ? 'menu-focus' : NULL; ?>"><span class="menu-enquiries">&nbsp;</span><?php __('menuReservations'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminFleets' ? 'menu-focus' : NULL; ?>"><span class="menu-fleets">&nbsp;</span><?php __('menuFleets'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminClients' ? 'menu-focus' : NULL; ?>"><span class="menu-clients">&nbsp;</span><?php __('menuClients'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminExtras' ? 'menu-focus' : NULL; ?>"><span class="menu-extras">&nbsp;</span><?php __('menuExtras'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm'))) || in_array($_GET['controller'], array('pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms', 'pjAdminReasons')) ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
			<!--<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionPreview' ? 'menu-focus' : NULL; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstallPreview'); ?></a></li>-->
			<?php
		}
		if($controller->isEditor())
		{
			?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
			<?php
		}
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>