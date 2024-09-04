window.CTCTModal = {};

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
	 * @since 1.0.0
	 */
	app.cache = () => {
		app.cache = {
			window: $( window ),
			modalSelector: $( '.ctct-modal' ),
			modalClose: $( '.ctct-modal-close' ),
			textareaModal: $( '#ctct-custom-textarea-modal' ),
			textareaLink: $( '#ctct-open-textarea-info' ),
			deleteLogLink: $( '#deletelog' )
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {

		app.cache.modalClose.addEventListener('click', () => {
			app.cache.modalSelector.classList.remove('ctct-modal-open');

			if (app.cache.modalSelector.classList.contains('ctct-custom-textarea-modal')) {
				return;
			}
		});
		app.cache.modalClose.on( 'click', () => {

			app.cache.modalSelector.removeClass( 'ctct-modal-open' );

			if ( app.cache.modalSelector.hasClass( 'ctct-custom-textarea-modal' ) ) {
				return;
			}

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'ctct_dismiss_first_modal',
					'ctct_is_dismissed': 'true'
				}
			} );
		} );

		app.cache.textareaLink.addEventListener('click', () => {
			app.cache.textareaModal.classList.add('ctct-modal-open');
		});

		app.cache.deleteLogLink.addEventListener('click', (event) => {
			event.preventDefault();

			// Get the link that was clicked on so we can redirect to it if the user confirms.
			let deleteLogLink = event.currentTarget.getAttribute('href');
		});
		app.cache.deleteLogLink.on( 'click', ( event ) => {
			event.preventDefault();

			// Get the link that was clicked on so we can redirect to it if the user confirms.
			let deleteLogLink = event.currentTarget.getAttribute( 'href' );

			$( '#confirmdelete' ).dialog( {
				resizable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					'Yes': () => {

						// If the user confirms the action, redirect them to the deletion page.
						window.location.replace( deleteLogLink );
					},
					'Cancel': () => {
						$( '#confirmdelete' ).closest( '.ui-dialog-content' ).dialog( 'close' );
					}
				}
			} );
		} );
	};

	$( app.init );

} ( window, jQuery, window.CTCTModal ) );
