/**
 * Woo Digital Downloads — Admin JS
 */
( function ( $ ) {
    'use strict';

    /**
     * Copy API key / license key to clipboard on click.
     */
    $( document ).on( 'click', '.wdd-api-key, .wdd-licenses-table code', function () {
        var text = $( this ).text().trim();

        if ( navigator.clipboard ) {
            navigator.clipboard.writeText( text ).then( function () {
                wddFlash( 'Copied!' );
            } );
        } else {
            // Fallback for older browsers.
            var $tmp = $( '<textarea>' ).val( text ).appendTo( 'body' ).select();
            document.execCommand( 'copy' );
            $tmp.remove();
            wddFlash( 'Copied!' );
        }
    } );

    function wddFlash( msg ) {
        var $notice = $( '<div class="wdd-flash">' + msg + '</div>' ).css( {
            position:   'fixed',
            bottom:     '24px',
            right:      '24px',
            background: '#2c3e50',
            color:      '#fff',
            padding:    '8px 16px',
            borderRadius: '4px',
            zIndex:     99999,
            fontSize:   '14px',
        } );

        $( 'body' ).append( $notice );

        setTimeout( function () {
            $notice.fadeOut( 400, function () { $( this ).remove(); } );
        }, 1500 );
    }

    /**
     * Toggle activation limit field visibility based on license type.
     */
    $( document ).on( 'change', '#wdd_license_type', function () {
        var type = $( this ).val();
        var $row = $( '#wdd_activation_limit' ).closest( 'tr' );

        if ( 'unlimited' === type || 'lifetime' === type ) {
            $row.hide();
        } else {
            $row.show();
        }
    } ).trigger( 'change' );

} )( jQuery );
