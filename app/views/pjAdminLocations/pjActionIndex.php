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
	$filter = __('filter', true, false);
	?>
	
	<?php pjUtil::printNotice(__('infoLocationsTitle2', true, false), __('infoLocationsDesc2', true, false)); ?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminLocations" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddLocation'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<br class="clear_both" />
	</div>
	
	<div id="grid"></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.title = "<?php __('lblLocationTitle'); ?>";
	myLabel.type = "<?php __('lblLocationType'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>