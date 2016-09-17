<?php

namespace Tagd;

function must_do_it_right() {
    return defined( __NAMESPACE__ . '\DIAG_MUST_DO_IT_RIGHT' ) && DIAG_MUST_DO_IT_RIGHT;
}

function doing_it_wrong( $context, $file, $line, $str ) {
    trigger_error(
        sprintf( 'Tagd fault in %s (%s:%d) : %s', $context, $file, $line, $str ),
        must_do_it_right() ? E_USER_ERROR : E_USER_WARNING
    );
}