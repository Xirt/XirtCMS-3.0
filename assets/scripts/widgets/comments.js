$(function() {

	/********************
	 * COMMENTS MANAGER *
	 *******************/
	$.CommentsManager = function(el) {
		this.$_el = (el);
	};

	$.CommentsManager.prototype = {

		init: function() {

			this.$_notificationBox = this.$_el.find(".box-notification");
			this.$_commentForm =  this.$_el.find(".form-comment");

			this._initButtons();
			this._initForms();

			return this;

		},

		_initButtons: function() {

			var that = this;
			this.$_el.find(".btn-close").on("click", function(e) {

				that._hideNotificationBox();
				e.preventDefault();

			});

			this.$_el.find(".btn-comment, .btn-cancel").click(function(e) {

				for (var i = 0; i < 10; i++) {
					that.$_commentForm.removeClass("comment-level-" + i);
				}

				that.$_el.append(that.$_commentForm.removeClass("belowComment"));
				that._hideNotificationBox();

				that.$_commentForm.find("label").text("Leave response");
				that.$_el.find(".btn-comment").hide();
				that.$_el.find(".btn-cancel").hide();
				e.preventDefault();

			}).hide();

			this.$_el.find("a.react").click(function(e) {

				// Variables
				var el = $(this);
				var parent = el.parent();

				// Update form
				that.$_commentForm.addClass("belowComment");
				that.$_commentForm.find("input[name=parent_id]").val(el.data("id"));
				for (var i = 0; i < 10; i++) {

					var className = "comment-level-" + i;
					that.$_commentForm.toggleClass(className, parent.hasClass(className));

				}

				parent.after(that.$_commentForm);
				that.$_commentForm.find("textarea").focus();
				that.$_commentForm.children("label").text("Respond to " + el.data("name"));

				that.$_el.find(".btn-comment").show();
				that.$_el.find(".btn-cancel").show();
				that._hideNotificationBox();

				// Prevent default
				e.preventDefault();

			});

		},

		_initForms: function() {

			var that = this;
			this.$_commentForm.on("submit", function(e) {

				$.post("comment/create", that.$_commentForm.serialize(), function (json) {

					if (json.type != "error") {
						return location.reload();
					}

					that._showNotificationBox(json.message);

				}, "json");

				e.preventDefault();

			});

		},

		_showNotificationBox: function(message) {
			this.$_notificationBox.fadeIn().find("span").html(message);
		},

		_hideNotificationBox: function() {
			this.$_notificationBox.hide();
		}

	};


	/***********
	 * TRIGGER *
	 **********/
	(new $.CommentsManager($(".x-widget-comments"))).init();

});