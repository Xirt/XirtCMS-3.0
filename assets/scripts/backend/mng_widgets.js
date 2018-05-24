$(function() {

	/*****************
	 * CONFIGURATION *
	 *****************/
	var cacheExpiryOptions = [{
			label	: "Never",
			seconds	: 0
		}, {
			label	: "5 seconds",
			seconds	: 5
		}, {
			label	: "15 seconds",
			seconds	: 15
		}, {
			label	: "30 seconds",
			seconds	: 30
		}, {
			label	: "1 minute",
			seconds	: 60
		}, {
			label	: "5 minutes",
			seconds	: 300
		}, {
			label	: "15 minutes",
			seconds	: 900
		}, {
			label	: "30 minutes",
			seconds	: 1800
		}, {
			label	: "1 hour",
			seconds	: 3600
		}, {
			label	: "6 hours",
			seconds	: 21600
		}, {
			label	: "12 hours",
			seconds	: 43200
		}, {
			label	: "1 day",
			seconds	: 86400
		}, {
			label	: "7 days",
			seconds	: 604800
		}, {
			label	: "30 days",
			seconds	: 2592000
		}
	];

	
	/****************
	 * PAGE MANAGER *
	 ****************/
	$.PageManager = function() {
	};

	$.PageManager.prototype = {

		init: function() {

			this._initGrid();
			this._initModals();
			this._initForms();
			this._initButtons();
			this._initDatepickers();

			$("#widget_cache_slider").on("change input", function() {

				var display = $("#" + $(this).attr("data-display"));
				var holder = $("#" + $(this).attr("data-holder"));
				
				display.val(cacheExpiryOptions[$(this).val()].label);
				holder.val(cacheExpiryOptions[$(this).val()].seconds);				
				
			}).trigger("change");
			
			
			return this;

		},


		_initGrid: function() {

			this.grid = (new $.GridManager($("#grid-basic"))).init();

		},


		_initForms: function() {

			Form.validate("#form-create", {

				currentModal: createModal,
				nextModal: createModal,
				grid: this.grid,
				rules: {
					item_name: { required: true, maxlength: 128 }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					item_name: { required: true, maxlength: 128 }
				}

			});

			Form.validate("#form-config", {

				currentModal: configModal,
				nextModal: configModal,
				grid: this.grid

			});

		},


		_initModals: function(initializedEditors) {

			createModal	= new $.XirtModal($("#createModal")).init();
			modifyModal	= new $.XirtModal($("#modifyModal")).init();
			configModal	= new $.XirtModal($("#configModal")).init();
			optionsModal	= new $.XirtModal($("#optionsModal")).init();
			publishModal	= new $.XirtModal($("#publishModal")).init();
			priorityModal	= new $.XirtModal($("#priorityModal")).init();

		},


		_initButtons: function() {

			var that = this;

			// Activate publishing button
			$("#widget_published").on("change", function() {
				$(".publish-dates").toggle(this.checked);
			});

			// Activate creation button
			$(".btn-create").click(function(e) {
				createModal.show();
			});

			// Activate toggle button (enable / disable page selection)
			$("#opt-toggle-page").on("change", function(el) {
				$("#page-selector").prop("disabled", $(this).prop("checked"));
				}
			);

			// Active "Edit main properties"-option
			$(".btn-edit-main").click(function() {

				optionsModal.hide();
				that.grid.modifyModal(current);

			});

			// Active "Edit configuration"-option
			$(".btn-edit-attributes").click(function() {

				optionsModal.hide();
				that.grid.modifyConfigModal(current);

			});

			// Active "Edit priorities"-option
			$(".btn-edit-priorities").click(function() {

				optionsModal.hide();
				that.grid.modifyPrioritiesModal(current);

			});

			// Active "Edit publishing schedule"-option
			$(".btn-edit-status").click(function() {

				optionsModal.hide();
				that.grid.showModifyPublicationModal(current);

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


	/****************
	 * GRID MANAGER *
	 ****************/
	$.GridManager = function(element) {
		this.element = (element instanceof $) ? element : $(element);
	};

	$.GridManager.prototype = {

		init: function() {

			var that = this;

			this.element.xgrid({

				sortable: false,
				rowCount: [10, 15, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				url: "backend/widgets/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"published": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-published " + ((data.published == 1) ? "active" : "inactive"),
								icon : (data.published == 1) ? "fas fa-eye" : "far fa-eye-slash",
								data : { id : data.id },
								label : "Toggle",
							}

						]);

					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.id },
								label: "Modify",
								icon : "far fa-edit",
							},

							{
								classNames : "command-delete",
								data : { id : data.id },
								label: "Trash",
								icon : "far fa-trash-alt",
							}

						]);

					}

				},

				onComplete: function() {
					that._onload();
				}

			});

			return this;

		},

		reload: function() {
			this.element.xgrid("reload");
		},

		_onload: function() {

			this.element.find(".command-edit").on("click", this._optionsModal);
			this.element.find(".command-published").on("click", this._togglePublished);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_optionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		modifyModal: function() {

			modifyModal.load({

				url	: "backend/widget/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "widget_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		modifyConfigModal: function() {

			configModal.load({

				url	: "backend/widget/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-config"), json, { prefix : "widget_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#settingsBox"), json.settings);

				}

			});

		},

		modifyPrioritiesModal: function() {

			priorityModal.load({

				url	: "backend/widget/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-priorities"), json, { prefix : "widget_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		showModifyPublicationModal: function() {

			publishModal.load({

				url	: "backend/widget/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-publish"), json, { prefix : "widget_", converters: {

						id: function (value) { return Xirt.pad(value, 5, "0"); },

						dt_publish: function (value) {
							var dt = new Date(value);
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						},

						dt_unpublish: function (value) {

							var dt = new Date(value);
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						}

					}});

				}

			});

		},

		_moveItemUp: function() {

			new $.XirtMessage({
				message: "This functionality (move item down) is pending implementation."
			});

			//var el = $(this);
			//$.get("backend/widget/move_up/" + el.data("id"), function () {
			//	el.closest("tr").prev().before(el.closest("tr"));
			//});

		},

		_moveItemDown: function() {

			new $.XirtMessage({
				message: "This functionality (move item down) is pending implementation."
			});

			//var el = $(this);
			//$.get("backend/widget/move_down/" + el.data("id"), function () {
			//	(el.closest("tr")).next().after(el.closest("tr"));
			//});

		},

		_togglePublished: function() {

			var el = $(this);
			$.get("backend/widget/toggle_published/" + el.data("id"), function () {
				el.toggleClass("inactive active");
			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/widget/remove/" + reference,
					reference,
					this
				);

			}

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var current;
	var optionsModal, createModal, modifyModal, configModal, publishModal, priorityModal;
	(new $.PageManager()).init();

});