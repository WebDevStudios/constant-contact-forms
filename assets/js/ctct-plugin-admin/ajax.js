window.CTCTAJAX = {};

( function( window, $, that ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {

		// Trigger any field modifications we need to do.
		that.handleReviewAJAX();
	};

	// Handle saving the decision regarding the review prompt admin notice.
	that.handleReviewAJAX = () => {
		let reviewRequest = document.querySelector('#ctct-admin-notice-review_request');
		if (reviewRequest) {
			reviewRequest.addEventListener('click', (e) => {  //figure out link target specifically.
				let ctctAction = 'dismissed';
				let ctctReviewAjax = {
					'action'            : 'constant_contact_review_ajax_handler',
					'ctct-review-action': ctctAction
				}

				if ($(this).hasClass('ctct-review')) {
					ctctAction = 'reviewed';
				}

				$.ajax({
					url     : window.ajaxurl,
					data    : ctctReviewAjax,
					dataType: 'json',
					success : (resp) => {
						if (window.console) {
							console.log(resp); // eslint-disable-line no-console
						}
						e.preventDefault();
						reviewRequest.hide();
					},
					error   : (x, t, m) => {
						if (window.console) {
							console.log([t, m]); // eslint-disable-line no-console
						}
					}
				});
			})
		}
		$( '#ctct-admin-notice-review_request' ).on( 'click', 'a', ( e ) => {

			if ( $( this ).hasClass( 'ctct-review' ) ) {
				ctctAction = 'reviewed';
			}

			$.ajax( {
				url: window.ajaxurl,
				data: ctctReviewAjax,
				dataType: 'json',
				success: ( resp ) => {
					if ( window.console ) {
						console.log( resp ); // eslint-disable-line no-console
					}
					e.preventDefault();
					$( '#ctct-admin-notice-review_request' ).hide();
				},
				error: ( x, t, m ) => {
					if ( window.console ) {
						console.log( [ t, m ] ); // eslint-disable-line no-console
					}
				}
			} );
		} );
	};

	that.init();
} ( window, jQuery, window.CTCTAJAX ) );
