<div class="pjTbs-head">
	<div class="row">
		<div class="col-sm-4 col-xs-8">
			<div class="pjTbs-head-title"><?php __('front_booking_details');?></div><!-- /.pjTbs-box-title -->
		</div>
		<?php if ((!isset($_GET['hide']) || (int) $_GET['hide'] === 0) && isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && count($tpl['locale_arr']) > 1) : ?>
		<?php
		$selected_title = null;
		$selected_src = NULL;
		foreach ($tpl['locale_arr'] as $locale)
		{
			if ($controller->getLocaleId() == $locale['id'])
			{
				$selected_title = $locale['language_iso'];
				$lang_iso = explode("-", $selected_title);
				if(isset($lang_iso[1]))
				{
					$selected_title = $lang_iso[1];
				}
				if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
				{
					$selected_src = PJ_INSTALL_URL . $locale['flag'];
				} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
					$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
				}
				break;
			}
		}
		?>
		<div class="col-sm-4 col-xs-4 pull-right">
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<img src="<?php echo $selected_src; ?>" alt="">
					<?php echo $selected_title; ?>
					<span class="caret"></span>
				</button>

				<ul class="dropdown-menu">
					<?php
					foreach ($tpl['locale_arr'] as $locale)
					{
						$selected_src = NULL;
						if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
						{
							$selected_src = PJ_INSTALL_URL . $locale['flag'];
						} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
							$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
						}
						?>
						<li<?php echo $controller->getLocaleId() == $locale['id'] ? ' active' : NULL; ?>>
							<a href="#" class="pjTbsLocale" data-id="<?php echo $locale['id']; ?>" data-dir="<?php echo $locale['dir']; ?>">
								<img src="<?php echo $selected_src; ?>" alt="">
								<?php echo pjSanitize::html($locale['name']); ?>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div><!-- /.col-sm-4 -->
		<?php endif;?>
	</div><!-- /.row -->
</div><!-- /.pjTbs-head -->