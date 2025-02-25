window.CTCTAJAX = {};

(function (window, that) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {

		// Trigger any field modifications we need to do.
		that.handleReviewAJAX();
	};

	// Handle saving the decision regarding the review prompt admin notice.
	that.handleReviewAJAX = () => {
		const reviewRequest = document.querySelector('#ctct-admin-notice-review_request');
		if (reviewRequest) {
			reviewRequest.addEventListener('click', (e) => {
				e.preventDefault();
				let ctctAction;
				if (e.target.matches('.ctct-notice-dismiss')) {
					ctctAction = 'dismissed';
				} else if (e.target.matches('.ctct-review')) {
					ctctAction = 'reviewed';
				}

				const data = new FormData();
				data.append('action', 'constant_contact_review_ajax_handler');
				data.append('ctct_review_action', ctctAction);

				fetch(window.ajaxurl, options = {
					method: 'POST', body: data,
				})
					.then((response) => response.json())
					.then((response) => {
						if (response.success) {
							reviewRequest.style.display = 'none';
						}
					}).catch((error) => {
					console.log(error);
				});
			});
		}
	};

	that.init();
}(window, window.CTCTAJAX));
