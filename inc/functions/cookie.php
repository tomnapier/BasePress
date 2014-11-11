<?php 
/* Cookies
=================================================== */
add_filter('body_class', 'cookie_body_classes');

function cookie_body_classes( $classes ) {
    $cookie = ( $_COOKIE['cc_cookie_accept'] != 'cc_cookie_accept' )? 'no-cookie' : null;
    $classes[] = $cookie;
    return $classes;
}
 ?>