grecaptcha.ready(function () {
	grecaptcha.execute( recaptchav3.site_key, {action: 'constantcontactsubmit'} ).then( function ( token ) {
		let forms = document.querySelectorAll( '.ctct-form-wrapper form' );
		let recaptchaResponse = document.createElement('input');
		recaptchaResponse.setAttribute('type', 'hidden');
		recaptchaResponse.setAttribute('name', 'g-recaptcha-response');
		recaptchaResponse.setAttribute('value', token);

		Array.from( forms ).forEach( function( form ) {
			form.append(recaptchaResponse.cloneNode(true));
		} );
	});
});
