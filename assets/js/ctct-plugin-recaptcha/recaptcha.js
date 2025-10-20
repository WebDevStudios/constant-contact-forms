grecaptcha.ready(function () {
	let forms = document.querySelectorAll('.ctct-form-wrapper form');
	Array.from(forms).forEach(function (form) {
		// Do not attempt to process if form is submitting via ajax.
		let doingajax = form.getAttribute('data-doajax');
		if (doingajax && 'on' === doingajax) {
			return;
		}
		form.addEventListener('submit', (e) => {
			e.preventDefault();

			try {
				grecaptcha.execute(recaptchav3.site_key, {action: 'constantcontactsubmit'}).then(function (token) {
					let recaptchaResponse = document.createElement('input');
					recaptchaResponse.setAttribute('type', 'hidden');
					recaptchaResponse.setAttribute('name', 'g-recaptcha-response');
					recaptchaResponse.setAttribute('value', token);

					form.append(recaptchaResponse.cloneNode(true));

					// Because of how we're ending up submitting at this point. we are losing
					// the original name attribute and "value" from the original submit button.
					// Here we are instead just creating a hidden element with the "ctct-submitted"
					// name attribute to met things proceed on the server.
					let origBtnVal = document.createElement('input');
					origBtnVal.setAttribute('type', 'hidden');
					origBtnVal.setAttribute('name', 'ctct-submitted');
					origBtnVal.setAttribute('value', 'true');
					form.append(origBtnVal);

					form.submit();
				});
			} catch (error) {
				console.log(error);
				return false;
			}
		});
	});
});
