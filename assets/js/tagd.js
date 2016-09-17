// Stars UI.
( function( $, exports ) {
    function Ratings( container_el ) {
        this.$container = $( container_el );
    }
    
    Ratings.prototype.set( )
    
    exports.Ratings = Ratings;
} )( jQuery, window );

// Search and filters.
jQuery( function( $ ) {
    var search = $( '[data-control="search"]' );
    var search_pills_container = $( '[data-control="search_pills"]' );
    var pill_template = $( '[data-template="pill"]' );
    var clear_btn = $( '[data-control="clear_btn"]' );
    var ratings_filter = new Ratings( '[data-control=search_rating]' );
    
    search.autocomplete( {
        'source': tagd_js.rpc.tag_autocomplete,
        'select': function( e, ui ) {
            e.preventDefault();
            search.val( '' );
            search_pills_container.append( make_pill( ui.item.value ) );
        }
    } );
    
    $( document.body ).on( 'click', 'button', function( e ) {
        e.preventDefault();
        $( this ).parents( '.pill:first' ).remove();
    } );
    
    $( clear_btn ).click( function( e ) {
        search_pills_container.empty();
        search.val( '' ); 
   } );
    
    function make_pill( value ) {
        var pill = pill_template.clone();
        pill.removeAttr( 'data-template' );
        pill.prop( 'data-value', value );
        $( '[data-template-tag="label"]', pill ).replaceWith( value );
        return pill;
    }
} );