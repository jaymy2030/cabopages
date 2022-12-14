var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateClient = $("#frmCreateClient"),
			$frmUpdateClient = $("#frmUpdateClient"),
			chosen = ($.fn.chosen !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if (chosen) {
			$("#country_id").chosen();
		}
		if ($frmCreateClient.length > 0) {
			$frmCreateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminClients&action=pjActionCheckEmail"
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_used
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateClient.length > 0) {
			$frmUpdateClient.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminClients&action=pjActionCheckEmail&id=" + $frmUpdateClient.find("input[name='id']").val()
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_used
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		function formatEnquiries (str, obj) {
			if (parseInt(str, 10) > 0) {
				return '<a href="index.php?controller=pjAdminBookings&action=pjActionIndex&client_id='+obj.id+'">'+str+'</a>';
			} else {
				return '0';
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminClients&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminClients&action=pjActionDeleteClient&id={:id}"}
				          ],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: false},
				          {text: myLabel.email, type: "text", sortable: true, editable: true},
				          {text: myLabel.enquiries, type: "text", sortable: true, editable: false, align: 'center', renderer: formatEnquiries},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminClients&action=pjActionGetClient",
				dataType: "json",
				fields: ['name', 'email', 'cnt_bookings', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminClients&action=pjActionDeleteClientBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminClients&action=pjActionExportClient", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminClients&action=pjActionSaveClient&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "fname", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "fname", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminClients&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient");
			});
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
			$grid.datagrid("load", "index.php?controller=pjAdminClients&action=pjActionGetClient", "fname", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);