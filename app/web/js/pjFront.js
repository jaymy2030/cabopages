(function (window, undefined){
	"use strict";
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		routes = [
		          	{pattern: /^#!\/loadSearch$/, eventName: "loadSearch"},
		          	{pattern: /^#!\/loadFleets$/, eventName: "loadFleets"},
		          	{pattern: /^#!\/loadCheckout$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/loadPreview$/, eventName: "loadPreview"}
		         ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadSearch");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function TaxiBooking(opts) {
		if (!(this instanceof TaxiBooking)) {
			return new TaxiBooking(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	TaxiBooking.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	TaxiBooking.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	TaxiBooking.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.opts = {};
			this.map = null;
			this.directionsDisplay = new google.maps.DirectionsRenderer();
			this.directionsService = new google.maps.DirectionsService();
			this.google_button_clicked = false;
			return this;
		},
		
		disableButtons: function () {
			this.$container.find(".btn").each(function (i, el) {
				pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjTbsContainer_" + self.opts.index);
						
			self.$container = pjQ.$(self.container);
			pjQ.$("html").attr('dir',self.opts.direction);
			this.$container.on("change.tbs", ".pjTbsMenu", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var load = pjQ.$(this).val();
				if (!hashBang("#!/" + load)) 
				{
					pjQ.$(window).trigger(load);
				}
				return false;
			}).on("click.tbs", ".pjTbsLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}				
				self.opts.locale = pjQ.$(this).data("id");
				var dir = pjQ.$(this).data("dir");
				self.opts.direction = dir;
				var params = {};
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				params.locale_id = self.opts.locale;
				params.index = self.opts.index;
				
				self.disableButtons.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), params).done(function (data) {
					pjQ.$("html").attr('dir',dir);
					var i, iCnt, m;
					for (i = 0, iCnt = routes.length; i < iCnt; i++) {
						m = window.location.hash.match(routes[i].pattern);
						if (m !== null) {
							pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
							break;
						}
					}
					if (m === null) {
						if (self.opts.search_form == 1) {
							self.loadSearchForm.call(self);
						} else {
							if (!hashBang("#!/loadFleets")) {
								pjQ.$(window).trigger("loadFleets");
							}
						}
					}else{
						if (!hashBang(m)) {
							m = str.replace("#!/", "");
							pjQ.$(window).trigger(m);
						}
					}
				}).fail(function () {
					self.enableButtons.call(self);
				});
				return false;
			}).on("change.tbs", ".pjTbsServiceSelector", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var fleet_id = pjQ.$(this).attr('data-fleet_id');
				var service_id = pjQ.$(this).val();
				pjQ.$('.pjTbs-price-' + fleet_id).hide();
				if(service_id != '')
				{
					pjQ.$('#pjTbsPriceLabel_' + service_id).show();
					pjQ.$('#pjTbsBtnReserve_' + fleet_id).removeAttr('disabled');
				}else{
					pjQ.$('#pjTbsBtnReserve_' + fleet_id).attr('disabled', 'disabled');
				}
				return false;
			}).on("click.tbs", ".pjTbsBtnReserve", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var ajax_url = [self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetService"].join("");
				if(self.opts.session_id != '')
				{
					ajax_url = [self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetService", "&session_id=", self.opts.session_id].join("")
				}
				
				self.disableButtons.call(self);
				pjQ.$.post(ajax_url, pjQ.$(this).closest('form').serialize()).done(function (data) {
					if (!hashBang("#!/loadCheckout")) 
					{
						self.loadCheckout.call(self);
					}
				}).fail(function () {
					self.enableButtons.call(self);
				});
				return false;
			}).on('click.tbs', '.pjCssLogin', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $loginForm = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				$loginForm.find('input[name="login_email"]').val("");
				$loginForm.find('input[name="login_password"]').val("");
				pjQ.$('#pjLoginMessage_'+ self.opts.index).html("").parent().parent().hide();
				pjQ.$('#pjCssLoginModal').modal('show');
				return false;
			}).on('click.tbs', '.pjCssLogout', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var params = {};
				params.locale = self.opts.locale;
				params.index = self.opts.index;
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				self.disableButtons.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionLogout"].join(""), params).done(function (data) {
					if (!hashBang("#!/loadCheckout")) 
					{
						self.loadCheckout.call(self);
					}
				}).fail(function () {
					
				});
				return false;
			}).on('click.tbs', '#pjTbsImage_' + self.opts.index, function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captchaImg = pjQ.$(this);
				if($captchaImg.length > 0){
					var rand = Math.floor((Math.random()*999999)+1); 
					
					if(self.opts.session_id != '')
					{
						$captchaImg.attr("src", self.opts.folder + 'index.php?controller=pjFrontEnd&action=pjActionCaptcha&rand=' + rand + "&session_id=" + self.opts.session_id);
					}else{
						$captchaImg.attr("src", self.opts.folder + 'index.php?controller=pjFrontEnd&action=pjActionCaptcha&rand=' + rand);
					}
					pjQ.$('#pjTbsCheckoutForm_' + self.opts.index).find('input[name="captcha"]').val("").removeData("previousValue");
				}
				return false;
			}).on('click.tbs', '.pjAvailExtra', function(e){
				self.calcPrices.call(self);
			}).on('click.tbs', '.pjTbsBtnBack', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var load = pjQ.$(this).attr('data-load');
				if (!hashBang("#!/" + load)) 
				{
					pjQ.$(window).trigger(load);
				}
				return false;
			}).on('click.tbs', '.pjTbsBtnStartOver', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}			
				self.map = null;
				if (!hashBang("#!/loadSearch")) 
				{
					self.loadSearch.call(self);
				}
				return false;
			}).on('click.tbs', '.pjTbsBtnBookTaxi', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var id = pjQ.$(this).attr('data-id');
				var params = {};
				params.fleet_id = id;
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				self.disableButtons.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetTaxi"].join(""), params).done(function (data) {
					if(data.status == 'OK')
					{
						if (!hashBang("#!/loadCheckout")) 
						{
							self.loadCheckout.call(self);
						}
					}else{
						self.enableButtons.call(self);
					}
				}).fail(function () {
					self.enableButtons.call(self);
				});
				return false;
			}).on("change.tbs", '#from_location_id_' + self.opts.index, function (e) {
				var params = {};
				params.from_location_id = pjQ.$(this).val();
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				params.index = self.opts.index;
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetToLocations"].join(""), params).done(function (data) {
					pjQ.$('#pjTbToLocationContainer_' + self.opts.index).html(data);
				});
			}).on("change.tbs", '#to_location_id_' + self.opts.index, function (e) {
				self.calcRoute(self);
			}).on("change", 'input[type=radio][name=booking_type]', function (e) {
				if (this.value == 'from') {
			        pjQ.$('#pjTbDropoffTitle').show();
			        pjQ.$('#pjTbPickupTitle').hide();
			    }else {
			    	pjQ.$('#pjTbDropoffTitle').hide();
			    	pjQ.$('#pjTbPickupTitle').show();
			    }
			}).on("change", 'input[type=radio][name=booking_option]', function (e) {
				if (this.value == 'roundtrip') {
			        pjQ.$('.pjTbsReturnDatetime').show();
			    }else {
			    	pjQ.$('.pjTbsReturnDatetime').hide();
			    }
			});
			
			pjQ.$(window).on("loadSearch", this.$container, function (e) {
				self.loadSearch.call(self);
			}).on("loadFleets", this.$container, function (e) {
				self.loadFleets.call(self);
			}).on("loadCheckout", this.$container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.$container, function (e) {
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				if (this.opts.search_form == 1) {
					this.loadSearchForm.call(this);
				} else if (this.opts.autoload_fleets == 1) {
					if (!hashBang("#!/loadFleets")) 
					{
						self.loadFleets.call(self);
					}
				} else {
					this.loadSearch.call(this);
				}
			} else {
				onHashChange.call(null);
			}
			
			pjQ.$(document).on("click.tbs", '.pjCssLinkForgotPassword', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $forgotForm = pjQ.$('#pjCssForgotForm_'+ self.opts.index);
				$forgotForm.find('input[name="email"]').val("");
				pjQ.$('#pjForgotMessage_'+ self.opts.index).removeClass('text-danger text-success').html("").parent().parent().hide();
				pjQ.$('#pjCssLoginModal').modal('hide');
				pjQ.$('#pjCssForgotModal').modal('show');
				return false;
			}).on("click.tbs", '.pjCssLinkLogin', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $loginForm = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				$loginForm.find('input[name="login_email"]').val("");
				$loginForm.find('input[name="login_password"]').val("");
				pjQ.$('#pjLoginMessage_'+ self.opts.index).html("").parent().parent().hide();
				pjQ.$('#pjCssForgotModal').modal('hide');
				pjQ.$('#pjCssLoginModal').modal('show');
				return false;
			}).on("change.tbs", "select[name='payment_method']", function () {
				self.$container.find(".pjTbsCcWrap").hide();
				self.$container.find(".pjTbsBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".pjTbsCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".pjTbsBankWrap").show();
					break;
				}
			});
		},
		
		loopPrices: function() {
			var self = this;
			pjQ.$('.pjTbsServiceSelector').each(function(e){
				var service_id = pjQ.$(this).val();
				pjQ.$('#pjTbsPriceLabel_' + service_id).show();
			});
		},
		loadSearch: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionSearch"].join(""), params).done(function (data) {
				self.$container.html(data);
				self.bindSearch.call(self);
			}).fail(function () {
				
			});
		},
		loadSearchForm: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionSearchForm"].join(""), params).done(function (data) {
				self.$container.html(data);
				self.bindSearchForm.call(self);
			}).fail(function () {
				
			});
		},
		loadFleets: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionFleets"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadSearch")) 
					{
						self.loadSearch.call(self);
					}
				}else{
					self.$container.html(data);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadFleets")) 
					{
						self.loadFleets.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindCheckout.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		calcPrices: function(){
			var self = this;
			
			var $form = pjQ.$('#pjTbsCheckoutForm_'+ self.opts.index);
			
			var ajax_url = [self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetPrices"].join("");
			if(self.opts.session_id != '')
			{
				ajax_url = [self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetPrices", "&session_id=", self.opts.session_id].join("")
			}
			self.disableButtons.call(self);
			pjQ.$.post(ajax_url, $form.serialize()).done(function (data) {
				pjQ.$('#pjTbsPriceBox').html(data);
				self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		initMap: function(){
			var self = this;
			
			self.map = new google.maps.Map(document.getElementById('pjTbsMapCanvas'), {
				center: {lat: self.opts.lat, lng: self.opts.lng},
				zoom: self.opts.zoom
        	});
			
			self.calcRoute.call(self);
		},
		calcRoute: function(){
			var self = this;
			
	        var start = pjQ.$('#from_location_id_' + self.opts.index).find(':selected').attr('data-address');
			var end = pjQ.$('#to_location_id_'+ self.opts.index).find(':selected').attr('data-address');
	        if(start != '' && end != '')
	        {
	        	self.directionsDisplay.setMap(self.map);
	        	var request = {
		        		origin: start,
		        		destination: end,
		        		travelMode: google.maps.DirectionsTravelMode.DRIVING
	              	};
		        self.directionsService.route(request, function(response, status) {
		        	if (status == google.maps.DirectionsStatus.OK) {
		        		self.directionsDisplay.setDirections(response);
		        		var distanceinkm = parseInt(response.routes[0].legs[0].distance.value / 1000, 10);
		        		pjQ.$('#pjTbsDistanceFiled').val(distanceinkm);
		            }
		         });
	        }else{
	        	self.directionsDisplay.setMap(null);
	        	pjQ.$('#pjTbsDistanceFiled').val("");
	        }
		},
		bindSearch: function(){
			var self = this,
				index = this.opts.index;
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			if(pjQ.$('#pjTbsMapCanvas').length > 0)
			{
				self.initMap.call(self);
			}
						
			if(pjQ.$('#pjTbsCalendarLocale').length > 0)
			{
				var fday = parseInt(pjQ.$('#pjTbsCalendarLocale').data('fday'), 10);
				moment.updateLocale('en', {
					months : pjQ.$('#pjTbsCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjTbsCalendarLocale').data('days').split("_"),
			        week: { dow: fday }
				});
			}
			if(pjQ.$('.date-pick').length > 0)
			{
				var currentDate = new Date();
				var datetimeOptions = {
					format: self.opts.momentDateFormat.toUpperCase(),
					locale: moment.locale('en'),
					allowInputToggle: true,
					minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()),
					ignoreReadonly: true,
					useCurrent: false
				};
				var $dt_from = pjQ.$('.pjTbsDateFrom'),
					$dt_to = pjQ.$('.pjTbsDateTo');
				$dt_from.datetimepicker(datetimeOptions).on('dp.change', function (e) {
					var $from = e.date.valueOf();
					if ($dt_to.val() != '') {
						var $to = $dt_to.data("DateTimePicker").date().valueOf();
						if ($to < $from) {
							$dt_to.val($dt_from.val());
						}
					}
					$dt_to.data("DateTimePicker").minDate(e.date);
				});
				
				$dt_to.datetimepicker(datetimeOptions).on('dp.change', function (e) {
					$dt_from.data("DateTimePicker").maxDate(e.date);
				});
			}
			pjQ.$('.time-pick').datetimepicker({
				format: self.opts.time_format,
				ignoreReadonly: true,
				allowInputToggle: true
			});
			if (pjQ.$('.pjTbs-spinner').length) {
		        var spinnerUpClass = 'pjTbs-spinner-up';
		        var spinnerDownClass = 'pjTbs-spinner-down';
		        var spinnerResult = '.pjTbs-spinner-result';

		        pjQ.$('.pjTbs-spinner').on('click', '.pjTbs-spinner', function(e) {
		            var $clickedSpinnerBtn = pjQ.$(this);
		            var $spinnerField = $clickedSpinnerBtn.siblings(spinnerResult);
		            var $spinnerValue = $spinnerField.val();
		            var $maxValue = parseInt($spinnerField.attr('data-max'), 10);
		           
		            if ($clickedSpinnerBtn.hasClass(spinnerUpClass)) {
		                $spinnerValue = $spinnerValue +++ 1;
		            } else if ($clickedSpinnerBtn.hasClass(spinnerDownClass)) {
		                $spinnerValue = $spinnerValue --- 1;
		            };
		            if($spinnerField.attr('name') == 'passengers')
		            {
		            	if ($spinnerValue <= 1) {
			                $spinnerValue = 1;
			            };
		            }else{
		            	if ($spinnerValue <= 0) {
			                $spinnerValue = '';
			            };
		            }
		            if ($spinnerValue >= $maxValue) {
		                $spinnerValue = $maxValue;
		            };
		            $spinnerField.val($spinnerValue);

		            e.preventDefault();
		        });
		    };
			if (validate) 
			{
				var $form = pjQ.$('#pjTbsSearchForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					errorElement: 'li',
					ignore: ":hidden",
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							error.appendTo(element.parent().parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							element.parent().parent().removeClass('has-success').addClass('has-error');
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							element.parent().parent().parent().removeClass('has-success').addClass('has-error');
						}else{
							element.parent().removeClass('has-success').addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							element.parent().parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionSearch", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								

								if (!hashBang("#!/loadFleets")) 
								{
									self.loadFleets.call(self);
								}
							}else{
								if(data.code == '120') {
									pjQ.$('html, body').animate({
								        scrollTop: self.$container.offset().top
								    }, 500);
									pjQ.$('#pjTbsEarlierModal').modal('show');
								} else if(data.code == '130') {
									pjQ.$('html, body').animate({
								        scrollTop: self.$container.offset().top
								    }, 500);
									pjQ.$('#pjTbsCheckTimeModal').modal('show');
								}
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		bindSearchForm: function(){
			var self = this,
				index = this.opts.index;
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			if(pjQ.$('#pjTbsMapCanvas').length > 0)
			{
				self.initMap.call(self);
			}
						
			if(pjQ.$('#pjTbsCalendarLocale').length > 0)
			{
				var fday = parseInt(pjQ.$('#pjTbsCalendarLocale').data('fday'), 10);
				moment.updateLocale('en', {
					months : pjQ.$('#pjTbsCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjTbsCalendarLocale').data('days').split("_"),
			        week: { dow: fday }
				});
			}
			if(pjQ.$('.date-pick').length > 0)
			{
				var currentDate = new Date();
				var datetimeOptions = {
					format: self.opts.momentDateFormat.toUpperCase(),
					locale: moment.locale('en'),
					allowInputToggle: true,
					minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()),
					ignoreReadonly: true,
					useCurrent: false
				};
				var $dt_from = pjQ.$('.pjTbsDateFrom'),
					$dt_to = pjQ.$('.pjTbsDateTo');
				$dt_from.datetimepicker(datetimeOptions).on('dp.change', function (e) {
					var $from = e.date.valueOf();
					if ($dt_to.val() != '') {
						var $to = $dt_to.data("DateTimePicker").date().valueOf();
						if ($to < $from) {
							$dt_to.val($dt_from.val());
						}
					}
					$dt_to.data("DateTimePicker").minDate(e.date);
				});
				
				$dt_to.datetimepicker(datetimeOptions).on('dp.change', function (e) {
					$dt_from.data("DateTimePicker").maxDate(e.date);
				});
			}
			pjQ.$('.time-pick').datetimepicker({
				format: self.opts.time_format,
				ignoreReadonly: true,
				allowInputToggle: true
			});
			if (pjQ.$('.pjTbs-spinner').length) {
		        var spinnerUpClass = 'pjTbs-spinner-up';
		        var spinnerDownClass = 'pjTbs-spinner-down';
		        var spinnerResult = '.pjTbs-spinner-result';

		        pjQ.$('.pjTbs-spinner').on('click', '.pjTbs-spinner', function(e) {
		            var $clickedSpinnerBtn = pjQ.$(this);
		            var $spinnerField = $clickedSpinnerBtn.siblings(spinnerResult);
		            var $spinnerValue = $spinnerField.val();
		            var $maxValue = parseInt($spinnerField.attr('data-max'), 10);
		           
		            if ($clickedSpinnerBtn.hasClass(spinnerUpClass)) {
		                $spinnerValue = $spinnerValue +++ 1;
		            } else if ($clickedSpinnerBtn.hasClass(spinnerDownClass)) {
		                $spinnerValue = $spinnerValue --- 1;
		            };
		            if($spinnerField.attr('name') == 'passengers')
		            {
		            	if ($spinnerValue <= 1) {
			                $spinnerValue = 1;
			            };
		            }else{
		            	if ($spinnerValue <= 0) {
			                $spinnerValue = '';
			            };
		            }
		            if ($spinnerValue >= $maxValue) {
		                $spinnerValue = $maxValue;
		            };
		            $spinnerField.val($spinnerValue);

		            e.preventDefault();
		        });
		    };
			if (validate) 
			{
				var $form = pjQ.$('#pjTbsSearchForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					errorElement: 'li',
					ignore: '',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							error.appendTo(element.parent().parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							element.parent().parent().removeClass('has-success').addClass('has-error');
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							element.parent().parent().parent().removeClass('has-success').addClass('has-error');
						}else{
							element.parent().removeClass('has-success').addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'booking_date' || element.attr('name') == 'booking_time' || element.attr('name') == 'return_date' || element.attr('name') == 'return_time' || element.attr('name') == 'distance')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else if(element.attr('name') == 'terms' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage'){
							element.parent().parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionSearchForm", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								var $booking_option = $form.find("input[name='booking_option']:checked").val(),
									$params = {
										booking_type: $form.find("input[name='booking_type']:checked").val(),
										booking_option: $booking_option,
										from_location_id: $form.find('select[name="from_location_id"]').val(),
										to_location_id: $form.find('select[name="to_location_id"]').val(),
										passengers: $form.find('input[name="passengers"]').val(),
										luggage: $form.find('input[name="luggage"]').val(),
										booking_date: $form.find('input[name="booking_date"]').val(),
										booking_time: $form.find('input[name="booking_time"]').val(),
										distance: $form.find('input[name="distance"]').val()
									};
								if ($booking_option == 'roundtrip') {
									$params.return_date = $form.find('input[name="return_date"]').val();
									$params.return_time = $form.find('input[name="return_time"]').val();
								}
								window.location.href = $form.attr('action') + "&" + pjQ.$.param($params);
							}else{
								if(data.code == '120') {
									pjQ.$('html, body').animate({
								        scrollTop: self.$container.offset().top
								    }, 500);
									pjQ.$('#pjTbsEarlierModal').modal('show');
								} else if(data.code == '130') {
									pjQ.$('html, body').animate({
								        scrollTop: self.$container.offset().top
								    }, 500);
									pjQ.$('#pjTbsCheckTimeModal').modal('show');
								}
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		bindCheckout: function(){
			var self = this,
				index = this.opts.index;
		
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			pjQ.$('.time-pick').datetimepicker({
				format: self.opts.time_format,
				ignoreReadonly: true,
				allowInputToggle: true
			});
			if (validate) 
			{
				var $form = pjQ.$('#pjTbsCheckoutForm_'+ self.opts.index);
				var remote_url = self.opts.folder + "index.php?controller=pjFrontEnd&action=pjActionCheckCaptcha";
				if(self.opts.session_id != '')
				{
					remote_url += "&session_id=" + self.opts.session_id;
				}
				$form.validate({
					rules: {
						"captcha": {
							remote: remote_url
						}
					},
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'c_flight_time')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else if(element.attr('name') == 'terms'){
							error.appendTo(element.parent().parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'c_flight_time')
						{
							element.parent().parent().removeClass('has-success').addClass('has-error');
						}else if(element.attr('name') == 'terms'){
							element.parent().parent().parent().removeClass('has-success').addClass('has-error');
						}else{
							element.parent().removeClass('has-success').addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'c_flight_time')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else if(element.attr('name') == 'terms'){
							element.parent().parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								if (!hashBang("#!/loadPreview")) 
								{
									self.loadPreview.call(self);
								}
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
				
				var $form = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				if($form.length > 0)
				{
					$form.validate({
						onkeyup: false,
						errorElement: 'li',
						errorPlacement: function (error, element) {
							error.appendTo(element.next().find('ul'));
						},
			            highlight: function(ele, errorClass, validClass) {
			            	var element = pjQ.$(ele);
			            	element.parent().removeClass('has-success').addClass('has-error');
			            },
			            unhighlight: function(ele, errorClass, validClass) {
			            	var element = pjQ.$(ele);
			            	element.parent().removeClass('has-error').addClass('has-success');
			            },
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionCheckLogin", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
								if(data.code == '200')
								{
									pjQ.$('#pjCssLoginModal').modal('hide');
									if (!hashBang("#!/loadCheckout")) 
									{
										self.loadCheckout.call(self);
									}
								}else{
									var $loginMessage = pjQ.$('#pjLoginMessage_'+ self.opts.index);
									$loginMessage.html(data.text);
									$loginMessage.parent().parent().show();
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
					
					if (typeof window.initializeTb == "undefined") 
					{
						window.initializeTb = function () 
						{
							self.googleLogin.call(self);
						}
						pjQ.$.getScript("https://apis.google.com/js/platform.js?onload=initializeTb");
					}else{
						self.googleLogin.call(self);
					}
				}
				var $form = pjQ.$('#pjCssForgotForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-success').addClass('has-error');
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-error').addClass('has-success');
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSendPassword", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
							var $forgotMessage = pjQ.$('#pjForgotMessage_'+ self.opts.index);
							if(data.code == '200')
							{
								$forgotMessage.addClass('text-success');
							}else{
								$forgotMessage.addClass('text-danger');
							}
							$forgotMessage.html(data.text);
							$forgotMessage.parent().parent().show();
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		googleLogin: function(){
			var self = this;
			
			gapi.load('auth2', function() {
				var auth2 = gapi.auth2.init({ 
					client_id: self.opts.google_signin_client_id 
				});
				var element = document.getElementById('gSignIn');
				auth2.attachClickHandler(element, {}, function(googleUser){
					var profile = googleUser.getBasicProfile();
		        	var post_data = {};
		        	post_data.email = profile.getEmail();
		        	post_data.fname = profile.getGivenName();
		        	post_data.lname = profile.getFamilyName();
		        	self.disableButtons.call(self);
		        	pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionGoogleLogin", "&session_id=", self.opts.session_id].join(""), post_data).done(function (data) {
						if(data.status == 'OK')
						{
							pjQ.$('#pjCssLoginModal').modal('hide');
							if (!hashBang("#!/loadCheckout")) 
							{
								self.loadCheckout.call(self);
							}
						}
						self.enableButtons.call(self);
					})
				}, function(){
					console.log('onfailure');
				});
			});
			gapi.signin2.render('gSignIn', {
		        'scope': 'profile email',
		        'width': 200,
		        'height': 42,
		        'longtitle': true,
		        'theme': 'dark'
		    });
		},
		loadPreview: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionPreview"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadFleets")) 
					{
						self.loadFleets.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindPreview.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindPreview: function(){
			var self = this,
				index = this.opts.index;
		
			if (validate) 
			{
				var $form = pjQ.$('#pjTbsPreviewForm_'+ self.opts.index);
				$form.validate({
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSaveBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
							if (data.code == "200") {
								self.getPaymentForm.call(self, data);
							} else if (data.code == "119") {
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var	params = {};
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			params.booking_id =  obj.booking_id;
			params.payment_method = obj.payment;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetPaymentForm"].join(""), params).done(function (data) {
				self.$container.html(data);
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='tbsPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='tbsAuthorize']").trigger('submit');
						break;
					case 'stripe':
						var $stripForm = self.$container.find("form[name='tbsStripe']");
						var session_id = $stripForm.find('input[name="stripe_session_id"]').val();
						var public_key = $stripForm.find('input[name="public_key"]').val();
						var stripe = Stripe(public_key);
						stripe.redirectToCheckout({
							sessionId: session_id
						}).then(function (result) {
							
						});
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.TaxiBooking = TaxiBooking;	
})(window);