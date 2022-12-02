<?php
if((isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr'])))
{ 
	?>
	<div class="p">
		<label class="title"><?php __('lblExtras'); ?></label>
		<div class="float_left overflow">
			<table class="pj-table" width="100%">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th width="300"><?php __('lblExtraName');?></th>
						<th width="200"><?php __('lblPrice');?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr']))
					{ 
						foreach($tpl['avail_extra_arr'] as $k => $v)
						{
							?>
							<tr>
								<td><input type="checkbox" name="extra_id[]" value="<?php echo $v['extra_id']?>" class="pjAvailExtra" data-price="<?php echo $v['price'];?>" data-per="<?php echo $v['per']?>"/></td>
								<td><?php echo pjSanitize::html($v['name']);?></td>
								<td><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ($v['per'] == 'person' ? ' ' . __('lblPerPerson', true) : '');?></td>
							</tr>
							<?php
						}							
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
} 
?>