<?php
if(isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr']))
{ 
	?>
	<div class="pjTbs-box">
		<div class="pjTbs-box-title"><?php __('front_choose_extras');?></div><!-- /.pjTbs-box-title -->
		
		<ul class="pjTbs-extras">
			<?php
		 
			foreach($tpl['avail_extra_arr'] as $k => $v)
			{
				?>
				<li>
					<div class="row">
						<div class="col-sm-5 col-xs-12">
							<div class="checkbox">
								<label><input type="checkbox" name="extra_id[<?php echo $v['extra_id'];?>]" value="<?php echo $v['price'];?>"<?php echo isset($tpl['extra_id_arr']) ? (in_array($v['extra_id'], $tpl['extra_id_arr']) ? ' checked="checked"' : NULL) : NULL;?> class="pjAvailExtra" data-price="<?php echo $v['price'];?>" data-per="<?php echo $v['per']?>"> <?php echo pjSanitize::html($v['name']);?></label>
							</div><!-- /.checkbox -->
						</div><!-- /.col-sm-6 -->

						<div class="col-sm-4 col-xs-12">
							<span><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ($v['per'] == 'person' ? ' ' . __('front_per_person', true) : '');?></span>
						</div><!-- /.col-sm-6 -->
						<?php
						if($v['per'] == 'person')
						{
							$extra_price = $v['price'] * $tpl['passengers'];
							?>
							<div class="col-sm-3 col-xs-12">
								<strong><?php echo pjUtil::formatCurrencySign(number_format($extra_price, 2), $tpl['option_arr']['o_currency']);?></strong>
							</div><!-- /.col-sm-6 -->
							<?php
						} 
						?>
					</div><!-- /.row -->
				</li>
				<?php
			}
			?>
				
		</ul><!-- /.pjTbs-extras -->
	
	</div><!-- /.pjTbs-car -->
	<?php
} 
?>	

<div class="pjTbs-subtotal">
	<?php
	if(isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr']) && (float) $tpl['price_arr']['extra'] > 0)
	{ 
		?>
		<p>
			<span><?php __('front_extras');?>:</span>
	
			<span><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['extra'], 2), $tpl['option_arr']['o_currency']);?></span>
		</p>
		<?php
	} 
	?>
	<p>
		<span><?php __('front_subtotal');?>:</span>

		<span><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['subtotal'], 2), $tpl['option_arr']['o_currency']);?></span>
	</p>

	<p>
		<span><?php __('front_tax');?>:</span>

		<span><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']);?></span>
	</p>

	<p>
		<span><?php __('front_total');?>:</span>

		<span><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $tpl['option_arr']['o_currency']);?></span>
	</p>

	<p>
		<span><?php __('front_deposit_required');?>:</span>

		<span><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></span>
	</p>
</div><!-- /.pjTbs-subtotal -->		