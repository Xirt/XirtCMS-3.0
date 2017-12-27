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

			createModal	    = new $.XirtModal($("#createModal")).init();
			configModal	    = new $.XirtModal($("#configModal")).init();
			optionsModal    = new $.XirtModal($("#optionsModal")).init();
			publishModal	= new $.XirtModal($("#publishModal")).init();
			categoriesModal = new $.XirtModal($("#categoriesModal")).init();

			modifyModal	= new $.XirtModal($("#modifyModal"), {
				editors : initializedEditors
			}).init();

		},


		_initButtons: function() {

			var that = this;

			// Activate publishing button
			$("#article_published").on("change", function() {
				$(".publish-dates").toggle(this.checked);
			});

			// Activate creation button
			$('.btn-create').click(function(e) {
				createModal.show();
			});

			// Active "Edit content"-option
			$(".btn-edit-content").click(function() {

				optionsModal.hide();
				that.grid.showModifyContentModal(current);

			});

			// Active "Edit main properties"-option
			$(".btn-edit-properties").click(function() {

				optionsModal.hide();
				that.grid.showModifyPropertiesModal(current);

			});

			// Active "Edit publishing schedule"-option
			$(".btn-edit-status").click(function() {

				optionsModal.hide();
				that.grid.showModifyPublicationModal(current);

			});

			// Active "Edit categories"-option
			$(".btn-edit-categories").click(function() {

				optionsModal.hide();
				that.grid.showModifyCategoriesModal(current);

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

			var that = this;

			this.element.xgrid({

				rowCount: [10, 15, 20, 50, -1],
				defaultRowCount: +($(window).height() > 1100),
				url: "backend/articles/view",
				formatters: {

					"id" : function (data) {

						return Xirt.pad(data.id, 5, "0");

					},

					"published": function(data) {
						return (data.published == 1) ? "<i class=\"fa fa-check\"></i>" : "";
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
								label : "Trash",
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

			this.element.find(".command-edit").on("click", this._showOptionsModal);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_showOptionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		showModifyContentModal: function() {

			tinyMCE.activeEditor.setContent("");
			tinyMCE.activeEditor.setProgressState(true);

			modifyModal.load({

				url	: "backend/article/view/" + current,
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

		showModifyPropertiesModal: function() {

			configModal.load({

				url	: "backend/article/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-config"), json, { prefix : "article_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

					AttributesManager.createFromJSON($("#attrBox"), json.attributes);

				}

			});

		},

		showModifyCategoriesModal: function() {

			categoriesModal.load({

				url	: "backend/article/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-categories"), json, { prefix : "article_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		showModifyPublicationModal: function() {

			publishModal.load({

				url	: "backend/article/view/" + current,
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
	var createModal, optionsModal, configModal, publishModal, categoriesModal, modifyModal;
	(new $.PageManager()).init();

});