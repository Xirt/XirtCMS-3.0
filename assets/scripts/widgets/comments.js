$(document).ready(function() {

	var form = $("#commentBox").submit(function(e) {

		$.post("comment/create", form.serialize(), function (json) {

			if (json.type != "error") {
				return location.reload();
			}

			Xirt.removeNotifications();
			form.notification(json.message, {
				type 		: "danger",
				position	: "top"
			});

		}, "json");

		e.preventDefault();

	});

	// Activate comment links
	$("#commentButton, #cancelButton").hide();
	$(".x-widget-comments a.react").click(function(e) {

		// Variables
		var el = $(this);
		var parent = el.parent();
		var form = $("#commentBox");

		// Update form
		form.addClass("belowComment");
		form.find("input[name=parent_id]").val(el.data("id"));
		for (var i = 0; i < 10; i++) {

			var className = "comment-level-" + i;
			form.toggleClass(className, parent.hasClass(className));

		}

		parent.after(form);
		form.find("textarea").focus();
		form.children("label").text("Respond to " + el.data("name"));
		Xirt.removeNotifications();
		$("#commentButton").show();
		$("#cancelButton").show();

		// Prevent default
		e.preventDefault();

	});

	$("#commentButton, #cancelButton").click(function(e) {

		var form = $("#commentBox");
		for (var i = 0; i < 10; i++) {
			form.removeClass("comment-level-" + i);
		}

		$(".x-widget-comments").append(form.removeClass("belowComment"));
		Xirt.removeNotifications();
		form.find("label").text("Leave response");
		$("#commentButton").hide();
		$("#cancelButton").hide();
		e.preventDefault();

	});

});