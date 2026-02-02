window.ctctsettings = {};

(function (window, that) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {
		that.cache();
		that.bindEvents();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.cache = () => {
		that.cache = {
			window   : window,
			service: '#_ctct_captcha_service',
			recaptcha: '#ctct-recaptcha',
			hcaptcha : '#ctct-hcaptcha',
			turnstile: '#ctct-turnstile',
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.bindEvents = () => {
		const service = document.querySelector(that.cache.service);
		const recaptcha = document.querySelector(that.cache.recaptcha);
		const hcaptcha = document.querySelector(that.cache.hcaptcha);
		const turnstile = document.querySelector(that.cache.turnstile);

		const sections = [
			recaptcha,
			hcaptcha,
			turnstile
		];

		if ('recaptcha' === service.value) {
			recaptcha.style.display = 'block';
			hcaptcha.style.display = 'none';
			turnstile.style.display = 'none';
		}
		if ('hcaptcha' === service.value) {
			recaptcha.style.display = 'none';
			hcaptcha.style.display = 'block';
			turnstile.style.display = 'none';
		}
		if ('turnstile' === service.value) {
			recaptcha.style.display = 'none';
			hcaptcha.style.display = 'none';
			turnstile.style.display = 'block';
		}
		if ('disabled' === service.value) {
			recaptcha.style.display = 'none';
			hcaptcha.style.display = 'none';
			turnstile.style.display = 'none';
		}

		service.addEventListener('change', (e) => {
			if ('recaptcha' === e.currentTarget.value) {
				recaptcha.style.display = 'block';
				hcaptcha.style.display = 'none';
				turnstile.style.display = 'none';
			}
			if ('hcaptcha' === e.currentTarget.value) {
				recaptcha.style.display = 'none';
				hcaptcha.style.display = 'block';
				turnstile.style.display = 'none';
			}
			if ('turnstile' === e.currentTarget.value) {
				recaptcha.style.display = 'none';
				hcaptcha.style.display = 'none';
				turnstile.style.display = 'block';
			}
			if ('disabled' === e.currentTarget.value) {
				sections.forEach((section) => {
					section.style.display = 'none';
				});
			}
		});
	};

	that.init();

}(window, window.ctctsettings));
