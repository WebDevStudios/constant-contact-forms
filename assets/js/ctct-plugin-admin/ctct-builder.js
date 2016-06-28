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
		that.disableFields();

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {
			that.disableFields();
		});

		$( document ).on( 'cmb2_add_row', function( e ) {
			var oldRow  = $( '#ctct_fields_metabox' ).find( '.cmb-repeatable-grouping' ).last();
			var map = oldRow.find( '.map select' );
			$(map).val( 'custom' );
			that.disableFields();
		});
    }

    // Function to handle which items should be showing/hiding.
    that.metaShowHide = function(showem) {
        var hideThese = $( that.$c.hide ).not( showem );
        showem.slideDown( 'fast' );
        hideThese.hide();
    }

	// Disable required email fields.
	that.disableFields = function() {

		$( '#ctct_fields_metabox .map select' ).each( function( key, value ) {

			var $field_parent = $( this ).closest( '.cmb-field-list' );
			var $button = $( $field_parent ).find( '.cmb-remove-group-row' );
			var $required = $( $field_parent ).find( '.required input[type=checkbox]' );
			var $requiredRow = $required.closest( '.cmb-row' );
			var $map = $( $field_parent ).find( '.map select option:selected' );

			if ( 'email' === $( this ).val() ) {
				$required.prop( 'checked', true );
				$requiredRow.hide();
				$button.hide();
				$map.attr( 'disabled', true );
			} else {
				$required.prop( 'checked', false);
				$requiredRow.show();
				$button.show();
				$map.attr( 'disabled', false);
			}

		});

	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );