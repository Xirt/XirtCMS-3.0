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

			// TODO :: box-internal to be saved in type field prior to submittin
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

					linkCreator.update(json);

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


	/**************
	 * LINK PANEL *
	 *************/
	$.LinkPanel = function() {
	};

	$.LinkPanel.prototype = {

		init: function() {

			var that = this;

			// [Input] Link existence check
			$("#inp-public_url").on("keyup", function() {
				that._checkLink($(this).val());
			});
			
			// [Select] Update module details
			$("#sel-module-type").on("change", function() {

				var moduleType = $(this).val();
				that._updateModuleMenu(moduleType, that.getModuleTarget().module);
				that._updateModuleConfigurations(moduleType);

			});

			// [Button] Editor activation
			$("#btn-editor").on("click", function() {
				$("#box-link").slideToggle({ duration : 200 });		
			});

			return this;

		},

		getModuleTarget: function() {

			return {
				target_url	: $("#inp-target_url").val(),
				module_type	: $("#sel-module-type").val(),
				module		: parseInt($("#sel-module-config").val()),
			};

		},

		update: function(data) {

			// Populate field
			if (data.target_url) {
				
				var parts = data.target_url.split("/");
				$.extend(data, { module_type : parts.length ? parts[0] : null });
				
			}
				
			// Populate form
			Xirt.populateForm($("#box-link"), data, { prefix : "menuitem_" });

			// Update GUI
			this._updateModuleConfigurations(data.module_type, data.module);
			this._updateModuleView(data.target_url, data.public_url);
			this._updateModuleMenu(data.module_type);			
			this._updateTabView(data.type);


		},

		_updateModuleConfigurations : function(type, module) {

			var target = $("#sel-module-config").empty();

			// Retrieve module configurations for given module type
			$.post("backend/moduleconfigurations/view", { moduleType : type, sort : "name" }, function(json) {

				$.each(json.rows, function(key, data) {

					$("<option></option")
						.text(data.name)
						.val(data.id)
						.appendTo(target);

				});
				
				target.val(module ? module : target.find("option:first").val());

			}, "json");

		},

		_updateModuleMenu : function(type) {

			var that = this;
			var target = $("#box-params").empty();
			
			// Retrieve module menu parameters for given module type
			$.post("backend/module/view_menu_parameters/" + type, function(json) {

				AttributesManager.createFromJSON(target, json);				
				var parts = that.getModuleTarget().target_url.split("/");
				
				// Activate events on new items
				target.find("[name*='attr_']").each(function(key) {

					$(this).on("change keyup", $.proxy(that._updateLink, that));
					if (parts[0] == type && key < parts.length) {
						$(this).val(parts[key + 1]);
					}
					
				});
				
				that._updateLink();

			}, "json");

		},
		
		_updateModuleView: function(publicURL, targetURL) {
			
			// Toggle visibility
			$("#box-link").toggle(publicURL ? false : true);
			$("#box-relations").hide(); 
			
			// Trigger additional updates
			this._checkLink(targetURL);
			this._updateLink();
			
		},
		
		_updateTabView(linkType) {
			
			var tabs = $(".tab-pane");
			var current = (linkType ? linkType : "internal");
			
			// Instant switch tabs
			tabs.removeClass("fade in");
			$("#type-" + current).tab("show");
			tabs.addClass("fade in");
			
		},

		_checkLink : function(link) {

			var options = {duration : 200};
			$.post("backend/route/convert_public_url", { uri : link }, function (json) {
				json.success ? $("#box-relations").slideDown(options) : $("#box-relations").slideUp(options);
			}, "json");

		},

		_updateLink : function() {

			var parts = [this.getModuleTarget().module_type];
			$.each($("#box-params").find("[name*='attr_']"), function() {
				parts.push($(this).val());
			});

			$("#inp-target_url").val(parts.join("/"));

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