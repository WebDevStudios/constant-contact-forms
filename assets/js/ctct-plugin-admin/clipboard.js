window.CTCTClipboard = {};

(function (window, app) {

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
	 * @since 1.11.0
	 */
	app.cache = () => {
		app.cache = {
			window       : window,
			copyshortcode: document.querySelectorAll('.ctct-shortcode-wrap'),
		};
	};

	/**
	 * Copy to clipboard click event.
	 *
	 * @param {object} button The clicked element.
	 * @param {HTMLElement} input The input element.
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.copyClick = (button, input) => {

		if (!button || !input) {
			return;
		}

		// Select the input.
		input.select();
		input.setSelectionRange(0, 99999); // For mobile devices.

		if (window.isSecureContext && navigator.clipboard) {
			button.addEventListener('click', async (e) => {
				e.preventDefault();
				await copyCode(input, button);
			});
		}
	}

	async function copyCode(field, button) {
		try {
			await navigator.clipboard.writeText(field.value);

			// visual feedback that task is completed.
			const reset = button.innerHTML;
			button.innerHTML = button.dataset.copied;

			// Reset button text.
			setTimeout(function () {
				button.innerHTML = reset;
			}, 700);
		} catch (err) {
			console.error(err.message);
		}
	}

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.bindEvents = () => {

		// Add click event for copy buttons.
		if (app.cache.copyshortcode) {
			Array.from(app.cache.copyshortcode).forEach((element) => {
				const input = element.querySelector('input');
				const button = element.querySelector('button');

				if (input && button) {
					button.addEventListener('click', function (e) {
						e.preventDefault();
						app.copyClick(this, input);
					});
				}
			});
		}
	};

	app.init();

}(window, window.CTCTClipboard));
