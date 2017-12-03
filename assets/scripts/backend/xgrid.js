(function($) {
	 
	$.XirtGrid = function(element, options) {

		this.element = $(element);
		this.options = $.extend(true, {
			
			sortable: true,
			searchable: true,
			rowCount: [10, 20, 50, -1],
			defaultRowCount: +($(window).height() > 1100),
			url: "index.php",
			converters: {},
			formatters: {}

		}, options);
		
		this.columns = [];
		this.filter = "";
		//this.current = 1;
		//this.currentRows = [];
		this.ordering = {};
		this.identifier = null;
		//this.rowCount = ($.isArray(rowCount)) ? rowCount[this.options.defaultRowCount] : rowCount;
		//this.rows = [];
		//this.selectedRows = [];
		//this.sortDictionary = {};
		//this.total = 0;
		//this.totalPages = 0;
	
	};

	$.XirtGrid.prototype = {

		init: function() {
			
			this._initialize();
			this._renderToolbar();
			this._renderTable();

			return this;

		},
		
		_initialize: function() {
			
			var that = this;
			var primaryHeader = this._getHeaderContainer();
			
			$.each(primaryHeader.children(), function() {
				
				var $this = $(this);
				var data = $this.data();
				
				
				var column = {
					
					id				: data.columnId,
					identifier		: that.identifier == null && data.identifier || false,
					text			: $this.text(),
					bodyClasses		: data.cssClass || "",
					headerClasses	: data.headerCssClass || "",
					formatter		: that.options.formatters[data.columnId] ? true : false,
					order			: (that.options.sortable && (data.order === "asc" || data.order === "desc")) ? data.order : null,
					sortable		: !(data.sortable === false),
					visible			: that._checkColumnVisibility(data.visible),
					hidable			: !(data.visibleInSelection === false), // default: true
					
				};
				
				// Make sure there is an identifier
	            if (column.identifier || !that.identifier) {
	                that.identifier = column.id;
	            }

	            if (column.order != null) {
	            	that.setOrdering(column.id, column.order);
	            }
	            
				that.columns.push(column);
				$this.remove();
				
			});
			
		},
		
		_getHeaderContainer: function() {
			return this.element.find("thead > tr").first();
		},
		
		_getBodyContainer: function() {
			return this.element.find("tbody");
		},
		
		_renderTable: function() {

			var that = this;
			
			var primaryHeader = this._getHeaderContainer().empty();
			$.each(this.columns, function(key, column) {
				that._renderTableHeaderCell(primaryHeader, column);
			});

			this.reload();
			
		},
		
		_renderToolbar: function() {
			
			var toolbar = $(document.createElement("div"))
				.insertBefore(this.element)
				.addClass("xgrid-toolbar");
			
			var group = $(document.createElement("div"))
				.addClass("input-group")
				.appendTo(toolbar);
			
			$(document.createElement("span"))
				.addClass("icon fa input-group-addon fa-search")
				.appendTo(group);
			
			var search = $(document.createElement("input"))
				.addClass("search-field form-control form-control-sm")
				.attr("placeholder", "Search...")
				.attr("type", "text")
				.appendTo(group);
			
			var that = this;
			search.on("keyup", function() {
				
				that.setFilter($(this).val());
				that.reload();
				
			});
			
		},
		
		_renderTableHeaderCell: function(row, options) {

			// Skip obsolete items
			if (!options.visible) {
				return;
			}
			
			var cell = $(document.createElement("th"))
				.addClass(options.headerClasses)
				.toggle(options.visible)
				.appendTo(row);
				
			var button =$(document.createElement("button")) 
				.addClass("column-header-anchor")
				.data("id", options.id)
				.text(options.text)
				.appendTo(cell);
			
			var arrow = $(document.createElement("span"))
				.addClass("icon fa")
				.appendTo(button);
			
			 if (options.sortable) {
			 
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
				that._renderTableBodyCell(row, column, data[column.id]);
			});

		},

		_renderTableBodyCell: function(row, options, value) {

			// Skip obsolete items
			if (!options.visible) {
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
            	cell.html(this.options.formatters[id](row.data(this.identifier), value));
            }

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
		
		reload: function() {

			var that = this;
			
			$.ajax(this.options.url, {
				
				method: "POST", 
				data : {
					current: 1,
					rowCount: 20,
					sort: this.ordering,
					searchPhrase: this.filter
				},
				success : function(data) {

					that._renderTableBody(data.rows);
					if ($.type(that.options.onComplete) === "function") {
						that.options.onComplete();
					}
					
				}
				
			});

		},
		
		setOrdering: function (column, order) {
			
			var subject = {};
			subject[column] = order;
			this.ordering = subject;

		},
		
		setFilter: function (filter) {
			this.filter = filter;
		},
		
		_checkColumnVisibility: function(value) {

			if (typeof value == 'number') {
				return !($(window).width() < value);
			}

			return !(value === false);	
		}
			
	};

	
	$.fn.xgrid = function (options) {
		
		this.each(function(index) {
		
			var $this = $(this);			
			(new $.XirtGrid($this, options)).init();
			
		});
		
	};

}(jQuery));