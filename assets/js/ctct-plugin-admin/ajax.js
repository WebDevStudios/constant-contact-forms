window.CTCTAJAX = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		// Trigger any field modifications we need to do
		that.handleOptinAJAX();
	}

	// We need to manipulate our form builder a bit. We do this here.
	that.handleOptinAJAX = function() {
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

		if ( 'no' === ctct_settings.privacy_set ) {
			$('#_ctct_data_tracking').on('click', function (e) {
				$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
			});
		}

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
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});
	};

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTAJAX );
