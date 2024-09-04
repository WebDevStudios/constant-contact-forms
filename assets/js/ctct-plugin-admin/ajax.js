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
			let dismissLink = reviewRequest.querySelector('a.ctct-notice-dismiss');
			dismissLink.addEventListener('click', (e) => { e.preventDefault();
				let ctctAction = 'dismissed';
				if (e.target.classList.contains('ctct-review')) {
					ctctAction = 'reviewed';
				}
				let ctctReviewAjax = {
					'action'            : 'constant_contact_review_ajax_handler',
					'ctct-review-action': ctctAction
				}
				const data = new FormData();
				const formParams = new URLSearchParams(ctctReviewAjax);

				data.append('action', 'constant_contact_review_ajax_handler');
				data.append('ctct-review-action', ctctAction);
				data.append('data', formParams);

				let options = {
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded;'},
					body  : data,
				};

				wp.ajax.send('constant_contact_review_ajax_handler', {
					success: function( thing ) {
						e.preventDefault();
						reviewRequest.style.display = 'none';
						console.log(thing);
					},
					error: function( thing ) {
						console.log(thing);
					},
					data: {
						ctct_review_action: ctctAction
					}
				});

				/*fetch(
					window.ajaxurl,
					options
				)
					.then((response) => response.json())
					.then((response) => {








						if (response.success) {
							e.preventDefault();
							reviewRequest.style.display = 'none';
						}
					}).catch((error)=>{
						console.log(error);
				});*/
			});
		}
	};

	that.init();
} ( window, window.CTCTAJAX ) );
