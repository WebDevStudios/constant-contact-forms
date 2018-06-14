window.CTCTAJAX = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		// Trigger any field modifications we need to do
		that.handleOptinAJAX();
		that.handleReviewAJAX();
	}

	// We need to manipulate our form builder a bit. We do this here.
	that.handleOptinAJAX = function() {

		// Handles GA opt-in confirmation for admin notice.
		$('#ctct_admin_notice_tracking_optin').on('click',function(e){
			var ctct_optin_ajax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin': ($(this).is(':checked')) ? 'on' : 'off'
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_optin_ajax,
				dataType: 'json',
				success : function (response) {},
				error: function(x, t, m){
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
			$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
		});

		// Opens the privacy modal once clicking on the checkbox to opt in via the admin notice.
		$('#ctct-connect-ga-optin a').on('click', function (e) {
			var ctct_optin_ajax = {
				'action': 'constant_contact_optin_ajax_handler',
				'optin' : $(this).attr('data-allow')
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_optin_ajax,
				dataType: 'json',
				success : function (response) {
					$('.ctct-connected-opt-in').hide();
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});

		$('#_ctct_data_tracking').on('click', function(e) {
			$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
		});

		// Unchecks the value if they have closed the privacy modal without agreeing/disagreeing.
		// We won't force them to agree.
		$('.ctct-modal-close').on('click', function(e){
			if( $('#_ctct_data_tracking').is(':checked')){
				$('#_ctct_data_tracking').attr('checked', false);
			}
		});

		// Handle the agreeing or disagreeing regarding privacy modal.
		$('#ctct-modal-footer-privacy a').on('click',function(e){
			var ctct_privacy_ajax = {
				'action': 'constant_contact_privacy_ajax_handler',
				'privacy_agree' : $(this).attr('data-agree')
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_privacy_ajax,
				dataType: 'json',
				success : function (response) {
					$('#ctct-privacy-modal').toggleClass('ctct-modal-open');
					if( 'false' === ctct_privacy_ajax.privacy_agree ) {
						if ($('#_ctct_data_tracking').is(':checked')) {
							$('#_ctct_data_tracking').attr('checked', false);
						}
					}
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});

	};

	// Handle saving the decision regarding the review prompt admin notice.
	that.handleReviewAJAX = function() {
		$('#ctct-admin-notice-review_request').on('click', 'a', function (e) {
			var ctct_action = 'dismissed';
			if ( $(this).hasClass('ctct-review') ) {
				ctct_action = 'reviewed';
			}
			var ctct_review_ajax = {
				'action': 'constant_contact_review_ajax_handler',
				'ctct-review-action': ctct_action
			}

			$.ajax({
				url     : ajaxurl,
				data    : ctct_review_ajax,
				dataType: 'json',
				success: function (resp) {
					if (window.console) {
						console.log(resp);
					}
					e.preventDefault();
					$('#ctct-admin-notice-review_request').hide();
				},
				error   : function (x, t, m) {
					if (window.console) {
						console.log([t, m]);
					}
				}
			});
		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTAJAX );

window.CTCTBuilder = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {

		// If we do actually have an email field set, then remove our error
		if ( $( "#cmb2-metabox-ctct_2_fields_metabox option[value='email']:selected" ).length ) {
			$( '#ctct-no-email-error' ).remove();
		}

		// Cache it all
		that.cache();

		// Bind our events
		that.bindEvents();

		// Bind our select dropdown events
		that.selectBinds();

		// Trigger any field modifications we need to do
		that.modifyFields();

		// Make description non-draggable, so we don't run into weird cmb2 issues
		$( '#ctct_0_description_metabox h2.hndle' ).removeClass( 'ui-sortable-handle, hndle' );

		// Inject our new labels for the up/down CMB2 buttons, so they can be properly localized.
		// Because we're using :after, we can't use .css() to do this, we need to inject a style tag
		$( 'head' ).append( '<style> #cmb2-metabox-ctct_2_fields_metabox a.move-up::after { content: "' + ctct_texts.move_up + '" } #cmb2-metabox-ctct_2_fields_metabox a.move-down::after { content: "' + ctct_texts.move_down + '" }</style>' );
	}

	// Cache all the things.
	that.cache = function() {

		that.$c = {
			window: $( window ),
			body:   $( 'body' ),
		};

		that.isLeaveWarningBound = false;
	}

	// Triggers our leave warning if we modify things in the form
	that.bindLeaveWarning = function() {

		// Don't double-bind it
		if ( ! that.isLeaveWarningBound ) {

			// Bind our error that displays before leaving page
			$( window ).bind( 'beforeunload', function(){
				return ctct_texts.leavewarning;
			});

			// Save our state
			that.isLeaveWarningBound = true;
		}
	}

	// Removes our binding of our leave warning
	that.unbindLeaveWarning = function() {
		$( window ).unbind( 'beforeunload' );
	}

	// Combine all events.
	that.bindEvents = function() {

		// Trigger before saving post
		$( '#post' ).submit( function () {

			// Make sure our email dropdown reverts from disbled, as CMB2 doesn't save those values
			$( '.ctct-email-disabled' ).removeClass( 'disabled' ).prop( 'disabled', false );

			// Unbind our leave warning, so we don't trigger it when we shouldn't.
			that.unbindLeaveWarning();
		});

		// On cmb2 select changes, fire our leave warning function
		$( '.cmb2-wrap input, .cmb2-wrap textarea' ).on( 'input', function() {
			if (typeof(tinyMCE) != "undefined") {
				that.bindLeaveWarning();
			}
		});

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {

			// Fire our field modifications function
			// functionality to apply to all saved values
			that.modifyFields();

			// Bind our leave warning
			that.bindLeaveWarning();

			// Re-run our mapping de-dupe
			that.removeDuplicateMappings();
		});

		// If we get a row added, then do our stuff
		$( document ).on( 'cmb2_add_row', function( newRow ) {

			// Automatically set new rows to be 'custom' field type
			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'none' );

			// Trigger bind events again for our selects, as well as our field changes
			that.modifyFields();
			that.selectBinds();
    		that.removeDuplicateMappings();
		});

		// Remove any duplicate mappings in fields
		that.removeDuplicateMappings();
    }

    // When .cmb2_select <selects> get changed, do some actions
    that.selectBinds = function() {

    	// For each fields select
    	$( '#cmb2-metabox-ctct_2_fields_metabox .cmb2_select' ).change( function() {

    		// Modify our fields
    		that.modifyFields();

    		// Don't allow duplicate mappings in form
    		that.removeDuplicateMappings();

    		// Bind our leave warning
    		that.bindLeaveWarning();
    	});
    }

	// We need to manipulate our form builder a bit. We do this here.
	that.modifyFields = function() {

		// Set that we haven't found an email
		var foundEmail = false;

		// Loop through all fields to modify them
		$( '#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping' ).each( function( key, value ) {

			// Set some of our helper paramaters
			var $field_parent = $( this ).find( '.cmb-field-list' );
			var $button       = $( $field_parent ).find( '.cmb-remove-group-row' );
			var $required     = $( $field_parent ).find( '.required input[type=checkbox]' );
			var $requiredRow  = $required.closest( '.cmb-row' );
			var $map          = $( $field_parent ).find( '.map select option:selected' );
			var $mapName      = $map.text();
			var $fieldTitle   = $( this ).find( 'h3' );
			var $labelField   = $( this ).find( "input[name*='_ctct_field_label']" );
			var $descField    = $( this ).find( "input[name*='_ctct_field_desc']" );

			// Set our field row to be the name of the selected option
			$fieldTitle.text( $mapName );

			// If we have a blank field label, then use the name of the field to fill it in
			if ($labelField.val().length === 0) {
				$labelField.val($mapName).addClass('ctct-label-filled');
			} else {
				$labelField.addClass('ctct-label-filled');
			}

			// If we haven't yet found an email field, and this is our email field
			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {

				// Set that we found an email field
				foundEmail = true;

				// Make it required
				$required.prop( 'checked', true );

				// Set it to be 'disabled'
				$( value ).find( 'select' ).addClass( 'disabled ctct-email-disabled' ).prop( 'disabled', true );

				// Hide the required row
				$requiredRow.hide();

				// Hide the remove row button
				$button.hide();

			} else {

				// Verify its not disabled
				$( value ).find( 'select' ).removeClass( 'disabled ctct-email-disabled' ).prop( 'disabled', false );

				// If we're not an email field, reshow the required field
				$requiredRow.show();

				// and the remove button
				$button.show();
			}

			// Set the placeholder text if there's something to set.
			if ( window.ctct_admin_placeholders ) {
				var placeholder = window.ctct_admin_placeholders[ $( value ).find( 'select' ).val() ];

				// If we have a valid placeholder, display it or try the fallback.
				if ( placeholder && placeholder.length && $descField.length ) {
					$descField.attr( 'placeholder', 'Example: ' + placeholder );
				} else if( window.ctct_admin_placeholders.default ) {
					$descField.attr( 'placeholder', window.ctct_admin_placeholders.default );
				}
			}
		});
	};

	// Go through all dropdowns, and remove used options
	that.removeDuplicateMappings = function() {

		// Set up an array for our mappings
		var usedMappings = [];

		// Get all our dropdowns on the page
		var dropdowns = '#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping select';
		var $dropdowns = $( dropdowns );

		// For each dropdown, build up our array of used values
		$dropdowns.each( function( key, value ) {
			usedMappings.push( $( value ).val() );
		});

		// Re-show all the children options we may have hidden
		$dropdowns.children().show();

		// For each of our mappings that we already have, remove them from all selects
		usedMappings.forEach( function( value ) {

			// But only do it if the value isn't one of our custom ones
			if ( ( 'custom_text_area' != value ) && ( 'custom' != value ) ) {

				// Remove all options from our dropdowns with the value
				$( dropdowns + ' option[value=' + value +']:not( :selected )' ).hide();
			}
		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );

window.CTCTForms = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
	}

	// Cache all the things.
	that.cache = function() {
		that.$c = {
			window: $( window ),
			body: $( 'body' ),
			disconnect: '.ctct-disconnect',
		};
	}

	// Combine all events.
	that.bindEvents = function() {

        $( that.$c.disconnect ).on( 'click', function(e) {
			confirm( ctct_texts.disconnectconfirm );
		});
    }

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTForms );

window.CTCTModal = {};
( function( window, $, app ) {

    // Constructor
    app.init = function() {
        app.cache();
        app.bindEvents();
    };

    // Cache all the things
    app.cache = function() {
        app.$c = {
            window: $( window ),
            modalSelector: $( '.ctct-modal' ),
            modalClose: $( '.ctct-modal-close' ),
            textareaModal: $( '#ctct-custom-textarea-modal' ),
            textareaLink: $( '#ctct-open-textarea-info' ),
            deleteLogLink: $( '#deletelog' )
        };
    };

    // Combine all events
    app.bindEvents = function() {
        app.$c.modalClose.click( function() {
            app.$c.modalSelector.removeClass( 'ctct-modal-open' );
            if ( app.$c.modalSelector.hasClass('ctct-custom-textarea-modal') ) {
                return;
            }
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl,
                data : {
                    action: 'ctct_dismiss_first_modal',
                    'ctct_is_dismissed' : 'true',
                }
            });
        });
        app.$c.textareaLink.on('click', function(){
           app.$c.textareaModal.addClass( 'ctct-modal-open' );
        });
        app.$c.deleteLogLink.on( 'click', function( event ) {
			event.preventDefault();

            // Get the link that was clicked on so we can redirect to it if the user confirms.
            var delete_log_link = jQuery( this ).attr( 'href' );

            jQuery( "#confirmdelete" ).dialog({
				resizable: false,
				height   : "auto",
				width    : 400,
				modal    : true,
				buttons  : {
					"Yes": function () {
					    // If the user confirms the action, redirect them to the deletion page.
						window.location.replace( delete_log_link );
					},
					Cancel: function () {
						jQuery( this ).dialog( "close" );
					}
				}
            });
        } );
    };

    // Engage
    $( app.init );

})( window, jQuery, window.CTCTModal );

window.CTCTNewsletter = {};
(function (window, $, app) {

	// Constructor
	app.init = function () {
		app.submitNewsletter();
	};

	// Engage
	$(app.init);

	app.submitNewsletter = function() {
		// Connect page.
		$('.ctct-body #subscribe').on('submit', function (event) {
			event.preventDefault();

			var $ctctNewsWrapper = $("#subscribe .ctct-call-to-action"),
				ctctNewsForm = $(".ctct-body #subscribe")[0];

			var ctctEmailField = $(".ctct-call-to-action input[type='text']")[0],
			subscribeEndpoint = event.target.action;

			if (ctctEmailField.validity.valid === true) {
				$("<iframe>", {
					"src"   : subscribeEndpoint + "?" + $(ctctNewsForm).serialize(),
					"height": 0,
					"width" : 0,
					"style" : "display: none;"
				}).appendTo($ctctNewsWrapper);

				$('#subbutton').val('Thanks for signing up').css({'background-color':'rgb(1, 128, 0)','color':'rgb(255,255,255)'});
				$('#subscribe .ctct-call-to-action-text').css({'width':'70%'});
			} else {
				$('#subbutton').val('Error occurred');
			}
		});

		// About page.
		$('.ctct-section #subscribe').on('submit', function (event) {
			event.preventDefault();

			var $ctctNewsWrapper = $(".section-marketing-tips"),
				ctctNewsForm = $(".ctct-section #subscribe")[0];

			var ctctEmailField = $(".ctct-section #subscribe input[type='text']")[0],
				subscribeEndpoint = event.target.action;

			if (ctctEmailField.validity.valid === true) {
				$("<iframe>", {
					"src"   : subscribeEndpoint + "?" + $(ctctNewsForm).serialize(),
					"height": 0,
					"width" : 0,
					"style" : "display: none;"
				}).appendTo($ctctNewsWrapper);
				$('#subbutton').val('Thanks for signing up').css({'background-color':'rgb(1, 128, 0)'});
			} else {
				$('#subbutton').val('Error occurred');
			}
		});
	}

})(window, jQuery, window.CTCTNewsletter);

window.CTCT_OptIns = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			optin_no_conn : $( '#cmb2-metabox-ctct_1_optin_metabox #_ctct_opt_in_not_connected' ),
			list          : $( '#cmb2-metabox-ctct_0_list_metabox #_ctct_list' ),

			title         : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id-email-optin-title' ),
			optin         : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in' ),
			instruct      : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in-instructions' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.optin_no_conn.length ) {

			// Fire once to get our loaded state set up
			app.toggleNoConnectionFields();

			// Bind to fire when needed
			app.$c.optin_no_conn.change( function() {
				app.toggleNoConnectionFields();
			});
		}

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.list.length ) {

			// Fire once to get our loaded state set up
			app.toggleConnectionFields();

			// Bind to fire when needed
			app.$c.list.change( function() {
				app.toggleConnectionFields();
			});
		}
	};

	// Toggle un-needed optin fields if we're not showing the opt-in.
	// this runs for the not-connected fields
	app.toggleNoConnectionFields = function() {

		// If checked, show them, else hide it.
		if ( app.$c.optin_no_conn.prop( 'checked' ) ) {
			app.$c.instruct.slideDown();
		} else {
			app.$c.instruct.slideUp();
		}
	}

	// Toggle un-needed optin fields if we're not showing the opt-in.
	// this runs for our connected fields
	app.toggleConnectionFields = function() {

		// If checked, show them, else hide it.
		if ( '' != app.$c.list.val() ) {
			app.$c.title.slideDown();
			app.$c.optin.slideDown();
			app.$c.instruct.slideDown();
		} else {
			app.$c.title.slideUp();
			app.$c.optin.slideUp();
			app.$c.instruct.slideUp();
		}
	}

	// Engage
	$( app.init );

})( window, jQuery, window.CTCT_OptIns );
