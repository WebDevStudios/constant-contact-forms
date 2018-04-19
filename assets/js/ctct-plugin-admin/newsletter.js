window.CTCTNewsletter = {};
(function (window, $, app) {

	// Constructor
	app.init = function () {
		app.submitNewsletter();
	};

	// Engage
	$(app.init);

	app.submitNewsletter = function() {
		// Connect page.
		$('.ctct-body #subscribe').on('submit', function (event) {
			event.preventDefault();

			var $ctctNewsWrapper = $("#subscribe .ctct-call-to-action"),
				ctctNewsForm = $(".ctct-body #subscribe")[0];

			var ctctEmailField = $(".ctct-call-to-action input[type='text']")[0],
			subscribeEndpoint = event.target.action;

			if (ctctEmailField.validity.valid === true) {
				$("<iframe>", {
					"src"   : subscribeEndpoint + "?" + $(ctctNewsForm).serialize(),
					"height": 0,
					"width" : 0,
					"style" : "display: none;"
				}).appendTo($ctctNewsWrapper);

				$('#subbutton').val('Thanks for signing up').css({'background-color':'rgb(1, 128, 0)','color':'rgb(255,255,255)'});
				$('#subscribe .ctct-call-to-action-text').css({'width':'70%'});
			} else {
				$('#subbutton').val('Error occurred');
			}
		});

		// About page.
		$('.ctct-section #subscribe').on('submit', function (event) {
			event.preventDefault();

			var $ctctNewsWrapper = $(".section-marketing-tips"),
				ctctNewsForm = $(".ctct-section #subscribe")[0];

			var ctctEmailField = $(".ctct-section #subscribe input[type='text']")[0],
				subscribeEndpoint = event.target.action;

			if (ctctEmailField.validity.valid === true) {
				$("<iframe>", {
					"src"   : subscribeEndpoint + "?" + $(ctctNewsForm).serialize(),
					"height": 0,
					"width" : 0,
					"style" : "display: none;"
				}).appendTo($ctctNewsWrapper);
				$('#subbutton').val('Thanks for signing up').css({'background-color':'rgb(1, 128, 0)'});
			} else {
				$('#subbutton').val('Error occurred');
			}
		});
	}

})(window, jQuery, window.CTCTNewsletter);
