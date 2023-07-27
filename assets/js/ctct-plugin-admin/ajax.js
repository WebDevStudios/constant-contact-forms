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
		that.handleOptinAJAX();
		that.handleReviewAJAX();
	};

	/**
	 * We need to manipulate our form builder a bit. We do this here.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.handleOptinAJAX = () => {

		// eslint-disable-next-line no-unused-vars
		$( '#ctct_admin_notice_tracking_optin' ).on( 'click', ( e ) => {
			var ctctOptinAjax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin': ( $( this ).is( ':checked' ) ) ? 'on' : 'off',
				'ctct_option_from_notification': $('#ctct_option_from_notification').val(),
			};

			$.ajax( {
				url: window.ajaxurl,
				data: ctctOptinAjax,
				dataType: 'json',
				success: ( response ) => {
					$( document.getElementById( 'ctct-admin-notice-optin_admin_notice' ) ).remove();
				},
				error: ( x, t, m ) => {
					if ( window.console ) {
						console.log( [ t, m ] );
					}
				}
			} );

			$( '#ctct-privacy-modal' ).toggleClass( 'ctct-modal-open' );
		} );

		// Opens the privacy modal once clicking on the checkbox to opt in via the admin notice.
		// eslint-disable-next-line no-unused-vars
		$( '#ctct-connect-ga-optin a' ).on( 'click', ( e ) => {
			var ctctOptinAjax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin': $( this ).attr( 'data-allow' )
			};

			$.ajax( {
				url: window.ajaxurl,
				data: ctctOptinAjax,
				dataType: 'json',
				success: ( response ) => { // eslint-disable-line no-unused-vars
					$( '.ctct-connected-opt-in' ).hide();
				},
				error: ( x, t, m ) => {
					if ( window.console ) {
						console.log( [ t, m ] ); // eslint-disable-line no-console
					}
				}
			} );
		} );

		// eslint-disable-next-line no-unused-vars
		$( '#_ctct_data_tracking' ).on( 'click', ( e ) => {
			$( '#ctct-privacy-modal' ).toggleClass( 'ctct-modal-open' );
		} );

		// Unchecks the value if they have closed the privacy modal without agreeing/disagreeing.
		// eslint-disable-next-line no-unused-vars
		$( '.ctct-modal-close' ).on( 'click', ( e ) => {
			var $checkbox = $( '#_ctct_data_tracking' );
			if ( $checkbox.is( ':checked' ) ) {
				$checkbox.prop( 'checked', false );
			}
		} );

		// Handle the agreeing or disagreeing regarding privacy modal.
		// eslint-disable-next-line no-unused-vars
		$( '#ctct-modal-footer-privacy a' ).on( 'click', function( e ) {
			e.preventDefault();
			let params = new URLSearchParams(e.target.search)
			let nonce = params.get('modal_privacy');
			let ctctPrivacyAjax = {
				'action'       : 'constant_contact_privacy_ajax_handler',
				'privacy_agree': $(this).attr('data-agree'),
			};
			if ( null !== nonce ) {
				ctctPrivacyAjax.nonce = nonce;
			}

			$.ajax( {
				url: window.ajaxurl,
				data: ctctPrivacyAjax,
				dataType: 'json',
				success: ( response ) => { // eslint-disable-line no-unused-vars
					if ( 'false' === ctctPrivacyAjax.privacy_agree ) {
						var $checkbox = $( '#_ctct_data_tracking' );
						if ( $checkbox.is( ':checked' ) ) {
							$checkbox.prop( 'checked', false );
						}
					}
				},
				error: ( x, t, m ) => {
					if ( window.console ) {
						console.log( [ t, m ] ); // eslint-disable-line no-console
					}
				}
			} );

			$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
		} );
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
