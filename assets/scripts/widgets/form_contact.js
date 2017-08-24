$(document).ready(function() {

	var form = $("#formContactBox").submit(function(e) {

		$.post("contact/post", form.serialize(), function (json) {

			if (json.type != "error") {
                return form.empty().append(json.message);
			}

			Xirt.removeNotifications();
			form.notification(json.message, {
				type 		: "danger",
				position	: "top"
			});

		}, "json");

		e.preventDefault();

	});

});