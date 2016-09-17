<!DOCTYPE html>
<html>
    <head>
        <title>Slideshow v3.bootstrap</title>
        
        <!-- {% load static %} -->

        <link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css' />
        <link rel="stylesheet" href="{% static 'bootstrap/css/bootstrap.css' %}" type="text/css" />
        <link rel="stylesheet" href="{% static 'css/viewer3.css' %}" type="text/css" />
    </head>
    <body>
        <div id="browser" class="container-fluid">
            <div class="row">
                <div class="left_panel col-sm-2">
                    <div class="row search">
                        <div class="col-sm-12 input-group input-group-sm">
                          <input type="text" class="form-control" data-control="search" placeholder="Search..." />
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                          </span>
                        </div>
                    </div>

                    <div class="row form-inline">
                        <div class="col-sm-12">
                            <ul class="search_rating" data-control="search_rating" data-control-mixin="ratings">
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star-empty"></li>
                                <li class="glyphicon glyphicon-star-empty"></li>
                            </ul>
                            <div class="form-group unrated">
                                <label>
                                    <input type="checkbox" class="form-control" />
                                    Unrated
                                </label>
                            </div>
                            <div class="col-sm-12"></div>
                        </div>
                    </div>
                    
                    <div class="row search_pills">
                        <ul class="col-sm-12 list-inline" data-control="search_pills">
                            <li data-control data-control-mixin="pill">
                                <button type="button" class="btn btn-primary btn-xs">porn </a><span class="glyphicon glyphicon-remove"></span></button>
                            </li>
                            <li data-control data-control-mixin="pill">
                                <button type="button" class="btn btn-primary btn-xs">weird stuff with tentacles </a><span class="glyphicon glyphicon-remove"></span></button>
                            </li>
                            <li data-control data-control-mixin="pill">
                                <button type="button" class="btn btn-primary btn-xs">butt stuff </a><span class="glyphicon glyphicon-remove"></span></button>
                            </li>
                            <li data-control data-control-mixin="pill">
                                <button type="button" class="btn btn-warning btn-xs">bananas </a><span class="glyphicon glyphicon-remove"></span></button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="row clear">
                        <div class="col-sm-12">
                            <a href="clear" href="#" data-control="clear_btn">Clear</a>
                        </div>
                    </div>
                    
                    <div class="row title">
                        <div class="col-sm-12">
                            </a><span data-control="current_title" data-contrl-mixin="label">a file named whatever</span>
                        </div>
                    </div>
                    
                    <div class="row current_rating">
                        <div class="col-sm-12">
                            <ul data-control="current_rating" data-control-mixin="ratings">
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star"></li>
                                <li class="glyphicon glyphicon-star-empty"></li>
                                <li class="glyphicon glyphicon-star-empty"></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row details">
                        <div class="col-sm-12">
                            <div data-control="current_dimentions" data-control-mixin="label">800 x 600</div>
                            <div>
                                Posted on
                                </a><span data-control="post_date" data-control-mixin="label">1/13/13</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row tags">
                        <div class="col-sm-12">
                            <ul data-control="tags">
                                <li><a data-control href="#">brunette</a><span class="c">(4,573)</span></li>
                                <li><a data-control href="#">young </a><span class="c">(1,774)</span></li>
                                <li><a data-control href="#">blowjob </a><span class="c">(534)</span></li>
                                <li><a data-control href="#">bending over </a><span class="c">(377)</span></li>
                                <li><a data-control href="#">nn </a><span class="c">(310)</span></li>
                                <li><a data-control href="#">3 girls </a><span class="c">(152)</span></li>
                                <li><a data-control href="#">crying </a><span class="c">(40)</span></li>
                                <li><a data-control href="#">forced deepthroat </a><span class="c">(35)</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="stage col-sm-10">
                    The stage.
                </div>
            </div>
        </div>
        
        <script src="{% static 'js/browser/jquery-2.2.3.min.js' %}"></script>
        <script src="{% static 'lattice/js/lattice.js' %}"></script>
        <script src="{% static 'bootstrap/js/bootstrap.js' %}"></script>
        <script src="{% static 'js/browser/browser.js' %}"></script>
        <script type="text/javascript">
            jQuery( function( $ ) {
                window.browser = Lattice.create( $( '#browser' ) );
            } );
        </script>
    </body>
</html>