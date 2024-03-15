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
		$( '#ctct-admin-notice-review_request' ).on( 'click', 'a', ( e ) => {

			var ctctAction = 'dismissed';

			if ( $( this ).hasClass( 'ctct-review' ) ) {
				ctctAction = 'reviewed';
			}

			var ctctReviewAjax = {
				'action': 'constant_contact_review_ajax_handler',
				'ctct-review-action': ctctAction
			};

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

	$( that.init );

} ( window, jQuery, window.CTCTAJAX ) );
