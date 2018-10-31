window.CTCTSupport = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
		that.removePlaceholder();
	};

	that.removePlaceholder = function() {
		$( '.ctct-form-field input,textarea' ).focus( function() {
			$( this ).data( 'placeholder', $( this ).attr( 'placeholder' ) ).attr( 'placeholder', '' );
		}).blur( function() {
			$( this ).attr( 'placeholder', $( this ).data( 'placeholder' ) );
		});
	};

	// Cache all the things.
	that.cache = function() {
		that.$c = {
			window: $( window ),
			body: $( 'body' ),
			form: '.ctct-form-wrapper form',
			honeypot: $( '#ctct_usage_field' ),
			submitButton: $( '.ctct-form-wrapper form input[type=submit]' ),
			recaptcha: $( '.ctct-form-wrapper form .g-recaptcha' )
		};

		that.timeout = null;
	};

	that.setAllInputsValid = function() {
		$( that.$c.form + ' .ctct-invalid' ).removeClass( 'ctct-invalid' );
	};

	that.clearFormInputs = function (form_id_selector) {
		var submitted_form = $(form_id_selector + ' form');
		// jQuery doesn't have a native reset function so the [0] will convert to a JavaScript object.
		submitted_form[0].reset();
	};

	that.processError = function( error ) {

		// If we have an id property set
		if ( typeof( error.id ) !== 'undefined' ) {
			$( '#' + error.id ).addClass( 'ctct-invalid' );
		}

	};

	/**
	 * Check the value of the hidden honeypot field.
	 * If there is anything in it, disable the form submission button.
	 */
	that.checkHoneypot = function() {
		var honeypot_length = that.$c.honeypot.val().length;

		// If there is text in the honeypot, disable the submit button
		if( honeypot_length > 0 ) {
			that.$c.submitButton.attr( 'disabled', 'disabled' );
		} else {
			that.$c.submitButton.attr( 'disabled', false );
		}
	};

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.form ).on( 'click', 'input[type=submit]', function(e) {

			if ('on' === $('.ctct-form').attr('data-doajax')) {
				var $form_id = $(this).closest('.ctct-form-wrapper').attr('id');
				var form_id_selector = '';
				if ( $form_id != '' ) {
					form_id_selector = '#'+ $form_id +' ';
				}
				var doProcess = true;
				$.each($(form_id_selector+'.ctct-form [required]'), function (i, field) {
					if (field.checkValidity() === false) {
						doProcess = false;
					}
				});
				if (false === doProcess) {
					return;
				}

				e.preventDefault();
				clearTimeout(that.timeout);

				that.timeout = setTimeout(function () {
					$('#ctct-submitted').prop('disabled', true);
					$.post(
						ajaxurl,
						{
							'action': 'ctct_process_form',
							'data'  : $(form_id_selector + 'form').serialize()
						},
						function (response) {
							$('#ctct-submitted').prop('disabled', false);
							// Make sure we got the 'status' attribute in our response
							if (typeof( response.status ) !== 'undefined') {

								if ( 'success' === response.status ) {
									// Add a timestamp to the message so that we only remove this message and not all at once.
									var time_class = 'message-time-' + $.now();

									var message_class = 'ctct-message ' + response.status + ' ' + time_class;
									$(form_id_selector+'.ctct-form').before('<p class="' + message_class + '">' + response.message + '</p>');

									if ( '' !== form_id_selector ) {
										that.clearFormInputs( form_id_selector );
									}
									// Set a 5 second timeout to remove the added success message.
									setTimeout( function() {
										$( '.' + time_class ).fadeOut('slow');
									}, 5000 );
								} else {
									// Here we'll want to disable the submit button and
									// add some error classes
									if (typeof( response.errors ) !== 'undefined') {
										that.setAllInputsValid();
										response.errors.forEach(that.processError);
									} else {
										$(form_id_selector + '.ctct-form').before('<p class="ctct-message ' + response.status + '">' + response.message + '</p>');
									}

								}
							}
						}
					);
				}, 500)
			}
		});

		// Look for any changes on the honeypot input field.
		$( that.$c.honeypot ).on( 'change keyup', function( e ) {
			that.checkHoneypot();
		});

		if ( that.$c.recaptcha.length > 0 ) {
			that.$c.submitButton.attr('disabled', 'disabled');
		}
    };

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );
