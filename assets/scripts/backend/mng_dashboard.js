$(function() {

	/****************
	 * PAGE MANAGER *
	 ****************/
	$.PageManager = function() {
	};

	$.PageManager.prototype = {

		init: function() {

			this._initModals();
			this._initButtons();

			return this;

		},

		_initModals: function(initializedEditors) {

			logModal = new $.XirtModal($("#logPanel")).init();

		},


		_initButtons: function() {

			var that = this;

			// Activate "View logs"-button
			$('.btn-view-logs').click(function() {

				that._retrieveLog();
				logModal.show();

			});

		},


		_retrieveLog: function() {

			// Activate "View logs"-button
			$("#logBoard").css("height", $(window).height() - 250);
			$.get("backend/dashboard/get_logfile/0", function(data) {
				$("#logBoard").html(data)
			});

		}




	};


	/***********
	 * TRIGGER *
	 **********/
	var logModal;
	(new $.PageManager()).init();

});