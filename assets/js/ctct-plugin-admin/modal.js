window.CTCTModal = {};

(function (window, $, app) {

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
			window                   : window,
			notConnectedModalSelector: document.querySelector('#ctct-not-connected-modal'),
			notConnectedModalClose   : document.querySelector('#ctct-not-connected-modal .ctct-modal-close'),
			textareaModal            : document.querySelector('#ctct-custom-textarea-modal'),
			textareaLink             : document.querySelector('#ctct-open-textarea-info'),
			textareaModalClose       : document.querySelector('#ctct-custom-textarea-modal .ctct-modal-close'),
			deleteLogLink            : document.querySelector('#deletelog')
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {
		if (app.cache.notConnectedModalClose) {
			app.cache.notConnectedModalClose.addEventListener('click', (e) => {
				e.preventDefault();
				app.cache.notConnectedModalSelector.classList.remove('ctct-modal-open');

				const data = new FormData();
				data.append('action', 'ctct_dismiss_first_modal');
				data.append('ctct_is_dismissed', 'true');
				fetch(
					window.ajaxurl,
					options = {
						method: 'POST',
						body  : data
					}
				)
					.then((response) => response.json())
					.then((response) => {
						if ('undefined' === typeof response.success) {
							return false;
						}
						console.log(response.data.message);
					});
			});
		}

		if (app.cache.textareaModalClose) {
			app.cache.textareaModalClose.addEventListener('click', (e) => {
				app.cache.textareaModal.classList.remove('ctct-modal-open');
			})
		}

		if (app.cache.textareaLink) {
			app.cache.textareaLink.addEventListener('click', () => {
				app.cache.textareaModal.classList.add('ctct-modal-open');
			});
		}

		if (app.cache.deleteLogLink) {
			app.cache.deleteLogLink.addEventListener('click', (event) => {
				event.preventDefault();

				// Get the link that was clicked on so we can redirect to it if the user confirms.
				const deleteLogLinkHref = event.currentTarget.getAttribute('href');

				$('#confirmdelete').dialog({
					resizable: false,
					height   : 'auto',
					width    : 400,
					modal    : true,
					buttons  : {
						'Yes'   : () => {

							// If the user confirms the action, redirect them to the deletion page.
							window.location.replace(deleteLogLinkHref);
						},
						'Cancel': () => {
							$('#confirmdelete').closest('.ui-dialog-content').dialog('close');
						}
					}
				});
			});
		}
	};

	app.init();
}(window, jQuery, window.CTCTModal));
