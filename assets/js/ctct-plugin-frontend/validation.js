/**
 * Front-end form validation.
 *
 * @since 1.0.0
 */

 window.CTCTSupport = {};

( function( window, $, app ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.init = function() {
		app.cache();
		app.bindEvents();
		app.removePlaceholder();
	};

	/**
	 * Remove placeholder text values.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.removePlaceholder = function() {
		$( '.ctct-form-field input,textarea' ).focus( function() {
			$( this ).data( 'placeholder', $( this ).attr( 'placeholder' ) ).attr( 'placeholder', '' );
		} ).blur( function() {
			$( this ).attr( 'placeholder', $( this ).data( 'placeholder' ) );
		} );
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.cache = function() {
		app.$c = {
			window: $( window ),
			body: $( 'body' ),
			form: '.ctct-form-wrapper form',
			honeypot: $( '#ctct_usage_field' ),
			submitButton: $( '.ctct-form-wrapper form input[type=submit]' ),
			recaptcha: $( '.ctct-form-wrapper form .g-recaptcha' )
		};

		app.timeout = null;
	};

	/**
	 * Remove the ctct-invalid class from elements that have it.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.setAllInputsValid = function() {
		$( app.$c.form + ' .ctct-invalid' ).removeClass( 'ctct-invalid' );
	};

	/**
	 * Clears form inputs of current values.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.clearFormInputs = function( form_id_selector ) {
		var submitted_form = $( form_id_selector + ' form' );

		// jQuery doesn't have a native reset function so the [0] will convert to a JavaScript object.
		submitted_form[0].reset();
	};

	/**
	 * Adds .ctct-invalid HTML class to inputs whose values are invalid.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.processError = function( error ) {

		// If we have an id property set.
		if ( 'undefined' !== typeof( error.id ) ) {
			$( '#' + error.id ).addClass( 'ctct-invalid' );
		}
	};

	/**
	 * Check the value of the hidden honeypot field; disable form submission button if anything in it.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.checkHoneypot = function() {
		var honeypot_length = app.$c.honeypot.val().length;

		// If there is text in the honeypot, disable the submit button
		if ( 0 < honeypot_length ) {
			app.$c.submitButton.attr( 'disabled', 'disabled' );
		} else {
			app.$c.submitButton.attr( 'disabled', false );
		}
	};

	/**
	 * Set up event bindings and callbacks.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = function() {
		$( app.$c.form ).on( 'click', 'input[type=submit]', function( e ) {

			if ( 'on' === $( '.ctct-form' ).attr( 'data-doajax' ) ) {

				var $form_id         = $( this ).closest( '.ctct-form-wrapper' ).attr( 'id' );
				var form_id_selector = '';

				if ( '' !== $form_id ) {
					form_id_selector = '#' + $form_id + ' ';
				}

				var doProcess = true;

				$.each( $( form_id_selector + '.ctct-form [required]' ), function( i, field ) {
					if ( false === field.checkValidity() ) {
						doProcess = false;
					}
				} );

				if ( false === doProcess ) {
					return;
				}

				e.preventDefault();

				clearTimeout( app.timeout );

				app.timeout = setTimeout( function() {
					$( '#ctct-submitted' ).prop( 'disabled', true );
					$.post(
						ajaxurl,
						{
							'action': 'ctct_process_form',
							'data': $( form_id_selector + 'form' ).serialize()
						},
						function( response ) {
							$( '#ctct-submitted' ).prop( 'disabled', false );

							// Make sure we got the 'status' attribute in our response.
							if ( 'undefined' !== typeof( response.status ) ) {

								if ( 'success' === response.status ) {

									// Add a timestamp to the message so that we only remove this message and not all at once.
									var time_class = 'message-time-' + $.now();

									var message_class = 'ctct-message ' + response.status + ' ' + time_class;
									$( form_id_selector + '.ctct-form' ).before( '<p class="' + message_class + '">' + response.message + '</p>' );

									if ( '' !== form_id_selector ) {
										app.clearFormInputs( form_id_selector );
									}

									// Set a 5 second timeout to remove the added success message.
									setTimeout( function() {
										$( '.' + time_class ).fadeOut( 'slow' );
									}, 5000 );
								} else {

									// Here we'll want to disable the submit button and add some error classes.
									if ( 'undefined' !== typeof( response.errors ) ) {
										app.setAllInputsValid();
										response.errors.forEach( app.processError );
									} else {
										$( form_id_selector + '.ctct-form' ).before( '<p class="ctct-message ' + response.status + '">' + response.message + '</p>' );
									}

								}
							}
						}
					);
				}, 500 );
			}
		} );

		/**
		 * Listen for changes on the honeypot input field.
		 *
		 * @author Constant Contact
		 * @since 1.0.0
		 */
		$( app.$c.honeypot ).on( 'change keyup', function() {
			app.checkHoneypot();
		} );

		/**
		 * Disable the submit button by default until the captcha is passed (if captcha exists).
		 *
		 * @author Constant Contact
		 * @since 1.0.0
		 */
		if ( 0 < app.$c.recaptcha.length ) {
			app.$c.submitButton.attr( 'disabled', 'disabled' );
		}
	};


	$( app.init );

} ( window, jQuery, window.CTCTSupport ) );
