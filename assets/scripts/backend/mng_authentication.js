$(function() {

	/****************
	 * PAGE MANAGER *
	 ****************/
	$.PageManager = function() {
	};

	$.PageManager.prototype = {

		init: function() {

			this._initButtons();
			this._initForms();

		},


		_initForms: function() {

			// Activate "Login"-GUI
			new Form.validate($("#form-login"), {

				requestOptions : {

					success: function(data) {

						new $.XirtMessage({
							title    : data.title,
							message  : data.message,
							type     : data.type,
							callback : function() {
								if (data.type == "success") {
									location.reload(true);
								}
							}
						});

					},

				},

				rules: {
					user_name: { required: true },
					user_password: { required: true }
				}

			});

			// Activate "Request"-GUI
			Form.validate("#form-request", {

				currentModal: modifyModal,
				nextModal: modifyModal,
				grid: this.grid,
				rules: {
					request_name: { required: true },
					request_email: { required: true, email: true }
				}

			});

		},

		_initButtons: function() {

			var speed = 300;

			// Toggle loginModal to requestModal
			$(".btn-request").on("click",function() {
				$("#form-login").fadeToggle(speed, function() {
					$("#form-request").fadeToggle(speed);
				});
			});

			// Toggle loginModal to requestModal
			$(".btn-login").on("click", function() {
				$("#form-request").fadeToggle(speed, function() {
					$("#form-login").fadeToggle(speed);
				});
			});

		}

	};


	/***********
	 * TRIGGER *
	 **********/
	(new $.PageManager()).init();

});