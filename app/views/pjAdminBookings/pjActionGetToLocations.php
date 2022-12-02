<select name="to_location_id" id="to_location_id" class="pj-form-field w250 required" data-msg-required="<?php __('tr_field_required'); ?>">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	if(isset($tpl['to_location_arr']))
	{
    	foreach($tpl['to_location_arr'] as $k => $v)
    	{
    	    ?><option value="<?php echo $v['id'];?>" data-address="<?php echo pjSanitize::html($v['address']);?>"><?php echo pjSanitize::html($v['name']);?></option><?php
    	}
	}
	?>
</select>