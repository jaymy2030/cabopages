var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateFleet = $("#frmCreateFleet"),
			$frmUpdateFleet = $("#frmUpdateFleet"),
			$dialogDelete = $("#dialogDeleteImage"),
			multiselect = ($.fn.multiselect !== undefined),
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			remove_arr = new Array();
		
		$("#frmCreateFleet .field-int").spinner({
			min: 0
		});
		$("#frmUpdateFleet .field-int").spinner({
			min: 0
		});
		if (multiselect) {
			$("#extra_id").multiselect({
				noneSelectedText: myLabel.choose
			});
			$(".pj-multieselect").multiselect({
				noneSelectedText: myLabel.choose
			});
		}
		function setPrices()
		{
			var index_arr = new Array();
				
			$('#pjTbPriceTable').find(".pjTbPriceRow").each(function (index, row) {
				index_arr.push($(row).attr('data-index'));
			});
			$('#index_arr').val(index_arr.join("|"));
		}
		if ($frmCreateFleet.length > 0) {
			$.validator.addMethod('not_smaller_than', function (value, element, param) {
		        return + $(element).val() > + $(param).val();
		    });
			$frmCreateFleet.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					setPrices();
					form.submit();
					return false;
				}
			});
		}
		if ($frmUpdateFleet.length > 0) {
		    $.validator.addMethod('not_smaller_than', function (value, element, param) {
		        return + $(element).val() > + $(param).val();
		    });
			$frmUpdateFleet.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					setPrices();
					form.submit();
				}
			});
		}
		
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[lbsApp.locale.button.yes] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[lbsApp.locale.button.no] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function formatImage(val, obj) {
			var src = val != null ? val : 'app/web/img/backend/no-image.png';
			return ['<a href="index.php?controller=pjAdminFleets&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 100px" /></a>'].join("");
		}
		if ($("#grid").length > 0 && datagrid) {
			
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminFleets&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminFleets&action=pjActionDeleteFleet&id={:id}"}
				          ],
				columns: [{text: myLabel.thumb, type: "text", sortable: false, editable: false, renderer: formatImage, width: 110},
				          {text: myLabel.fleet, type: "text", sortable: true, width: 190, editable: true, editableWidth: 170},
				          {text: myLabel.passengers, type: "text", sortable: true, width: 100, editable: true, editableWidth: 70},
				          {text: myLabel.luggage, type: "text", sortable: true, width: 80, editable: true, editableWidth: 70},
				          {text: myLabel.status, type: "select", sortable: true, width: 100, editable: true, editableWidth: 90,options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminFleets&action=pjActionGetFleet",
				dataType: "json",
				fields: ['thumb_path', 'fleet', 'passengers', 'luggage', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminFleets&action=pjActionDeleteFleetBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminFleets&action=pjActionStatusFleet", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminFleets&action=pjActionExportFleet", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminFleets&action=pjActionSaveFleet&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("click", ".btnAddPrice", function () {
			var index = 'sub_new_' + Math.ceil(Math.random() * 999999);
			var subindex = 'sub_new_' + Math.ceil(Math.random() * 999999);
			var $c = $("#pjTbPriceTableClone").clone();
			
			var	r = $c.html().replace(/\{INDEX\}/g, index);
			r = r.replace(/\{SUBINDEX\}/g, subindex);
			
			$('#pjTbPriceContainer').append(r);
		}).on("click", ".btnAddSubPrice", function () {
			var index = $(this).attr('data-index');
			var $c = $("#pjTbSubPriceClone tbody").clone();
			var subindex = 'sub_new_' + Math.ceil(Math.random() * 999999);
			
			var	r = $c.html().replace(/\{INDEX\}/g, index);
			r = r.replace(/\{SUBINDEX\}/g, subindex);
			
			$(this).closest("tbody").find("table").find("tbody").append(r);
		}).on("click", ".lnkRemoveMainPrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $table = $(this).closest("table");
			$table.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$table.remove();
			});			
			return false;
		}).on("click", ".lnkRemoveSubPrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		});
	});
})(jQuery_1_8_2);