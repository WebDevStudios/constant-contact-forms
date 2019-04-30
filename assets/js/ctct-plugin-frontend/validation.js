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
		$( '.ctct-form-field input, textarea' ).focus( function() {
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
	app.cache = () => {

		app.$c = {
			$forms: []
		};

		// Cache each form on the page.
		$( '.ctct-form-wrapper' ).each( ( i, formWrapper ) => {
			app.$c.$forms.push( $( formWrapper ).find( 'form' ) );
		} );

		// For each form, cache its common elements.
		$.each( app.$c.$forms, ( i, form ) => {

			var $form = $( form );

			app.$c.$forms[ i ].$honeypot     = $form.find( '#ctct_usage_field' );
			app.$c.$forms[ i ].$submitButton = $form.find( 'input[type=submit]' );
			app.$c.$forms[ i ].$recaptcha    = $form.find( '.g-recaptcha' );
		} );

		app.timeout = null;
	};

	/**
	 * Remove the ctct-invalid class from elements that have it.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.setAllInputsValid = function() {
		$( app.$c.$form + ' .ctct-invalid' ).removeClass( 'ctct-invalid' );
	};

	/**
	 * Clears form inputs of current values.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {string} formIdSelector The selector for the form that has just been submitted.
	 */
	app.clearFormInputs = function( formIdSelector ) {

		// jQuery doesn't have a native reset function so the [0] will convert to a JavaScript object.
		var submittedForm = $( formIdSelector + ' form' );
		submittedForm[0].reset();
	};

	/**
	 * Adds .ctct-invalid HTML class to inputs whose values are invalid.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} error AJAX response error object.
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
	 *
	 * @param {object} e The change or keyup event triggering this callback.
	 * @param {object} $honeyPot The jQuery object for the actual input field being checked.
	 * @param {object} $submitButton The jQuery object for the submit button in the same form as the honeypot field.
	 */
	app.checkHoneypot = ( e, $honeyPot, $submitButton ) => {

		// If there is text in the honeypot, disable the submit button
		if ( 0 < $honeyPot.val().length ) {
			$submitButton.attr( 'disabled', 'disabled' );
		} else {
			$submitButton.attr( 'disabled', false );
		}
	};

	/**
	 * Ensures that we should use AJAX to process the specified form, and that all required fields are not empty.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} $form jQuery object for the form being validated.
	 * @return {boolean} False if AJAX processing is disabled for this form or if a required field is empty.
	 */
	app.validateSubmission = function( $form ) {

		if ( 'on' !== $form.attr( 'data-doajax' ) ) {
			return false;
		}

		// Ensure all required fields in this form are valid.
		$.each( $form.find( '[required]' ), ( i, field ) => {

			if ( false === field.checkValidity() ) {
				return false;
			}
		} );

		return true;
	};

	/**
	 * Submits the actual form via AJAX.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} $form jQuery object for the form being submitted.
	 */
	app.submitForm = function( $form ) {

		$form.find( '#ctct-submitted' ).prop( 'disabled', true );

		var ajaxData = {
			'action': 'ctct_process_form',
			'data': $form.serialize()
		};

		$.post( window.ajaxurl, ajaxData, function( response ) {

			$form.find( '#ctct-submitted' ).prop( 'disabled', false );

			// Make sure we got the 'status' attribute in our response.
			if ( 'undefined' !== typeof( response.status ) ) {

				if ( 'success' === response.status ) {

					// Add a timestamp to the message so that we only remove this message and not all at once.
					var timeClass = 'message-time-' + $.now();

					var messageClass = 'ctct-message ' + response.status + ' ' + timeClass;

					$form.before( '<p class="' + messageClass + '">' + response.message + '</p>' );

					if ( '' !== formIdSelector ) {
						app.clearFormInputs( formIdSelector );
					}

					// Set a 5 second timeout to remove the added success message.
					setTimeout( function() {
						$( '.' + timeClass ).fadeOut( 'slow' );
					}, 5000 );

				} else {

					// Here we'll want to disable the submit button and add some error classes.
					if ( 'undefined' !== typeof( response.errors ) ) {
						app.setAllInputsValid();
						response.errors.forEach( app.processError );
					} else {
						$form.before( '<p class="ctct-message ' + response.status + '">' + response.message + '</p>' );
					}

				}
			}
		} );
	};

	/**
	 * Handle the form submission.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} e The submit event.
	 * @param {object} $form jQuery object for the current form being handled.
	 * @return {boolean} False if unable to validate the form.
	 */
	app.handleSubmission = ( e, $form ) => {

		if ( ! app.validateSubmission( $form ) ) {
			return false;
		}

		e.preventDefault();

		clearTimeout( app.timeout );

		app.timeout = setTimeout( app.submitForm, 500, $form );
	};

	/**
	 * Set up event bindings and callbacks.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {

		// eslint-disable-next-line no-unused-vars
		$.each( app.$c.$forms, ( i, form ) => {

			// Attach submission handler to each form's Submit button.
			app.$c.$forms[ i ].on( 'click', 'input[type=submit]', ( e ) => {
				app.handleSubmission( e, app.$c.$forms[ i ] );
			} );

			// Ensure each form's honeypot is checked.
			app.$c.$forms[ i ].$honeypot.on( 'change keyup', ( e ) => {
				app.checkHoneypot(
					e,
					app.$c.$forms[ i ].$honeypot,
					app.$c.$forms[ i ].$submitButton
				);
			} );

			// Disable the submit button by default until the captcha is passed (if captcha exists).
			if ( 0 < app.$c.$forms[ i ].$recaptcha.length ) {
				app.$c.$forms[ i ].$submitButton.attr( 'disabled', 'disabled' );
			}

		} );
	};

	$( app.init );

} ( window, jQuery, window.CTCTSupport ) );
