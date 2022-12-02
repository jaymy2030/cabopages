<select name="to_location_id" id="to_location_id_<?php echo $_GET['index'];?>" class="form-control required" data-msg-required="<?php __('tr_field_required'); ?>">
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
<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>