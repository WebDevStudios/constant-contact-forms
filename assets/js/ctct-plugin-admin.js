window.CTCTBuilder = {};
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
			body:   $( 'body' ),
			hide:   '.cmb2-id--ctct-new-list',
		};
	}

	// Combine all events.
	that.bindEvents = function() {

		that.metaShowHide( $( '#_ctct_list' ) );
		that.modifyFields();

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {
			that.modifyFields();
		});

		$( document ).on( 'cmb2_add_row', function() {
			that.modifyFields();
			that.checkForNewRows();
		});

		that.checkForNewRows();
    }

    that.checkForNewRows = function() {
    	$( '.cmb-nested .postbox .inside .cmb-row .cmb-td select' ).change( function() {
			that.modifyFields();
		});
    }

    // Function to handle which items should be showing/hiding.
    that.metaShowHide = function(showem) {
        var hideThese = $( that.$c.hide ).not( showem );
        showem.slideDown( 'fast' );
        hideThese.hide();
    }

	// Disable required email fields.
	that.modifyFields = function() {

		var foundEmail = false;
		$( '#cmb2-metabox-ctct_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping' ).each( function( key, value ) {
			// .map select
			var $field_parent = $( this ).find( '.cmb-field-list' );
			var $button = $( $field_parent ).find( '.cmb-remove-group-row' );
			var $required = $( $field_parent ).find( '.required input[type=checkbox]' );
			var $requiredRow = $required.closest( '.cmb-row' );
			var $map = $( $field_parent ).find( '.map select option:selected' );
			var $mapRow = $map.closest( '.cmb-row' );

			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {
				foundEmail = true;
				$required.prop( 'checked', true );
				$requiredRow.hide();
				$button.hide();
			} else {
				$requiredRow.show();
				$button.show();
			}

		});

	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );

window.CTCTSupport = {};
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
			hide: '.answer',
		};
	}

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.hide ).hide();
        // Show fields based on selection
        $('.question').on( 'click', function(e) {
			that.metaShowHide( $(this).next('.answer') );
		});
    }

    // Function to handle which items should be showing/hiding
    that.metaShowHide = function(showem) {
        var hideThese = $( that.$c.hide ).not(showem);
        showem.slideDown('fast');
        hideThese.hide();
    }

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );

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
				confirm( 'Are you sure you want to disconnect?' );
			});
        }

		// Engage!
		$( that.init );

})( window, jQuery, window.CTCTForms );
