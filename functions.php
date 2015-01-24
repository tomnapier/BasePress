<?php

#######################################################################################
# CALL ALL ADDITIONAL FUNCTIONS
require_once( 'inc/functions/audio.php' );
require_once( 'inc/functions/category.php' );
require_once( 'inc/functions/colours.php' );
require_once( 'inc/functions/content.php' );
require_once( 'inc/functions/cookie.php' );
require_once( 'inc/functions/essentials.php' );
require_once( 'inc/functions/homecategory.php' );
require_once( 'inc/functions/fonts.php' );
require_once( 'inc/functions/menus.php' );
require_once( 'inc/functions/meta-boxes.php' );
require_once( 'inc/functions/page-numbers.php' );
require_once( 'inc/functions/scripts.php' );
require_once( 'inc/functions/shortcodes.php' );
require_once( 'inc/functions/social.php' );
require_once( 'inc/functions/styling.php' );
require_once( 'inc/functions/thumbs.php' );
require_once( 'inc/functions/tinymce.php' );
require_once( 'inc/functions/video.php' );

require_once( 'theme-options/theme-options.php' );

require_once( 'inc/post/page.php' );

require_once( 'inc/_s/template-tags.php' );
#######################################################################################

/* Reorganise Menu
=================================================== */
add_action('admin_init', 'dashboard_style');

function dashboard_style() {
    wp_enqueue_style(
        'dashboard_css',
        get_bloginfo('template_directory') . '/inc/styling/css/menu.css'
    );
}

function edit_admin_menus() {
    global $menu;
    global $submenu;
    
    //$menu[6][0] = 'Galleries'; // Change Photos to Galleries

   // remove_submenu_page('edit.php?post_type=photo','post-new.php?post_type=photo');
}
add_action( 'admin_menu', 'edit_admin_menus' );

/* Add Seperators to Admin Menu
=================================================== */
add_action( 'admin_menu', 'set_admin_menu_separator' );

function set_menu_separator( ) {
    $positions = array( 9, 14 );

        set_admin_menu_separator( $positions );
}

function set_admin_menu_separator( ) {
    global $menu;

    $menu[19] = array(
        0    =>    '',
        1    =>    'read',
        2    =>    'separator19',
        3    =>    '',
        4    =>    'wp-menu-separator'
    );
    $menu[9] = array(
        0    =>    '',
        1    =>    'read',
        2    =>    'separator9',
        3    =>    '',
        4    =>    'wp-menu-separator'
    );

    ksort( $menu );

    return $menu;

}

function additional_admin_color_schemes() {
    //Get the theme directory
    $theme_dir = get_template_directory_uri();

    //Ocean
    wp_admin_css_color( 'comercia', __( 'Comercia' ),
        $theme_dir . '/inc/styling/css/admin.min.css',
        array( '#4c8dc2', '#7cadd3', '#365688', '#446caa' )
    );
}
add_action('admin_init', 'additional_admin_color_schemes');

function is_custom( $template = "Home Page" ){
    $classes = get_body_class();
    $types = wp_get_theme()->get_page_templates();

    $output = '';
    $key = array_search( $template, $types );
    $name = 'page-template-' . sanitize_html_class( str_replace( '.', '-', $key ) );

    if ( in_array( $name, $classes ) ) $output = true;
    return $output;
}

function WPSE_1595_image_post() {
    $args = array(
        'posts_per_page'    => -1,
        'post_type'         => 'attachment',
        'post_status'       =>'any'
    );
    $all_images = get_posts( $args );

    foreach ( $all_images as $image ) {
        //Customize this post data as you wish
        $my_post_data = array(
            'post_title'    => $image->post_title,
            'post_type'     => 'photo',
            'post_author'   => 1,
            'post_status'   => 'publish'
        );

        $post_id = wp_insert_post( $my_post_data );
        set_post_thumbnail( $post_id, $image->ID );
    }
}
?>
