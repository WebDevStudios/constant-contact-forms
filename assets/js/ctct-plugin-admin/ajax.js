window.CTCTAJAX = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		// Trigger any field modifications we need to do
		that.handleOptinAJAX();
		that.handleReviewAJAX();
	}

	// We need to manipulate our form builder a bit. We do this here.
	that.handleOptinAJAX = function() {

		// Handles GA opt-in confirmation for admin notice.
		$('#ctct_admin_notice_tracking_optin').on('click',function(e){
			var ctct_optin_ajax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin': ($(this).is(':checked')) ? 'on' : 'off'
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_optin_ajax,
				dataType: 'json',
				success : function (response) {},
				error: function(x, t, m){
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
			$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
		});

		// Opens the privacy modal once clicking on the checkbox to opt in via the admin notice.
		$('#ctct-connect-ga-optin a').on('click', function (e) {
			var ctct_optin_ajax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin' : $(this).attr('data-allow')
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_optin_ajax,
				dataType: 'json',
				success : function (response) {
					$('.ctct-connected-opt-in').hide();
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});

		$('#_ctct_data_tracking').on('click', function(e) {
			$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
		});

		// Unchecks the value if they have closed the privacy modal without agreeing/disagreeing.
		// We won't force them to agree.
		$('.ctct-modal-close').on('click', function(e){
			if( $('#_ctct_data_tracking').is(':checked')){
				$('#_ctct_data_tracking').attr('checked', false);
			}
		});

		// Handle the agreeing or disagreeing regarding privacy modal.
		$('#ctct-modal-footer-privacy a').on('click',function(e){
			var ctct_privacy_ajax = {
				'action': 'constant_contact_privacy_ajax_handler',
				'privacy_agree' : $(this).attr('data-agree')
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_privacy_ajax,
				dataType: 'json',
				success : function (response) {
					$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
					if( 'false' === ctct_privacy_ajax.privacy_agree ) {
						if ($('#_ctct_data_tracking').is(':checked')) {
							$('#_ctct_data_tracking').attr('checked', false);
						}
					}
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});

	};

	// Handle saving the decision regarding the review prompt admin notice.
	that.handleReviewAJAX = function() {
		$('#ctct-admin-notice-review_request').on('click', 'a', function (e) {
			var ctct_action = 'dismissed';
			if ( $(this).hasClass('ctct-review') ) {
				ctct_action = 'reviewed';
			}
			var ctct_review_ajax = {
				'action': 'constant_contact_review_ajax_handler',
				'ctct-review-action': ctct_action
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_review_ajax,
				dataType: 'json',
				success: function (resp) {
					if (window.console) {
						console.log(resp);
					}
					e.preventDefault();
					$('#ctct-admin-notice-review_request').hide();
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTAJAX );
