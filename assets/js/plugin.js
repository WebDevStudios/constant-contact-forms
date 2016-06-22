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

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImN0Y3QtYnVpbGRlci5qcyIsImN0Y3Qtc3VwcG9ydC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNuRkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6InBsdWdpbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIndpbmRvdy5DVENUQnVpbGRlciA9IHt9O1xuXHQoIGZ1bmN0aW9uKCB3aW5kb3csICQsIHRoYXQgKSB7XG5cblx0XHQvLyBDb25zdHJ1Y3Rvci5cblx0XHR0aGF0LmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRoYXQuY2FjaGUoKTtcblx0XHRcdHRoYXQuYmluZEV2ZW50cygpO1xuXHRcdH1cblxuXHRcdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRcdHRoYXQuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRoYXQuJGMgPSB7XG5cdFx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRcdGJvZHk6ICQoICdib2R5JyApLFxuXHRcdFx0XHRoaWRlOiAnLmNtYjItaWQtLWN0Y3QtbmV3LWxpc3QnLFxuXHRcdFx0fTtcblx0XHR9XG5cblx0XHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdFx0dGhhdC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cblx0XHRcdHRoYXQubWV0YVNob3dIaWRlKCAkKCcjX2N0Y3RfbGlzdCcpICk7XG5cdFx0XHR0aGF0LmRpc2FibGVGaWVsZHMoKTtcblxuICAgICAgICAgICAgLy8gU2hvdyBmaWVsZHMgYmFzZWQgb24gc2VsZWN0aW9uLlxuICAgICAgICAgICAgJCgnc2VsZWN0W25hbWU9X2N0Y3RfbGlzdF0nKS5jaGFuZ2UoIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dGhhdC5tZXRhU2hvd0hpZGUoICQoJyNfY3RjdF9saXN0JykgKTtcblx0XHRcdFx0JCgnaW5wdXRbbmFtZT1cIl9jdGN0X25ld19saXN0XCJdJykudmFsKCcnKTtcblx0XHRcdFx0aWYgKCAnbmV3JyA9PT0gdGhpcy52YWx1ZSApIHtcblx0XHRcdFx0XHR0aGF0Lm1ldGFTaG93SGlkZSggJCgnLmNtYjItaWQtLWN0Y3QtbmV3LWxpc3QnKSApO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblxuXHRcdFx0Ly8gRGlzYWJsZSBlbWFpbCBvcHRpb25zIG9uIHJvdyBjaGFuZ2UgdHJpZ2dlci5cblx0XHRcdCQoZG9jdW1lbnQpLm9uKCAnY21iMl9zaGlmdF9yb3dzX2NvbXBsZXRlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHRoYXQuZGlzYWJsZUZpZWxkcygpO1xuXHRcdFx0fSk7XG5cblx0XHRcdCQoZG9jdW1lbnQpLm9uKCAnY21iMl9hZGRfcm93JywgZnVuY3Rpb24oIGUgKSB7XG5cdFx0XHRcdGNvbnNvbGUubG9nKCBlICk7XG5cdFx0XHRcdHZhciBvbGRSb3cgID0gJCggJyNjdGN0X2ZpZWxkc19tZXRhYm94JyApLmZpbmQoJy5jbWItcmVwZWF0YWJsZS1ncm91cGluZycpLmxhc3QoKTtcblx0XHRcdFx0dmFyIG1hcCA9IG9sZFJvdy5maW5kKCAnLm1hcCBzZWxlY3QnKTtcblx0XHRcdFx0JChtYXApLnZhbCgnY3VzdG9tJyk7XG5cdFx0XHRcdHRoYXQuZGlzYWJsZUZpZWxkcygpO1xuXHRcdFx0fSk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBGdW5jdGlvbiB0byBoYW5kbGUgd2hpY2ggaXRlbXMgc2hvdWxkIGJlIHNob3dpbmcvaGlkaW5nLlxuICAgICAgICB0aGF0Lm1ldGFTaG93SGlkZSA9IGZ1bmN0aW9uKHNob3dlbSkge1xuICAgICAgICAgICAgdmFyIGhpZGVUaGVzZSA9ICQoIHRoYXQuJGMuaGlkZSApLm5vdChzaG93ZW0pO1xuICAgICAgICAgICAgc2hvd2VtLnNsaWRlRG93bignZmFzdCcpO1xuICAgICAgICAgICAgaGlkZVRoZXNlLmhpZGUoKTtcbiAgICAgICAgfVxuXG5cdFx0Ly8gRGlzYWJsZSByZXF1aXJlZCBlbWFpbCBmaWVsZHMuXG5cdFx0dGhhdC5kaXNhYmxlRmllbGRzID0gZnVuY3Rpb24oKSB7XG5cblx0XHRcdCQoICcjY3RjdF9maWVsZHNfbWV0YWJveCAubWFwIHNlbGVjdCcgKS5lYWNoKCBmdW5jdGlvbigga2V5LCB2YWx1ZSApIHtcblxuXHRcdFx0XHR2YXIgZmllbGRfcGFyZW50ID0gJCggdGhpcyApLnBhcmVudCgpLnBhcmVudCgpLnBhcmVudCgpO1xuXHRcdFx0XHR2YXIgYnV0dG9uID0gJCggZmllbGRfcGFyZW50ICkuZmluZCggJy5jbWItcmVtb3ZlLXJvdyBidXR0b24nKTtcblx0XHRcdFx0dmFyIHJlcXVpcmVkID0gJCggZmllbGRfcGFyZW50ICkuZmluZCggJy5yZXF1aXJlZCBpbnB1dFt0eXBlPWNoZWNrYm94XScpO1xuXHRcdFx0XHR2YXIgbWFwID0gJCggZmllbGRfcGFyZW50ICkuZmluZCggJy5tYXAgc2VsZWN0Jyk7XG5cblx0XHRcdFx0aWYgKCAnZW1haWwnID09PSAkKCB0aGlzICkudmFsKCkgKSB7XG5cdFx0XHRcdFx0YnV0dG9uLmF0dHIoICdkaXNhYmxlZCcsIHRydWUpO1xuXHRcdFx0XHRcdHJlcXVpcmVkLmF0dHIoICdkaXNhYmxlZCcsIHRydWUpO1xuXHRcdFx0XHRcdG1hcC5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlKTtcblxuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdGJ1dHRvbi5hdHRyKCAnZGlzYWJsZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0cmVxdWlyZWQuYXR0ciggJ2Rpc2FibGVkJywgZmFsc2UpO1xuXHRcdFx0XHRcdG1hcC5wcm9wKCAnZGlzYWJsZWQnLCBmYWxzZSk7XG5cdFx0XHRcdH1cblxuXHRcdFx0fSk7XG5cblx0XHR9XG5cblx0XHQvLyBFbmdhZ2UhXG5cdFx0JCggdGhhdC5pbml0ICk7XG5cblx0fSkoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuQ1RDVEJ1aWxkZXIgKTtcbiIsIndpbmRvdy5DVENUU3VwcG9ydCA9IHt9O1xuXHQoIGZ1bmN0aW9uKCB3aW5kb3csICQsIHRoYXQgKSB7XG5cblx0XHQvLyBDb25zdHJ1Y3Rvci5cblx0XHR0aGF0LmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRoYXQuY2FjaGUoKTtcblx0XHRcdHRoYXQuYmluZEV2ZW50cygpO1xuXHRcdH1cblxuXHRcdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRcdHRoYXQuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRoYXQuJGMgPSB7XG5cdFx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRcdGJvZHk6ICQoICdib2R5JyApLFxuXHRcdFx0XHRoaWRlOiAnLmFuc3dlcicsXG5cdFx0XHR9O1xuXHRcdH1cblxuXHRcdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0XHR0aGF0LmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoIHRoYXQuJGMuaGlkZSApLmhpZGUoKTtcbiAgICAgICAgICAgIC8vIFNob3cgZmllbGRzIGJhc2VkIG9uIHNlbGVjdGlvblxuICAgICAgICAgICAgJCgnLnF1ZXN0aW9uJykub24oICdjbGljaycsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dGhhdC5tZXRhU2hvd0hpZGUoICQodGhpcykubmV4dCgnLmFuc3dlcicpICk7XG5cdFx0XHR9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEZ1bmN0aW9uIHRvIGhhbmRsZSB3aGljaCBpdGVtcyBzaG91bGQgYmUgc2hvd2luZy9oaWRpbmdcbiAgICAgICAgdGhhdC5tZXRhU2hvd0hpZGUgPSBmdW5jdGlvbihzaG93ZW0pIHtcbiAgICAgICAgICAgIHZhciBoaWRlVGhlc2UgPSAkKCB0aGF0LiRjLmhpZGUgKS5ub3Qoc2hvd2VtKTtcbiAgICAgICAgICAgIHNob3dlbS5zbGlkZURvd24oJ2Zhc3QnKTtcbiAgICAgICAgICAgIGhpZGVUaGVzZS5oaWRlKCk7XG4gICAgICAgIH1cblxuXHRcdC8vIEVuZ2FnZSFcblx0XHQkKCB0aGF0LmluaXQgKTtcblxuXHR9KSggd2luZG93LCBqUXVlcnksIHdpbmRvdy5DVENUU3VwcG9ydCApO1xuIl0sInNvdXJjZVJvb3QiOiIvc291cmNlLyJ9
