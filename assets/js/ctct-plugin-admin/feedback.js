(function (window, app) {
	window.addEventListener('load', function () {
		let modal = document.querySelector("#ctct-feedback-modal");
		let closeBtn = document.querySelector('#ctct-feedback-close-btn');
		let cancelLink = document.querySelector('#ctct-feedback-cancel');
		let deactivateLink = document.querySelector('#deactivate-constant-contact-forms');
		let skipdeactivate = document.querySelector('#ctct-feedback-modal-skip-deactivate');

		if (deactivateLink) {
			deactivateLink.addEventListener('click', (e) => {
				e.preventDefault();
				window.ctctDeactivationLink = e.target.href;
				skipdeactivate.setAttribute('href', window.ctctDeactivationLink);
				modal.style.display = 'block';
			});
		}
		if (closeBtn) {
			closeBtn.addEventListener('click', (e) => {
				e.preventDefault();
				modal.style.display = 'none';
			});
		}

		if (cancelLink) {
			cancelLink.addEventListener('click', (e) => {
				e.preventDefault();
				modal.style.display = 'none';
			});
		}

		window.addEventListener('click', (e) => {
			if (e.target === modal) {
				modal.style.display = "none";
			}
		});
	});
}(window));

