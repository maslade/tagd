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
                <div class="left_panel col-xs-12 col-sm-3 col-md-2" data-control="meta_panel">
                    <div class="input-group input-group-sm">
                        <input type="hidden" data-control="request_item_ids" autocomplete="off" />
                        <input type="text" class="form-control" data-control="search" placeholder="Search..." autocomplete="off" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" data-control="go_btn" type="button">Go!</button>
                        </span>
                    </div>

                    <div class="form-inline">
                        <ul class="search_rating" data-control="search_rating">
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                        </ul>
                        <div class="form-group unrated">
                            <label>
                                <input type="checkbox" class="form-control" data-control="unrated" autocomplete="off" />
                                Unrated
                            </label>
                        </div>
                        <div class="col-sm-12"></div>
                    </div>

                    <div class="search-pills">
                         <ul class="list-inline" data-control="search_pills">
                            <li class="pill" data-template="pill">
                                <button type="button" class="btn btn-primary btn-xs">
                                    <span data-template-tag="label"></span>
                                    <span data-control="close" class="glyphicon glyphicon-remove"></span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <a class="clear clickable" data-control="clear_btn">Clear</a>
                    </div>
                    
                    <hr/>
                    
                    <header class="push-container">
                        <h1 class="ellipsed" data-control="current_title"></h1>
                        <a class="permalink push-right push-top" data-control="permalink"><span class="permalink glyphicon glyphicon-link"></span></a>
                    </header>
                    <span class="item_id" data-control="item_id"></span>
                    <span class="small-link" data-control="admin-edit"></span>

                    <div class="current_rating">
                        <ul data-control="current_rating">
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                        </ul>
                        <span data-control="speed_rate" class="clickable label label-info"><span class="glyphicon glyphicon-lock"></span></span>
                    </div>

                    <div class="details">
                        <div data-control="current_dimensions"></div>
                        <div>
                            Posted on
                            <span data-control="post_date"></span>
                        </div>
                    </div>

                    <div class="tags">
                        <input data-control="new_tag" autocomplete="off" />
                        <span data-control="bulk_tag" class="clickable label label-info"><span class="glyphicon glyphicon-tags"></span></span>
                        <ul class="btn-group-vertical" data-control="tags">
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class="row">
                        <div class="ondeck ssc-full-height col-xs-12 col-sm-2 col-lg-1" data-control="ondeck">
                            <div class="deck-settings">
                                <div class="btn-group-xs btn-group-vertical" role="group">
                                    <button type="button" class="btn btn-success" data-control="toggle_slideshow">
                                        <span class="glyphicon glyphicon-play"></span>
                                    </button>
                                </div>
                                <div class="btn-group-xs btn-group-vertical" role="group">
                                    <button type="button" class="btn btn-info" data-control="toggle_mute">
                                        <span class="glyphicon glyphicon-volume-up"></span>
                                    </button>
                                </div>
                                <div class="btn-group-xs btn-group-vertical" role="group" aria-label="Expand / Collapse Deck">
                                    <button type="button" class="btn btn-info" data-control="deck_shrink">
                                        <span class="glyphicon glyphicon-minus-sign"></span>
                                    </button>
                                    <button type="button" class="btn btn-info" data-control="deck_grow">
                                        <span class="glyphicon glyphicon-plus-sign"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <ul></ul>
                        </div>
                        <div class="stage ssc-full-height col-xs-12 col-sm-10 col-lg-11" data-control="stage">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php wp_footer(); ?>
    </body>    
</html>