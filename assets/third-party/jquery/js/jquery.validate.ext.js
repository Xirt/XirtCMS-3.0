$.validator.addMethod( "alpha_dashdot", function( value, element ) {
	return this.optional( element ) || /^[A-Z0-9._-]+$/i.test( value );
}, "Only alphanumeric characters, periods, underscores and dashes are allowed." );

$.validator.addMethod( "alpha_dash", function( value, element ) {
	return this.optional( element ) || /^[A-Z0-9._-]+$/i.test( value );
}, "Only alphanumeric characters, underscores and dashes are allowed." );

$.validator.addMethod("pwcheck", function(value, element) {
	return /^[A-Za-z0-9\d=!\-@._*]+$/.test(value);
}, "Except for alphanumerical characters, only limited characters are valid.");

$.validator.addMethod("pwstrength", function(value, element) {
	return /[A-Z]/.test(value) && /[a-z]/.test(value) && /\d/.test(value);
}, "At least one lowercase, uppercase and digit are required.");

// Trims values
(function ($) {

	$.each($.validator.methods, function (key, value) {
		$.validator.methods[key] = function () {

			if (arguments.length > 0) {
				arguments[0] = $.trim(arguments[0]);
			}

			return value.apply(this, arguments);
		};
	});

} (jQuery));