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

		},


		_initButtons: function() {

			// Activate creation button
			$('.btn-create').click(function(e) {
				
				createModal.show();
				$("input[name=menu_id]").val(menuId);
				
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
				url: "backend/menuitems/view/" + menuId,
				responseHandler: function (response) {

					$.each(["#create_parent_id", "#modify_parent_id"], function(index, el) {

						el = $(el);
						el.children("option[value!=0]").remove();

						$.each(response.rows, function(index, row) {

							row.level = parseInt(row.level);
							var indent = Xirt.lead("- ", (row.level) * 5);

							$("<option></option>")
							.text(indent + row.name)
							.val(row.item_id)
							.appendTo(el);

						});

					});

					return response;
				},
				converters: {

					identifier: {
						to: function (value) { return Xirt.pad(value, 5, "0"); }
					}

				},
				formatters: {

					"name": function(column, row) {

						var leadingSpaces = Xirt.lead('', row.level * 3);
						var nameValue = "<span>" + leadingSpaces + (row.level ? "<sup>L</sup>" : "") + row.name + "</span>";
						return (row.home == 1) ? nameValue + "<i><span class=\"fa indicator fa-home\"></span></i>" : nameValue;

					},

					"ordering": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-order-down",
								data : { id : row.item_id },
								icon : "arrow-down",
							},

							{
								classNames : "command-order-up",
								data : { id : row.item_id },
								icon : "arrow-up",
							}

						]);

					},

					"published": function(column, row) {

						return	XCMS.createButtons([

							{
								classNames : "command-published " + ((row.published == 1) ? "active" : "inactive"),
								data : { id : row.item_id },
								icon : "globe",
							}

						]);

					},

					"sitemap": function(column, row) {

						return	XCMS.createButtons([

							{
								classNames : "command-sitemap " + ((row.sitemap == 1) ? "active" : "inactive"),
								data : { id : row.item_id },
								icon : "sitemap",
							}

						]);

					},

					"commands": function(column, row) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : row.item_id },
								icon : "pencil",
							},

							{
								classNames : "command-config",
								data : { id : row.item_id },
								icon : "gears",
							},

							{
								classNames : "command-home",
								data : { id : row.item_id },
								icon : "home",
							},

							{
								classNames : "command-delete",
								data : { id : row.item_id },
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
			this.element.find(".command-order-up").on("click", $.proxy(this._moveItemUp, this));
			this.element.find(".command-order-down").on("click", $.proxy(this._moveItemDown, this));
			this.element.find(".command-sitemap").on("click", $.proxy(this._toggleHome, this));
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
					$.extend(json, LinkFactory.parseLink(json.target_url, json.type, json.anchor));

					manager.populateModuleConfigurations(json.module_type, function(e) {

						Xirt.hideSpinner();
						configModal.modal("show");

						$("#itemCount").text(json.relations);
						parseInt(json.relations) ? $("#countBox").show() : $("#countBox").hide();

						Xirt.populateForm($("#form-config"), json, { prefix : "menuitem_", converters: {
							id: function (value) { return Xirt.pad(value, 5, "0"); },
							name: function (value) { return Xirt.ellipsis(value, 40); }
						}});

						manager.updateView(false);
						manager.updateAnchorView();

					});

				}

			});

		},

		_moveItemUp: function(e) {

			var that = $(this);
			$.get("backend/menuitem/move_up/" + $(this).data("id"), function () {
				that.reload();
			});
			
		},

		_moveItemDown: function(e) {

			var that = $(this);
			$.get("backend/menuitem/move_down/" + $(this).data("id"), function () {
				that.reload();
			});

		},

		_toggleHome: function(e) {

			var that = $(this);
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
	var manager = new RouteManager();
	
	(new $.PageManager()).init();

});


function RouteManager() {
	this.init();
}

RouteManager.prototype = {

	init: function() {

		var that = this;

		this.type       = $("#modify_type");
		this.anchor     = $("#modify_anchor");
		this.config     = $("#modify_module");
		this.module     = $("#uri_module");
		this.article    = $("#uri_article");
		this.targetURI  = $("#targetURI");
		this.sourceURI  = $("#sourceURI");
		this.sourceBox  = $("#sourceBox");
		this.moduleBox  = $("#moduleBox");
		this.anchorBox  = $("#anchorBox");
		this.articleBox = $("#articleBox");

		$("#modify_type,#uri_module,#uri_article,#modify_anchor,#modify_module").each(function(e) {
			$(this).on("change", $.proxy(that.updateView, that));
		});

		$("#modify_anchor").each(function(e) {
			$(this).on("keyup", $.proxy(that.updateAnchorView, that));
		});

		$("#uri_module").on("change", function() {
			that.populateModuleConfigurations($(this).val());
		});

	},

	getType : function() {
		return this.type.val();
	},

	getModule : function() {
		return this.module.val();
	},

	getArticle : function() {
		return this.article.val();
	},

	getAnchor : function() {
		return this.anchor.val() ? '#' + this.anchor.val() : '';
	},

	updateView : function(e) {

			switch (this.getType()) {

				case "anchor":

					this.moduleBox.hide();
					this.anchorBox.show();
					this.sourceBox.hide();
					this.updateAnchorView();
					this.targetURI.prop('disabled', true);

				break;

				case "module":

					this.moduleBox.show();
					this.anchorBox.show();
					this.sourceBox.show();
					this.updateModuleView(e);
					this.targetURI.prop('disabled', true);

				break;

				case "custom":

					this.moduleBox.hide();
					this.anchorBox.hide();
					this.sourceBox.hide();
					this.updateCustomView();
					this.targetURI.prop('disabled', false);

				break;

			}

	},

	updateModuleView : function(sourceNotKnown) {

		switch (this.getModule()) {

			case "article":

				this.articleBox.show();
				this.targetURI.val('article/view/' + this.getArticle());

			break;

			default:

				this.articleBox.hide();
				this.targetURI.val(this.getModule());

			break;

		}

		// Source is known
		if (!sourceNotKnown) {
			return this.sourceURI.data("val", this.sourceURI.val());
		}

		// Source not known (update)
		var that = this;
		$.post("backend/route/convert_target_url", { uri : this.targetURI.val(), config : this.config.val() }, function (json) {

			if (that.sourceURI.data("val") == json.source) {}

				that.sourceURI.val(json.source + that.getAnchor());
				that.sourceURI.data("val", json.source);

		}, "json");

	},

	updateAnchorView : function(e) {

		switch (this.getType()) {

			case "anchor":
				this.targetURI.val(this.getAnchor());
			break;

			case "module":
				this.sourceURI.val(this.sourceURI.data("val") + this.getAnchor());
			break;

		}

	},

	updateCustomView : function(e) {
		this.targetURI.val("");
	},

	populateModuleConfigurations : function(type, onSuccess) {

		data = {
			current : 1,
			rowCount : 999999,
			sort : "name"
		};

		$.post("backend/moduleconfigurations/view", data, function(json) {

			var el = $("#modify_module").empty();
			$.each(json.rows, function (key, data) {

				if (data.type == type) {

					$("<option></option")
					.text(data.name)
					.val(data.id)
					.appendTo(el);

				}

			});

			(onSuccess || function(){})();

		}, "json");

	}

};

var LinkFactory = {

	parseLink : function(url, type, anchor) {

		switch (type) {

			case "anchor":

				return {
					module_type	: null,
					article 	: null,
					anchor  	: anchor
				};

			case "module":

				if (url && url.substring(0,url.indexOf("/")) == "article") {

					return {
						module_type	: url.substring(0,url.indexOf("/")),
						article		: url.substring(url.lastIndexOf("/") + 1),
						anchor		: anchor
					};

				}

				return {
					module_type	: url,
					article 	: null,
					anchor  	: anchor
				};

			default:

				return {
					module_type : null,
					article 	: null,
					anchor  	: null
				};

		}

	}

};