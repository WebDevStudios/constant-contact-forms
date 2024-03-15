window.CTCTNewsletter = {};

( function( window, $, app ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.init = () => {
		app.submitNewsletter();
	};

	/**
	 * Handle newsletter signups on the "Connect" and "About" pages.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.submitNewsletter = () => {

		// Connect page.
		$( '.ctct-body #subscribe' ).on( 'submit', ( event ) => {
			event.preventDefault();

			var $ctctNewsWrapper  = $( '#subscribe .ctct-call-to-action' );
			var ctctNewsForm      = $( '.ctct-body #subscribe' )[0];
			var ctctEmailField    = $( '.ctct-call-to-action input[type="text"]' )[0];
			var subscribeEndpoint = event.target.action;

			if ( true === ctctEmailField.validity.valid ) {
				$( '<iframe>', {
					'src': subscribeEndpoint + '?' + $( ctctNewsForm ).serialize(),
					'height': 0,
					'width': 0,
					'style': 'display: none;'
				} ).appendTo( $ctctNewsWrapper );

				$( '#subbutton' ).val( 'Thanks for signing up' ).css( { 'background-color': 'rgb(1, 128, 0)', 'color': 'rgb(255,255,255)' } );
				$( '#subscribe .ctct-call-to-action-text' ).css( { 'width': '70%' } );
			} else {
				$( '#subbutton' ).val( 'Error occurred' );
			}
		} );

		// About page.
		$( '.ctct-section #subscribe' ).on( 'submit', ( event ) => {
			event.preventDefault();

			var $ctctNewsWrapper  = $( '.section-marketing-tips' );
			var ctctNewsForm      = $( '.ctct-section #subscribe' )[0];
			var ctctEmailField    = $( '.ctct-section #subscribe input[type="text"]' )[0];
			var subscribeEndpoint = event.target.action;

			if ( true === ctctEmailField.validity.valid ) {
				$( '<iframe>', {
					'src': subscribeEndpoint + '?' + $( ctctNewsForm ).serialize(),
					'height': 0,
					'width': 0,
					'style': 'display: none;'
				} ).appendTo( $ctctNewsWrapper );
				$( '#subbutton' ).val( 'Thanks for signing up' ).css( { 'background-color': 'rgb(1, 128, 0)' } );
			} else {
				$( '#subbutton' ).val( 'Error occurred' );
			}
		} );
	};

	$( app.init );

} ( window, jQuery, window.CTCTNewsletter ) );
