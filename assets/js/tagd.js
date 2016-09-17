// Ratings plugin.
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

    function API( container, options ) {
        this.$container = $( container );
        this.options = $.extend( {}, API.options, typeof options === 'object' && options || {} );
        this.init();
    }
    
    API.options =
        {
            'class_on': 'glyphicon-star',
            'class_off': 'glyphicon-star-empty',
            'initial': [ 1, 1, 1 ],
            'clickable': true,
            'child_selector': '*'
        };
    
    API.prototype.init = function() {
        if ( this.options.clickable ) {
            this.$container.on( 'click', this.options.child_selector, { 'api': this }, this.events.click );
        }
        this.reset();
    };
    
    API.prototype.reset = function() {        
        this.set_all( false );
        this.set( this.options.initial );
    };
    
    API.prototype.get = function( i ) {
        return ( typeof i === 'undefined' )
            ? this.stars().map( is_on.bind( this ) ).toArray()
            : is_on.call( this, i );
    };
    
    API.prototype.set = function( i, val ) {
        if ( typeof i === 'object' && 'length' in i ) {
            $.each( i, set.bind( this ) );
        } else {
            set.call( this, i, val );
        }
    };
    
    API.prototype.set_all = function( val ) {
        for ( i = 0; i < this.stars().length; i++ ) { 
            this.set( i, val );
        }
    };
    
    API.prototype.star = function( i ) {
        var stars = this.stars();
        return $( i < stars.length && stars[ i ] );
    };
    
    API.prototype.stars = function() {
        return ( this.$stars = this.$stars || this.$container.children( this.options.child_selector ) );
    };
    
    API.prototype.events = {
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

        var action = typeof options === 'string' ? options : '';
        var options = typeof options === 'object' ? options : {};
        var args = Array.prototype.slice.call( arguments, 1 );
        
        return this.each(
            function() {
                var $this = $(this);
                var api = $this.data( NAMESPACE );
                if ( action && ! api ) return;
                if ( ! api )           $this.data( NAMESPACE, api = new API( this, options ) );
                if ( action )          api[ action ].apply( api, args );
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

// Pill search plugin.
( function( $ ) {
    NAMESPACE = 'autocomplete_pills';
    
    function make_pill( value ) {
        return this.$pill_template.clone()
                   .removeAttr( 'data-template' )
                   .prop( 'data-value', value )
                   .find( '[data-template-tag="label"]' )
                     .replaceWith( value )
                   .end();
    }
    
    function API( search, options ) {
        this.$search = $( search );
        this.options = $.extend( {}, API.options, typeof options === 'object' && options || {} );
        this.init();
    }
    
    API.options =
        {
            'autocomplete_url': '',
            'pill_container': null
        };
    
    API.prototype.init = function() {
        if ( this.options.pill_container ) {
            this.$pills_container = $( this.options.pill_container );
        } else {
            this.$pills_container = this.$search.parent();
        }
        
        this.$pill_template = $( '[data-template="pill"]', this.$pills_container );

        this.$search.autocomplete(
            {
                'source': this.options.autocomplete_url,
                'select': this.events.select_suggestion.bind( this )
            }
        );

        $( document.body ).on( 'click', '.pill button', function( e ) {
            e.preventDefault();
            $( this ).parents( '.pill:first' ).remove();
        } );
        this.reset();
    };
    
    API.prototype.reset = function() {
        this.$search.val( '' );
        this.$pills_container.empty();
    };
    
    API.prototype.events = {
        'select_suggestion': function( e, ui ) {
            e.preventDefault();
            this.$search.val( '' );
            this.$pills_container.append( make_pill.call( this, ui.item.value ) );
        }
    };
    
    function Plugin( options ) {
        if ( options === 'api' ) {
            return this.data( NAMESPACE );
        }

        var action = typeof options === 'string' ? options : '';
        var options = typeof options === 'object' ? options : {};
        var args = Array.prototype.slice.call( arguments, 1 );
        
        return this.each(
            function() {
                var $this = $(this);
                var api = $this.data( NAMESPACE );
                if ( action && ! api ) return;
                if ( ! api )           $this.data( NAMESPACE, api = new API( this, options ) );
                if ( action )          api[ action ].apply( api, args );
            }
        );
    }
    
    var old = $.fn[ NAMESPACE ];

    $.fn[ NAMESPACE ] = Plugin;
    
    $.fn[ NAMESPACE ].noConflict = function() {
        $.fn[ NAMESPACE ] = old;
        return this;
    };
} )( jQuery );

// Stage plugin.
( function( $ ) {
    NAMESPACE = 'stage';
    
    function API( container, options ) {
        this.$container = $( container );
        this.options = $.extend( {}, API.options, typeof options === 'object' && options || {} );
        this.init();
    }
    
    API.options =
        {
        };
    
    API.prototype.init = function() {
        this.reset();
    };
    
    API.prototype.reset = function() {
        this.items = [];
        this.$container.empty();
    };
    
    API.prototype.show = function( items ) {
        this.items = items = 'length' in items ? items : [ items ];
        
        if ( items.length === 0 ) {
            this.items.show_none();
        }
        
        if ( items.length === 1 ) {
            this.show_single();
        }
        
        if ( items.length > 1 ) {
            this.show_multiple();
        }
    };
    
    API.prototype.show_none = function() {
        alert( 'no results - todo' );
    };
    
    API.prototype.show_single = function() {
        this.$container.append( this.items[0].markup_full );
    };
    
    API.prototype.show_multiple = function() {
        for ( var i = 0; i < this.items.length; i++ ) {
            this.$container.append( this.items[ i ].markup_thumb );
        }
    };
    
    function Plugin( options ) {
        if ( options === 'api' ) {
            return this.data( NAMESPACE );
        }

        var action = typeof options === 'string' ? options : '';
        var options = typeof options === 'object' ? options : {};
        var args = Array.prototype.slice.call( arguments, 1 );
        
        return this.each(
            function() {
                var $this = $(this);
                var api = $this.data( NAMESPACE );
                if ( action && ! api ) return;
                if ( ! api )           $this.data( NAMESPACE, api = new API( this, options ) );
                if ( action )          api[ action ].apply( api, args );
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

// Feed.
( function( $, exports ) {
    function results_handler_wrapper( user_handler ) {
        return function inner() {
            this.loading = false;
            user_handler.apply( this, arguments );
        };
    }
    
    function Feed( url, filters ) {
        this.url = url;
        this.filters = $.extend( {}, Feed.filters, filters === 'object' ? filters : {} );
        this.current_request = null;
    }
    
    Feed.filters = {};
    
    Feed.prototype.update_filters = function( filters, reset ) {
        if ( reset ) {
            this.filters = Feed.filters;
        }
        $.extend( this.filters, filters );
    };
    
    Feed.prototype.set_filters = function( filters ) { this.update_filters( filters, true ); };
    
    Feed.prototype.fetch = function( results_handler ) {
        var args = {};
        
        for ( var key in this.filters ) {
            args[ 'filters[' + key + ']' ] = this.filters[ key ];
        }
        
        if ( this.current_request ) {
            this.current_request.abort();
        }
        
        this.current_request = $.getJSON( this.url, args, results_handler_wrapper( results_handler ).bind( this ) );
    };
    
    exports.Feed = Feed;
} )( jQuery, window );

// Front-end glue.
jQuery( function( $ ) {
    var clear_btn = $( '[data-control="clear_btn"]' );
    var unrated = $( '[data-control="unrated"]' );
    var search = $( '[data-control="search"]' ).autocomplete_pills(
        {
            'autocomplete_url': tagd_js.rpc.tag_autocomplete,
            'pill_container':   '[data-control="search_pills"]'
        }
    );
    var ratings = $( '[data-control="search_rating"]' ).ratings();
    var stage = $( '[data-control="stage"]' ).stage();
    var feed = new Feed( tagd_js.rpc.feed );
    
    $( document ).data( 'tagd',
        {
            'feed': feed
        }
    );
    
    $( clear_btn ).click( function( e ) {
        search.autocomplete_pills( 'reset' );
        ratings.ratings( 'reset' );
        unrated.prop( 'checked', false );
    } );
} );

function tagd( yarr_matey ) {
    var data = jQuery( document ).data( 'tagd' );
    return data[ yarr_matey ];
};