<?php

namespace Tagd\Views;

$view = new ViewerView();

?><!DOCTYPE html>
<html>
    <head>
        <link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css' />
        
        <?php wp_head(); ?>
    </head>
    <body>
        <div id="browser" class="container-fluid">
            <div class="row">
                <div class="left_panel col-sm-2">
                    <div class="row search">
                        <div class="col-sm-12 input-group input-group-sm">
                          <input type="text" class="form-control" data-control="search" placeholder="Search..." autocomplete="off" />
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                          </span>
                        </div>
                    </div>

                    <div class="row form-inline">
                        <div class="col-sm-12">
                            <ul class="search_rating" data-control="search_rating">
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
                            <li class="pill" data-template="pill">
                                <button type="button" class="btn btn-primary btn-xs">
                                    <span data-template-tag="label"></span>
                                    <span data-control="close" class="glyphicon glyphicon-remove"></span>
                                </button>
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
                            <ul data-control="current_rating">
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
                            <div data-control="current_dimentions">800 x 600</div>
                            <div>
                                Posted on
                                <span data-control="post_date">1/13/13</span>
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

        <?php wp_footer(); ?>
    </body>    
</html>