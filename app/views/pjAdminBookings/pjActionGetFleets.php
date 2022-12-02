<select name="fleet_id" id="fleet_id" class="pj-form-field w300 required" data-msg-required="<?php __('tr_field_required'); ?>">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	if(isset($tpl['fleet_arr']))
	{
    	foreach($tpl['fleet_arr'] as $k => $v)
    	{
    		?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>" data-luggage="<?php echo !empty($v['luggage']) ? $v['luggage'] : null; ?>"><?php echo $v['fleet'];?></option><?php
    	}
	}
	?>
</select>