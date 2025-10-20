/**
 * Front-end form validation.
 *
 * @since 1.0.0
 */

 window.CTCTSupport = {};

( function( window, app ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.init = () => {
		app.cache();
		app.bindEvents();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.cache = () => {

		app.cache = {
			forms: []
		};

		let wrapper = document.querySelectorAll('.ctct-form-wrapper');
		if (wrapper.length) {
			wrapper.forEach((formWrapper) => {
				let found = formWrapper.querySelector('form');
				if (found) {
					app.cache.forms.push(found);
				}
			});
		}

		app.cache.forms.forEach((form, index) => {
			app.cache.forms[index].honeypot = form.querySelector('.ctct_usage_field');
			app.cache.forms[index].submitButton = form.querySelector('input[type=submit]');
			app.cache.forms[index].recaptcha = form.querySelector('.g-recaptcha');
		});

		app.timeout = null;
	};

	/**
	 * Remove the ctct-invalid class from elements that have it.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.setAllInputsValid = () => {
		app.cache.forms.forEach((form) => {
			let invalid = form.querySelectorAll('.ctct-invalid');
			Array.from(invalid).forEach((field) => {
				field.classList.remove('ctct-invalid');
			});
		});
	};

	/**
	 * Adds .ctct-invalid HTML class to inputs whose values are invalid.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} error AJAX response error object.
	 */
	app.processError = ( error ) => {

		// If we have an id property set.
		if ( 'undefined' !== typeof( error.id ) ) {
			let invalid = document.querySelectorAll('#' + error.id);
			Array.from(invalid).forEach((theInvalid) => {
				theInvalid.classList.add('ctct-invalid');
			});
		}
	};

	/**
	 * Check the value of the hidden honeypot field; disable form submission button if anything in it.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} event The change or keyup event triggering this callback.
	 * @param {object} honeyPot The object for the actual input field being checked.
	 * @param {object} submitButton The object for the submit button in the same form as the honeypot field.
	 */
	app.checkHoneypot = ( event, honeyPot, submitButton ) => {
		// If there is text in the honeypot, disable the submit button.

		// Leaving this disabling in place because it should not be getting used by screen readers in the first place, and I feel it's going to help more than hurt to keep.
		if ( 0 < honeyPot.value.length ) {
			submitButton.setAttribute('disabled','disabled');
		} else {
			submitButton.removeAttribute('disabled');
		}
	};

	/**
	 * Ensures that we should use AJAX to process the specified form, and that all required fields are not empty.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} form object for the form being validated.
	 * @return {boolean} False if AJAX processing is disabled for this form or if a required field is empty.
	 */
	app.validateSubmission = ( form ) => {
		if ( 'on' !== form.getAttribute( 'data-doajax' ) ) {
			return false;
		}

		let fields = form.querySelectorAll('[required]');
		Array.from(fields).forEach((field) => {
			if (false === field.checkValidity()) {
				return false;
			}
		});

		return true;
	};

	/**
	 * Prepends form with a message that fades out in 5 seconds.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} form object for the form a message is being displayed for.
	 * @param {string} message The message content.
	 * @param {string} classes Optional. HTML classes to add to the message wrapper.
	 * @param {string} role Role attribute for accessibility.
	 */
	app.showMessage = ( form, message, classes = '', role = 'log' ) => {

		const wrapper = form.parentElement;

		if ( wrapper.querySelector('p.ctct-message') ) {
			wrapper.querySelector('p.ctct-message').remove();
		}

		let message_tag = document.createElement('p');
		message_tag.setAttribute('class', 'ctct-message ' + classes);
		message_tag.setAttribute('role', role);
		message_tag.innerHTML = message;

		let dismiss_btn = document.createElement('button');
		dismiss_btn.setAttribute('class', 'button button-secondary ctct-dismiss ctct-dismiss-ajax-notice');
		dismiss_btn.setAttribute('aria-label', 'Dismiss notification');
		dismiss_btn.innerHTML = '&#10005;';

		message_tag.prepend(dismiss_btn);

		form.parentElement.prepend(message_tag);

		wrapper.querySelector( '.ctct-dismiss-ajax-notice' ).addEventListener(
			'click',
			function() {
				this.parentElement.remove();
			}
		);
	};

	/**
	 * Submits the actual form via AJAX.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} form object for the form being submitted.
	 */
	app.submitForm = ( form ) => {
		const data = new FormData();
		const formData = new FormData(form);
		const formParams = new URLSearchParams(formData);

		data.append('action', 'ctct_process_form');
		data.append('data', formParams);

		let options = {
			method: 'POST',
			body: data
		};

		fetch(
			window.ajaxurl,
			options
		)
		.then((response)=>response.json())
		.then((response)=>{

			if ( 'undefined' === typeof response.status ) {
				return false;
			}

			if ( 'success' !== response.status ) {
				if ('undefined' !== typeof (response.errors)) {
					app.setAllInputsValid();
					response.errors.forEach(app.processError);
				} else {
					app.showMessage(form, response.message, 'ctct-error', 'alert');
				}

				return false;
			}

			form.style.display = 'none';
			// If we're here, the submission was a success; show message and reset form fields.
			app.showMessage(form, response.message, 'ctct-success', 'status');
			form.reset();
		});
	};

	/**
	 * Handle the form submission.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @param {object} event The submit event.
	 * @param {object} form object for the current form being handled.
	 * @return {boolean} False if unable to validate the form.
	 */
	app.handleSubmission = ( event, form ) => {

		if ( ! app.validateSubmission( form ) ) {
			return false;
		}

		clearTimeout( app.timeout );

		if (form.checkValidity()) {
			app.timeout = setTimeout(app.submitForm, 500, form);
		}
	};

	/**
	 * Set up event bindings and callbacks.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {
		app.cache.forms.forEach((form) => {
			let thesubmit = form.querySelector('[type=submit]');
			thesubmit.addEventListener('click', (event) => {
				let doingajax = form.getAttribute( 'data-doajax' );
				if ( doingajax && 'on' === doingajax ) {
					event.preventDefault();

					app.handlerecaptcha(form);
				}

				if ( form.classList.contains( 'ctct-submitted' ) ) {
					return;
				}
				form.classList.add( 'ctct-submitted' );
				app.handleSubmission( event, form );
				form.classList.remove( 'ctct-submitted' );
			});

			form.honeypot.addEventListener('change', (event) => {
				app.checkHoneypot(
					event,
					form.honeypot,
					form.submitButton
				);
			});

			form.honeypot.addEventListener('keyup', (event) => {
				app.checkHoneypot(
					event,
					form.honeypot,
					form.submitButton
				);
			});
		});
	};

	/**
	 * Custom handling within our validation file, for cases of reCAPTCHA v3 + AJAX submit.
	 *
	 * @param form Form being submitted.
	 */
	app.handlerecaptcha = (form) => {
		if ('undefined' === typeof (recaptchav3.site_key)) {
			return;
		}

		grecaptcha.ready(function () {
			try {
				grecaptcha.execute(recaptchav3.site_key, {action: 'constantcontactsubmit'}).then(function (token) {
					let recaptchaResponse = document.createElement('input');
					recaptchaResponse.setAttribute('type', 'hidden');
					recaptchaResponse.setAttribute('name', 'g-recaptcha-response');
					recaptchaResponse.setAttribute('value', token);

					form.append(recaptchaResponse.cloneNode(true));
				});
			} catch (error) {
				console.log(error);
			}
		});
	}

	app.init();

} ( window, window.CTCTSupport ) );
