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
			optionsModal = new $.XirtModal($("#optionsModal")).init();

		},


		_initButtons: function() {

			var that = this;

			// Activate "Create item"-button
			$('.btn-create').click(function(e) {
				createModal.show();
			});

			// Active "Edit main properties"-option
			$(".btn-edit-main").click(function() {

				optionsModal.hide();
				that.grid.modifyModal(current);

			});

			// Active "Edit configuration"-option
			$(".btn-edit-config").click(function() {

				optionsModal.hide();
				that.grid.modifyConfigModal(current);

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

			this.element.xgrid({

				rowCount: [10, 15, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				url: "backend/moduleconfigurations/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"default": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-default " + ((data.default == 1) ? "active" : "inactive"),
								additionalAttributes : (data.default == 1) ? "disabled" : "",
								data : { id : data.id },
								label: "Toggle",
								icon : "globe"
							}

						]);

					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.id },
								label: "Modify",
								icon : "pencil"
							},

							{
								classNames : "command-delete",
								data : { id : data.id },
								label: "Trash",
								icon : "trash-o"
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

			this.element.bootgrid("reload");

		},

		_onload: function() {

			this.element.find(".command-edit").on("click", this._optionsModal);
			this.element.find(".command-default").on("click", $.proxy(this._setDefault, this));
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_optionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		modifyModal: function() {

			modifyModal.load({

				url	: "backend/moduleconfiguration/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "configuration_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		modifyConfigModal: function() {

			configModal.load({

				url	: "backend/moduleconfiguration/view/" + current,
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
	var current;
	var optionsModal, createModal, modifyModal, configModal;
	(new $.PageManager()).init();

});