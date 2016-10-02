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
                <div class="left_panel col-xs-2" data-control="meta_panel">
                    <div class="row search">
                        <div class="col-sm-12 input-group input-group-sm">
                          <input type="text" class="form-control" data-control="search" placeholder="Search..." autocomplete="off" />
                          <span class="input-group-btn">
                            <button class="btn btn-primary" data-control="go_btn" type="button">Go!</button>
                          </span>
                        </div>
                    </div>

                    <div class="row form-inline">
                        <div class="col-sm-12">
                            <ul class="search_rating" data-control="search_rating">
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                            </ul>
                            <div class="form-group unrated">
                                <label>
                                    <input type="checkbox" class="form-control" data-control="unrated" />
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
                            <a class="clear clickable" data-control="clear_btn">Clear</a>
                        </div>
                    </div>
                    
                    <div class="row title">
                        <div class="col-sm-12">
                            <span data-control="current_title"></span>
                        </div>
                    </div>
                    
                    <div class="row current_rating">
                        <div class="col-sm-12">
                            <ul data-control="current_rating">
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                                <li class="glyphicon clickable"></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row details">
                        <div class="col-sm-12">
                            <div data-control="current_dimensions"></div>
                            <div>
                                Posted on
                                <span data-control="post_date"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row tags">
                        <div class="col-sm-12">
                            <ul data-control="tags">
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-10">
                    <ul class="ondeck pull-left" data-control="ondeck"></ul>
                    <div class="stage" data-control="stage">
                    </div>
                </div>
            </div>
        </div>

        <?php wp_footer(); ?>
    </body>    
</html>