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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
			
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']) - 1;
			if ($count > 0)
			{
				?>
				<?php
				$locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
				if (is_null($locale))
				{
					foreach ($tpl['lp_arr'] as $v)
					{
						if ($v['is_default'] == 1)
						{
							$locale = $v['id'];
							break;
						}
					}
				}
				if (is_null($locale))
				{
					$locale = @$tpl['lp_arr'][0]['id'];
				}
				?>
		
				<div class="clear_both">
					<form id="frmNotification" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
						<input type="hidden" name="options_update" value="1" />
						<input type="hidden" name="next_action" value="pjActionConfirmation" />
						<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
						
						<div id="tabs">
							<ul>
								<li><a href="#tabs-1"><?php __('lblOptionClient');?></a></li>
								<li><a href="#tabs-2"><?php __('lblOptionAdministrator');?></a></li>
							</ul>
							<div id="tabs-1">
								<?php
								pjUtil::printNotice(__('infoConfirmationsTitle', true), __('infoConfirmationDesc', true), false); 
								?>
								<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<div class="multilang"></div>
								<br/><br/>
								<?php endif;?>
								
								<fieldset class="fieldset white">
									<legend><?php __('lblLegendEmails');?></legend>
									<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
										<thead>
											<tr>
												<th><?php __('lblOption'); ?></th>
												<th><?php __('lblValue'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$client_notify_arr = __('client_email', true);
											$key_arr = array('confirmation','payment','cancel', 'account', 'forgot');
											$client_notify_arr = pjUtil::sortArrayByArray($client_notify_arr, $key_arr);
											?>
											<tr>
												<td><b><?php __('lblNotifications'); ?></b></td>
												<td>
													<select name="client_email_notify" id="client_email_notify" class="pj-form-field w300">
														<?php
														foreach ($client_notify_arr as $k => $v)
														{
															?><option value="<?php echo ucfirst($k); ?>"<?php echo isset($_GET['opt']) ? ($_GET['opt'] == ucfirst($k) ? ' selected="selected"' : NULL) : NULL;?>><?php echo $v; ?></option><?php
														}
														?>
													</select>
												</td>
											</tr>
											<?php
											for ($i = 0; $i < $count; $i++)
											{
												if ($tpl['arr'][$i]['tab_id'] == 3 && (int) $tpl['arr'][$i]['is_visible'] === 1 && (strpos($tpl['arr'][$i]['key'], 'admin') === false) && (strpos($tpl['arr'][$i]['key'], 'email') > -1))
												{
												
													$rowClass = NULL;
													$rowStyle = "display: none";
													if (in_array($tpl['arr'][$i]['key'], array('o_email_confirmation', 'o_email_confirmation_subject', 'o_email_confirmation_message')))
													{
														$rowClass = " boxClient boxClientConfirmation";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_email_payment', 'o_email_payment_subject', 'o_email_payment_message')))
													{
														$rowClass = " boxClient boxClientPayment";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_email_cancel', 'o_email_cancel_subject', 'o_email_cancel_message')))
													{
														$rowClass = " boxClient boxClientCancel";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_email_client_account', 'o_email_client_account_subject', 'o_email_client_account_message')))
													{
														$rowClass = " boxClient boxClientAccount";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_email_forgot_subject', 'o_email_forgot_message')))
													{
														$rowClass = " boxClient boxClientForgot";
													}
													?>
													<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
														<td width="180" valign="top">
															<span class="block bold"><?php __('opt_' . $tpl['arr'][$i]['key']); ?></span>
															<span class="fs10">
																<?php 
																if(in_array($tpl['arr'][$i]['key'], array('o_email_confirmation','o_email_payment','o_email_cancel', 'o_email_client_account', 'o_email_client_account_message', 'o_email_forgot_message')))
																{
																	nl2br(__('opt_' . $tpl['arr'][$i]['key'].'_text'));
																} 
																?>
															</span>
														</td>
														<td valign="top">
															<?php
															switch ($tpl['arr'][$i]['type'])
															{
																case 'string':
																	if(in_array($tpl['arr'][$i]['key'], array('o_email_confirmation_subject','o_email_payment_subject','o_email_cancel_subject', 'o_email_client_account_subject', 'o_email_forgot_subject')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<input type="text" name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?>" />
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
																	<?php }
																	break;
																case 'text':
																	
																	if(in_array($tpl['arr'][$i]['key'], array('o_email_confirmation_message','o_email_payment_message','o_email_cancel_message', 'o_email_client_account_message', 'o_email_forgot_message')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<textarea name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field mceEditor" style="width: 400px; height: 400px;"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?></textarea>
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 460px; height: 400px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea>
																	<?php }
																			
																	break;
																case 'int':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'float':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'enum':
																	?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
																	<?php
																	$default = explode("::", $tpl['arr'][$i]['value']);
																	$enum = explode("|", $default[0]);
																	
																	$enumLabels = array();
																	if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
																	{
																		$enumLabels = explode("|", $tpl['arr'][$i]['label']);
																	}
																	
																	foreach ($enum as $k => $el)
																	{
																		if ($default[1] == $el)
																		{
																			?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		} else {
																			?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		}
																	}
																	?>
																	</select>
																	<?php
																	break;
															}
															?>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tbody>
									</table>
								</fieldset>
								
								<fieldset class="fieldset white">
									<legend><?php __('lblLegendSMS');?></legend>
									<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
										<thead>
											<tr>
												<th><?php __('lblOption'); ?></th>
												<th><?php __('lblValue'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											for ($i = 0; $i < $count; $i++)
											{
												if ($tpl['arr'][$i]['tab_id'] == 3 && (int) $tpl['arr'][$i]['is_visible'] === 1 && (strpos($tpl['arr'][$i]['key'], 'admin') === false) && (strpos($tpl['arr'][$i]['key'], 'sms') > -1))
												{
												
													$rowClass = NULL;
													$rowStyle = NULL;
											
													?>
													<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
														<td width="180" valign="top">
															<span class="block bold"><?php __('opt_' . $tpl['arr'][$i]['key']); ?></span>
															<span class="fs10">
																<?php 
																if(in_array($tpl['arr'][$i]['key'], array('o_sms_confirmation_message')))
																{
																	echo nl2br(__('opt_' . $tpl['arr'][$i]['key'].'_text', true, false));
																} 
																?>
															</span>
														</td>
														<td valign="top">
															<?php
															switch ($tpl['arr'][$i]['type'])
															{
																case 'string':
																	?>
																		<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
																	<?php 
																	break;
																case 'text':
																	
																	if(in_array($tpl['arr'][$i]['key'], array('o_sms_confirmation_message','o_sms_payment_message','o_sms_cancel_message')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<textarea name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field" style="width: 400px; height: 200px;"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?></textarea>
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 460px; height: 400px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea>
																	<?php }
																			
																	break;
																case 'int':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'float':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'enum':
																	?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
																	<?php
																	$default = explode("::", $tpl['arr'][$i]['value']);
																	$enum = explode("|", $default[0]);
																	
																	$enumLabels = array();
																	if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
																	{
																		$enumLabels = explode("|", $tpl['arr'][$i]['label']);
																	}
																	
																	foreach ($enum as $k => $el)
																	{
																		if ($default[1] == $el)
																		{
																			?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		} else {
																			?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		}
																	}
																	?>
																	</select>
																	<?php
																	break;
															}
															?>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tbody>
									</table>
								</fieldset>
								
								<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
							</div><!-- tabs-1 -->
							
							<div id="tabs-2">
								<?php
								pjUtil::printNotice(__('infoConfirmations2Title', true), __('infoConfirmation2Desc', true), false); 
								?>
								<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<div class="multilang"></div>
								<br/><br/>
								<?php endif;?>
								<fieldset class="fieldset white">
									<legend><?php __('lblLegendEmails');?></legend>
									<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
										<thead>
											<tr>
												<th><?php __('lblOption'); ?></th>
												<th><?php __('lblValue'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$client_notify_arr = __('client_email', true);
											unset($client_notify_arr['forgot']);
											$key_arr = array('confirmation','payment','cancel','account');
											$client_notify_arr = pjUtil::sortArrayByArray($client_notify_arr, $key_arr);
											?>
											<tr>
												<td><b><?php __('lblNotifications'); ?></b></td>
												<td>
													<select name="admin_email_notify" id="admin_email_notify" class="pj-form-field w300">
														<?php
														foreach ($client_notify_arr as $k => $v)
														{
															?><option value="<?php echo ucfirst($k); ?>"><?php echo $v; ?></option><?php
														}
														?>
													</select>
												</td>
											</tr>
											<?php
											for ($i = 0; $i < $count; $i++)
											{
												if (($tpl['arr'][$i]['tab_id'] == 3 && (int) $tpl['arr'][$i]['is_visible'] === 1 && strpos($tpl['arr'][$i]['key'], 'admin') >-1 && (strpos($tpl['arr'][$i]['key'], 'email') > -1)))
												{											
													$rowClass = NULL;
													$rowStyle = "display: none";
													if (in_array($tpl['arr'][$i]['key'], array('o_admin_email_confirmation', 'o_admin_email_confirmation_subject', 'o_admin_email_confirmation_message')))
													{
														$rowClass = " boxAdmin boxAdminConfirmation";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_admin_email_payment', 'o_admin_email_payment_subject', 'o_admin_email_payment_message')))
													{
														$rowClass = " boxAdmin boxAdminPayment";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_admin_email_cancel', 'o_admin_email_cancel_subject', 'o_admin_email_cancel_message')))
													{
														$rowClass = " boxAdmin boxAdminCancel";
													}
													if (in_array($tpl['arr'][$i]['key'], array('o_admin_email_client_account', 'o_admin_email_client_account_subject', 'o_admin_email_client_account_message')))
													{
														$rowClass = " boxAdmin boxAdminAccount";
													}
													?>
													<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
														<td width="180" valign="top">
															<span class="block bold"><?php __('opt_' . str_replace('admin_', '', $tpl['arr'][$i]['key'])); ?></span>
															<span class="fs10">
																<?php 
																if(in_array($tpl['arr'][$i]['key'], array('o_admin_email_confirmation','o_admin_email_payment','o_admin_email_cancel', 'o_admin_email_client_account', 'o_admin_email_client_account_message')))
																{
																	nl2br(__('opt_' . $tpl['arr'][$i]['key'].'_text'));
																} 
																?>
															</span>
														</td>
														<td valign="top">
															<?php
															switch ($tpl['arr'][$i]['type'])
															{
																case 'string':
																	if(in_array($tpl['arr'][$i]['key'], array('o_admin_email_confirmation_subject','o_admin_email_payment_subject','o_admin_email_cancel_subject', 'o_admin_email_client_account_subject')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<input type="text" name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?>" />
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
																	<?php }
																	break;
																case 'text':
																	
																	if(in_array($tpl['arr'][$i]['key'], array('o_admin_email_confirmation_message','o_admin_email_payment_message','o_admin_email_cancel_message', 'o_admin_email_client_account_message')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<textarea name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field mceEditor" style="width: 400px; height: 400px;"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?></textarea>
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 460px; height: 400px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea>
																	<?php }
																			
																	break;
																case 'int':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'float':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'enum':
																	?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
																	<?php
																	$default = explode("::", $tpl['arr'][$i]['value']);
																	$enum = explode("|", $default[0]);
																	
																	$enumLabels = array();
																	if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
																	{
																		$enumLabels = explode("|", $tpl['arr'][$i]['label']);
																	}
																	
																	foreach ($enum as $k => $el)
																	{
																		if ($default[1] == $el)
																		{
																			?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		} else {
																			?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		}
																	}
																	?>
																	</select>
																	<?php
																	break;
															}
															?>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tbody>
									</table>
								</fieldset>
								
								<fieldset class="fieldset white">
									<legend><?php __('lblLegendSMS');?></legend>
									<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
										<thead>
											<tr>
												<th><?php __('lblOption'); ?></th>
												<th><?php __('lblValue'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											for ($i = 0; $i < $count; $i++)
											{
												if (($tpl['arr'][$i]['tab_id'] == 3 && (int) $tpl['arr'][$i]['is_visible'] === 1 && strpos($tpl['arr'][$i]['key'], 'admin') >-1 && (strpos($tpl['arr'][$i]['key'], 'sms') > -1)))
												{											
													$rowClass = NULL;
													$rowStyle = NULL;
												
													?>
													<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
														<td width="180" valign="top">
															<span class="block bold"><?php __('opt_' . $tpl['arr'][$i]['key']); ?></span>
														</td>
														<td valign="top">
															<?php
															switch ($tpl['arr'][$i]['type'])
															{
																case 'string':
																	?>
																		<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
																	<?php
																	break;
																case 'text':
																	
																	if(in_array($tpl['arr'][$i]['key'], array('o_admin_sms_confirmation_message','o_admin_sms_payment_message','o_admin_sms_cancel_message')))
																	{
																	?>
																		<?php
																			foreach ($tpl['lp_arr'] as $v)
																			{
																				?>
																				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																					<span class="inline_block">
																						<textarea name="i18n[<?php echo $v['id']; ?>][<?php echo $tpl['arr'][$i]['key'] ?>]" class="pj-form-field" style="width: 400px; height: 200px;"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']][$tpl['arr'][$i]['key']])); ?></textarea>
																						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
																						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																						<?php endif;?>
																					</span>
																				</p>
																				<?php
																			}
																		?>
																	<?php
																	}
																	else { ?>
																		<textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 460px; height: 200px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea>
																	<?php }
																			
																	break;
																case 'int':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'float':
																	?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
																	break;
																case 'enum':
																	?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
																	<?php
																	$default = explode("::", $tpl['arr'][$i]['value']);
																	$enum = explode("|", $default[0]);
																	
																	$enumLabels = array();
																	if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
																	{
																		$enumLabels = explode("|", $tpl['arr'][$i]['label']);
																	}
																	
																	foreach ($enum as $k => $el)
																	{
																		if ($default[1] == $el)
																		{
																			?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		} else {
																			?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
																		}
																	}
																	?>
																	</select>
																	<?php
																	break;
															}
															?>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tbody>
									</table>
								</fieldset>
								
								<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
							</div><!-- tabs-2 -->
						</div><!-- #tabs -->
					</form>
				</div>	
								
				<?php
			}
		}
	}
}
?>
<script type="text/javascript">
(function ($) {
$(function() {
	$(".multilang").multilang({
		langs: <?php echo $tpl['locale_str']; ?>,
		flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
		select: function (event, ui) {
			
		}
	});
	$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");

	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		$tab_id = $tab_id < 0 ? 0 : $tab_id; 
		?>$("#tabs").tabs("option", "selected", <?php echo str_replace("tabs-", "", $tab_id) - 1;?>);<?php
	}
	?>
});
})(jQuery_1_8_2);
</script>