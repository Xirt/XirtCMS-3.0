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
					user_real_name: { required: true, maxlength: 64 },
					user_username: { required: true, minlength: 4, maxlength: 16, alpha_dashdot: true},
					user_email: { required: true, maxlength: 64, email: true }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					user_real_name: { required: true, maxlength: 64 },
					user_username: { required: true, minlength: 4, maxlength: 16, alpha_dashdot: true},
					user_email: { required: true, maxlength: 64, email: true }
				}

			});

			Form.validate("#form-attr", {

				currentModal: attributesModal,
				nextModal: attributesModal

			});

			Form.validate("#form-password", {

				currentModal: passwordModal,
				nextModal: passwordModal,
				rules: {
					user_password: { required: true, minlength: 8, maxlength: 64, pwstrength: true },
					user_password_check: { required: true, equalTo: "#user_password" }
				}

			});

		},


		_initModals: function(initializedEditors) {

			createModal	= new $.XirtModal($("#createModal")).init();
			modifyModal	= new $.XirtModal($("#modifyModal")).init();
			attributesModal	= new $.XirtModal($("#attrModal")).init();
			passwordModal	= new $.XirtModal($("#passwordModal")).init();

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

				rowCount: [10, 25, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				ajax: true,
				url: "backend/users/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"commands": function(column, row)
					{

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : row.id },
								icon : "pencil",
							},

							{
								classNames : "command-attributes",
								data : { id : row.id },
								icon : "user-circle-o",
							},

							{
								classNames : "command-password",
								data : { id : row.id },
								icon : "unlock-alt",
							},

							{
								additionalAttributes : (row.id == 1) ? "disabled=\"disabled\"" : "",
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
			this.element.find(".command-attributes").on("click", this._modifyAttributesModal);
			this.element.find(".command-password").on("click", this._modifyPasswordModal);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/user/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyAttributesModal: function() {

			attributesModal.load({

				url	: "backend/user/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-attr"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#attrBox"), json.attributes);

				}

			});

		},

		_modifyPasswordModal: function() {

			passwordModal.load({

				url	: "backend/user/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-password"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/user/remove/" + reference,
					reference,
					this
				);

			}

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var createModal, modifyModal, attributesModal, passwordModal;
	(new $.PageManager()).init();

});