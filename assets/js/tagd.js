// Ratings plugin.
( function( $ ) {
    NAMESPACE = 'ratings';
    
    function is_on( i ) {
        return this.star( i ).hasClass( this.options.class_on );
    }
    
    function set( i, val ) {
        this.star( i ).toggleClass( this.options.class_on, Boolean( val ) )
                      .toggleClass( this.options.class_off, ! Boolean( val ) );
        do_event.call( this, val ? 'star_on' : 'star_off', i );
    }
    
    function do_event( evt ) {
        var args  = Array.prototype.slice.call( arguments, 1 );
        this.$container.trigger( evt + '.tagd', args );
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
        var old_val = this.get();
        this.set_all( false );
        this.set( this.options.initial );
        
        if ( this.get() != old_val ) {
            this.change();
        }
    };
    
    API.prototype.get = function( i ) {
        return ( typeof i === 'undefined' )
            ? this.stars().map( is_on.bind( this ) ).toArray()
            : is_on.call( this, i );
    };
    
    API.prototype.get_highest = function( i ) {
        for ( var i = this.stars().length - 1; i >= 0; i-- ) {
            if ( is_on.call( this, i ) ) {
                return i + 1;
            }
        }
    };
    
    API.prototype.set = function( i, val ) {
        if ( typeof i === 'object' && 'length' in i ) {
            // set( [1, 0, 1, 0, 1] )
            $.each( i, set.bind( this ) );
        } else if ( typeof val === 'undefined' ) {
            // set( 3 ), same as set( [ 1, 1, 1, 0, 0 ] )
            this.set_all( 0 );
            $.each( new Array( parseInt( i ) ).fill( 1 ), set.bind( this ) );
        } else {
            // set( 3, 1 ), turns on star #3
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
    
    API.prototype.change = function() {
        do_event.call( this, 'change', this );
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
                
                e.data.api.change();
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
    
    function make_pill( value, label ) {
        return this.$pill_template.clone()
                   .removeAttr( 'data-template' )
                   .attr( 'data-value', value )
                   .find( '[data-template-tag="label"]' )
                     .replaceWith( label )
                   .end();
    }
    
    function API( search, options ) {
        this.$search = $( search );
        this.options = $.extend( {}, API.options, typeof options === 'object' && options || {} );
        this.init();
    }
    
    API.options =
        {
            'allow_unknown':    true,
            'autocomplete_url': '',
            'pill_container':   null
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
    
        this.$pills_container.on( 'click', '.pill button', { 'api': this }, function( e ) {
            e.preventDefault();
            $( this ).parents( '.pill:first' ).remove();
            e.data.api.$search.trigger( 'change.tagd' );
        } );
        this.reset();
    };
    
    API.prototype.reset = function() {
        var old_val = this.get();
        this.$search.val( '' );
        this.$pills_container.empty();
        
        if ( this.get() != old_val ) {
            this.$search.trigger( 'change.tagd' );
        }
    };
    
    API.prototype.get = function() {
        return this.$pills_container.children().map( function() { return $( this ).data( 'value' ); } ).toArray();
    };
    
    API.prototype.add_pill = function( value, label ) {
        this.$pills_container.append( make_pill.call( this, value, label ) );
        this.$search.trigger( 'change.tagd' );
    };
    
    API.prototype.events = {
        'select_suggestion': function( e, ui ) {
            e.preventDefault();
            this.$search.val( '' );
            this.add_pill( ui.item.value, ui.item.label );
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
            'ondeck': '[data-control="ondeck"] ul',
            'grow_btn': '[data-control="deck_grow"]',
            'shrink_btn': '[data-control="deck_shrink"]',
        };
    
    API.prototype.init = function() {
        this.$ondeck = $( this.options.ondeck );
        this.$shrink = $( this.options.shrink_btn )
            .click( this.deck_shrink.bind( this, 2 ) );
        this.$grow = $( this.options.grow_btn )
            .click( this.deck_grow.bind( this, 2 ) );
        this.reset();
    };
    
    API.prototype.reset = function() {
        this.items = [];
        this.$container.empty();
    };
    
    API.prototype.ondeck = function( items ) {
        var changing = items != this.items;
        
        if ( changing ) {
            this.items = items;
            this.$ondeck.empty();
            $.each( items, add.bind( this ) );
            this.$container.trigger( 'ondeck.tagd', [ items ] );
        }
        
        function add( i, val ) {
            this.$ondeck.append( $( '<li>' ).append( val.markup_pinky )
                                            .data( 'item.tagd', val )
                                            .on( 'click', { 'api': this }, this.show_item.bind( this, val ) )
            );
        }
    };
    
    API.prototype.deck_grow = function( cols ) {
        cols = parseInt( cols ) || 2;
        add_columns( this.$ondeck.parent().get( 0 ), cols );
        remove_columns( this.$container.get( 0 ), cols );
    };
    
    API.prototype.deck_shrink = function( cols ) {
        cols = parseInt( cols ) || 2;
        remove_columns( this.$ondeck.parent().get( 0 ), cols );
        add_columns( this.$container.get( 0 ), cols );
    };
    
        function add_columns( el, cols ) {
            el.className = el.className.replace( /col-(sm|md|lg)-(\d\d?)( |$)/g, adjust_columns.bind( null, cols ) );
        }
        
        function remove_columns( el, cols ) {
            el.className = el.className.replace( /col-(sm|md|lg)-(\d\d?)( |$)/g, adjust_columns.bind( null, cols * -1 ) );
        }
        
        function adjust_columns( amt, m, sz, cols, tail ) {
            var col = Math.min( 11, Math.max( 1, parseInt( cols ) + amt ) );
            return 'col-' + String( sz ) + '-' + String( col ) + String( tail );
        }
    
    API.prototype.back_button = function( previous ) {
        
    };
    
    API.prototype.show_index = function( index ) {
        this.show_item( this.items[ index ] );
    };
    
    API.prototype.show_item = function( item ) {
        this.$container.empty().append(
            $( item.markup_full ).data( 'item.tagd', item )
            .attr( 'data-item-id', item.id )
        );
        this.$container.trigger( 'show_item', [ item ] );
        
        if ( $( document.body ).hasClass( 'xs' ) ) {
            $( window ).scrollTop( this.$container.offset().top );
        }
    };
    
    API.prototype.show_multiple = function( items ) {
        var item;
        
        for ( var i = 0; i < items.length; i++ ) {
            item = $( items[ i ].markup_thumb )
                   .data( 'item.tagd', items[ i ] )
                   .on( 'click', { 'api': this }, click );
            this.$container.append( $( '<div>' ).append( item ) );
        }
        
        function click( e ) {
            e.data.api.show( $( this ).data( 'item.tagd' ) );
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

// Meta plugin.
( function( $ ) {
    NAMESPACE = 'meta_panel';
    
    function API( container, options ) {
        this.$container = $( container );
        this.options = $.extend( {}, API.options, typeof options === 'object' && options || {} );
        this.init();
        this.reset();
    }
    
    API.options =
        {
        };
    
    API.prototype.init = function() {
        this.item = undefined;
        this.$search = $( '[data-control="search"]' );
        this.$new_tag = $( '[data-control="new_tag"]', this.$container );

        this.$title = $( '[data-control="current_title"]', this.$container );
        this.$rating = $( '[data-control="current_rating"]', this.$container );
        this.$dimensions = $( '[data-control="current_dimensions"]', this.$container );
        this.$date = $( '[data-control="post_date"]', this.$container );
        this.$tags = $( '[data-control="tags"]', this.$container );
        this.$all = $( [ this.$title, this.$rating, this.$dimensions, this.$date, this.$tags ] );
        
        this.$new_tag.autocomplete(
            {
                'source': tagd_js.rpc.tag_autocomplete,
                'select': this.events.select_suggestion.bind( this )
            }
        );
        this.$new_tag.on( 'keypress', { 'api': this }, this.events.keypress );
        
        this.$rating.ratings( { 'initial': [] } );

        this.$rating.on( 'change.tagd', { 'api': this }, this.events.change_rating );
        
        this.$tags.on( 'click', 'li', { 'api': this }, this.events.click_tag );
    };
    
    API.prototype.reset = function() {
        if ( this.item ) {
            this.$all.show();
            this.$title.text( this.item.title ).show();
            if ( this.item.rating ) {
                this.$rating.ratings( 'set', this.item.rating );
            } else {
                this.$rating.ratings( 'set_all', 0 );
            }
            this.$dimensions.text( this.item.dimensions );
            this.$date.text( this.item.date );
            this.set_tags( this.item.tags );
        } else {
            this.$all.hide();
            this.$title.text( tagd_js.lang.no_results ).hide();
        }
    };
    
    API.prototype.set_tags = function( tag_list ) {
        this.$tags.empty();
        
        for ( var i = 0, t; i < tag_list.length; i++ ) {
            t = tag_list[ i ];
            var tag_btn = '<div class="btn-group"><button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button><button type="button" class="btn btn-info btn-xs">' + t.label + '</button></div>';
            $( '<li>' ).append( tag_btn )
                       .data( 'tag.tagd', t )
                       .attr( 'data-tag-id', t.id )
                       .appendTo( this.$tags );
        }
    };
    
    API.prototype.update = function( item ) {
        this.item = item;
        this.reset();
    };
    
    API.prototype.new_tag = function( tag ) {
        // todo - move me into template
        if ( $( '[data-tag-id="' + String( tag.id ) + '"]' ).length === 0 ) {
            append_new_tag.call( this, tag );
        } else {
            var btn = $( '.btn-info', find( tag ) );
            btn.addClass( 'notransition' );
                btn.removeClass( 'btn-info' );
                btn.addClass( 'btn-success' );
                btn[0].offsetHeight;
            btn.removeClass( 'notransition' );
        }        

        setTimeout( unflash_tag( tag ).bind( this ), 3000 );

        function find( tag ) {
            return $( '[data-tag-id="' + String( tag.id ) + '"]', this.$tags );
        }
        
        function append_new_tag() {
            var tag_btn = '<div class="btn-group"><button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button><button type="button" class="btn btn-success btn-xs">' + tag.label + '</button></div>';
            var new_tag = $( '<li>' ).append( tag_btn )
                                     .data( 'tag.tagd', tag )
                                     .attr( 'data-tag-id', tag.id )
                                     .fadeIn( 150 );
            this.$tags.append( new_tag );
        }
        
        function unflash_tag( tag ) {
            return function() {
                $( '.btn-success', find( tag ) )
                    .removeClass( 'btn-success' )
                    .addClass( 'btn-info' );
            };
        }
    };
    
    API.prototype.remove_tag = function( tag ) {
        
    };
    
    API.prototype.events = {
        'select_suggestion': function( e, ui ) {
            e.preventDefault();
            this.$new_tag.val( '' );
            var item = new Item( this.item );
            item.add_tag( ui.item.value, this.events.tag_added.bind( this ) );
        },
        
        'keypress': function( e ) {
            if ( e.keyCode === 13 ) {
                var item = new Item( e.data.api.item );
                item.add_unknown_tag( $( this ).val(), e.data.api.events.tag_added.bind( e.data.api ) );
                $( this ).val( '' );
            }
        },
        
        'change_rating': function( e, ratings ) {
            var api = e.data.api;
            if ( api.item ) {
                var item = new Item( api.item );
                item.rate( ratings.get_highest() );
            }
        },
        
        'click_tag': function( e ) {
            var tag = $( this ).data( 'tag.tagd' );
            e.data.api.$search.autocomplete_pills( 'add_pill', tag.id, tag.name );
        },
        
        'tag_added': function( response ) {
            this.new_tag( response.new_tag );
        },
        
        'tag_removed': function( response ) {
            this.remove_tag( response.removed_tag );
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

// Item API.
( function( $, exports ) {
    function Item( item ) {
        this.item = item;
    }
    
    Item.prototype.rate = function( rating, handler ) {
        var data = { 'id': this.item.id, 'rating': rating };
        $.post( tagd_js.rpc.update, data, this.events.response( handler ) );
    };
    
    Item.prototype.add_tag = function( tag_id, handler ) {
        var data = { 'id': this.item.id, 'new_tag_id': tag_id };
        $.post( tagd_js.rpc.update, data, this.events.response( handler ) );
    };
    
    Item.prototype.add_unknown_tag = function( tag_str, handler ) {
        var data = { 'id': this.item.id, 'new_tag_str': tag_str };
        $.post( tagd_js.rpc.update, data, this.events.response( handler ) );
    };
    
    Item.prototype.remove_tag = function( tag_id, handler ) {
        var data = { 'id': this.item.id, 'remove_tag_id': tag_id };
        $.post( tagd_js.rpc.update, data, this.events.response( handler ) );
    };
    
    Item.prototype.events = {
        'response': function( handler ) {
            return function( response ) {
                if ( typeof handler === 'function' ) {
                    handler.apply( this, arguments );
                }
            }
        }
    }
    
    exports.Item = Item;
} )( jQuery, window );

// Feed.
( function( $, exports ) {
    function results_handler_wrapper( user_handler ) {
        return function inner() {
            this.current_request = null;
            user_handler.apply( this, arguments );
        };
    }
    
    function Feed( url, filters ) {
        this.url = url;
        this.filters = $.extend( {}, Feed.filters, filters === 'object' ? filters : {} );
        this.current_request = null;
    }
    
    Feed.filters = {
        'tags': null,
        'unrated': null,
        'ratings': null
    };
    
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
    
    Feed.prototype.is_loading = function() { return Boolean( this.current_request ); }
    
    Feed.prototype.refresh = function( args ) {
        this.update_filters( args );
        this.fetch( function( results ) {
            $( this ).trigger( 'results.tagd', [ results ] );
        } );
    }

    exports.Feed = Feed;
} )( jQuery, window );

// Filter controller.
( function( $, exports ) {
    function FilterController( options ) {
        this.options = $.extend( {}, FilterController.options, options );
        this.init();
    }
    
    FilterController.options = {
        'search': null,
        'unrated_cb': null,
        'ratings': null
    };
    
    FilterController.prototype.init = function() {
        this.$search = $( this.options.search );
        this.$unrated_cb = $( this.options.unrated_cb );
        this.$ratings = $( this.options.ratings );
        
        this.$search.on( 'change.tagd', this.change.bind( this ) )
        this.$unrated_cb.click( this.change.bind( this ) );
        this.$ratings.on( 'change.tagd', this.change.bind( this ) );
    };
    
    FilterController.prototype.change = function( e ) {
        $( this ).trigger( 'change.tagd' )
    };
    
    FilterController.prototype.extract = function() {
        return {
            'tags': this.$search.autocomplete_pills( 'api' ).get(),
            'unrated': this.$unrated_cb.prop( 'checked' ),
            'ratings': this.$ratings.ratings( 'api' ).get(),
        };
    };
    
    exports.FilterController = FilterController;
} )( jQuery, window );

( function( $, exports ) {
    var running = false;
    
    function ScreenSizeController( options ) {
        this.options = $.extend( {}, ScreenSizeController.options, options );
        this.init();
    }
    
    ScreenSizeController.options =
        {
            'targets': '.ssc-full-height',
            'skip_height_for_sizes': [ 'xs' ]
        };
    
    ScreenSizeController.prototype.init = function() {
        window.addEventListener( 'resize', resize_handler( this.resize.bind( this ) ) );
        this.signals = {
            'xs': $( '<div>' ).addClass( 'visible-xs' ),
            'sm': $( '<div>' ).addClass( 'visible-sm' ),
            'md': $( '<div>' ).addClass( 'visible-md' ),
            'lg': $( '<div>' ).addClass( 'visible-lg' ),
        }
        $( document.body ).append( this.signals.xs, this.signals.sm, this.signals.md, this.signals.lg );
    };
    
    ScreenSizeController.prototype.resize = function() {
        var size = get_size_class.call( this );
        $( document.body ).removeClass( 'xs sm md lg' ).addClass( size );

        if ( this.options.skip_height_for_sizes.indexOf( size ) === -1 ) {
            var margin_top = parseInt( $( 'html' ).css( 'margin-top' ) );
            $( document.body ).css( 'height', '100vh' );
            $( document.body ).css( 'height', $( document.body ).outerHeight() - margin_top );

            $( this.options.targets ).each( function() {
                var height = String( $( document.body ).outerHeight() - $( document.body ).offset().top ) + 'px';
                $( this ).css( 'height', height );
            } );
        } else {
            $( this.options.targets ).css( 'height', '');
        }
        
        function get_size_class() {
            if ( this.signals.lg.is( ':visible' ) ) return 'lg';
            if ( this.signals.md.is( ':visible' ) ) return 'md';
            if ( this.signals.sm.is( ':visible' ) ) return 'sm';
            return 'xs';
        }
    };
    
    function resize_handler( user_handler ) {
        return outer_handler;
        
        function outer_handler( e ) {
            if ( ! running ) {
                requestAnimationFrame( inner_handler.bind( this ) )
                running = true;
            }
        };
        
        function inner_handler() {
            user_handler();
            running = false;
        };
    }
    
    exports.ScreenSizeController = ScreenSizeController;
} )( jQuery, window );

// Front-end glue.
jQuery( function( $ ) {
    var feed = new Feed( tagd_js.rpc.feed );
    var filters = new FilterController(
        {
            'search':      $( '[data-control="search"]' ),
            'unrated_cb':  $( '[data-control="unrated"]' ),
            'ratings':     $( '[data-control="search_rating"]' ),
        }
    );
    var resizer = new ScreenSizeController();

    $( '[data-control="meta_panel"]' ).meta_panel();
    $( '[data-control="search_rating"]' ).ratings();
    $( '[data-control="stage"]' ).stage();
    $( '[data-control="go_btn"]' ).click( refresh );
    $( '[data-control="search"]' ).autocomplete_pills(
        {
            'autocomplete_url':  tagd_js.rpc.tag_autocomplete,
            'pill_container':    '[data-control="search_pills"]'
        }
    );
    
    $( filters ).on( 'change.tagd', refresh );
    
    $( '[data-control="stage"]' ).on( 'show_item.tagd', function( e, item ) {
        $( '[data-control="meta_panel"]' ).meta_panel( 'update', item );
    } );
    
    $( feed ).on( 'results.tagd', function( e, results ) {
        $( '[data-control="stage"]' ).stage( 'ondeck', results.items ).stage( 'show_index', 0 );
    } );
    
    $( '[data-control="clear_btn"]' ).click( function( e ) {
        $( '[data-control="search"]' ).autocomplete_pills( 'reset' );
        $( '[data-control="search_rating"]' ).ratings( 'reset' );
        $( '[data-control="unrated"]' ).prop( 'checked', false );
    } );
    
    $( document ).data( 'tagd',
        {
            'feed': feed,
            'filter': filters
        }
    );
    
    resizer.resize();
    refresh();
    
    function refresh() {
        var args = filters.extract();
        feed.refresh( args );
    }
} );

function tagd( yarr_matey ) {
    var data = jQuery( document ).data( 'tagd' );
    return data[ yarr_matey ];
};