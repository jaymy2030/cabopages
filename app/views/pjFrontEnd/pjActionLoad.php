<?php
mt_srand();
$index = mt_rand(1, 9999);
$theme = isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme'];

$time_ampm = 0;
if(strpos($tpl['option_arr']['o_time_format'], 'a') > -1)
{
	$time_ampm = 1;
}
if(strpos($tpl['option_arr']['o_time_format'], 'A') > -1)
{
	$time_ampm = 2;
}
?>
<div id="pjWrapperTaxiBooking_<?php echo $theme;?>">
	<div id="pjTbsContainer_<?php echo $index; ?>" class="container-fluid <?php echo isset($_GET['search_form']) ? 'pjTbsContainerSearchForm' : '';?>"></div>
	
	<div class="modal fade pjTbModal" id="pjNcbTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
	        		<h4 class="modal-title pjTbModalTitle" id="myModalLabel"><?php __('front_terms_title', false, true); ?></h4>
	      		</div>
			    <div class="modal-body">
			    	<?php echo nl2br(stripslashes($tpl['terms_conditions'])); ?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('front_btn_close');?></button>
		      	</div>
	    	</div>
	  	</div>
	</div>
	<div class="modal fade pjTbModal" id="pjTbsEarlierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
	        		<h4 class="modal-title pjTbModalTitle" id="myModalLabel"><?php __('front_hour_earlier_title', false, true); ?></h4>
	      		</div>
			    <div class="modal-body">
			    	<?php
			    	$description = __('front_hour_earlier_desc', true);
			    	$description = str_replace("{HOURS}", $tpl['option_arr']['o_hour_earlier'], $description);
			    	echo $description;
			    	?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('front_btn_close');?></button>
		      	</div>
	    	</div>
	  	</div>
	</div>
	<div class="modal fade pjTbModal" id="pjCssLoginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	  		<form id="pjCssLoginForm_<?php echo $index;?>" action="" method="post">
	  			<input type="hidden" name="lbs_login" value="1" />
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
		        		<h4 class="modal-title pjCssModalTitle" id="loginModalLabel"><?php __('front_btn_login', false, true); ?></h4>
		      		</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('front_email'); ?> <span>*</span></label>
									
									<input type="text" name="login_email" class="form-control email required" data-msg-required="<?php __('front_required_field'); ?>" data-msg-email="<?php __('front_email_validation'); ?>">
							    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('front_password'); ?> <span>*</span></label>
									
									<input type="password" name="login_password" class="form-control required" data-msg-required="<?php __('front_required_field'); ?>">
							    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
								</div>
							</div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<div class="form-group"><a href="#" class="pjCssLinkForgotPassword"><?php __('front_link_forgot_password');?></a></div>
				    		</div>
				    	</div>
				    	<div class="row" style="display: none;">
				    		<div class="col-sm-12">
				    			<div id="pjLoginMessage_<?php echo $index;?>" class="form-group text-danger"></div>
				    		</div>
				    	</div>
				    </div>
			      	<div class="modal-footer">
			      		<div class="row">
			      			<div class="col-sm-6">
			      				<div id="gSignIn"></div>
			      			</div>
			      			<div class="col-sm-6">
        			      		<button type="submit" class="btn btn-primary"><?php __('front_btn_login');?></button>
        			        	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('front_btn_close');?></button>
    			        	</div>
			      		</div>
			      	</div>
		    	</div>
		    </form>
	  	</div>
	</div>
	
	<div class="modal fade pjTbModal" id="pjCssForgotModal" tabindex="-1" role="dialog" aria-labelledby="forgotModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	  		<form id="pjCssForgotForm_<?php echo $index;?>" action="" method="post">
	  			<input type="hidden" name="lbs_forgot" value="1" />
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
		        		<h4 class="modal-title pjCssModalTitle" id="forgotModalLabel"><?php __('front_link_forgot_password', false, true); ?></h4>
		      		</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
								<div class="form-group">
									<label><?php __('front_email'); ?> <span>*</span></label>
									
									<input type="text" name="email" class="form-control email required" data-msg-required="<?php __('front_required_field'); ?>" data-msg-email="<?php __('front_email_validation'); ?>">
							    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
								</div>
							</div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<div class="form-group"><a href="#" class="pjCssLinkLogin"><?php __('front_link_login');?></a></div>
				    		</div>
				    	</div>
				    	<div class="row" style="display: none;">
				    		<div class="col-sm-12">
				    			<div id="pjForgotMessage_<?php echo $index;?>" class="form-group"></div>
				    		</div>
				    	</div>
				    </div>
			      	<div class="modal-footer">
			      		<button type="submit" class="btn btn-primary"><?php __('front_btn_send');?></button>
			        	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('front_btn_close');?></button>
			      	</div>
		    	</div>
		    </form>
	  	</div>
	</div>
	
	<div class="modal fade pjTbModal" id="pjTbsCheckTimeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
	        		<h4 class="modal-title pjTbModalTitle" id="myModalLabel"><?php __('front_check_time_title', false, true); ?></h4>
	      		</div>
			    <div class="modal-body">
			    	<?php
			    	$description = __('front_check_time_desc', true);
			    	echo $description;
			    	?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('front_btn_close');?></button>
		      	</div>
	    	</div>
	  	</div>
	</div>
</div>

<script type="text/javascript">
var pjQ = pjQ || {},
	TaxiBooking_<?php echo $index; ?>;
(function () {
	"use strict";
	
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
		
	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		theme: "<?php echo $theme;?>",
		index: <?php echo $index; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
		direction: "<?php echo isset($_GET['dir']) ? $_GET['dir'] : $controller->getDirection(); ?>",
		week_start: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>,
		momentDateFormat: "<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>",
		time_format: "<?php echo $time_ampm > 0 ? 'LT' : "HH:mm";?>",
		lat: <?php echo isset($tpl['option_arr']['o_latitude']) ? (float) $tpl['option_arr']['o_latitude'] : 51.509865; ?>,
		lng: <?php echo isset($tpl['option_arr']['o_longitude']) ? (float) $tpl['option_arr']['o_longitude'] : -0.118092; ?>,
		zoom: <?php echo isset($tpl['option_arr']['o_zoom']) ? (float) $tpl['option_arr']['o_zoom'] : 13; ?>,
		google_signin_client_id: "<?php echo isset($tpl['option_arr']['o_google_signin_client_id']) ? $tpl['option_arr']['o_google_signin_client_id'] : ""; ?>",	
		search_form: <?php echo isset($_GET['search_form']) ? 1 : 0; ?>,
		autoload_fleets: <?php echo (int) $tpl['autoload_fleets']; ?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	$api_key = isset($tpl['option_arr']['o_google_api_key']) && !empty($tpl['option_arr']['o_google_api_key']) ? '&key=' . $tpl['option_arr']['o_google_api_key'] : '';
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("https://maps.googleapis.com/maps/api/js?libraries=places<?php echo $api_key;?>", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>moment-with-locales.min.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>pjQuery.bootstrap-datetimepicker.min.js", function () {
								loadScript("https://js.stripe.com/v3/", function () {
    								loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH ?>pjFront.js", function () {
    									TaxiBooking = TaxiBooking(options);
    								});
								});
							});
						});
					});
				});
			});
		});
	});
})();
</script>