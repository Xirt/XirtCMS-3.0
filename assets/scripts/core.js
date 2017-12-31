var Form = Form ? Form : [];

$(document).ready(function() {

	// Trims form input on blur
	$("input").change(function() {
		$(this).val($(this).val().trim());
	});

	Xirt.activate();

});


/*********************************************************
*		  XIRT - Utility Library for XirtCMS			*
*			  (version 2.0 - 12.01.2014)				*
**********************************************************/
var Xirt = {

	activate : function() {

		// External links
		$.each($("a[rel*=external]"), function(index, el) {

			$(el).attr("target", "_blank");
			$(el).addClass("external");

		});

	},


	noAJAX : function() {
		alert("Request failed");
	},


	populateForm : function(form, data, optionsOverride) {

		var options = $.extend(true, { prefix : "", converters: {}}, optionsOverride)

		$.each(data, function(key, value) {

			// Find element (using optional prefix)
			value = options.converters[key] ? options.converters[key](value) : value;
			if (!(element = form.find("[name='" + key + "']")) || !element.length) {

				if (!(element = form.find("[name='" + key + "[]']")) || !element.length) {

					if (!options.prefix || !(element = form.find("[name='" + options.prefix + key + "']")) || !element.length) {

						if (!(element = form.find("[name='" + options.prefix + key + "[]']")) || !element.length) {
							return true;
						}

					}

				}

			}

			// Populate (taking type into account)
			switch (element.prop("type").toLowerCase()) {

				case "checkbox":
				case "radio":
					value = (value && value != 0 && value != "FALSE");
					return element.prop("checked", value).trigger("change");

				default:
					return element.val(value);

			}

		});

	},

	pad: function(str, length, chr) {

		if (typeof str != "undefined") {

			chr = chr ? chr : '0';
			length = isNaN(length) ? 5 : length;

			return str.length < length ? this.pad(chr + str, length, chr) : str;
		}

		return null;
	},

	ellipsis: function(str, length) {
		return (str.length > length ? str.substring(0, length) + "..." : str);
	},

	lead: function(str, length) {
		return this.pad(str, length + str.length, "\u00a0");
	},

	showSpinner: function() {
		$("body").addClass("ajaxRequest");
	},

	hideSpinner: function() {
		$("body").removeClass("ajaxRequest");
	}

};

var XCMS = (function(){

	return {

		// Current method (replaced once BootGrid has been rewritten)
		createButtons(buttons) {

			var result = "";

			$.each(buttons, function(key, button) {
				
				if ($.type(button.label) == "undefined") {
					button.label = "";					
				}
				
				result = result + " " + XCMS.createButton(button.classNames, button.icon, button.data, button.additionalAttributes, button.label);
			});

			return result;

		},

		// Current method (replaced once BootGrid has been rewritten)
		createButton : function(classNames, icon, data, attributes, label) {

			var dataString = "";
			$.each(data, function(key, value) {
				dataString = dataString + " data-" + key + "=\"" + value + "\"";
			});

			attributes = (typeof attributes == "undefined") ? "" : attributes;

			return "<button type=\"button\" class=\"btn btn-sm btn-default " + classNames + "\"" + dataString + " " + attributes + "><span class=\"" + icon + "\"></span><b>" + label + "</b></button>";

		},

		// Future method (once BootGrid has been rewritten)
		future_createButtons(buttons) {

			var result = $("<div />");

			$.each(buttons, function(key, button) {
				result.append(XCMS.createButton(button.classNames, button.icon, button.data)).append(" ");
			});

			return result;

		},

		// Future method (once BootGrid has been rewritten)
		future_createButton : function(classNames, icon, data) {

			var button = $('<button/>');
			button.attr("type", "button");
			button.addClass('btn btn-xs btn-default ' + classNames);
			button.html('<span class=\"fa fa-' + icon + '\"></span>');
			button.data(data);

			return button;

		}

	};

}());

Form.validate = function (targetForm, options) {

	$(targetForm).validate({

		rules      : options.rules,
		messages   : options.messages,
		onfocusout : function (element) { return $(element).valid(); },
		onfocusin  : function (element) { return $(element).valid(); },
		onkeyup    : function (element) { return $(element).valid(); },

		showErrors : function (errorMap, errorList) {

			// Remove obsolete messages
			$.each(this.validElements(), function(i, el) {
				$(el).popover("dispose");
			});

			// Create / modify relevant messages
			$.each(errorList, function(i, item) {

				$(item.element).popover({
					content: item.message,
					placement: "right",
					trigger: "manual"
				}).popover("show");

				// Ensure updated text
				var $popover = $($(item.element).data('bs.popover').getTipElement());
				$popover.find(".popover-body").html(item.message);

			});

		},

		submitHandler: ($.type(options.submitHandler)) === "function" ? options.submitHandler: function(form, e) {

			targetForm = $(form);
			$.each(targetForm.find(":button"), function (index, el) {
				$(el).attr("disabled", true);
				$(el).addClass("spinner");
			});

			setTimeout(function() {
				$.each(targetForm.find(":button"), function (index, el) {
					$(el).attr("disabled", false);
					$(el).removeClass("spinner");
				});
			}, 7500);

			new Form.Request(form, {

				onSuccess: function(data) {

					$.each(targetForm.find(":button"), function (index, el) {
						$(el).attr("disabled", false);
						$(el).removeClass("spinner");
					});

					switch (data.type) {

						case "error":

							return _showDialogAndReturn(
								"error",
								options.currentModal,
								options.nextModal,
								data.title,
								data.message
							);

						case "info":
							return _showDialogAndList(
								"info",
								options.currentModal,
								options.grid,
								data.title,
								data.message
							);

					}

				}

			});

			e.preventDefault();

		}

	});

	
	// Show dialog and return to given modal
	function _showDialogAndReturn(type, triggerModal, nextModal, title, message) {

		triggerModal.hide();
		var that = new $.XirtMessage({

			type	 : type,
			title	 : title,
			message  : message,
			callback : function(e) {

				nextModal.show();

			}

		});

	}

	
	// Show dialog and show overview (list)
	function _showDialogAndList(type, triggerModal, grid, title, message) {

		triggerModal.hide();
		var that = new $.XirtMessage({

			type	 : type,
			title	 : title,
			message  : message,
			callback  : function(e) {

				if (grid) typeof grid.reload == "function" ? grid.reload() : grid.bootgrid("reload");

			}
		});

	}

}

/*********************************************************
*		FORM.REQUEST - Default Form Submit (AJAX)	   *
*				(version 1.0 - 13.01.2014)			  *
**********************************************************/
Form.Request = function(form, options) {

	// Allows for inclusion of disabled fields
	var disabled = $(form).find(":input:disabled").removeAttr("disabled");

	options = $.extend({
		onSuccess: function() {},
		onFailure: Xirt.noAJAX,
		onSend: function() {},
		target: form.action,
		method: "POST"
	}, options);

	jQuery.ajax({
		url: options.target,
		method: options.method,
		data: $(form).serializeArray(),
		beforeSend: options.beforeSend
	}).done(options.onSuccess)
	.fail(options.onFailure);

	// Resets disable status
	disabled.attr("disabled", "disabled");

};


/************************************************
*       XirtModal - Default Xirt Modal          *
*           (version 1.0 - 22.10.2017)          *
************************************************/
(function ($) {

	$.XirtModalObject = function(options) {

		return this._createModal(
			options.type,
			options.title,
			options.message,
			options.buttons
		);

	};

	$.XirtModalObject.prototype = {

		_createModal: function(type, title, message, buttons) {

			return $('<div class="modal fade" role="dialog"></div>')
				.append(this._createModalDialog(type, title, message, buttons));

		},

		_createModalDialog: function(type, title, message, buttons) {

			return $('<div class="modal-dialog" role="document"></div>')
				.append(this._createModalContent(type, title, message, buttons));

		},

		_createModalContent: function(type, title, message, buttons) {

			return $('<div class="modal-content"></div>')
				.append(title   ? this._createModalHeader(type, title) : $("<div>"))
				.append(message ? this._createModalBody(message)       : $("<div class='modal-body'>"))
				.append(buttons ? this._createModalFooter(buttons)     : $("<div>"));

		},

		_createModalHeader: function(type, title) {

			return $("<div class='modal-header'></div>")
				.append(this._createModalTitle(title))
				.addClass(type);

		},

		_createModalTitle: function(title) {

			return $("<h5 class='modal-title'></h5>")
				.text(title);

		},

		_createModalBody: function(message) {

			return $("<div class='modal-body'></div>")
				.text(message);

		},

		_createModalFooter: function(buttons) {

			var that = this;
			var $container = $('<div class="modal-footer"></div>');

			$.each(buttons, function(key, options) {
				$container.append(that._createButton(options.id, options.type, options.label));
			});

			return $container;

		},

		_createButton(id, type, label) {

			return $('<button type="button" class="btn btn-sm" aria-hidden="true"></button>')
				.addClass("btn-" + type)
				.addClass("btn-" + id)
				.text(label);


		}

	};


	/*****************
	 * XIRTCMS MODAL *
	 *****************/
	$.XirtMessage = function(options) {

		var $el = new $.XirtModalObject(options);
		var $modal = (new $.XirtModal($el, options)).init().show();

		setTimeout(function() {

			$el.on('hidden.bs.modal', function (e) {
				if ($.type(options.callback) == "function") {
					options.callback();
				}
			});

			$modal.hide();

		}, 1500);

	};


	$.XirtConfirmation = function(options) {

		options.buttons = [

			{
				id	: "ok",
				type	: "warning",
				label	: "Ok"

			},
			{
				id	: "close",
				type	: "default",
				label	: "Cancel"

			}

		];

		var $el = new $.XirtModalObject(options);
		var $modal = (new $.XirtModal($el, options)).init().show();

		// Active button 'Ok'
		$el.find(".btn").off("click").on("click", function() {

			$modal.hide();
			options.callback($(this).hasClass("btn-warning"));

		});

	};


	$.XirtModal = function(element, options) {

		this.element = (element instanceof $) ? element : $(element);
		this._options = $.extend({}, {
			backdrop: "static",
			keyboard: false,
			show: false
		}, options);

	};

	$.XirtModal.prototype = {

		init: function () {

			var that = this;

			// Create modal
			$(this.element).modal({
				backdrop: this._options.backdrop,
				keyboard: this._options.keyboard,
				show: this._options.show
			});

			// Activate closure button
			this.element.find(".btn-close").on("click", function() {

				// Check for content changes
				var isDirty = (that._initState != that.element.find("form").serialize());
				$.each(that._options.editors, function(i, editor) {
					isDirty = editor.isDirty() ? true : isDirty;
				});

				if (isDirty) {

					new $.XirtConfirmation({

						backdrop: false,
						title: "Confirm cancellation",
						message: "Are you sure that you want to close without saving?",
						type: "warning",
						callback: function(result) {

							if (result) {
								that.hide();
							}

						}

					});

					return;

				}

				that.hide();

			});

			return this;

		},

		load: function(options) {

			var that = this;
			_options = $.extend({
				url:	  "index.php",
				onLoad:   function() {},
				autoShow: true
			}, options);

			Xirt.showSpinner();
			$.getJSON(_options.url, function (json) {

				Xirt.hideSpinner();
				_options.onLoad(json);

				if (_options.autoShow) {
					that.show();
				}

			});

		},

		show: function() {

			this._initState = this.element.find("form").serialize();
			this.element.modal("show");
			return this;

		},

		hide: function() {

			// Optionally reset form values
			if (this._options.resetForms) {
				this.element.find("input").val("");
			}

			// Show the modal
			this.element.modal("hide");
			return this;

		},
		
		getElement: function() {
			return this.element;			
		}

	};

}(jQuery));

/* TODO:: Replace placeholder for JQuery validation messages
required: "A value for this field is required prior to submitting.",
minlength: jQuery.validator.format("The minimum length for this field is {0} characters."),
maxlength: jQuery.validator.format("The maximum length for this field is {0} characters.")
alpha_dashdot: "Only alphanumeric characters, periods and dashes are allowed."
email: "Only valid e-mail addresses are allowed for this field."
equalTo: "The provided password should match to prevent accidental mistakes."
*/