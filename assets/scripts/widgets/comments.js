// TODO :: Drafty code; to be refactured (incl. template)
$(document).ready(function() {

	$("#errorBox .close").on("click", function(e) {
		
		$("#errorBox").hide();
		e.preventDefault();
		
	});

	var form = $("#commentBox").submit(function(e) {

		$.post("comment/create", form.serialize(), function (json) {

			if (json.type != "error") {
				return location.reload();
			}

			showNotificationBox(json.message);

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

		$("#commentButton").show();
		$("#cancelButton").show();
		hideNotificationBox();

		// Prevent default
		e.preventDefault();

	});

	$("#commentButton, #cancelButton").click(function(e) {

		var form = $("#commentBox");
		for (var i = 0; i < 10; i++) {
			form.removeClass("comment-level-" + i);
		}

		$(".x-widget-comments").append(form.removeClass("belowComment"));
		hideNotificationBox();
		
		form.find("label").text("Leave response");
		$("#commentButton").hide();
		$("#cancelButton").hide();
		e.preventDefault();

	});	

});

function showNotificationBox(message) {
	
	var notificationBox = $("#errorBox").fadeIn();
	notificationBox.find("span").html(message);
	
}

function hideNotificationBox() {
	$("#errorBox").fadeOut();
}
