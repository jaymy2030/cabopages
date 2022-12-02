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
		$titles = __('error_titles', true, false);
		$bodies = __('error_bodies', true, false);
		
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	pjUtil::printNotice(__('infoUpdateFleetTitle', true, false), __('infoUpdateFleetDesc', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionUpdate" method="post" id="frmUpdateFleet" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="fleet_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblFleet'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][fleet]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['fleet'])); ?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php __('lblImage', false, true); ?></label>
				<span class="inline_block">
					<input type="file" name="image" id="image" class="pj-form-field w300"/>
				</span>
			</p>
			<?php
			if(!empty($tpl['arr']['source_path']))
			{
				$thumb_url = PJ_INSTALL_URL . $tpl['arr']['thumb_path'];
				?>
				<p id="image_container">
					<label class="title">&nbsp;</label>
					<span class="inline_block">
						<img class="tr-image" src="<?php echo $thumb_url; ?>" />
						<a href="javascript:void(0);" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('lblDelete');?></a>
					</span>
				</p>
				<?php
			} 
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h150" lang="<?php echo $v['id']; ?>" ><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php __('lblPassengers'); ?></label>
				<span class="inline-block">
					<input type="text" name="passengers" id="passengers" class="pj-form-field field-int w80 digits" value="<?php echo pjSanitize::clean($tpl['arr']['passengers'])?>" data-msg-digits="<?php __('pj_digits_validation');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblLuggage'); ?></label>
				<span class="inline-block">
					<input type="text" name="luggage" id="luggage" class="pj-form-field field-int w80 digits" value="<?php echo pjSanitize::clean($tpl['arr']['luggage'])?>" data-msg-digits="<?php __('pj_digits_validation');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblExtras'); ?></label>
				<span class="inline_block">
					<?php
					if(!empty($tpl['extra_arr']))
					{ 
						?>
						<select name="extra_id[]" id="extra_id" multiple="multiple" size="5" class="pj-form-field required w300" data-msg-required="<?php __('tr_field_required'); ?>">
							<?php
							foreach ($tpl['extra_arr'] as $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo in_array($v['id'], $tpl['extra_id_arr']) ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['name']); ?></option><?php
							}
							?>
						</select>
						<?php
					}
					?>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStartFee'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="start_fee" name="start_fee" value="<?php echo pjSanitize::clean($tpl['arr']['start_fee'])?>" class="pj-form-field number required w100" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblFeePerPerson'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="fee_per_person" name="fee_per_person" value="<?php echo pjSanitize::clean($tpl['arr']['fee_per_person'])?>" class="pj-form-field number required w100" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
				</span>
			</p>
			<div class="p">
				<label class="title bold b5"><?php __('lblPrices'); ?>:</label>
				<div id="pjTbPriceContainer" class="overlow">
					<?php
					foreach($tpl['price_arr'] as $from_location_id => $price_set)
					{
					    $index = 'new_' . rand(1, 999999);
    					?>
    					<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
    						<thead>
    							<tr>
    								<th>&nbsp;</th>
    								<th style="width: 24px;">&nbsp;</th>
    							</tr>
    						</thead>
    						<tbody>
    							<tr>
    								<td>
    									<table class="pj-table" style="width: 100%">
    										<tbody>
    											<tr>
        											<td><?php __('lblDepartureArrivalLocation');?></td>
        											<td colspan="3">
        												<select name="da_location_id[<?php echo $index;?>]" class="pj-form-field w350">
                                							<?php
                                							foreach ($tpl['da_arr'] as $v)
                                							{
                                							    ?><option value="<?php echo $v['id']; ?>"<?php echo $from_location_id == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['name']); ?></option><?php
                                							}
                                							?>
                                						</select>
        											</td>
    											</tr>
    											<tr>
													<td><?php __('lblPickupDropoff');?></td>
													<td><?php __('lblOneWayPrice');?></td>
													<td colspan="2"><?php __('lblRoundtripPrice');?></td>
												</tr>
    											<?php
    											foreach($price_set as $price)
    											{
    											    $subindex = 'sub_new_' . rand(1, 999999); 
        											?>
        											<tr>
        												<td>
            												<select name="pd_location_id[<?php echo $index;?>][<?php echo $subindex;?>]" class="pj-form-field w180">
                                    							<?php
                                    							foreach ($tpl['pd_arr'] as $v)
                                    							{
                                    							    ?><option value="<?php echo $v['id']; ?>"<?php echo $price['to_location_id'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['name']); ?></option><?php
                                    							}
                                    							?>
                                    						</select>
            											</td>
            											<td>
            												<span class="pj-form-field-custom pj-form-field-custom-before">
                                            					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
                                            					<input type="text" name="price[<?php echo $index;?>][<?php echo $subindex;?>]" value="<?php echo pjSanitize::html($price['price'])?>" class="pj-form-field number w60"/>
                                            				</span>
            											</td>
            											<td>
            												<span class="pj-form-field-custom pj-form-field-custom-before">
                                            					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
                                            					<input type="text" name="price_roundtrip[<?php echo $index;?>][<?php echo $subindex;?>]" value="<?php echo pjSanitize::html($price['price_roundtrip'])?>" class="pj-form-field number w60"/>
                                            				</span>
            											</td>
            											<td><a href="#" class="lnkRemovePrice lnkRemoveSubPrice"></a></td>
        											</tr>
        											<?php
    											}
        										?>
    										</tbody>
    									</table>
    								</td>
    								<td><a href="#" class="lnkRemovePrice lnkRemoveMainPrice" data-index="<?php echo $index;?>"></a></td>
    							</tr>
    							<tr>
    								<td><input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddSubPrice" data-index="<?php echo $index;?>"/></td>
    								<td>&nbsp;</td>
    							</tr>
    						</tbody>
    					</table>
    					<?php
					}
    				?>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddPrice" />
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminFleets&action=pjActionIndex';" />
			</p>
		</div>
	</form>
	
	<div id="dialogDeleteImage" style="display: none" title="<?php __('lblDeleteImage');?>"><?php __('lblDeleteConfirmation');?></div>
	
	<div id="pjTbPriceTableClone" style="display:none;">
	
		<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th style="width: 24px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<table class="pj-table" style="width: 100%">
							<tbody>
								<tr>
									<td><?php __('lblDepartureArrivalLocation');?></td>
									<td colspan="3">
										<select name="da_location_id[{INDEX}]" class="pj-form-field w350">
                							<?php
                							foreach ($tpl['da_arr'] as $v)
                							{
                								?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
                							}
                							?>
                						</select>
									</td>
								</tr>
								<tr>
									<td><?php __('lblPickupDropoff');?></td>
									<td><?php __('lblOneWayPrice');?></td>
									<td colspan="2"><?php __('lblRoundtripPrice');?></td>
								</tr>
								<tr>
									<td>
										<select name="pd_location_id[{INDEX}][{SUBINDEX}]" class="pj-form-field w180">
                							<?php
                							foreach ($tpl['pd_arr'] as $v)
                							{
                								?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
                							}
                							?>
                						</select>
									</td>
									<td>
										<span class="pj-form-field-custom pj-form-field-custom-before">
                        					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
                        					<input type="text" name="price[{INDEX}][{SUBINDEX}]" class="pj-form-field number w60"/>
                        				</span>
									</td>
									<td>
										<span class="pj-form-field-custom pj-form-field-custom-before">
                        					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
                        					<input type="text" name="price_roundtrip[{INDEX}][{SUBINDEX}]" class="pj-form-field number w60"/>
                        				</span>
									</td>
									<td><a href="#" class="lnkRemovePrice lnkRemoveSubPrice"></a></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td><a href="#" class="lnkRemovePrice lnkRemoveMainPrice"></a></td>
				</tr>
				<tr>
					<td><input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddSubPrice" data-index="{INDEX}"/></td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>		
	</div>
	<table id="pjTbSubPriceClone" style="display: none">
		<tbody>
			<tr>
				<td>
					<select name="pd_location_id[{INDEX}][{SUBINDEX}]" class="pj-form-field w180">
						<?php
						foreach ($tpl['pd_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
						}
						?>
					</select>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
    					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    					<input type="text" name="price[{INDEX}][{SUBINDEX}]" class="pj-form-field number w60"/>
    				</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
    					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    					<input type="text" name="price_roundtrip[{INDEX}][{SUBINDEX}]" class="pj-form-field number w60"/>
    				</span>
				</td>
				<td><a href="#" class="lnkRemovePrice lnkRemoveSubPrice"></a></td>
			</tr>
		</tbody>
	</table>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>