// Stars UI.
( function( $ ) {
    NAMESPACE = 'ratings';
    
    function is_on( i ) {
        return this.star( i ).hasClass( this.options.class_on );
    }
    
    function set( i, val ) {
        this.star( i ).toggleClass( this.options.class_on, Boolean( val ) )
                      .toggleClass( this.options.class_off, ! Boolean( val ) );

        do_event.call( this, 'star_toggle', i, Boolean( val ) );
        do_event.call( this, val ? 'star_on' : 'star_off', i );
    }
    
    function do_event( evt ) {
        var args  = Array.prototype.slice.call( arguments, 1 );
        
        if ( typeof this.options[ evt ] === 'function' ) {
            this.options[ evt ]( args );
        }
    }

    function Ratings( container, options ) {
        this.$container = $( container );
        this.options = $.extend( {}, Ratings.options, typeof options === 'object' && options || {} );
        this.init();
    }
    
    Ratings.options =
        {
            'class_on': 'glyphicon-star',
            'class_off': 'glyphicon-star-empty',
            'initial': [ 1, 1, 1],
            'clickable': true,
            'child_selector': '*'
        };
    
    Ratings.prototype.init = function() {
        if ( this.options.clickable ) {
            this.$container.on( 'click', this.options.child_selector, { 'api': this }, this.events.click );
        }
    };
    
    Ratings.prototype.get = function( i ) {
        return ( typeof i === 'undefined' )
            ? this.stars().map( is_on.bind( this ) ).toArray()
            : is_on.call( this, i );
    };
    
    Ratings.prototype.set = function( i, val ) {
        if ( typeof i === 'object' && 'length' in i ) {
            $.each( i, set.bind( this ) );
        } else {
            set.call( this, i, val );
        }
    };
    
    Ratings.prototype.set_all = function( val ) {
        for ( i = 0; i < this.stars().length; i++ ) { 
            this.set( i, val );
        }
    };
    
    Ratings.prototype.star = function( i ) {
        var stars = this.stars();
        return $( i < stars.length && stars[ i ] );
    };
    
    Ratings.prototype.stars = function() {
        return ( this.$stars = this.$stars || this.$container.children( this.options.child_selector ) );
    };
    
    Ratings.prototype.events = {
        'click': function( e )
            {
                e.preventDefault();
                var api = e.data.api;
                var i = api.stars().index( this );
                
                api.set_all( false );
                
                while ( i >= 0 ) {
                    api.set( i, true );
                    i--;
                }
            }
    };
    
    function Plugin( options ) {
        if ( options === 'api' ) {
            return this.data( NAMESPACE );
        }

        var options = typeof options === 'object' ? options : {};
        var action = typeof options === 'string' ? options : '';
        var args = Array.prototype.slice.call( arguments, 1 );
        
        return this.each(
            function() {
                var $this = $(this);
                var api = $this.data( NAMESPACE );
                if ( action && ! api ) return;
                if ( ! api )           $this.data( NAMESPACE, api = new Ratings( this, options ) );
                if ( action )          api[ options ]( args );
            }
        );
    }
    
    var old = $.fn[ NAMESPACE ];

    $.fn[ NAMESPACE ] = Plugin;
    
    $.fn[ NAMESPACE ].noConflict = function() {
        $.fn[ NAMESPACE ] = old;
        return this;
    };
} )( jQuery, window );

// Search and filters.
jQuery( function( $ ) {
    var search = $( '[data-control="search"]' );
    var search_pills_container = $( '[data-control="search_pills"]' );
    var pill_template = $( '[data-template="pill"]' );
    var clear_btn = $( '[data-control="clear_btn"]' );
    var ratings_filter = $( '[data-control=search_rating]' ).ratings();
    
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