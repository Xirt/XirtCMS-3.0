$(function() {

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

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();
			configModal = new $.XirtModal($("#configModal")).init();

		},


		_initButtons: function() {

			// Activate creation button
			$(".btn-create").click(function(e) {
				createModal.show();
			});

			// Activate toggle button (enable / disable page selection)
			$("#opt-toggle-page").on("change", function(el) {
				$("#page-selector").prop("disabled", $(this).prop("checked"));
				}
			);

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

			this.element.bootgrid({

				search: true,
				sorting: false,
				rowCount: [10, 25, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				ajax: true,
				url: "backend/widgets/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"ordering": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-order-down",
								data : { id : row.id },
								icon : "arrow-down",
							},

							{
								classNames : "command-order-up",
								data : { id : row.id },
								icon : "arrow-up",
							}

						]);

					},

					"published": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-published " + ((row.published == 1) ? "active" : "inactive"),
								data : { id : row.id },
								icon : "globe",
							}

						]);

					},

					"commands": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : row.id },
								icon : "pencil",
							},

							{
								classNames : "command-config",
								data : { id : row.id },
								icon : "gears",
							},

							{
								classNames : "command-delete",
								data : { id : row.id },
								icon : "trash-o",
							}

						]);

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
			this.element.find(".command-order-up").on("click", this._moveItemUp);
			this.element.find(".command-order-down").on("click", this._moveItemDown);
			this.element.find(".command-published").on("click", this._togglePublished);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/widget/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "widget_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyConfigModal: function() {

			configModal.load({

				url	: "backend/widget/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-config"), json, { prefix : "widget_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#settingsBox"), json.settings);

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
	var createModal, modifyModal, configModal;
	(new $.PageManager()).init();

});