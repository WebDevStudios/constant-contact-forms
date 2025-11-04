window.CTCTBuilder = {};

(function (window, $, that) {

	let required_items;

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {

		// If we do actually have an email field set, then remove our error.
		const emailField = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox option[value="email"]');
		const selectedField = Array.from(emailField).filter(option => option.selected);
		if (selectedField.length) {
			const noEmailError = document.querySelector('#ctct-no-email-error');
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
		const cmb2handle = document.querySelectorAll('#ctct_0_description_metabox h2.hndle');
		if (cmb2handle) {
			Array.from(cmb2handle).forEach((hndle) => {
				hndle.classList.remove('ui-sortable-handle', 'hndle');
			});
		}

		// Inject our new labels for the up/down CMB2 buttons, so they can be properly localized.
		// Because we're using :after, we can't use .css() to do this, we need to inject a style tag.
		const headTag = document.querySelector('head');
		const styleTag = document.createElement('style');
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

	/**
	 * Removes our binding of our leave warning.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.unbindLeaveWarning = () => {
		that.cache.window.removeEventListener('beforeunload', that.bindMessage);
	};

	/**
	 * Handles the beforeunload callback and display.
	 *
	 * @param e beforeunload event.
	 * @since NEXT
	 */
	that.bindMessage = (e) => {
		e.preventDefault();
		e.returnValue = '';
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.bindEvents = () => {

		const submitted = document.querySelector('#post');
		if (submitted) {
			document.addEventListener('submit', () => {
				const disabledEmails = document.querySelectorAll('.ctct-email-disabled');
				if (disabledEmails) {
					Array.from(disabledEmails).forEach((item) => {
						item.classList.remove('disabled');
						item.removeAttribute('disabled');
					});
				}
				that.unbindLeaveWarning();
			});
		}

		const cmb2inputs = document.querySelectorAll('.cmb2-wrap input, .cmb2-wrap textarea');
		Array.from(cmb2inputs).forEach((input_item) => {
			input_item.addEventListener('input', () => {
				if ('undefined' !== typeof (tinyMCE)) {
					that.bindLeaveWarning();
				}
			});
		});

		// Disable email options on row change trigger.
		// `cmb2_shift_rows_complete` is a custom jQuery based event, so we are leaving this selector.
		$(document).on('cmb2_shift_rows_complete', () => {
			that.modifyFields();
			that.bindLeaveWarning();
			that.removeDuplicateMappings();
		});

		const inlineForm = document.querySelector('#_ctct_inline_display');
		// If we get a row added, then do our stuff.
		// `cmb2_add_row` is a custom jQuery based event, so we are leaving this selector.
		$(document).on('cmb2_add_row', (newRow) => { // eslint-disable-line no-unused-vars
			const groupPostBoxes = document.querySelectorAll('#custom_fields_group_repeat .postbox');
			if (groupPostBoxes) {
				const lastBox = [...groupPostBoxes].pop();
				const boxSelect = lastBox.querySelector('.map select');
				if (boxSelect) {
					boxSelect.value = 'none';
				}
			}

			if (groupPostBoxes.length > 1) {
				inlineForm.checked = false;
				inlineForm.setAttribute('disabled',true);
			}

			that.modifyFields();
			that.selectBinds();
			that.removeDuplicateMappings();
		});

		$(document).on('cmb2_remove_row', () => { // eslint-disable-line no-unused-vars
			// Maybe enable inline checkbox.
			const groupPostBoxes = document.querySelectorAll('#custom_fields_group_repeat .postbox');
			if (groupPostBoxes.length === 1) {
				inlineForm.removeAttribute('disabled');
			}
		});

		that.removeDuplicateMappings();

		const cssReset = document.querySelector('#ctct-reset-css');
		if (cssReset) {
			cssReset.addEventListener('click', (e) => {
				e.preventDefault();

				const selectFields = [
					'#_ctct_form_description_font_size',
					'#_ctct_form_submit_button_font_size',
					'#_ctct_form_label_placement'
				];

				selectFields.forEach((fieldSelector) => {
					const field = document.querySelector(fieldSelector);
					if (field) {
						field.selectedIndex = 0;
					}
				});

				const textFields = [
					'#_ctct_form_padding_top',
					'#_ctct_form_padding_bottom',
					'#_ctct_form_padding_left',
					'#_ctct_form_padding_right',
					'#_ctct_input_custom_classes',
					'#_ctct_form_max_width'
				];

				textFields.forEach((textSelector) => {
					const text = document.querySelector(textSelector);
					if (text) {
						text.value = '';
					}
				});

				// Clear out color pickers.
				const pickerClears = document.querySelectorAll('.wp-picker-clear');
				if (pickerClears) {
					Array.from(pickerClears).forEach((picker) => {
						picker.click();
					});
				}
			});
		}

		window.addEventListener('load', function () {
			const addressBox = document.querySelector('#address_settings');
			if (addressBox) {
				const includeItems = addressBox.querySelectorAll('.cmb2-id--ctct-address-fields-include input[type="checkbox"]');
				const checkedItems = addressBox.querySelectorAll('.cmb2-id--ctct-address-fields-include input[type="checkbox"]:checked');
				required_items = addressBox.querySelectorAll('.cmb2-id--ctct-address-fields-require input[type="checkbox"]');

				if (checkedItems.length === 0) {
					Array.from(required_items).forEach((item) => {
						item.setAttribute('disabled', true);
					});
				}

				Array.from(includeItems).forEach((item) => {
					item.addEventListener('change', that.addressChange);
				});
			}

			const groupPostBoxes = document.querySelectorAll('#custom_fields_group_repeat .postbox');
			if (groupPostBoxes.length > 1) {
				inlineForm.checked = false;
				inlineForm.setAttribute('disabled', true);
			}
		});
	};

	/**
	 * Handle the enabled/disabled state of rwquired items when address "include" options change.
	 *
	 * @param e Checkbox being checked.
	 */
	that.addressChange = (e) => {
		const item = e.target;
		if (item.checked) {
			Array.from(required_items).forEach((required_item) => {
				if (item.value === required_item.value) {
					required_item.removeAttribute('disabled');
				}
			});
		} else {
			Array.from(required_items).forEach((required_item) => {
				if (item.value === required_item.value) {
					required_item.checked = false;
					required_item.setAttribute('disabled', true);
				}
			});
		}
	}

	/**
	 * When .cmb2_select <selects> get changed, do some actions.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.selectBinds = () => {

		// For each fields select.
		const selects = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox .cmb2_select');
		if (selects) {
			Array.from(selects).forEach((select) => {
				select.addEventListener('change', () => {

					// Modify our fields.
					that.modifyFields();

					// Don't allow duplicate mappings in form.
					that.removeDuplicateMappings();

					// Bind our leave warning.
					that.bindLeaveWarning();

					const customField = document.querySelectorAll('.form-field-is-custom-field');
					if (customField) {
						Array.from(customField).forEach((field) => {
							field.addEventListener('keyup', that.noUniqueWarning);
						});
					}
				});
			});
		}
	};

	/**
	 * Validates whether or not all of our custom field labels all have unique labels.
	 */
	that.validateUniqueFieldLabels = () => {
		const cfValuesOrig = document.querySelectorAll('.form-field-is-custom-field');
		let cfValues; // Leaving as `let` since we are need some hoisting.
		if (cfValuesOrig) {
			cfValues = Array.from(cfValuesOrig).map((item) => {
				return item.value;
			});
		}

		const cfValuesTotal = cfValues.length;
		const cfValuesFiltered = cfValues.filter(
			(item, position) => {
				return cfValues.indexOf(item) === position;
			}
		);
		const cfValuesFilteredTotal = cfValuesFiltered.length;

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
		let foundEmail = false; // Leaving as let due to use as boolean flag.
		let cfnumber = 1; // Leaving as let due to incrementor usage.

		const fieldgroups = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping');
		if (fieldgroups) {
			Array.from(fieldgroups).forEach((field, key) => {
				const fieldList = field.querySelector('.cmb-field-list');
				const removeButton = fieldList.querySelector('.cmb-remove-group-row');
				const requiredToggle = fieldList.querySelector('.required input[type=checkbox]');
				const requiredRow = requiredToggle.closest('.cmb-row');
				const map = fieldList.querySelector('.map select option:checked');
				let mapName = ''; // Leaving as `let` due to conditional assignment
				if (map && map.text) {
					mapName = map.text;
				}
				const fieldTitle = field.querySelector('h3');
				const fieldLabel = field.querySelector('input[name*="_ctct_field_label"]');
				const fieldDesc  = field.querySelector('input[name*="_ctct_field_desc"]');

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

				const fieldDropdown = field.querySelector('select');
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

				if (ctct_admin_placeholders) {
					const placeholder = ctct_admin_placeholders[fieldDropdown.value];
					if (placeholder && placeholder.length && fieldDesc) {
						fieldDesc.setAttribute('placeholder', 'Example: ' + placeholder);
					} else if (ctct_admin_placeholders.default) {
						fieldDesc.setAttribute('placeholder', ctct_admin_placeholders.default);
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

		let usedMappings = []; // Leaving as `let` due to changing array indices.
		const dropdowns = document.querySelectorAll('#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping select');

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

}(window, jQuery, window.CTCTBuilder));
