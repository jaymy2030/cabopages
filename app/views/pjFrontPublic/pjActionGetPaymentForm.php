<?php
include_once dirname(__FILE__) . '/elements/header.php'; 
?>
<div class="pjTbs-body">
	<div class="row">
		<div class="col-sm-12 text-center">
			<?php
			if (isset($tpl['get']['payment_method']))
			{
				$status = __('front_messages', true, false);
				switch ($tpl['get']['payment_method'])
				{
					case 'paypal':
						?><p class="text-success text-center"><?php echo $status[1]; ?></p><?php
						if (pjObject::getPlugin('pjPaypal') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'authorize':
						?><p class="text-success text-center"><?php echo $status[2]; ?></p><?php
						if (pjObject::getPlugin('pjAuthorize') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
                    case 'stripe':
                        ?><p class="text-success text-center"><?php echo $status[6]; ?></p><?php
                        if(isset($tpl['session_id']))
                        {
                            ?>
                            <form id="tbsStripe" name="tbsStripe" action="#" method="POST">
                            	<input type="hidden" name="stripe_session_id" value="<?php echo $tpl['session_id'];?>"/>
                            	<input type="hidden" name="public_key" value="<?php echo $tpl['option_arr']['o_stripe_public_key'];?>"/>
                            </form>
                        	<?php
                        }
    				    break;
					case 'bank':
						?><p class="text-success text-center"><?php echo $status[4]; ?></p><?php
						break;
					case 'creditcard':
					case 'cash':
					default:
						?><p class="text-success text-center"><?php echo $status[4]; ?></p><?php
				}
			}
			
			if($tpl['get']['payment_method'] == 'bank' || $tpl['get']['payment_method'] == 'creditcard' || $tpl['get']['payment_method'] == 'cash' || $tpl['option_arr']['o_payment_disable'] == 'Yes') 
			{
				?>
				<input type="button" class="btn btn-primary pjTbsBtnStartOver" value="<?php __('front_btn_start_over')?>" />
				<?php
			} 
			?>
		</div>
	</div>
</div>