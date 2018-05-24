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
					menu_title: { required: true, maxlength: 128 }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					menu_title: { required: true, maxlength: 128 }
				}

			});

		},


		_initModals: function(initializedEditors) {

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();

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

			var that = this;

			this.element.xgrid({

				searchable: false,
				sortable: false,
				rowCount: [-1],
				url: "backend/menus/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"ordering": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-order-down",
								data : { id : data.id },
								label : "Move down",
								icon : "fas fa-arrow-alt-circle-down",
							},

							{
								classNames : "command-order-up",
								data : { id : data.id },
								label : "Move up",
								icon : "fas fa-arrow-alt-circle-up",
							}

						]);

					},

					"sitemap": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-sitemap " + ((data.sitemap == 1) ? "active" : "inactive"),
								data : { id : data.id },
								label : "Toggle",
								icon : "fas fa-sitemap",
							}

						]);

					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.id },
								label : "Modify",
								icon : "far fa-edit",
							},

							{
								classNames : "command-menu",
								data : { id : data.id },
								label : "Entries",
								icon : "fas fa-bars",
							},

							{
								classNames : "command-delete",
								data : { id : data.id },
								label : "Trash",
								icon : "far fa-trash-alt",
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

			this.element.find(".command-edit").on("click", this._modifyContentModal);
			this.element.find(".command-order-up").on("click", this._moveMenuUp);
			this.element.find(".command-order-down").on("click", this._moveMenuDown);
			this.element.find(".command-sitemap").on("click", this._toggleSitemap);
			this.element.find(".command-menu").on("click", this._navigateMenu);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/menus/menu/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "menu_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_moveMenuUp: function() {

			var el = $(this);
			$.get("backend/menus/menu/move_up/" + el.data("id"), function () {
				el.closest("tr").prev().before(el.closest("tr"));
			});

		},

		_moveMenuDown: function() {

			var el = $(this);
			$.get("backend/menus/menu/move_down/" + el.data("id"), function () {
				(el.closest("tr")).next().after(el.closest("tr"));
			});

		},

		_toggleSitemap: function() {

			var el = $(this);
			$.get("backend/menus/menu/toggle_sitemap/" + el.data("id"), function () {
				el.toggleClass("inactive active");
			});

		},

		_navigateMenu: function() {

			document.location.assign("backend/menuitems/" + $(this).data("id"));

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/menus/menu/remove/" + reference,
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