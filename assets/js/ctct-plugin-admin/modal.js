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
		app.$c = {
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

		app.$c.modalClose.on( 'click', () => {

			app.$c.modalSelector.removeClass( 'ctct-modal-open' );

			if ( app.$c.modalSelector.hasClass( 'ctct-custom-textarea-modal' ) ) {
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

		app.$c.textareaLink.on( 'click', () => {
			app.$c.textareaModal.addClass( 'ctct-modal-open' );
		} );

		app.$c.deleteLogLink.on( 'click', ( event ) => {
			event.preventDefault();

			// Get the link that was clicked on so we can redirect to it if the user confirms.
			var deleteLogLink = $( event.currentTarget ).attr( 'href' );

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
