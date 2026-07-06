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
		that.handleAPITest();
	};

	// Handle saving the decision regarding the review prompt admin notice.
	that.handleReviewAJAX = () => {
		const reviewRequest = document.querySelector('#ctct-admin-notice-review_request');
		if (reviewRequest) {
			reviewRequest.addEventListener('click', (e) => {
				e.preventDefault();
				let ctctAction;
				if (e.target.matches('button.notice-dismiss')) {
					ctctAction = 'dismissed';
				} else if (e.target.matches('.ctct-review')) {
					ctctAction = 'reviewed';
				}

				const data = new FormData();
				data.append('action', 'constant_contact_review_ajax_handler');
				data.append('ctct_review_action', ctctAction);

				if (reviewRequest.dataset.nonce) {
					data.append('ctct_nonce', reviewRequest.dataset.nonce);
				}

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

	that.handleAPITest = () => {
		const apitestlink = document.querySelector('#ctct-test-api');
		if (apitestlink) {
			apitestlink.addEventListener('click', (e) => {
				e.preventDefault();

				const data = new FormData();
				data.append('action', 'constant_contact_test_api_ajax_handler');

				const params = new URLSearchParams(e.target.href);

				if (params.get('ctct-test-connection')) {
					data.append('ctct-test-connection-nonce', params.get('ctct-test-connection'));
				}

				fetch(window.ajaxurl, options = {
					method: 'POST', body: data,
				})
					.then((response) => response.json())
					.then((response) => {
						if (response.success) {
							document.querySelector('#ctct-test-api-result').innerHTML = response.data.is_connected;
						}
					}).catch((error) => {
					console.log(error);
				});
			});
		}
	};

	that.init();
}(window, window.CTCTAJAX));
