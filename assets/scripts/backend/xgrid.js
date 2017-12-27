(function($) {

	$.XirtGrid = function(element, options) {

		// Identify containers
		this.$table = $(element);

		this.pagination = $(document.createElement("nav"))
			.addClass("xgrid-pagination")
			.insertAfter(this.$table);

		this.options = $.extend(true, {

			sortable: true,
			searchable: true,
			rowCount: [10, 20, 50, -1],
			defaultRowCount: +($(window).height() > 1100),
			url: "index.php",
			converters: {},
			formatters: {}

		}, options);

		this.identifier	= null;
		this.columns	= [];
		this.current	= 1;
		this.filter		= "";
		this.ordering	= {};
		this.rowCount	= ($.isArray(this.options.rowCount)) ? this.options.rowCount[this.options.defaultRowCount] : this.options.rowCount;

		this.templates = {

			// Toolbar templates
			"toolbarContainer" : '<div class="xgrid-toolbar"></div>',
			"toolbarSearch"    : '<div class="input-group"><span class="icon fa input-group-addon fa-search"></span><input class="search-field form-control form-control-sm" placeholder="Search..." type="text"></div>',
			"toolbarConfig"    : '<button class="btn btn-primary btn-sm config"><span class="fa fa-gears"></span><span class="label">Filters</span></button>'

		};

	};


	$.XirtGrid.prototype = {

		init: function() {

			this._initialize();
			this._renderToolbar();
			this._renderTable();
			this._wrapTable();

			return this;

		},

		_initialize: function() {

			var that = this;

			// Retrieve initial column properties
			$.each(this._getHeaderContainer().children(), function() {

				var $this = $(this);
				var data = $this.data();

				// Initialize
				var column = {

					id				: data.columnId,
					label			: $this.text(),
					bodyClasses		: data.cssClass || "",
					headerClasses	: data.headerCssClass || "",
					isSelectable	: !(data.visibleInSelection === false),
					isVisible		: that._checkColumnVisibility(data.visible),
					isSortable		: that.options.sortable && !(data.sortable === false),
					formatter		: that.options.formatters[data.columnId] ? true : false,

				};

				// [Optional] Set column as identifier
				if (data.identifier || !that.identifier) {
					that.identifier = column.id;
				}

				// [Optional] Set ordering for this column
				if (!(data.sortable === false) && data.order) {
					that.setOrdering(column.id, (data.order == "asc" ? "desc" : "asc"));
				}

				that.columns.push(column);
				$this.remove();

			});

		},

		_getHeaderContainer: function() {
			return this.$table.find("thead > tr").first();
		},

		_getBodyContainer: function() {
			return this.$table.find("tbody");
		},

		_getFooterContainer: function() {
			return this.$table.find("tfoot");
		},

		_renderToolbar: function() {

			var that = this;

			// To be rewritten
			var $toolbar = $(this.templates.toolbarContainer)
				.insertBefore(this.$table);

			if (this.options.searchable) {

				$toolbar.append($(this.templates.toolbarSearch)).find(".search-field").on("keyup", function() {

					that.setFilter($(this).val());
					that.reload();

				});

			}

			var modal = (new $.XirtGridModal(that)).init();
			$toolbar.append($(this.templates.toolbarConfig)).find(".btn.config").on("click", function() {
				modal.show();
			});

		},

		_renderTable: function() {

			var that = this;

			var primaryHeader = this._getHeaderContainer().empty();
			$.each(this.columns, function(key, column) {
				that._renderTableHeaderCell(primaryHeader, column);
			});

			that._updateTableFooter(primaryHeader.find("th").length);
			this.reload();

		},

		_renderTableHeaderCell: function(row, options) {

			// Skip obsolete items
			if (options.isVisible) {

				var cell = $(document.createElement("th"))
					.addClass(options.headerClasses)
					.toggle(options.isVisible)
					.appendTo(row);

			}

			if (!options.isVisible || !options.label.length) {
				return;
			}

			var button =$(document.createElement("button"))
				.addClass("column-header-anchor")
				.data("id", options.id)
				.attr("tabindex", -1)
				.text(options.label)
				.appendTo(cell);

			var arrow = $(document.createElement("span"))
				.addClass("icon fa")
				.appendTo(button);

			if (options.isSortable) {

				 button.addClass("sortable");
				 if (this.ordering[options.id]) {
					 arrow.addClass("fa-sort-" + this.ordering[options.id]);
				 }

				 var that = this;
				 button.on("click", function(e) {
					 that._onSort(e);
				 });

			 }

		},

		_renderTableBody: function(data) {

			var that = this;

			var container = this._getBodyContainer().empty();
			$.each(data, function(row, record) {
				that._renderTableBodyRow(container, row, record);
			});

		},

		_renderTableBodyRow: function(container, rowID, data) {

			// Create container
			var row = $(document.createElement("tr"))
				.data("id", data["id"])
				.appendTo(container);

			// Create cols
			var that = this;
			$.each(this.columns, function(key, column) {
				that._renderTableBodyCell(row, column, data, data[column.id]);
			});

		},

		_renderTableBodyCell: function(row, options, data, value) {

			// Skip obsolete items
			if (!options.isVisible) {
				return;
			}

			// Create item
			var cell = $(document.createElement("td"))
				.addClass(options.bodyClasses)
				.appendTo(row)
				.text(value);

			// Optional formaters
			var id = options["id"];
			if (options.formatter && $.type(this.options.formatters[id]) === "function") {
				cell.html(this.options.formatters[id](data));
			}

		},

		_updateTableFooter: function(tableColumnCount) {

			var footerColumnCount = this._getFooterContainer().find("tr > td").length;
			if (footerColumnCount && footerColumnCount < tableColumnCount) {
				this._getFooterContainer().find("tr > td:last").attr("colspan", tableColumnCount - footerColumnCount + 1);
			}

		},

		_renderPagination: function(page, rowCount, total) {

			if (this.pagination.empty() && rowCount < 0) {
				return;
			}

			var that = this;
			var list = $(document.createElement("div"))
				.addClass("btn-group")
				.appendTo(this.pagination);

			// Create button "previous"
			var prev = this._createPaginationItem("&laquo;", false, page == 1);
			prev.on("click", function() {
				that._onPageSwitch(page - 1);
			});

			// Create button "current"
			var current = this._createPaginationItem("page " + page + " of " + Math.ceil(total / rowCount), true, false);
			current.on("click", function() {
				that._onPageSwitch(page);
			});

			// Create button "next"
			var next = (this._createPaginationItem("&raquo;", false, (page >= (total / rowCount))));
			next.on("click", function() {
				that._onPageSwitch(page + 1);
			});

			list.append(prev, current, next);

		},

		_createPaginationItem: function(text, active, disabled) {

			var button = $(document.createElement("button"))
				.addClass(active ? "btn-primary" : "btn-light")
				.addClass("btn btn-sm")
				.html(text);

			if (disabled) {

				button
					.attr("disabled", "disabled")
					.attr("tabindex", -1);

			}

			return button;

		},

		_wrapTable: function() {

			$(document.createElement("div"))
				.addClass("table-container")
				.insertBefore(this.$table)
				.append(this.$table);

		},

		_onSort: function(e) {

			var $this = $(e.target);
			var column = $this.data("id");

			if ($.type(this.ordering[column]) !== "undefined") {
				this.setOrdering(column, (this.ordering[column] == "asc") ? "desc" : "asc");
			} else {
				this.setOrdering(column, "asc");
			}

			this._renderTable();

		},

		_onPageSwitch: function(page) {

			this.setPage(page);
			this._renderTable();

		},

		reload: function() {

			var that = this;

			$.ajax(this.options.url, {

				method: "POST",
				data : {
					sort: this.ordering,
					current: this.current,
					rowCount: this.rowCount,
					searchPhrase: this.filter
				},
				success : function(data) {

					that._renderTableBody(data.rows);
					that._renderPagination(data.current, data.rowCount, data.total);
					if ($.type(that.options.onComplete) === "function") {
						that.options.onComplete(data);
					}

				}

			});

		},

		setRowCount: function (count) {
			this.rowCount = count;
		},

		setPage: function (page) {
			this.current = page;
		},

		setOrdering: function (column, order) {

			var subject = {};
			subject[column] = order;
			this.ordering = subject;

		},

		setFilter: function (filter) {
			this.filter = filter;
		},

		setVisibility(column, visibility) {

			$.each(this.columns, function(key, candidate) {
				if (candidate.id == column) {
					candidate.isVisible = visibility;
				}
			});

		},

		isVisible: function(column) {

			var result = false;
			$.each(this.columns, function(key, candidate) {
				if (candidate.id == column && candidate.isVisible) {
					return (result = true);
				}
			});

			return result;

		},

		_checkColumnVisibility: function(value) {

			if (typeof value == 'number') {
				return !($(window).width() < value);
			}

			return !(value === false);
		}

	};


	$.XirtGridModal = function(grid) {

		this.$grid = grid;
		this.$element = new $.XirtModalObject({

			type: "primary",
			title: "Filter settings",
			message: null,
			buttons: [{
				id	: "ok",
				type	: "warning",
				label	: "Ok"

			},
			{
				id	: "close",
				type	: "default",
				label	: "Cancel"

			}]

		});

	};

	$.XirtGridModal.prototype = {

		init : function() {

			var that = this;

			this.$modal = (new $.XirtModal(this.$element)).init();
			var $modalBody = this.getModalBody().empty();
			this._renderColumnSelector($modalBody, this.$grid.columns);
			this._renderCountSelector($modalBody, this.$grid);

			// Activate modal button "ok"
			this.$element.find(".modal-footer .btn-ok").off("click").on("click", function() {

				that.$grid.setRowCount($modalBody.find("select").val());
				$.each($modalBody.find("a"), function() {
					that.$grid.setVisibility($(this).data("id"), $(this).hasClass("list-group-item-primary"));
				});

				that.hide();
				that.$grid._renderTable();

			});

			// Activate modal button "close"
			this.$element.find(".modal-footer .btn-close").off("click").on("click", function() {
				that.reset();
				that.hide();
			});

			return this;

		},

		show: function() {

			this.$modal.show();
			return this;

		},

		hide: function() {

			this.$modal.hide();
			return this;

		},

		getModalBody: function() {
			return this.$modal.element.find(".modal-body").first();
		},

		_renderColumnSelector: function(container, columns) {

			var elementContainer = ModalHelper.getFormElementContainer("toggle", "Column visiblity");
			var groupContainer = $(document.createElement("div")).addClass("list-group");
			$.each(columns, function(key, column) {

				if (!column.isSelectable) {
					return;
				}

				var item = $(document.createElement("a"))
					.addClass("list-group-item list-group-item-action")
					.addClass(column.isVisible ? "list-group-item-primary" : "")
					.data("id", column.id)
					.text(column.label)
					.appendTo(groupContainer);

				item.append($(document.createElement("button"))
					.addClass("btn btn-sm btn-primary")
					.attr("type", "button")
					.text(column.isVisible ? "hide" : "show"));

			});

			elementContainer.find(".input-container").append(groupContainer);
			container.append(elementContainer);

			// Activate visibility buttons
			container.find("button").on("click", function() {

				$(this).parent().toggleClass("list-group-item-primary");
				$(this).text($(this).parent().hasClass("list-group-item-primary") ? "hide" : "show");

			});

		},

		_renderCountSelector: function(container, $grid) {

			var elementContainer = ModalHelper.getFormElementContainer("count", "Rows per page");
			var groupContainer = $(document.createElement("select")).addClass("form-control");
			$.each($grid.options.rowCount, function(key, count) {

				var item = $(document.createElement("option"))
					.text(count < 0 ? "All" : count)
					.appendTo(groupContainer)
					.val(count);

			});

			elementContainer.find(".input-container").append(groupContainer.val($grid.rowCount));
			container.append(elementContainer);

		},

		reset: function() {

			var $that = this;

			// Reverting row count
			this.getModalBody().find("select").val(this.$grid.rowCount);

			// Reverting visibility
			$.each(this.getModalBody().find("button"), function() {

				var $button = $(this);

				// Revert unconfirmed hiding of column
				if ($button.parent().hasClass("list-group-item-primary") && !$that.$grid.isVisible($button.parent().data("id"))) {
					$button.trigger("click");
				}

				// Revert unconfirmed showing of column
				if (!$button.parent().hasClass("list-group-item-primary") && $that.$grid.isVisible($button.parent().data("id"))) {
					$button.trigger("click");
				}

			});

		}

	};


	$.fn.xgrid = function (options) {

		this.each(function(index) {

			var $this = $(this);

			var instance = $this.data("xgrid");
			if (instance && $.type(options) == "string") {

				instance[options].apply(instance);
				return;

			}

			$this.data("xgrid", (instance = new $.XirtGrid($this, options)).init());

		});

	};

}(jQuery));