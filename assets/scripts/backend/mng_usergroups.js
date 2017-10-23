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
					usergroup_name: { required: true, maxlength: 128 },
					usergroup_authorization_level: { required: true, maxlength: 4, digits: true }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					usergroup_name: { required: true, maxlength: 128 },
					usergroup_authorization_level: { required: true, maxlength: 4, digits: true }
				}

			});

		},


		_initModals: function(initializedEditors) {

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();

		},


		_initButtons: function() {

			// Activate creation button
			$(".btn-create").click(function(e) {
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
				url: "backend/usergroups/view",
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
								data : { id : row.id },
								icon : "pencil",
							},

							{
								additionalAttributes : parseInt(row.users) ? "disabled=\"disabled\"" : "",
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
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/usergroup/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "usergroup_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/usergroup/remove/" + reference,
					reference,
					this
				);

			}

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var createModal, modifyModal;
	(new $.PageManager()).init();

});