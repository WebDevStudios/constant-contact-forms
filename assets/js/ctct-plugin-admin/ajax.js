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
				success : function (response) {
					console.log(response);
				},
				error: function(x, t, m){
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
