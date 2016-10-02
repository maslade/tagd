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
                                <input type="checkbox" class="form-control" data-control="unrated" />
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
                    
                    <h1 data-control="current_title"></h1>

                    <div class="current_rating">
                        <ul data-control="current_rating">
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                            <li class="glyphicon clickable"></li>
                        </ul>
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
                        <ul class="btn-group-vertical" data-control="tags">
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class="toolbox" data-control="toolbox">
                        <div class="row hidden">
                            <div class="col-xs-1">
                                <!-- Nav tabs -->
                                <ul class="nav nav-pills nav-stacked" role="tablist">
                                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="pill">Home</a></li>
                                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="pill">Profile</a></li>
                                    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="pill">Messages</a></li>
                                    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="pill">Settings</a></li>
                                </ul>
                            </div>
                            <div class="col-xs-11">
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="home">...</div>
                                    <div role="tabpanel" class="tab-pane" id="profile">...</div>
                                    <div role="tabpanel" class="tab-pane" id="messages">...</div>
                                    <div role="tabpanel" class="tab-pane" id="settings">...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="ondeck ssc-full-height col-xs-12 col-sm-2 col-lg-1" data-control="ondeck">
                            <div class="btn-group-xs btn-group-vertical" role="group" aria-label="Expand / Collapse Deck">
                                <button type="button" class="btn btn-info" data-control="deck_shrink">
                                    <span class="glyphicon glyphicon-minus-sign"></span>
                                </button>
                                <button type="button" class="btn btn-info" data-control="deck_grow">
                                    <span class="glyphicon glyphicon-plus-sign"></span>
                                </button>
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