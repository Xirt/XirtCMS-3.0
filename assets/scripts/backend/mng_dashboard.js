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

			// Activate "Clear cache"-button
			$('.btn-clear-cache').click(function() {
				that._clearCache();
			});

			// Activate "View logs"-button
			$('.btn-view-logs').click(function() {
				that._retrieveLog();
			});

			// Activate "Clear logs"-button
			$('.btn-clear-logs').click(function() {
				that._clearLogs();
			});

			// Activate "View logs"-button
			$("#logPanel").find(".btn-prev").click(function() {
				that._retrieveLog($(this).data("log-id"));
			});

			// Activate "View logs"-button
			$("#logPanel").find(".btn-next").click(function() {
				that._retrieveLog($(this).data("log-id"));
			});

		},


		_retrieveLog: function(id) {

			// Activate "View logs"-button
			Xirt.showSpinner();
			$("#logBoard").css("height", $(window).height() - 250);
			$.ajax("backend/dashboard/get_logfile/" + (id ? id : ""), {

				cache: false,
				success : function(data) {

					var log = $("#logBoard").empty();
					$.each(data.content, function(key, txt) {
						log.append(txt + "<br />");
					});

					$("#logPanel").find(".btn-prev").data("log-id", data.prev_id).toggle(data.prev_id !== null);
					$("#logPanel").find(".btn-next").data("log-id", data.next_id).toggle(data.next_id !== null);

					Xirt.hideSpinner();
					logModal.show();

				}

			});

		},

		_clearLogs: function() {

			// Activate "View logs"-button
			new $.XirtConfirmation({

				title: "Confirm deletion",
				message: "Are you sure that you want to permanently delete all logs?",
				type: "warning",
				callback: function(result) {

					if (result) {

						$.ajax("backend/dashboard/clear_logs/", {
							success: function() {
								location.reload();
							}
						});

					}

				}

			});

		},

		_clearCache: function() {

			// Activate "View logs"-button
			new $.XirtConfirmation({

				title: "Confirm deletion",
				message: "Are you sure that you want to clear the cache?",
				type: "warning",
				callback: function(result) {

					if (result) {

						$.ajax("backend/dashboard/clear_cache/", {
							success: function() {
								location.reload();
							}
						});

					}

				}

			});

		}


	};


	/***********
	 * TRIGGER *
	 **********/
	var logModal;
	(new $.PageManager()).init();

});