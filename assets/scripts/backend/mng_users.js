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

			createModal		= new $.XirtModal($("#createModal")).init();
			modifyModal		= new $.XirtModal($("#modifyModal")).init();
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

						if (row.id == 1) {
							return	"<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-ref-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
									"<button type=\"button\" class=\"btn btn-xs btn-default command-attributes\" data-id=\"" + row.id + "\"><span class=\"fa fa-info\"></span></button> " +
									"<button type=\"button\" class=\"btn btn-xs btn-default command-password\" data-ref-id=\"" + row.id + "\"><span class=\"fa fa-unlock-alt\"></span></button> " +
									"<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" disabled=\"disabled\"><span class=\"fa fa-trash-o\"></span></button>";
						}

						return	"<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-ref-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
								"<button type=\"button\" class=\"btn btn-xs btn-default command-attributes\" data-id=\"" + row.id + "\"><span class=\"fa fa-info\"></span></button> " +
								"<button type=\"button\" class=\"btn btn-xs btn-default command-password\" data-ref-id=\"" + row.id + "\"><span class=\"fa fa-unlock-alt\"></span></button> " +
								"<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-ref-id=\"" + row.id + "\"><span class=\"fa fa-trash-o\"></span></button>";

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
			this.element.find(".command-delete").on("click", this._deleteItemModal);

		},

		_modifyContentModal: function() {

			modifyModal.load({
				url	   : "backend/user/view/" + $(this).data("ref-id"),
				onLoad : function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyAttributesModal: function() {

			attributesModal.load({
				url	   : "backend/user/view/" + $(this).data("id"),
				onLoad : function(json) {

					Xirt.populateForm($("#form-attr"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON(json.attributes);

				}

			});

		},

		_modifyPasswordModal: function() {

			passwordModal.load({
				url	   : "backend/user/view/" + $(this).data("ref-id"),
				onLoad : function(json) {

					Xirt.populateForm($("#form-password"), json, { prefix : "user_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_deleteItemModal: function() {

			var el = $(this);
			if (!el.data("ref-id")) {
				return;
			}

			BootstrapDialog.confirm({

				backdrop: false,
				title: "Confirm deletion",
				message: "Are you sure that you want to permanently delete item #" + Xirt.pad(el.data("ref-id").toString(), 5, "0") + "?",
				type: BootstrapDialog.TYPE_WARNING,
				callback: function(result) {

					if (result) {

						$.ajax({
							url: "backend/user/remove/" + el.data("ref-id"),
						}).done(function() {
							$("#grid-basic").bootgrid("reload");
						});

					}

				}

			});

		}

	};


	/*********************
	 * ATTRIBUTE MANAGER *
	 ********************/
	var AttributesManager = {

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
						AttributesManager._addTextField(setting, subContainer);
						break;

					case "textarea":
						AttributesManager._addTextareaField(setting, subContainer);
						break;

					case "select":
						AttributesManager._addSelectField(setting, subContainer);
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

		_addTextareaField : function(data, container) {

			$("<textarea class='form-control'></textarea>")
			.attr("id", "attr_" + data.name)
			.attr("name", "attr_" + data.name)
			.text(data.label)
			.val(data.value)
			.appendTo(container);

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var createModal	, modifyModal, attributesModal, passwordModal;
	(new $.PageManager()).init();

});