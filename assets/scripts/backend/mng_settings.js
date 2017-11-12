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

			return this;

		},


		_initGrid: function() {

			this.grid = (new $.GridManager($("#grid-basic"))).init();

		},


		_initModals: function(initializedEditors) {

			modifyModal = new $.XirtModal($("#modifyModal")).init();

		},


		_initForms: function() {

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					config_value: { maxlength: 128 }
				}

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
				rowCount: [10, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				ajax: true,
				url: "backend/settings/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"commands": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { name : row.name },
								icon : "pencil",
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

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/setting/view/" + $(this).data("name"),
				onLoad	: function(json) {
					Xirt.populateForm($("#form-modify"), json, { prefix : "setting_" });
				}

			});

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var modifyModal;
	(new $.PageManager()).init();

});