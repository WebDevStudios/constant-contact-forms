grecaptcha.ready(function () {
	let forms = document.querySelectorAll('.ctct-form-wrapper form');
	Array.from(forms).forEach(function (form) {
		form.addEventListener('submit', (e) => {
			e.preventDefault();
			debugger;
			try {
				grecaptcha.execute(recaptchav3.site_key, {action: 'constantcontactsubmit'}).then(function (token) {
					let recaptchaResponse = document.createElement('input');
					recaptchaResponse.setAttribute('type', 'hidden');
					recaptchaResponse.setAttribute('name', 'g-recaptcha-response');
					recaptchaResponse.setAttribute('value', token);

					form.append(recaptchaResponse.cloneNode(true));
					debugger;
					form.submit();
				});
			} catch (error) {
				debugger;
				console.log(error);
				return false;
			}
		});
	});
});
