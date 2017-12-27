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
					menuitem_name: { required: true, maxlength: 128 }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					menuitem_name: { required: true, maxlength: 128 }
				}

			});

			Form.validate("#form-config", {

				currentModal: configModal,
				nextModal: configModal,
				grid: this.grid,
				rules: {
					menuitem_uri: { required: true, maxlength: 512 }
				}

			});

		},


		_initModals: function(initializedEditors) {

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();
			configModal = new $.XirtModal($("#configModal"), {resetForms:	false }).init();

		},


		_initButtons: function() {

			// Activate creation button
			$('.btn-create').click(function(e) {

				createModal.show();
				$("input[name=menu_id]").val(menuId);

			});

			// Activate tab tracing
			$('.nav-tabs a').click(function(e) {
				$("#inp-type").val($(this).attr('id').substr(5));
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

				searchable: false,
				sortable: false,
				rowCount: [-1],
				url: "backend/menuitems/view/" + menuId,
				formatters: {

					"item_id" : function (data) {

						return Xirt.pad(data.item_id, 5, "0");

					},

					"name": function(data) {

						var leadingSpaces = Xirt.lead('', data.level * 3);
						var nameValue = "<span>" + leadingSpaces + (data.level ? "<sup>L</sup>" : "") + data.name + "</span>";
						return (data.home == 1) ? nameValue + "<i><span class=\"fa indicator fa-home\"></span></i>" : nameValue;

					},

					"ordering": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-order-down",
								data : { id : data.item_id },
								label : "Move",
								icon : "arrow-down"
							},

							{
								classNames : "command-order-up",
								data : { id : data.item_id },
								label : "Move",
								icon : "arrow-up"
							}

						]);

					},

					"published": function(data) {

						return	XCMS.createButtons([

							{
								classNames : "command-published " + ((data.published == 1) ? "active" : "inactive"),
								data : { id : data.item_id },
								label : "Toggle",
								icon : "globe"
							}

						]);

					},

					"sitemap": function(data) {

						return	XCMS.createButtons([

							{
								classNames : "command-sitemap " + ((data.sitemap == 1) ? "active" : "inactive"),
								data : { id : data.item_id },
								label : "Toggle",
								icon : "sitemap"
							}

						]);

					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.item_id },
								label : "Modify",
								icon : "pencil"
							},

							{
								classNames : "command-config",
								data : { id : data.item_id },
								label : "Config",
								icon : "gears"
							},

							{
								classNames : "command-home",
								data : { id : data.item_id },
								label : "Toggle",
								icon : "home"
							},

							{
								classNames : "command-delete",
								data : { id : data.item_id },
								label : "Trash",
								icon : "trash-o"
							}

						]);

					}

				},

				onComplete: function(data) {

					$.each(["#create_parent_id", "#modify_parent_id"], function(index, el) {

						el = $(el);
						el.children("option[value!=0]").remove();

						$.each(data.rows, function(index, row) {

							row.level = parseInt(row.level);
							var indent = Xirt.lead("- ", (row.level) * 5);

							$("<option></option>")
							.text(indent + row.name)
							.val(row.item_id)
							.appendTo(el);

						});

					});

					that._onload();

				}

			});

			return this;

		},

		reload: function() {
			this.element.xgrid("reload");
		},

		_onload: function() {

			this.element.find(".command-edit").on("click", this._modifyContentModal);
			this.element.find(".command-config").on("click", this._modifyConfigModal);
			this.element.find(".command-order-up").on("click", $.proxy(this._moveItemUp, this));
			this.element.find(".command-order-down").on("click", $.proxy(this._moveItemDown, this));
			this.element.find(".command-home").on("click", $.proxy(this._toggleHome, this));
			this.element.find(".command-sitemap").on("click", this._toggleSitemap);
			this.element.find(".command-published").on("click", this._togglePublished);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/menuitem/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "menuitem_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyConfigModal: function() {

			configModal.load({

				url	: "backend/menuitem/view/" + $(this).data("id"),
				onLoad	: function(json) {

					$("#form-config").find("input").val("");

					linkCreator.update("menuitem_", json);
					Xirt.populateForm($("#form-config"), json, { prefix : "menuitem_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); },
						name: function (value) { return Xirt.ellipsis(value, 40); }
					}});

				}

			});

		},

		_moveItemUp: function(e) {

			var that = this;
			$.get("backend/menuitem/move_up/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_moveItemDown: function(e) {

			var that = this;
			$.get("backend/menuitem/move_down/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_toggleHome: function(e) {

			var that = this;
			$.get("backend/menuitem/set_home/" +  $(e.currentTarget).data("id"), function() {
				that.reload();
			});

		},

		_toggleSitemap: function() {

			var that = $(this);
			$.get("backend/menuitem/toggle_sitemap/" + that.data("id"), function () {
				that.toggleClass("inactive active");
			});

		},

		_togglePublished: function() {

			var that = $(this);
			$.get("backend/menuitem/toggle_published/" + that.data("id"), function () {
				that.toggleClass("inactive active");
			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/menuitem/remove/" + reference,
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

	var uri = window.location.href;
	var menuId = uri.substr(uri.lastIndexOf("/") + 1);

	var linkCreator = (new $.LinkPanel()).init();
	(new $.PageManager()).init();

});