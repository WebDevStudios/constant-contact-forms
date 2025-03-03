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
					button.addEventListener('click', async (e) => {
						if (!window.isSecureContext || !navigator.clipboard) {
							return;
						}

						e.preventDefault();
						// Select the input.
						input.select();
						input.setSelectionRange(0, 99999); // For mobile devices.

						const text = input.value;
						try {
							await navigator.clipboard.writeText(text);
							// visual feedback that task is completed.
							const reset = button.innerHTML;
							e.target.textContent = button.dataset.copied;

							// Reset button text.
							setTimeout(function () {
								e.target.textContent = reset;
							}, 700);
						} catch (err) {
							console.error('Failed to copy!', err);
						}
					});
				}
			});
		}
	};

	app.init();

}(window, window.CTCTClipboard));
