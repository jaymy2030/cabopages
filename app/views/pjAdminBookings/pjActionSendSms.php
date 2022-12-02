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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSendSms&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblSendSMSNotification'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoSendSmsTitle', true, false), __('infoSendSmsDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSendSms&amp;id=<?php echo $tpl['arr']['id']; ?>" method="post" class="form pj-form" id="frmBookingSendSms">
		<input type="hidden" name="send_sms" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<p>
			<label class="title"><?php __('lblPhone', false, true); ?></label>
			<span class="inline-block">
				<input type="text" name="to" id="to" class="pj-form-field w300 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_phone'])); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblSmsMessage', false, true); ?></label>
			<span class="inline-block">
				<textarea name="message" id="message" class="pj-form-field w550 h300"><?php echo str_replace($tpl['arr']['data']['search'], $tpl['arr']['data']['replace'], $tpl['lang_message']); ?></textarea>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSend', false, true); ?>" class="pj-button" />&nbsp;
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>" class="pj-back"><?php __('btnBack', false, true);?></a>
		</p>
	</form>
	<?php
	
}
?>