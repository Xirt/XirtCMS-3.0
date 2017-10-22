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

			this.element.bootgrid({

				search: false,
				sorting: false,
				rowCount: [-1],
				ajax: true,
				url: "backend/menus/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"ordering": function(column, row) {
						return	"<button type=\"button\" class=\"btn btn-xs btn-default command-order-down\" data-id=\"" + row.id + "\"><span class=\"fa fa-arrow-down\"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-order-up\" data-id=\"" + row.id + "\"><span class=\"fa fa-arrow-up\"></span></button>";
					},

					"sitemap": function(column, row) {
						return	"<button type=\"button\" class=\"btn btn-xs btn-default command-sitemap " + ((row.sitemap == 1) ? "active" : "inactive") + "\" data-id=\"" + row.id + "\"><span class=\"fa fa-sitemap\"></span></button>";
					},

					"commands": function(column, row) {
						return	"<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
							"<button type=\"button\" class=\"btn btn-xs btn-default command-menu\" data-id=\"" + row.id + "\"><span class=\"fa fa-bars\"></span></button> " +
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
			this.element.find(".command-order-up").on("click", this._moveMenuUp);
			this.element.find(".command-order-down").on("click", this._moveMenuDown);
			this.element.find(".command-sitemap").on("click", this._toggleSitemap);
			this.element.find(".command-menu").on("click", this._navigateMenu);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/menu/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "menu_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_moveMenuUp: function() {

			var el = $(this);
			$.get("backend/menu/move_up/" + el.data("id"), function () {
				el.closest("tr").prev().before(el.closest("tr"));
			});

		},

		_moveMenuDown: function() {

			var el = $(this);
			$.get("backend/menu/move_down/" + el.data("id"), function () {
				(el.closest("tr")).next().after(el.closest("tr"));
			});

		},

		_toggleSitemap: function() {

			var el = $(this);
			$.get("backend/menu/toggle_sitemap/" + el.data("id"), function () {
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
					"backend/menu/remove/" + reference,
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