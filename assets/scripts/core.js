var Form = Form ? Form : [];

$(document).ready(function() {

	if (typeof BootstrapDialog != 'undefined') {

		BootstrapDialog.configDefaultOptions({
			btnsOrder: BootstrapDialog.BUTTONS_ORDER_OK_CANCEL
		});

	}

	// Trims form input on blur
	$("input").change(function() {
		$(this).val($(this).val().trim());
	});

	Xirt.activate();

});


(function ($) {

	$.fn.notification = function(text, options) {

		var settings = $.extend({
			position: "bottom",
			type: "info"
		}, options);

		var alert = $("<div class='alert alert-" + settings.type + " alert-dismissable'>")
			.append("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>")
			.append(text);

		return (settings.position == "top") ? this.prepend(alert) : this.append(alert);

	};

}(jQuery));



/*********************************************************
*          XIRT - Utility Library for XirtCMS            *
*              (version 2.0 - 12.01.2014)                *
**********************************************************/
var Xirt = {

	activate : function() {

		// External links
		$.each($("a[rel*=external]"), function(index, el) {

			$(el).attr("target", "_blank");
			$(el).addClass("external");

		});

	},

	notification : function (target, text, options) {
		$(target).notification(text, options);
	},

	removeNotifications : function() {
		$(".alert").hide();
	},












	noAJAX : function() {
		alert("Request failed");
	},

	error : function(text) {
		$.jGrowl(text, { themeState : "error" });
	},

	notice : function(text) {
		$.jGrowl(text);
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
				result = result + " " + XCMS.createButton(button.classNames, button.icon, button.data, button.additionalAttributes);
			});

			return result;

		},

		// Current method (replaced once BootGrid has been rewritten)
		createButton : function(classNames, icon, data, attributes) {

			var dataString = "";
			$.each(data, function(key, value) {
				dataString = dataString + " data-" + key + "=\"" + value + "\"";
			});

			attributes = (typeof attributes == "undefined") ? "" : attributes;

			return "<button type=\"button\" class=\"btn btn-xs btn-default " + classNames + "\"" + dataString + " " + attributes + "><span class=\"fa fa-" + icon + "\"></span></button>";

		},

		// Future method (once BootGrid has been rewritten)
		triggerDeletion : function(id, url) {

			BootstrapDialog.confirm({

				backdrop: false,
				title: "Confirm deletion",
				message: "Are you sure that you want to permanently delete item #" + Xirt.pad(id.toString(), 5, "0") + "?",
				type: BootstrapDialog.TYPE_WARNING,
				callback: function(result) {

					if (result) {

						$.ajax({
							url: url + id,
						}).done(function() {
							$("#grid-basic").bootgrid("reload");
						});

					}

				}

			});

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

		rules: options.rules,
		messages: options.messages,
		onfocusout: function (element) { return $(element).valid(); },
		onfocusin: function (element) { return $(element).valid(); },
		onkeyup: function (element) { return $(element).valid(); },

		showErrors: function (errorMap, errorList) {

			$(targetForm).find( "input, select, textarea, [contenteditable]").each(function (i, candidate) {

				candidate = $(candidate);

				var error = false;
				for (var key in errorList) {

					if (candidate.is($(errorList[key].element))) {
						error = errorList[key].message;
						continue;
					}

				}

				if (!error) {

					if (candidate.data("bs.popover.body")) {

						candidate.removeData("bs.popover.body");
						candidate.popover("destroy");

					}

					return;

				}

				if (!candidate.data('bs.popover.body')) {

					candidate.popover({
						content: error,
						container: 'body',
						placement: "right",
						trigger: "manual"
					}).popover('show');

				} else if (candidate.data("bs.popover.body") != error) {

					var dataPopover = candidate.data('bs.popover');
					dataPopover.tip().find('.popover-content').html(error);

				}

				candidate.data("bs.popover.body", error);

			});

		},

		submitHandler: function(form, e) {

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
								BootstrapDialog.TYPE_DANGER,
								options.currentModal,
								options.targetModal,
								data.title,
								data.message
							);

						case "info":
							return _showDialogAndList(
								BootstrapDialog.TYPE_INFO,
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
		var that = BootstrapDialog.show({
			type     : type,
			title    : title,
			message  : message,
			closable : false,
			onshown  : function(e) {
				setTimeout(function() {
					that.close();
					nextModal.show();
				}, 2500);
			}
		});

	}

	// Show dialog and show overview (list)
	function _showDialogAndList(type, triggerModal, grid, title, message) {

		triggerModal.hide();
		var that = BootstrapDialog.show({
			type     : type,
			title    : title,
			message  : message,
			closable : false,
			onshown  : function(e) {
				setTimeout(function() {
					if (grid) typeof grid.reload == "function" ? grid.reload() : grid.bootgrid("reload");
					that.close();
				}, 1000);
			}
		});

	}

}

/*********************************************************
*        FORM.REQUEST - Default Form Submit (AJAX)       *
*                (version 1.0 - 13.01.2014)              *
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

//console.log($(form).serializeArray());
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


/**********************************************
*        XirtModal - Default Xirt Modal       *
*          (version 1.0 - 22.10.2017)         *
**********************************************/
(function ($) {

	// Constructor
	$.XirtModal = function(element, options) {

		this.element = (element instanceof $) ? element : $(element);
		this.options = $.extend({}, {
			resetForms: true,
			editors:	[],
			backdrop:   false,
			keyboard:   false
		}, options);

	};

	$.XirtModal.prototype = {

		init: function () {

			var that = this;

			// Create modal
			$(this.element).modal({
				backdrop: this.options.backdrop,
				keyboard: this.options.keyboard
			}).hide();

			// Fix for slide-effect
			this.element.modal("hide");

			// Activate closure button
			this.element.find(".btn-close").on("click", function() {

				// Check for content changes
				var isDirty = (that._initState != that.element.find("form").serialize());
				$.each(that.options.editors, function(i, editor) {
					isDirty = editor.isDirty() ? true : isDirty;
				});

				if (isDirty) {

					BootstrapDialog.confirm({

						backdrop: false,
						title: "Confirm cancellation",
						message: "Are you sure that you want to close without saving?",
						type: BootstrapDialog.TYPE_WARNING,
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
			options = $.extend({
				url:      "index.php",
				onLoad:   function() {},
				autoShow: true
			}, options);

			Xirt.showSpinner();
			//this.element.find("form").reset();
			$.getJSON(options.url, function (json) {

				Xirt.hideSpinner();
				options.onLoad(json);

				if (options.autoShow) {
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
			if (this.options.resetForms) {
				this.element.find("input").val("");
			}

			// Show the modal
			this.element.modal("hide");
			return this;

		}

	};

}(jQuery));