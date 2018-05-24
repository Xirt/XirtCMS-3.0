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
			this._initDatepickers();

			return this;

		},


		_initGrid: function() {

			this.grid = (new $.GridManager($("#grid-basic"), this)).init();

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

			Form.validate("#form-home", {

				currentModal: homeModal,
				nextModal: homeModal,
				grid: this.grid,
				rules: {
					homepage_menu: { required: true },
					homepage_item: { required: true }
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

			Form.validate("#form-permit", {

				currentModal: permitModal,
				nextModal: permitModal,
				grid: this.grid,
				extSubmitHandler: function(form, e) {

					var disabled = $(form).find(":input:disabled").removeAttr("disabled");
					$.ajax("backend/menuitems/menuitem/modify_sitemap", {

						method: "POST",
						timeout : 7500,
						data: $("#form-permit").serializeArray(),

						error : function() {

							new $.XirtMessage({
								title    : "Communication failure",
								message  : "Unable to retrieve data properly from the server.",
								type     : "warning"
							});

						}

					});

					disabled.attr("disabled", "disabled");

				}

			});

		},


		_initModals: function(initializedEditors) {

			homeModal    = new $.XirtModal($("#homeModal")).init();
			createModal  = new $.XirtModal($("#createModal")).init();
			optionsModal = new $.XirtModal($("#optionsModal")).init();
			modifyModal  = new $.XirtModal($("#modifyModal")).init();
			permitModal  = new $.XirtModal($("#permitModal")).init();
			configModal  = new $.XirtModal($("#configModal"), {resetForms:	false }).init();

		},


		_initButtons: function() {

			var that = this;

			// Activate publishing button
			$("#permit-active").on("change", function() {
				$(".permit-attr").toggle(this.checked);
			}).trigger("change");

			// Activate creation button
			$('.btn-create').click(function(e) {

				createModal.show();
				$("input[name=menu_id]").val(menuId);

			});

			// Activate creation button
			$('.btn-home').click(function(e) {

				homeModal.show();
				$("#homepage_menu").val(menuId);

			});

			// Active "Edit properties"-option
			$(".btn-edit-properties").click(function() {

				optionsModal.hide();
				that.grid.showModifyPropertiesModal(current);

			});

			// Active "Edit routing"-option
			$(".btn-edit-routing").click(function() {

				optionsModal.hide();
				that.grid.showModifyRoutingModal(current);

			});

			// Active "Edit permit"-option
			$(".btn-edit-permit").click(function() {

				optionsModal.hide();
				that.grid.showModifyPermitModal(current);

			});


			// Activate tab tracing
			$('.nav-tabs a').click(function(e) {
				$("#inp-type").val($(this).attr('id').substr(5));
			});

			$("#homepage_menu").change(function(e) {

				$.ajax("backend/menuitems/view/" + $(this).val(), {

					method: "POST",
					success : $.proxy(that.populateHomeSelection, that)

				});

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
			$(".input-group.date .input-group-append").on("click", function(e) {

				$(this).siblings("input").datepicker("show");
				e.stopImmediatePropagation();

			});

		},

		populateMenuitemSelection: function(target, data) {

			$.each(target, function(index, el) {

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

		},

		populateHomeSelection: function(data) {

			var target = $("#homepage_item");

			this.populateMenuitemSelection(target, data);
			$.each(data.rows, function(key, row) {
				(row.home == "1") ? target.val(row.item_id) : false;
			});

		}

	};


	/****************
	 * GRID MANAGER *
	 ****************/
	$.GridManager = function(element, pageManager) {

		this.element = (element instanceof $) ? element : $(element);
		this.pageManager = pageManager;

	};

	$.GridManager.prototype = {

		init: function() {

			var that = this;

			this.element.xgrid({


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
								icon : "fas fa-arrow-alt-circle-down",
							},

							{
								classNames : "command-order-up",
								data : { id : data.item_id },
								label : "Move",
								icon : "fas fa-arrow-alt-circle-up",
							}

						]);

					},

					"published": function(data) {
						return (data.published == 1) ? "<i class=\"fas fa-check\"></i>" : "";
					},

					"sitemap": function(data) {
						return (data.sitemap == 1) ? "<i class=\"fas fa-check\"></i>" : "";
					},

					"commands": function(data) {

						return XCMS.createButtons([

							{
								classNames : "command-edit",
								data : { id : data.item_id },
								label : "Modify",
								icon : "far fa-edit"
							},

							{
								classNames : "command-delete",
								data : { id : data.item_id },
								label : "Trash",
								icon : "far fa-trash-alt"
							}

						]);

					}

				},

				onComplete: function(data) {

					that.pageManager.populateMenuitemSelection([".select-menuitem, #create_parent_id", "#modify_parent_id"], data);
					that.pageManager.populateHomeSelection(data);
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
			this.element.find(".command-order-up").on("click", $.proxy(this._moveItemUp, this));
			this.element.find(".command-order-down").on("click", $.proxy(this._moveItemDown, this));
			this.element.find(".command-home").on("click", this._modifyHomeModal);
			this.element.find(".command-sitemap").on("click", this._toggleSitemap);
			this.element.find(".command-delete").on("click", $.proxy(this._deleteItemModal, this));

		},

		_showOptionsModal: function() {

			optionsModal.show();
			current = $(this).data("id");

		},

		_modifyHomeModal: function() {
			homeModal.show();
		},

		showModifyPropertiesModal: function(current) {

			modifyModal.load({

				url	: "backend/menuitems/menuitem/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-modify"), json, { prefix : "menuitem_", converters: {
						id: function (value) { return Xirt.pad(value, 5, "0"); }
					}});

				}

			});

		},

		showModifyRoutingModal: function(current) {

			configModal.load({

				url	: "backend/menuitems/menuitem/view/" + current,
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

		showModifyPermitModal: function(current) {

			permitModal.load({

				url	: "backend/menuitems/menuitem/view/" + current,
				onLoad	: function(json) {

					Xirt.populateForm($("#form-permit"), json.permit, { prefix : "menuitem_", converters: {

						dt_start: function (value) {
							var dt = value ? new Date(value) : new Date();
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						},

						dt_expiry: function (value) {

							var dt = value ? new Date(value) : new Date("December 31, 2099 23:59:99");
							return ('0' + dt.getDate()).slice(-2) + "/"
								 + ('0' + (dt.getMonth() + 1)).slice(-2) + "/"
								 + dt.getFullYear();
						}

					}});

					Xirt.populateForm($("#form-permit"), json, { prefix : "menuitem_" , converters: {

						id: function (value) { return Xirt.pad(value, 5, "0"); }

					}});

				}

			});

		},

		_moveItemUp: function(e) {

			var that = this;
			$.get("backend/menuitems/menuitem/move_up/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_moveItemDown: function(e) {

			var that = this;
			$.get("backend/menuitems/menuitem/move_down/" + $(e.currentTarget).data("id"), function () {
				that.reload();
			});

		},

		_toggleSitemap: function() {

			var that = $(this);
			$.get("backend/menuitems/menuitem/toggle_sitemap/" + that.data("id"), function () {
				that.toggleClass("inactive active");
			});

		},

		_togglePublished: function() {

			var that = $(this);
			$.get("backend/menuitems/menuitem/toggle_published/" + that.data("id"), function () {
				that.toggleClass("inactive active");
			});

		},

		_deleteItemModal: function(e) {

			var reference = $(e.currentTarget).data("id");
			if (jQuery.type(reference) != "undefined") {

				confirmRemoval(
					"backend/menuitems/menuitem/remove/" + reference,
					reference,
					this
				);

			}

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	var createModal, homeModal, optionsModal, modifyModal, configModal, permitModal;

	var uri = window.location.href;
	var menuId = uri.substr(uri.lastIndexOf("/") + 1);

	var linkCreator = (new $.LinkPanel()).init();
	(new $.PageManager()).init();

});