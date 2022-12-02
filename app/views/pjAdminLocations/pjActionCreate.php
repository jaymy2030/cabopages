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
    ?>
	
	<?php pjUtil::printNotice(__('infoAddLocationTitle2', true, false), __('infoAddLocationDesc2', true, false)); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate" method="post" id="frmCreateLocation" class="form pj-form" autocomplete="off">
		<input type="hidden" name="location_create" value="1" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblLocationTitle'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('tr_field_required');?>"/>
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title"><?php __('lblAddress'); ?></label>
			<span class="inline_block">
				<input id="address" name="address" class="pj-form-field w400 required" data-msg-required="<?php __('tr_field_required');?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblLocationType'); ?></label>
			<span class="inline_block">
				<select name="type" class="pj-form-field w250 required" data-msg-required="<?php __('tr_field_required');?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php
					foreach(__('location_types', true) as $k => $v)
					{
					    ?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocations&action=pjActionIndex';" />
			</span>
		</p>
	</form>
	
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>