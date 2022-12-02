var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var 
			$frmCreateBooking = $('#frmCreateBooking'),
			$frmUpdateBooking = $('#frmUpdateBooking'),
			$frmBookingResend = $('#frmBookingResend'),
			$frmBookingCancel = $('#frmBookingCancel'),
			$dialogSelect = $("#dialogSelect"),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			chosen = ($.fn.chosen !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			};
	
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0,
			stop: function( event, ui ) {
				if($(this).attr('name')=='passengers')
				{
					calcPrice();
				}				
			}
		});
		if($frmUpdateBooking.length > 0)
		{
			var passengers = parseInt($( "#passengers" ).attr('data-value'), 10);
			var luggage = parseInt($( "#luggage" ).attr('data-value'), 10);
			if(passengers > 0)
			{
				$( "#passengers" ).spinner( "option", "max", passengers);
			}
			if(luggage > 0)
			{
				$( "#luggage").spinner( "option", "max", luggage);
			}
		}
		if (chosen) {
			$("#fleet_id").chosen();
			$("#c_country").chosen();
			$("#client_id").chosen();
		}
		function calcDistance() {
			var start = $('#from_location_id').find(':selected').attr('data-address');
			var end = $('#to_location_id').find(':selected').attr('data-address');
			if(start != '' && end != '')
			{
				var request = {
					origin: start,
				    destination: end,
				    travelMode: 'DRIVING'
				};
				directionsService.route(request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						var distanceinkm = parseInt(response.routes[0].legs[0].distance.value / 1000, 10);
						$('#distance').val(distanceinkm);
				    }
				});
			}else{
				$('#distance').val("");
			}
		}
		if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0) 
		{
			var directionsService = new google.maps.DirectionsService();
			
			if($('#pickup_address').length > 0)
			{
				var autocomplete_pickup = new google.maps.places.Autocomplete($('#pickup_address')[0], {
					types: ["geocode"]
				});
				
				var pickup_field = document.getElementById('pickup_address');
				google.maps.event.addDomListener(pickup_field, 'keydown', function(e) { 
				    if (e.keyCode == 13) { 
				        e.preventDefault(); 
				    }
				});
				google.maps.event.addListener(autocomplete_pickup, 'place_changed', function() {
					calcDistance();
				});
			}
			if($('#return_address').length > 0)
			{
				var autocomplete_return = new google.maps.places.Autocomplete($('#return_address')[0], {
					types: ["geocode"]
				});
				var return_field = document.getElementById('return_address');
				google.maps.event.addDomListener(return_field, 'keydown', function(e) { 
				    if (e.keyCode == 13) { 
				        e.preventDefault(); 
				    }
				});
				google.maps.event.addListener(autocomplete_return, 'place_changed', function() {
					calcDistance();
				});
			}
			if($('#client_id').length == 0)
			{
				$('.clientRequired').addClass('required');
			}
			$.validator.addMethod('positiveNumber', function (value) { 
				return Number(value) >= 0;
			}, myLabel.positive_number);
			
			$.validator.addMethod('maximumNumber', function (value, element) { 
				var data = parseInt($(element).attr('data-value'), 10);
				if(Number(value) > data)
				{
					return false;
				}else{
					return true;
				}
			}, myLabel.max_number);
			
			$frmCreateBooking.validate({
				rules: {
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					luggage: {
						positiveNumber: true,
						maximumNumber: true
					},
					c_email: {
						email: true,
						remote: 'index.php?controller=pjAdminBookings&action=pjActionCheckEmail'
					}
				},
				messages: {
					c_email: {
						remote: myLabel.email_already_exist
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'booking_date' || element.attr('name') == 'return_date' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function(form){
					$('.pjCheckDatetimeMsg').hide();
					var $booking_option = $(form).find("input[name='booking_option']:checked").val();
					if ($booking_option == 'roundtrip') {
						$.post("index.php?controller=pjAdminBookings&action=pjActionCheckDateTime", $(form).serialize()).done(function (data) {
							if (data.status == 'OK') {
								form.submit();
							} else {
								$('.pjCheckDatetimeMsg').show();
								return false;
							}
						});	
					} else {
						form.submit();
					}
				}
			});
			$frmUpdateBooking.validate({
				rules:{
					uuid: {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckID&id=" + $frmUpdateBooking.find("input[name='id']").val()
					},
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					luggage: {
						positiveNumber: true,
						maximumNumber: true
					},
					c_email: {
						email: true,
						remote: 'index.php?controller=pjAdminBookings&action=pjActionCheckEmail'
					}

				},
				messages:{
					uuid: {
						remote: myLabel.duplicated_id
					},
					c_email: {
						remote: myLabel.email_already_exist
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'booking_date' || element.attr('name') == 'return_date' || element.attr('name') == 'passengers' || element.attr('name') == 'luggage')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function(form){
					$('.pjCheckDatetimeMsg').hide();
					var $booking_option = $(form).find("input[name='booking_option']:checked").val();
					if ($booking_option == 'roundtrip') {
						$.post("index.php?controller=pjAdminBookings&action=pjActionCheckDateTime", $(form).serialize()).done(function (data) {
							if (data.status == 'OK') {
								form.submit();
							} else {
								$('.pjCheckDatetimeMsg').show();
								return false;
							}
						});	
					} else {
						form.submit();
					}
				}
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			function formatBookingOption(str, obj) {
				if (obj.booking_option == 'roundtrip') {
					return myLabel.yes;
				} else {
					return myLabel.no;
				}
			}
			var $grid = $("#grid").datagrid({
				buttons: [{type: "print", target: "_blank", url: "index.php?controller=pjAdminBookings&action=pjActionPrint&id={:id}"},
				          {type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"}
						  ],
				columns: [
				          {text: myLabel.client, type: "text", sortable: false, width:120},
				          {text: myLabel.fleet, type: "text", sortable: false, width:120},
				          {text: myLabel.distance, type: "text", sortable: false, width:70},
				          {text: myLabel.date_time, type: "text", sortable: false, width:110},
				          {text: myLabel.is_roundtrip, type: "text", sortable: true, editable: false, align: "center", width:90, renderer: formatBookingOption},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 80, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['client', 'fleet', 'distance', 'date_time', 'booking_option', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", render: false, ajax: false},
					   {text: myLabel.print, url: "javascript:void(0);", render: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onSelect: function (dateText, inst) {
					
				}
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("focusin", ".datetimepick", function (e) {
			var minDateTime = null;
			if($frmCreateBooking.length > 0)
			{
				minDateTime = new Date();
			}
			var	$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5,
					minDateTime: minDateTime
			};
			$(this).datetimepicker(o);
		}).on("focusin", ".timepick", function (e) {
			var minDateTime, maxDateTime,
				$this = $(this),
				custom = {},
				o = {
					timeFormat: $this.attr("lang"),
					stepMinute: 5,
					timeOnly: true
				};
			$(this).datetimepicker(o);
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".pj-button-detailed").trigger("click");
			if (chosen) {
				$("#pickup_id").val('').trigger("liszt:updated");
				$("#search_dropoff_id").val('').trigger("liszt:updated");
			}
			$('#date').val('');
			$('#email').val('');
			$('#name').val('');
			$('#phone').val('');
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}
		}).on("change", "#fleet_id", function (e) {
			
			var passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10),
				luggage = parseInt($('#fleet_id').find(':selected').attr('data-luggage'), 10),
				curr_passengers = parseInt($('#passengers').val(),10),
				curr_luggage = parseInt($("#luggage").val(), 10);
			if(passengers > 0)
			{
				$('#tr_max_passengers').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers" ).spinner( "option", "max", passengers);
				if(curr_passengers > passengers)
				{
					$( "#passengers" ).val("");
				}
				$( "#passengers" ).attr('data-value', passengers);
			}
			if(luggage > 0)
			{
				$('#tr_max_luggage').html("("+myLabel.maximum+" "+luggage+")");
				$( "#luggage").spinner( "option", "max", luggage);
				if(curr_luggage > luggage)
				{
					$( "#luggage").val("");
				}
				$( "#luggage" ).attr('data-value', luggage);
			}
			getExtras();
		}).on("click", ".pjAvailExtra", function (e) {
			calcPrice();
		}).on("change", "#client_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}

			$('#pjSbNewClientWrapper').find('input').val("");
			$('#pjSbNewClientWrapper').find('select').val("");

			if ($(this).val() != '') {
				$('#pjFdEditClient').css('display', 'block');
				var href = $('#pjFdEditClient').attr('data-href');
				href = href.replace("{ID}", $(this).val());
				$('#pjFdEditClient').attr('href', href);
				$('#pjSbNewClientWrapper').hide();
				$('#pjSbNewClientWrapper').find('.clientRequired').removeClass('required');
			}
			else {
				$('#pjFdEditClient').css('display', 'none');
				$('#pjSbNewClientWrapper').show();
				$('#pjSbNewClientWrapper').find('.clientRequired').addClass('required');
			}
		}).on("change", 'input[type=radio][name=booking_type]', function (e) {
			if (this.value == 'from') {
		        $('#pjTbDropoffTitle').show();
		        $('#pjTbPickupTitle').hide();
		    }else {
		    	$('#pjTbDropoffTitle').hide();
		        $('#pjTbPickupTitle').show();
		    }
		}).on("change", 'input[type=radio][name=booking_option]', function (e) {
			calcPrice();
			if (this.value == 'roundtrip') {
				$('.pjReturnDateTime').show();
				$('#return_date').addClass('required');
			} else {
				$('.pjReturnDateTime').hide();
				$('#return_date').removeClass('required');
			}
		}).on("change", '#from_location_id', function (e) {
			$.get("index.php?controller=pjAdminBookings&action=pjActionGetToLocations", {from_location_id: $(this).val()}).done(function (data) {
				$('#pjTbToLocationContainer').html(data);
			});	
		}).on("change", '#to_location_id', function (e) {
			calcDistance();	
			var $frm = $(this).closest('form');
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetFleets", $frm.serialize()).done(function (data) {
				$('#pjTbVehicleContainer').html(data);
				$("#fleet_id").chosen();
			});	
		});
		
		$("#grid").on("click", 'a.pj-paginator-action:last', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&record=' + booking_id,'_blank');
			}	
			return false;
		});
		function getExtras()
		{
			var $frm = null;
			if($frmCreateBooking.length > 0)
			{
				$frm = $frmCreateBooking;
			}
			if($frmUpdateBooking.length > 0)
			{
				$frm = $frmUpdateBooking;
			}
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetExtras", $frm.serialize()).done(function (data) {
				$('#extraBox').html(data);
				calcPrice();
			});	
		}
		function calcPrice()
		{
			var passengers = $('#passengers').val() != "" ? parseInt($('#passengers').val(), 10) : 0;
			var fleet_id = $('#fleet_id').val() != "" ? parseInt($('#fleet_id').val(), 10) : 0;
			var distance = $('#distance').val() != "" ? parseFloat($('#distance').val()) : 0;
			
			if(passengers > 0 && fleet_id > 0 && distance > 0)
			{
				if($('.pjAvailExtra').length > 0)
				{
					var params = $('.pjAvailExtra').serializeArray();
					params.push({name: "fleet_id", value: fleet_id});
					params.push({name: "passengers", value: passengers});
					params.push({name: "distance", value: distance});
				}else{
					var params = {};
					params.fleet_id = fleet_id;
					params.passengers = passengers;
					params.distance = distance;
				}
				var $frm = null;
				if($frmCreateBooking.length > 0)
				{
					$frm = $frmCreateBooking;
				}
				if($frmUpdateBooking.length > 0)
				{
					$frm = $frmUpdateBooking;
				}
				$.post(["index.php?controller=pjAdminBookings&action=pjActionCalPrice"].join(""), $frm.serialize()).done(function (data) {
					if(parseFloat(data.subtotal) > 0)
					{
						$('#sub_total').val((data.subtotal).toFixed(2));
						$('#tax').val((data.tax).toFixed(2));
			    		$('#total').val((data.total).toFixed(2));
			    		$('#deposit').val((data.deposit).toFixed(2));
					}else{
						$('#sub_total').val("");
						$('#tax').val("");
						$('#total').val("");
						$('#deposit').val("");
					}
				}).fail(function () {
					$('#sub_total').val("");
					$('#tax').val("");
					$('#total').val("");
					$('#deposit').val("");
				});
			}else{
				$('#sub_total').val("");
				$('#tax').val("");
				$('#total').val("");
				$('#deposit').val("");
			}
		}
		
		if($frmBookingResend.length > 0 || $frmBookingCancel.length > 0)
		{
			attachTinyMce.call(null);
		}
		
		function attachTinyMce(options) {
			if (window.tinymce !== undefined) {
				tinymce.EditorManager.editors = [];
				var defaults = {
					selector: "textarea.mceEditor",
					theme: "modern",
					width: 550,
					height: 330,
					plugins: [
				         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
				         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				         "save table contextmenu directionality emoticons template paste textcolor"
				    ],
				    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
				};
				
				var settings = $.extend({}, defaults, options);
				
				tinymce.init(settings);
			}
		}
		
	});
})(jQuery_1_8_2);