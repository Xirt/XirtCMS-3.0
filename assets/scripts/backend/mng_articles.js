$(function() {
	(new $.PageManager()).init();
});


/**
 * PAGE MANAGER
 */
(function($) {

	// Constructor
	$.PageManager = function() {
	};

	$.PageManager.prototype = {

		init: function() {

			this._initGrid();
			this._initEditor();
			this._initButtons();
			this._initDatepickers();

			return this;

		},


		_initGrid: function() {

			this.grid = (new $.GridManager($("#grid-basic"))).init();

		},


		_initEditor: function() {

			var that = this;

			tinymce.init($.extend({}, tinyMCE_Full, {
				height	: $(window).height() - 400,
				width	: "100%"
			})).then(function(editors) {
				that._initModals(editors);
				that._initForms();
			});

		},


		_initForms: function() {

			Form.validate("#form-create", {

				currentModal: createModal,
				nextModal: createModal,
				grid: this.grid,
				rules: {
					create_title: { required: true, maxlength: 256 }
				}

			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					create_title: { required: true, maxlength: 256 }
				}

			});

			Form.validate("#form-config", {

				currentModal: configModal,
				nextModal: configModal

			});

			Form.validate("#form-categories", {

				currentModal: categoriesModal,
				nextModal: categoriesModal

			});

			Form.validate("#form-publish", {

				currentModal: publishModal,
				nextModal: publishModal,
				grid: this.grid

			});

		},


		_initModals: function(initializedEditors) {

			createModal		= new $.XirtModal($("#createModal")).init();
			configModal		= new $.XirtModal($("#configModal")).init();
			publishModal	= new $.XirtModal($("#publishModal")).init();
			categoriesModal = new $.XirtModal($("#categoriesModal")).init();

			modifyModal	= new $.XirtModal($("#modifyModal"), {
				editors : initializedEditors
			}).init();

		},


		_initButtons: function() {

			// Activate publishing button
			$("#article_published").on("change", function() {
				$(".publish-dates").toggle(this.checked);
			});

			// Activate creation button
			$('.btn-create').click(function(e) {
				createModal.show();
			});

		},


		_initDatepickers: function() {

			// Activate fields
			$(".datepicker").datepicker({
				weekStart: 1,
				autoHide: true,
				autoPick: true,
				format: "dd/mm/yyyy"
			}).on('show.datepicker', function (e) {
				$(this).datepicker("setDate", $(this).val());
			});

			// Activate icons
			$(".input-group.date .input-group-addon").on("click", function(e) {

				$(this).siblings("input").datepicker("show");
				e.stopImmediatePropagation();

			});

		}

	};

}(jQuery));


/**
 * GRID MANAGER
 */
(function($) {

	$.GridManager = function(element) {
		this.element = (element instanceof $) ? element : $(element);
	};

	$.GridManager.prototype = {

		init: function() {

			var that = this;

			this.element.bootgrid({

				rowCount: [10, 25, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				converters: {
					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}
				},
				ajax: true,
				url: "backend/articles/view",
				formatters: {

					"published": function(column, row)
					{

						style = (row.published == 1) ? "active" : "inactive";
						return "<button type=\"button\" class=\"btn btn-xs btn-default command-publish " + style + "\" data-id=\"" + row.id + "\"><span class=\"fa fa-globe\"></span></button>";

					},

					"commands": function(column, row)
					{
						return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-config\" data-id=\"" + row.id + "\"><span class=\"fa fa-gears\"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-categories\" data-id=\"" + row.id + "\"><span class=\"fa fa-list-ul \"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-id=\"" + row.id + "\"><span class=\"fa fa-trash-o\"></span></button>";
					}

				}

			}).on("loaded.rs.jquery.bootgrid", $.proxy(this._onload, this));

			return this;

		},
		
		reload: function() {
			this.element.bootgrid("reload");
		},

		_onload: function() {

			this.element.find(".command-edit").on("click", this._modifyContentModal);
			this.element.find(".command-config").on("click", this._modifyConfigModal);
			this.element.find(".command-categories").on("click", this._modifyCategoriesModal);
			this.element.find(".command-publish").on("click", this._modifyPublicationModal);
			this.element.find(".command-delete").on("click", this._deleteItemModal);

		},

		_modifyContentModal: function() {

			Xirt.showSpinner();
			$("#form-modify")[0].reset();
			tinyMCE.activeEditor.setContent("");
			tinyMCE.activeEditor.setProgressState(true);

			// ... and display the result
			$.getJSON("backend/article/view/" + $(this).data("id"), function (json) {

				Xirt.hideSpinner();
				Xirt.populateForm($("#form-modify"), json, { prefix : "article_", converters: {
					id: function (value) { return Xirt.pad(value, 5, "0"); }
				}});

				modifyModal.show();
				tinyMCE.activeEditor.setContent(json.content);
				tinyMCE.activeEditor.setProgressState(false);
				tinyMCE.activeEditor.undoManager.clear();
				tinyMCE.activeEditor.setDirty(false);

			});

		},

		_modifyConfigModal: function() {

			Xirt.showSpinner();
			$.getJSON("backend/article/view/" + $(this).data("id"), function (json) {

				Xirt.hideSpinner();

				// Standard form population
				Xirt.populateForm($("#form-config"), json, { prefix : "article_", converters: {
					id: function (value) { return Xirt.pad(value, 5, "0"); }
				}});

				ArticleAttributes.createFromJSON(json.attributes);
				configModal.show();

			});

		},

		_modifyCategoriesModal: function() {

			Xirt.showSpinner();
			$.getJSON("backend/article/view/" + $(this).data("id"), function (json) {

				Xirt.hideSpinner();

				// Standard form population
				Xirt.populateForm($("#form-categories"), json, { prefix : "article_", converters: {
					id: function (value) { return Xirt.pad(value, 5, "0"); }
				}});

				categoriesModal.show();

			});

		},

		_modifyPublicationModal: function() {

			Xirt.showSpinner();
			$.getJSON("backend/article/view/" + $(this).data("id"), function (json) {

				// Standard form population
				Xirt.populateForm($("#form-publish"), json, { prefix : "article_", converters: {

					id: function (value) { return Xirt.pad(value, 5, "0"); },

					dt_publish: function (value) {
						dt = new Date(value);
						return ('0' + dt.getDate()).slice(-2) + "/"
							 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
							 + dt.getFullYear();
					},

					dt_unpublish: function (value) {

						dt = new Date(value);
						return ('0' + dt.getDate()).slice(-2) + "/"
							 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
							 + dt.getFullYear();
					}

				}});

				Xirt.hideSpinner();
				publishModal.show();

			});

		},

		_deleteItemModal: function() {

			var el = $(this);

			BootstrapDialog.confirm({

				backdrop: false,
				title: "Confirm deletion",
				message: "Are you sure that you want to permanently delete item #" + Xirt.pad(el.data("id").toString(), 5, "0") + "?",
				type: BootstrapDialog.TYPE_WARNING,
				callback: function(result) {

					if (result) {

						$.ajax({
							url: "backend/article/remove/" + el.data("id"),
						}).done(function() {
							$("#grid-basic").bootgrid("reload");
						});

					}

				}

			});

		}

	};

}(jQuery));


/**
 * XIRT MODAL
 */
(function ($) {

	// Constructor
	$.XirtModal = function(element, options) {

		this.element = (element instanceof $) ? element : $(element);
		this.options = $.extend({}, {
			resetForms: true,
			editors:	[],
			backdrop:   false,
			keyboard:   false
		}, options);

	};

	$.XirtModal.prototype = {

		init: function () {

			var that = this;

			// Create modal
			$(this.element).modal({
				backdrop: this.options.backdrop,
				keyboard: this.options.keyboard
			}).hide();

			// Fix for slide-effect
			this.element.modal("hide");

			// Activate closure button
			this.element.find(".btn-close").on("click", function() {

				// Check for content changes
				var isDirty = (that._initState != that.element.find("form").serialize());
				$.each(that.options.editors, function(i, editor) {
					isDirty = editor.isDirty() ? true : isDirty;
				});

				if (isDirty) {

					BootstrapDialog.confirm({

						backdrop: false,
						title: "Confirm cancellation",
						message: "Are you sure that you want to close without saving?",
						type: BootstrapDialog.TYPE_WARNING,
						callback: function(result) {

							if (result) {
								that.hide();
							}

						}

					});

					return;

				}

				that.hide();

			});

			return this;

		},

		show: function() {

			this._initState = this.element.find("form").serialize();
			this.element.modal("show");
			return this;

		},

		hide: function() {

			// Optionally reset form values
			if (this.options.resetForms) {
				this.element.find("input").val("");
			}

			// Show the modal
			this.element.modal("hide");
			return this;

		}

	};

}(jQuery));


var ArticleAttributes = {

	createFromJSON : function(data) {

		var container = $("#attrBox").empty();

		$.each(data, function(index, setting) {

			var group = $("<div class=\"form-group\"></div>").appendTo(container);

			$("<label class=\"col-sm-4 control-label\"></label>")
			.attr("for", "attr_" + setting.name)
			.text(setting.label)
			.appendTo(group);

			var subContainer = $("<div class=\"col-sm-7\"></div>").appendTo(group);

			switch (setting.type) {

				case "text":
					ArticleAttributes._addTextField(setting, subContainer);
					break;

				case "date":
					ArticleAttributes._addDateField(setting, subContainer);
					break;

				case "textarea":
					ArticleAttributes._addTextareaField(setting, subContainer);
					break;

				case "select":
					ArticleAttributes._addSelectField(setting, subContainer);
					break;

			}

		});

	},

	_addTextField : function(data, container) {

		$("<input type='text' class='form-control' />")
		.attr("id", "attr_" + data.name)
		.attr("name", "attr_" + data.name)
		.text(data.label)
		.val(data.value)
		.appendTo(container);

	},

	_addDateField : function(data, container) {

		var dateGroup = $("<div class='input-group date'>");

			var field = $("<input type='text' class='form-control datepicker' />")
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

		$("<textarea class='form-control'></textarea>")
		.attr("id", "attr_" + data.name)
		.attr("name", "attr_" + data.name)
		.text(data.label)
		.val(data.value)
		.appendTo(container);

	}

};