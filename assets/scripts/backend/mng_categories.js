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
					category_name: { required: true, maxlength: 128 }
				}


			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					category_name: { required: true, maxlength: 128 }
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
				url: "backend/categories/view",
				responseHandler: function (response) {

					$.each(["#create_parent_id", "#modify_parent_id"], function(index, el) {

						el = $(el);
						el.children("option[value!=0]").remove();

						$.each(response.rows, function(index, row) {

							row.level = parseInt(row.level);
							var indent = Xirt.lead("- ", (row.level) * 5);

							$("<option></option>")
							.text(indent + row.name)
							.val(row.id)
							.appendTo(el);

						});

					});

					return response;
				},
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"name": function(data) {

						var leadingSpaces = Xirt.lead('', data.level * 3);
						return $("<span>" + leadingSpaces + (data.level ? "<sup>L</sup>" : "") + data.name + "</span>").html();

					},

					"published": function(data) {

						return	XCMS.createButtons([

							{
								classNames : "command-published " + ((data.published == 1) ? "active" : "inactive"),
								data : { id : data.id },
								label : "Toggle",
								icon : "globe",
							}

						]);

					},

					"ordering": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-order-down",
								data : { id : data.id },
								label : "Move",
								icon : "arrow-down",
							},

							{
								classNames : "command-order-up",
								data : { id : data.id },
								label : "Move",
								icon : "arrow-up",
							}

						]);

					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.id },
								label : "Modify",
								icon : "pencil",
							},

							{
								classNames : "command-delete",
								data : { id : data.id },
								label : "trash",
								icon : "trash-o",
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
			this.element.find(".command-order-up").on("click", $.proxy(this._moveMenuUp, this));
			this.element.find(".command-order-down").on("click", $.proxy(this._moveMenuDown, this));
			this.element.find(".command-published").on("click", this._togglePublished);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/menu/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "category_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_moveMenuUp: function(e) {

			var that = this;
			$.get("backend/category/move_up/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_moveMenuDown: function(e) {

			var that = this;
			$.get("backend/category/move_down/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_togglePublished: function() {

			var el = $(this);
			$.get("backend/menu/toggle_sitemap/" + el.data("id"), function () {
				el.toggleClass("inactive active");
			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/category/remove/" + reference,
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