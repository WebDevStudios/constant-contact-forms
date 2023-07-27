window.CTCTClipboard = {};

( function( window, $, app ) {

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
		app.$c = {
			window: $( window ),
			copyshortcode: $( '.ctct-shortcode-wrap' )
		};
	};

	/**
	 * Copy to clipboard click event.
	 *
	 * @param {object} e The clicked element.
	 * @param {HTMLElement} input The input element.
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.copyClick = (button, input) => {

		if(!button || !input){
			return;
		}

		// Select the input.
		input.select();
		input.setSelectionRange(0, 99999); // For mobile devices.
	 
		// Copy the text inside the text field.
		document.execCommand("copy");
		var reset = button.innerHTML;
		button.innerHTML = button.dataset.copied;

		// Reset button text.
		setTimeout(function(){
			button.innerHTML = reset;
		}, 3000);

	}

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	 app.bindEvents = () => {

		// Add click event for copy buttons.
		if(app.$c.copyshortcode){
			app.$c.copyshortcode.each(function(index, element) {
				var input = element.querySelector('input');
				var button = element.querySelector('button');
				if(input && button){
					button.addEventListener('click', function( e ){
						e.preventDefault();
						app.copyClick( this, input);
					});
				}
			})
		}
	};

	app.init();

} ( window, jQuery, window.CTCTClipboard ) );

