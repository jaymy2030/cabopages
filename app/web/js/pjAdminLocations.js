var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateLocation = $("#frmCreateLocation"),
			$frmUpdateLocation = $("#frmUpdateLocation"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateLocation.length > 0 || $frmUpdateLocation.length > 0) 
		{
			var directionsService = new google.maps.DirectionsService();
			if($('#address').length > 0)
			{
				var autocomplete_pickup = new google.maps.places.Autocomplete($('#address')[0], {
					types: ["geocode"]
				});
				
				var address_field = document.getElementById('address');
				google.maps.event.addDomListener(address_field, 'keydown', function(e) { 
				    if (e.keyCode == 13) { 
				        e.preventDefault(); 
				    }
				});
			}
		}
		if ($frmCreateLocation.length > 0 && validate) {
			$frmCreateLocation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmUpdateLocation.length > 0 && validate) {
			$frmUpdateLocation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminLocations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocation&id={:id}"}
				          ],
				columns: [{text: myLabel.title, type: "text", sortable: true, editable: true, width: 400},
				          {text: myLabel.type, type: "text", sortable: true, editable: false, width: 200},
				         ],
				dataUrl: "index.php?controller=pjAdminLocations&action=pjActionGetLocation",
				dataType: "json",
				fields: ['name', 'type'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocationBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminLocations&action=pjActionSaveLocation&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminLocations&action=pjActionGetLocation", "name", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);