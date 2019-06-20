window.CTCTSettings = {};

( function( window, $, app ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.6.0
	 */
	app.init = () => {
        app.initTooltips();
    };

    /**
	 * Steps tooltip auto-hiding on mouseenter, closes tooltip on mouseleave.
	 *
	 * @author Constant Contact
	 * @since 1.6.0
	 */
    app.allowMouseHoverOnTooltips = () => {

        $( document ).on( 'mouseenter', '.ctct-options-ui-tooltip', ( e ) => {
			clearTimeout( window.CTCTCloseTooltipTimer );
        } );

        $( document ).on( 'mouseleave', '.ctct-options-ui-tooltip', ( e ) => {
            $( '.ctct-options-tooltip' ).tooltip( 'close' );
        } );
    };

	/**
	 * Inits jQuery UI tooltips.
	 *
	 * @author Constant Contact
	 * @since 1.6.0
	 */
    app.initTooltips = () => {

        $( '.ctct-options-tooltip' ).each( ( i, el ) => {
            let $tooltip = $( el );

            let settings = {
                hide: { duration: 200 },
                show: { duration: 200 },
                tooltipClass: 'ctct-options-ui-tooltip',
                position: {
                    my: 'center top',
                    at: 'center bottom',
                    collision: 'flipfit'
                },
                content: () => {
                    return $tooltip.prop( 'title' );
                },
                open: () => {
                    $( '.ctct-options-tooltip' ).not( $tooltip ).tooltip( 'close' );
                }
            };

            // Basic tooltip init, with simple callback that sets up app.allowMouseHoverOnTooltips() functionality.
            $tooltip.tooltip( settings ).on( 'mouseleave', ( e ) => {

                window.CTCTCloseTooltipTimer = setTimeout( () => {
                    $tooltip.tooltip( 'close' );
                }, settings.hide.duration );

                e.stopImmediatePropagation();
            } );

           app.allowMouseHoverOnTooltips();
        } );
    };

	app.init();

} ( window, jQuery, window.CTCTSettings ) );
