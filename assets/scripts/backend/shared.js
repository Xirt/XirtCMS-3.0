/********************
 * GRID ROW REMOVAL *
 *******************/
function confirmRemoval(url, id, grid) {

	new $.XirtConfirmation({

		title: "Confirm deletion",
		message: "Are you sure that you want to permanently delete item #" + Xirt.pad(id.toString(), 5, "0") + "?",
		type: "warning",
		callback: function(result) {

			if (result) {

				$.ajax(url).done(function() {
					grid.reload();
				});

			}

		}

	});

}


/*********************
 * ATTRIBUTE MANAGER *
 ********************/
var AttributesManager = {

	createFromJSON : function(target, data) {

		var container = $(target).empty();
		$.each(data, function(index, setting) {

			var group = $("<div class=\"form-group row\"></div>").appendTo(container);

			$("<label class=\"col-sm-4 col-form-label col-form-label-sm\"></label>")
				.attr("for", "attr_" + setting.name)
				.text(setting.label)
				.appendTo(group);

			var subContainer = $("<div class=\"col-sm-8\"></div>").appendTo(group);

			switch (setting.type) {

				case "text":
					AttributesManager._addTextField(setting, subContainer);
					break;

				case "date":
					AttributesManager._addDateField(setting, subContainer);
					break;

				case "textarea":
					AttributesManager._addTextareaField(setting, subContainer);
					break;

				case "select":
					AttributesManager._addSelectField(setting, subContainer);
					break;

			}

		});

	},

	_addTextField : function(data, container) {

		$("<input type='text' class='form-control form-control-sm' />")
			.attr("id", "attr_" + data.name)
			.attr("name", "attr_" + data.name)
			.text(data.label)
			.val(data.value)
			.appendTo(container);

	},

	_addDateField : function(data, container) {

		var dateGroup = $("<div class='input-group date'>");

			var field = $("<input type='text' class='form-control form-control-sm datepicker' />")
				.attr("id", "attr_" + data.name)
				.attr("name", "attr_" + data.name)
				.attr("readonly", "readonly")
				.appendTo(dateGroup)
				.text(data.label)
				.val(data.value)
				.datepicker({
					weekStart: 1,
					autoclose: true,
					format: "dd/mm/yyyy"
				});

			$("<div class='input-group-addon'><i class='fa fa-calendar'></i></div>")
				.on("click", function() { field.datepicker("show"); })
				.appendTo(dateGroup);

		dateGroup.appendTo(container);

	},

	_addTextareaField : function(data, container) {

		$("<textarea class='form-control form-control-sm'></textarea>")
			.attr("id", "attr_" + data.name)
			.attr("name", "attr_" + data.name)
			.text(data.label)
			.val(data.value)
			.appendTo(container);

	},

	_addSelectField : function(data, container) {

		var el = $("<select class='form-control custom-select form-control-sm'></select>")
			.attr("id", "attr_" + data.name)
			.attr("name", "attr_" + data.name)
			.appendTo(container);

		$.each(data.options, function(index, option) {

			$("<option></option")
				.html(option.name)
				.val(option.value)
				.appendTo(el);

		});

		el.val(data.value);

	}

};