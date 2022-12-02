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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	pjUtil::printNotice(__('infoPreviewInstallTitle', true), __('infoPreviewInstallDesc', true), false, false)
	?>
	<div class="pj-loader-outer">
		<fieldset class="fieldset white">
			<legend><?php __('lblChooseTheme'); ?></legend>
			<div class="theme-holder ">
				<?php include PJ_VIEWS_PATH . 'pjAdminOptions/elements/theme.php'; ?>
			</div>
		</fieldset>
		<fieldset class="fieldset white">
			<legend><?php __('lblInstallCode'); ?></legend>
			<form action="" method="get" class="pj-form form">
				<p>
					<textarea class="pj-form-field textarea_install" id="install_code" style="overflow: auto; height:250px; width: 695px;">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad&lt;?php echo isset($_GET['tbs_search']) ? '&tbs_search=1' : '';?&gt;&booking_type=&lt;?php echo isset($_GET['booking_type']) ? $_GET['booking_type'] : '';?&gt;&booking_option=&lt;?php echo isset($_GET['booking_option']) ? $_GET['booking_option'] : 'oneway';?&gt;&from_location_id=&lt;?php echo isset($_GET['from_location_id']) ? $_GET['from_location_id'] : '';?&gt;&to_location_id=&lt;?php echo isset($_GET['to_location_id']) ? $_GET['to_location_id'] : '';?&gt;&passengers=&lt;?php echo isset($_GET['passengers']) ? $_GET['passengers'] : '';?&gt;&luggage=&lt;?php echo isset($_GET['luggage']) ? $_GET['luggage'] : '';?&gt;&booking_date=&lt;?php echo isset($_GET['booking_date']) ? $_GET['booking_date'] : '';?&gt;&booking_time=&lt;?php echo isset($_GET['booking_time']) ? $_GET['booking_time'] : '';?&gt;&return_date=&lt;?php echo isset($_GET['return_date']) ? $_GET['return_date'] : '';?&gt;&return_time=&lt;?php echo isset($_GET['return_time']) ? $_GET['return_time'] : '';?&gt;&distance=&lt;?php echo isset($_GET['distance']) ? $_GET['distance'] : '';?&gt;"&gt;&lt;/script&gt;</textarea>
				</p>
			</form>
		</fieldset>
		
		<fieldset class="fieldset white">
			<legend><?php __('lblInstallCodeSearchForm'); ?></legend>
			<form action="" method="get" class="pj-form form">
				<p>
					<textarea class="pj-form-field textarea_install" id="install_code_search_form" style="overflow: auto; height:100px; width: 695px;">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad&search_form"&gt;&lt;/script&gt;</textarea>
				</p>
			</form>
		</fieldset>
	</div>
	<?php
}
?>