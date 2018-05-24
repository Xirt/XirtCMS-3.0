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
				that.grid.showModifyModal(current);

			});

			// Active "Edit user attributes"-option
			$(".btn-edit-attributes").click(function() {

				optionsModal.hide();
				that.grid.showAttributesModal(current);

			});

			// Active "Edit user password"-option
			$(".btn-edit-password").click(function() {

				optionsModal.hide();
				that.grid.showModifyPasswordModal(current);

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
								icon : "far fa-edit"
							},

							{
								additionalAttributes : (data.id == 1) ? "disabled=\"disabled\"" : "",
								classNames : "command-delete",
								data : { id : data.id },
								label : "Trash",
								icon : "far fa-trash-alt"
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

			this.element.find(".command-edit").on("click", this._showOptionsModal);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_showOptionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		showModifyModal: function() {

			modifyModal.load({

				url	: "backend/users/user/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		showAttributesModal: function() {

			attributesModal.load({

				url	: "backend/users/user/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-attr"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#attrBox"), json.attributes);

				}

			});

		},

		showModifyPasswordModal: function() {

			passwordModal.load({

				url	: "backend/users/user/view/" + current,
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
					"backend/users/user/remove/" + reference,
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