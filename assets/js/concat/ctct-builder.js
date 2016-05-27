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

            // Show fields based on selection
            $('select[name=_ctct_list]').change( function(e) {
				that.metaShowHide( $('#_ctct_list') );
				$('input[name="_ctct_new_list"]').val('');
				if ( 'new' === this.value ) {
					that.metaShowHide( $('.cmb2-id--ctct-new-list') );
				}
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

	})( window, jQuery, window.CTCTBuilder );
