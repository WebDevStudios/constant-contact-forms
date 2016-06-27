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
				body: $( 'body' ),
				hide: '.cmb2-id--ctct-new-list',
			};
		}

		// Combine all events.
		that.bindEvents = function() {

			that.metaShowHide( $('#_ctct_list') );
			that.disableFields();

            // Show fields based on selection.
            $('select[name=_ctct_list]').change( function(e) {
				that.metaShowHide( $('#_ctct_list') );
				$('input[name="_ctct_new_list"]').val('');
				if ( 'new' === this.value ) {
					that.metaShowHide( $('.cmb2-id--ctct-new-list') );
				}
			});

			// Disable email options on row change trigger.
			$(document).on( 'cmb2_shift_rows_complete', function() {
				that.disableFields();
			});

			$(document).on( 'cmb2_add_row', function( e ) {
				console.log( e );
				var oldRow  = $( '#ctct_fields_metabox' ).find('.cmb-repeatable-grouping').last();
				var map = oldRow.find( '.map select');
				$(map).val('custom');
				that.disableFields();
			});
        }

        // Function to handle which items should be showing/hiding.
        that.metaShowHide = function(showem) {
            var hideThese = $( that.$c.hide ).not(showem);
            showem.slideDown('fast');
            hideThese.hide();
        }

		// Disable required email fields.
		that.disableFields = function() {

			$( '#ctct_fields_metabox .map select' ).each( function( key, value ) {

				var field_parent = $( this ).parent().parent().parent();
				var button = $( field_parent ).find( '.cmb-remove-row button');
				var required = $( field_parent ).find( '.required input[type=checkbox]');
				var map = $( field_parent ).find( '.map select');

				if ( 'email' === $( this ).val() ) {
					button.attr( 'disabled', true);
					required.attr( 'disabled', true);
					map.prop( 'disabled', true);

				} else {
					button.attr( 'disabled', false);
					required.attr( 'disabled', false);
					map.prop( 'disabled', false);
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
