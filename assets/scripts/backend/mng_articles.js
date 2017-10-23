$(function() {

	/****************
	 * PAGE MANAGER *
	 ****************/
	$.PageManager = function() {
	};

	$.PageManager.prototype = {

		init: function() {

			this._initGrid();
			this._initEditor();
			this._initButtons();
			this._initDatepickers();

			return this;

		},


		_initGrid: function() {

			this.grid = (new $.GridManager($("#grid-basic"))).init();

		},


		_initEditor: function() {

			var that = this;

			tinymce.init($.extend({}, tinyMCE_Full, {
				height	: $(window).height() - 400,
				width	: "100%"
			})).then(function(editors) {
				that._initModals(editors);
				that._initForms();
			});

		},


		_initForms: function() {

			Form.validate("#form-create", {

				currentModal: createModal,
				nextModal: createModal,
				grid: this.grid,
				rules: {
					create_title: { required: true, maxlength: 256 }
				}

			});

			Form.validate("#form-modify", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					create_title: { required: true, maxlength: 256 }
				}

			});

			Form.validate("#form-config", {

				currentModal: configModal,
				nextModal: configModal

			});

			Form.validate("#form-categories", {

				currentModal: categoriesModal,
				nextModal: categoriesModal

			});

			Form.validate("#form-publish", {

				currentModal: publishModal,
				nextModal: publishModal,
				grid: this.grid

			});

		},


		_initModals: function(initializedEditors) {

			createModal	= new $.XirtModal($("#createModal")).init();
			configModal	= new $.XirtModal($("#configModal")).init();
			publishModal	= new $.XirtModal($("#publishModal")).init();
			categoriesModal = new $.XirtModal($("#categoriesModal")).init();

			modifyModal	= new $.XirtModal($("#modifyModal"), {
				editors : initializedEditors
			}).init();

		},


		_initButtons: function() {

			// Activate publishing button
			$("#article_published").on("change", function() {
				$(".publish-dates").toggle(this.checked);
			});

			// Activate creation button
			$('.btn-create').click(function(e) {
				createModal.show();
			});

		},


		_initDatepickers: function() {

			// Activate fields
			$(".datepicker").datepicker({
				weekStart: 1,
				autoHide: true,
				autoPick: true,
				format: "dd/mm/yyyy"
			}).on('show.datepicker', function (e) {
				$(this).datepicker("setDate", $(this).val());
			});

			// Activate icons
			$(".input-group.date .input-group-addon").on("click", function(e) {

				$(this).siblings("input").datepicker("show");
				e.stopImmediatePropagation();

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
				url: "backend/articles/view",
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"published": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-published " + ((row.published == 1) ? "active" : "inactive"),
								data : { id : row.id },
								icon : "globe",
							}

						]);

					},

					"commands": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : row.id },
								icon : "pencil",
							},

							{
								classNames : "command-config",
								data : { id : row.id },
								icon : "gears",
							},

							{
								classNames : "command-categories",
								data : { id : row.id },
								icon : "list-ul",
							},

							{
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
			this.element.find(".command-config").on("click", this._modifyConfigModal);
			this.element.find(".command-categories").on("click", this._modifyCategoriesModal);
			this.element.find(".command-publish").on("click", this._modifyPublicationModal);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_modifyContentModal: function() {

			tinyMCE.activeEditor.setContent("");
			tinyMCE.activeEditor.setProgressState(true);

			modifyModal.load({

				url	: "backend/article/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "article_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					tinyMCE.activeEditor.setContent(json.content);
					tinyMCE.activeEditor.setProgressState(false);
					tinyMCE.activeEditor.undoManager.clear();
					tinyMCE.activeEditor.setDirty(false);

				}

			});

		},

		_modifyConfigModal: function() {

			configModal.load({

				url	: "backend/article/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-config"), json, { prefix : "article_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#attrBox"), json.attributes);

				}

			});

		},

		_modifyCategoriesModal: function() {

			categoriesModal.load({

				url	: "backend/article/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-categories"), json, { prefix : "article_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		_modifyPublicationModal: function() {

			publishModal.load({

				url	: "backend/article/view/" + $(this).data("id"),
				onLoad	: function(json) {

					Xirt.populateForm($("#form-publish"), json, { prefix : "article_", converters: {

						id: function (value) { return Xirt.pad(value, 5, "0"); },

						dt_publish: function (value) {
							var dt = new Date(value);
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						},

						dt_unpublish: function (value) {

							var dt = new Date(value);
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						}

					}});

				}

			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/article/remove/" + reference,
					reference,
					this
				);

			}

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var createModal, configModal, publishModal, categoriesModal, modifyModal;
	(new $.PageManager()).init();

});