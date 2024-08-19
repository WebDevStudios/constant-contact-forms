window.CTCTBuilder = {};

( function( window, $, that ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {

		// If we do actually have an email field set, then remove our error.
		let emailField = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox option[value="email"]');
		if ( emailField.length ) {
			let noEmailError = document.querySelector('#ctct-no-email-error');
			if (noEmailError) {
				noEmailError.style.display = 'none';
			}
		}

		// Cache it all.
		that.cache();

		// Bind our events.
		that.bindEvents();

		// Bind our select dropdown events.
		that.selectBinds();

		// Trigger any field modifications we need to do.
		that.modifyFields();

		// Make description non-draggable, so we don't run into weird cmb2 issues.
		let cmb2handle = document.querySelectorAll('#ctct_0_description_metabox h2.hndle');
		if (cmb2handle) {
			Array.from(cmb2handle).forEach((hndle) => {
				hndle.classList.remove('ui-sortable-handle','hndle');
			});
		}

		// Inject our new labels for the up/down CMB2 buttons, so they can be properly localized.
		// Because we're using :after, we can't use .css() to do this, we need to inject a style tag.
		let headTag = document.querySelector('head');
		let styleTag = document.createElement('style');
		styleTag.textContent =
			`#cmb2-metabox-ctct_2_fields_metabox a.move-up::after { content: "` + window.ctctTexts.move_up + `" }`;
		styleTag.textContent +=
			`#cmb2-metabox-ctct_2_fields_metabox a.move-down::after { content: "` + window.ctctTexts.move_down + `" }`;
		headTag.appendChild(styleTag);
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.cache = () => {

		that.cache = {
			window: window,
			body  : document.querySelector('body'),
		};

		that.isLeaveWarningBound = false;
	};

	// Triggers our leave warning if we modify things in the form.
	that.bindLeaveWarning = () => {

		// Don't double-bind it.
		if (!that.isLeaveWarningBound) {

			// Bind our error that displays before leaving page.
			that.cache.window.addEventListener('beforeunload', that.bindMessage);

			// Save our state.
			that.isLeaveWarningBound = true;
		}
	};

	that.bindMessage = () => {
		return window.ctctTexts.leavewarning;
	};

	/**
	 * Removes our binding of our leave warning.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.unbindLeaveWarning = () => {
		that.cache.window.removeEventListener('beforeunload',that.bindMessage);
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.bindEvents = () => {

		$( '#post' ).submit( () => {

			// Make sure our email dropdown reverts from disbled, as CMB2 doesn't save those values.
			$( '.ctct-email-disabled' ).removeClass( 'disabled' ).prop( 'disabled', false );

			that.unbindLeaveWarning();
		} );

		$( '.cmb2-wrap input, .cmb2-wrap textarea' ).on( 'input', () => {
			if ( 'undefined' !== typeof( tinyMCE ) ) {
				that.bindLeaveWarning();
			}
		} );

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', () => {
			that.modifyFields();
			that.bindLeaveWarning();
			that.removeDuplicateMappings();
		} );

		// If we get a row added, then do our stuff.
		$( document ).on( 'cmb2_add_row', ( newRow ) => { // eslint-disable-line no-unused-vars

			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'none' );

			that.modifyFields();
			that.selectBinds();
			that.removeDuplicateMappings();
		} );

		that.removeDuplicateMappings();

		$( '#ctct-reset-css' ).on( 'click', ( event ) => {
			event.preventDefault();

			let selectFields = [
				'#_ctct_form_description_font_size',
				'#_ctct_form_submit_button_font_size',
				'#_ctct_form_label_placement'
			];

			let textFields = [
				'#_ctct_form_padding_top',
				'#_ctct_form_padding_bottom',
				'#_ctct_form_padding_left',
				'#_ctct_form_padding_right',
				'#_ctct_input_custom_classes'
			];

			// Reset color pickers.
			$( '.wp-picker-clear' ).each( function() {
				$( this ).click();
			} );

			for ( let i = selectFields.length; i--; ) {
				let firstOption = $( selectFields[i] ).children( 'option' ).first();
				$( selectFields[i] ).val( firstOption.val() );
			}

			for ( let i = textFields.length; i--; ) {
				$( textFields[i] ).val( '' );
			}
		} );

		$( document ).ready( () => {
			let $addressbox = $('#address_settings');
			if ( $addressbox.length > 0 ) {
				let $includes_checked = $addressbox.find('.cmb2-id--ctct-address-fields-include input[type="checkbox"]:checked');
				let required_items = $addressbox.find('.cmb2-id--ctct-address-fields-require input[type="checkbox"]');
				if ( $includes_checked.length === 0 ) {
					$(required_items).each( function(){
						$(this).prop('disabled', true);
					});
				}

				$addressbox.find('.cmb2-id--ctct-address-fields-include input[type="checkbox"]').on('change', function () {
					let checked_value = this;
					if ( checked_value.checked ) {
						$(required_items).each(function () {
							if ( checked_value.value === $(this).val() ) {
								$(this).prop('disabled', false);
							}
						});
					} else {
						$(required_items).each(function () {
							if (checked_value.value === $(this).val()) {
								$(this).prop('checked', false);
								$(this).prop('disabled', true);
							}
						});
					}
				})
			}
		} );

	};

	/**
	 * When .cmb2_select <selects> get changed, do some actions.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.selectBinds = () => {

		// For each fields select.
		$( '#cmb2-metabox-ctct_2_fields_metabox .cmb2_select' ).change( () => {

			// Modify our fields.
			that.modifyFields();

			// Don't allow duplicate mappings in form.
			that.removeDuplicateMappings();

			// Bind our leave warning.
			that.bindLeaveWarning();

			// Cached? Need to somehow listen for changed amounts.
			$('.form-field-is-custom-field').on('keyup', that.noUniqueWarning);
		} );
	};

	/**
	 * Validates whether or not all of our custom field labels all have unique labels.
	 */
	that.validateUniqueFieldLabels = () => {
		const cfValuesOrig = document.querySelectorAll('.form-field-is-custom-field');
		let cfValues;
		if (cfValuesOrig) {
			cfValues = Array.from(cfValuesOrig).map((item) => {
				return item.value;
			});
		}

		let cfValuesTotal = cfValues.length;
		let cfValuesFiltered = cfValues.filter(
			(item, position) => {
				return cfValues.indexOf(item) === position;
			}
		);
		let cfValuesFilteredTotal = cfValuesFiltered.length;

		return cfValuesTotal === cfValuesFilteredTotal;
	}

	/**
	 * Toggle inline warning that a given custom field label is not a unique value.
	 * @param event
	 */
	that.noUniqueWarning = function (event) {
		const ctctCustomField = event.currentTarget;
		const siblings = [...ctctCustomField.parentElement.children];
		if (siblings.length === 0) {
			return;
		}
		if (that.validateUniqueFieldLabels()) {
			siblings.forEach((sibling) => {
				if (sibling.classList.contains('ctct-warning')) {
					sibling.classList.remove('ctct-warning-no-unqiue');
				}
			});
		} else {
			siblings.forEach((sibling) => {
				if (sibling.classList.contains('ctct-warning')) {
					sibling.classList.add('ctct-warning-no-unqiue');
				}
			});
		}
	}

	/**
	 * We need to manipulate our form builder a bit. We do this here.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.modifyFields = () => {

		// Set that we haven't found an email.
		let foundEmail = false;
		let cfnumber = 1;

		let fieldgroups = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping');
		if (fieldgroups) {
			Array.from(fieldgroups).forEach((field, key) => {
				let fieldList = field.querySelector('.cmb-field-list');
				let removeButton = fieldList.querySelector('.cmb-remove-group-row');
				let requiredToggle = fieldList.querySelector('.required input[type=checkbox]');
				let requiredRow = requiredToggle.closest('.cmb-row');
				let map = fieldList.querySelector('.map select option:checked');
				let mapName = '';
				if (map && map.text) {
					mapName = map.text;
				}
				let fieldTitle = field.querySelector('h3');
				let fieldLabel = field.querySelector('input[name*="_ctct_field_label"]');
				let fieldDesc = field.querySelector('input[name*="_ctct_field_desc"]');

				if (mapName === 'Custom Text Field') {
					mapName += ' ' + cfnumber.toString();
					cfnumber++;
				}

				// Set our field row to be the name of the selected option.
				fieldTitle.innerText = mapName;
				// If we have a blank field label, then use the name of the field to fill it in.
				if (mapName && 0 === fieldLabel.value.length) {
					fieldLabel.value = mapName;
				}
				fieldLabel.classList.add('ctct-label-filled');

				let fieldDropdown = field.querySelector('select');
				// If we haven't yet found an email field, and this is our email field.
				if (!foundEmail && (map !== null)) {
					if ('email' === map.value) {
						// Set that we found an email field.
						foundEmail = true;

						// Make it required.
						requiredToggle.checked = true;

						if (fieldDropdown) {
							fieldDropdown.classList.add('disabled', 'ctct-email-disabled');
							fieldDropdown.disabled = true;
						}
						requiredRow.style.display = 'none';
						removeButton.style.display = 'none';
					}
				} else {
					if (fieldDropdown) {
						fieldDropdown.classList.remove('disabled', 'ctct-email-disabled');
						fieldDropdown.disabled = false;
					}
					requiredRow.style.display = 'block';
					removeButton.style.display = 'block';

					if (map !== null) {
						if ('custom' === map.value) {
							fieldLabel.classList.add('form-field-is-custom-field');
						} else {
							fieldLabel.classList.remove('form-field-is-custom-field')
						}
					}
				}

				if (window.ctct_admin_placeholders) {
					let placeholder = window.ctct_admin_placeholders[fieldDropdown.value];
					if (placeholder && placeholder.length && fieldDesc) {
						fieldDesc.setAttribute('placeholder', 'Example: ' + placeholder);
					} else if (window.ctct_admin_placeholders.default) {
						fieldDesc.setAttribute('placeholder', window.ctct_admin_placeholders.default);
					}
				}
			});
		}
	};

	/**
	 * Go through all dropdowns, and remove used options.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.removeDuplicateMappings = () => {

		let usedMappings = [];
		let dropdowns = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping select');

		// For each dropdown, build up our array of used values.
		Array.from(dropdowns).forEach((dropdown, index) => {
			usedMappings.push(dropdown.value);

			// Re-show all the children options we may have hidden.
			Array.from(dropdown.options).forEach((item) => {
				item.style.display = 'inline';
			});
		});
		usedMappings.forEach((mapping) => {
			// But only do it if the value isn't one of our custom ones.
			if ('custom' === mapping || 'custom_text_area' === mapping) {
				return;
			}

			// Remove all options from our dropdowns with the value.
			Array.from(dropdowns).forEach((dropdown) => {
				Array.from(dropdown.options).forEach((item) => {
					if (item.value === mapping && item.selected !== true) {
						item.style.display = 'none';
					}
				});
			});
		});
	};

	that.init();

} ( window, jQuery, window.CTCTBuilder ) );
