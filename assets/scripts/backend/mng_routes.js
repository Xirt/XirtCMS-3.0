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

			// [Input] Link existence check
			$("#inp-url").on("change", function() {
				linkCreator.checkLink($(this).val(), $("#box-exists"));
			}).trigger("change");

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
					route_public_url: { required: true, maxlength: 256 },
					route_target_url: { required: true, maxlength: 256 }
				}

			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					route_public_url: { required: true, maxlength: 256 },
					route_target_url: { required: true, maxlength: 256 }
				}

			});

		},

		_initModals: function(initializedEditors) {

			createModal = new $.XirtModal($("#createModal")).init();
			modifyModal = new $.XirtModal($("#modifyModal")).init();

		},

		_initButtons: function() {

			// Activate "Create item"-button
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

			var that = this;

			this.element.xgrid({

				rowCount: [10, 15, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				url: "backend/routes/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"menu_item_id": function(data) {
						return data.menu_items ? "<i class=\"fa fa-check\"></i>" : "";
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
								additionalAttributes : (data.published == 1) ? "disabled=\"disabled\"" : "",
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
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			modifyModal.load({

				url	: "backend/route/view/" + $(this).data("id"),
				onLoad	: function(json) {

					linkCreator.update("route_", json);
					Xirt.populateForm($("#form-modify"), json, { prefix : "route_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/route/remove/" + reference,
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
	var linkCreator = (new $.LinkPanel()).init();
	(new $.PageManager()).init();

});