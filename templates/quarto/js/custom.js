jQuery(function($){

    /* Addon: offcavans;*/
    (function() {
        $( '#offcanvas-toggler' ).off( 'click' );
        $( document ).delegate( '#offcanvas-toggler', 'click', function() {
            $( 'body' ).toggleClass( 'offcanvas-active' );
            return false;
        } ).delegate( '.jqu-ocanvas', 'click', function() {
            $( 'body' ).toggleClass( 'offcanvas-active' );
            return false;
        } );
    })();

});