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
			optionsModal = new $.XirtModal($("#optionsModal")).init();
			attributesModal	= new $.XirtModal($("#attrModal")).init();
			passwordModal	= new $.XirtModal($("#passwordModal")).init();

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

			// Active "Edit user attributes"-option
			$(".btn-edit-attributes").click(function() {

				optionsModal.hide();
				that.grid.modifyAttributesModal(current);

			});

			// Active "Edit user password"-option
			$(".btn-edit-password").click(function() {

				optionsModal.hide();
				that.grid._modifyPasswordModal(current);

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

				rowCount: [10, 15, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				url: "backend/users/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"commands": function(data)
					{

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.id },
								label : "Modify",
								icon : "pencil"
							},

							{
								additionalAttributes : (data.id == 1) ? "disabled=\"disabled\"" : "",
								classNames : "command-delete",
								data : { id : data.id },
								label : "Trash",
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

			this.element.xgrid("reload");

		},

		_onload: function() {

			this.element.find(".command-edit").on("click", this._optionsModal);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_optionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		modifyModal: function() {

			modifyModal.load({

				url	: "backend/user/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		modifyAttributesModal: function() {

			attributesModal.load({

				url	: "backend/user/view/" + current,
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

				url	: "backend/user/view/" + current,
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
	var subject;
	var optionsModal, createModal, modifyModal, attributesModal, passwordModal;
	(new $.PageManager()).init();

});