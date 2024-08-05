window.CTCTAJAX = {};

( function( window, that ) {

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
		let reviewRequest = document.querySelector('#ctct-admin-notice-review_request');
		if (reviewRequest) {
			reviewRequest.addEventListener('click', (e) => {  //figure out link target specifically.
				let ctctAction = 'dismissed';
				let ctctReviewAjax = {
					'action'            : 'constant_contact_review_ajax_handler',
					'ctct-review-action': ctctAction
				}

				if ( e.target.classList.contains( 'ctct-review' ) ) {
					ctctAction = 'reviewed';
				}

				const args = new URLSearchParams( ctctReviewAjax ).toString();

				const request = new XMLHttpRequest();

				request.open('POST', window.ajaxurl, true);
				request.setRequestHeader('Content-Type', 'application/json;');
				request.onload = function () {
					if (this.status >= 200 && this.status < 400) {
						console.log(this.response);
					} else {
						console.log(this.response);
					}
				};
				request.onerror = function () {
					console.log('update failed');
				};
				request.send(args);
			})
		}
	};

	that.init();
} ( window, window.CTCTAJAX ) );
