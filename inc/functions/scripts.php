<?php

/* Call jQuery scripts
=================================================== */

function my_scripts_method() {

    wp_deregister_script( 'jquery' );

    wp_enqueue_script(
        'modernizr',
        get_template_directory_uri() . '/bower_components/foundation/js/vendor/modernizr.js',
        array(),
        null,
        false
    );

    wp_enqueue_script(
        'jquery',
        get_template_directory_uri() . '/bower_components/foundation/js/vendor/jquery.js',
        array( 'modernizr' ),
        null,
        false
    );

    wp_enqueue_script(
        'fastclick',
        get_template_directory_uri() . '/bower_components/foundation/js/vendor/fastclick.js',
        array( 'modernizr' ),
        null,
        false
    );

    wp_enqueue_script(
        'cookies',
        get_template_directory_uri() . '/bower_components/foundation/js/vendor/jquery.cookie.js',
        array( 'modernizr' ),
        null,
        false
    );

    wp_enqueue_script(
        'foundation',
        get_template_directory_uri() . '/bower_components/foundation/js/foundation.min.js',
        array( 'modernizr' ),
        null,
        true
    );

    wp_enqueue_script(
        'viewports',
        get_template_directory_uri() . '/js/viewport-units-buggyfill.js',
        array( 'modernizr' ),
        null,
        false
    );

    wp_enqueue_script(
        'viewporthacks',
        get_template_directory_uri() . '/js/viewport-units-buggyfill.hacks.js',
        array( 'modernizr' ),
        null,
        false
    );

    wp_enqueue_script(
        'app',
        get_template_directory_uri() . '/js/app.js',
        array( 'modernizr' ),
        null,
        true
    );

    wp_enqueue_style( 'dashicons' );

}

add_action('wp_enqueue_scripts', 'my_scripts_method');

?>
