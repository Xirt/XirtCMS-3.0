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


/**************
 * LINK PANEL *
 *************/
$.LinkPanel = function() {
};

$.LinkPanel.prototype = {

	init: function() {

		var that = this;

		// [Input] Link existence check
		$("#inp-public_url").on("change", function() {
			that._checkLink($(this).val());
		});

		// [Select] Update module details
		$("#sel-module-type").on("change", function() {

			var moduleType = $(this).val();
			that._updateModuleMenu(moduleType, that.getModuleTarget().module);
			that._updateModuleConfigurations(moduleType);

		});

		// [Button] Editor activation
		$("#btn-editor").on("click", function() {
			$("#box-link").slideToggle({ duration : 200 });
		});

		return this;

	},

	getModuleTarget: function() {

		return {
			target_url	: $("#inp-target_url").val(),
			module_type	: $("#sel-module-type").val(),
			module		: parseInt($("#sel-module-config").val()),
		};

	},

	update: function(prefix, data) {

		// Populate field
		if (data.target_url) {

			var parts = data.target_url.split("/");
			$.extend(data, { module_type : parts.length ? parts[0] : null });

		}

		// Populate form
		Xirt.populateForm($("#box-link"), data, { prefix : prefix });

		// Update GUI
		this._updateModuleConfigurations(data.module_type, data.module);
		this._updateModuleView(data.target_url, data.public_url);
		this._updateModuleMenu(data.module_type);
		this._updateTabView(data.type);


	},

	_updateModuleConfigurations : function(type, module) {

		var target = $("#sel-module-config").empty();

		// Retrieve module configurations for given module type
		$.post("backend/moduleconfigurations/view", { moduleType : type, sort : "name" }, function(json) {

			$.each(json.rows, function(key, data) {

				$("<option></option")
					.text(data.name)
					.val(data.id)
					.appendTo(target);

			});

			target.val(module ? module : target.find("option:first").val());

		}, "json");

	},

	_updateModuleMenu : function(type) {

		var that = this;
		var target = $("#box-params").empty();

		// Retrieve module menu parameters for given module type
		$.post("backend/module/view_menu_parameters/" + type, function(json) {

			AttributesManager.createFromJSON(target, json);
			var parts = that.getModuleTarget().target_url.split("/");

			// Activate events on new items
			target.find("[name*='attr_']").each(function(key) {

				$(this).on("change keyup", $.proxy(that._updateLink, that));
				if (parts[0] == type && key < parts.length) {
					$(this).val(parts[key + 1]);
				}

			});

			that._updateLink();

		}, "json");

	},

	_updateModuleView: function(publicURL, targetURL) {

		// Toggle visibility
		$("#box-link").toggle(publicURL ? false : true);
		$("#box-relations").hide();

		// Trigger additional updates
		this._checkLink(targetURL);
		this._updateLink();

	},

	_updateTabView(linkType) {
		$("#type-" + (linkType ? linkType : "internal")).tab("show");
	},

	_checkLink : function(link) {

		var options = {duration : 200};
		$.post("backend/route/convert_public_url", { uri : link }, function (json) {
			json.success ? $("#box-relations").slideDown(options) : $("#box-relations").slideUp(options);
		}, "json");

	},

	_updateLink : function() {

		var parts = [this.getModuleTarget().module_type];
		$.each($("#box-params").find("[name*='attr_']"), function() {
			parts.push($(this).val());
		});

		$("#inp-target_url").val(parts.join("/"));

	}

};
	

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