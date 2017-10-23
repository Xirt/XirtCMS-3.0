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
					item_name: { required: true, maxlength: 128 },
					item_type: { required: true }
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
				nextModal: configModal

			});

		},


		_initModals: function(initializedEditors) {

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();
			configModal = new $.XirtModal($("#configModal")).init();

		},


		_initButtons: function() {

			// Activate creation button
			$('.btn-create').click(function(e) {
				createModal.show();
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

			this.element.bootgrid({

				search: true,
				sorting: true,
				rowCount: [10, 25, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				ajax: true,
				url: "backend/moduleconfigurations/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"default": function(column, row) {

						if (row.default == 1) {
							return "<button type=\"button\" class=\"btn btn-xs btn-default command-default active\" disabled=\"disabled\"><span class=\"fa fa-globe\"></span></button>";
						}

						return "<button type=\"button\" class=\"btn btn-xs btn-default command-default inactive\" data-id=\"" + row.id + "\"><span class=\"fa fa-globe\"></span></button>";

					},

					"commands": function(column, row) {

						return	"<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-config\" data-id=\"" + row.id + "\"><span class=\"fa fa-gears\"></span></button> " +
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
			this.element.find(".command-default").on("click", $.proxy(this._setDefault, this));
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/moduleconfiguration/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "configuration_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyConfigModal: function() {

			configModal.load({

				url	: "backend/moduleconfiguration/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-config"), json, { prefix : "configuration_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#settingsBox"), json.settings);

				}

			});

		},

		_setDefault: function(e) {

			var that = this;
			var reference = $(e.currentTarget).data("id");

			$.get("backend/moduleconfiguration/toggle_default/" + reference, function() {
				that.reload();
			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/moduleconfiguration/remove/" + reference,
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