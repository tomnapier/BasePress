<?php 

/* Show only one post from one category on home page
======================================= */

function my_home_category( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
    $option = get_option('iw_home_cat'); 
        $query->set( 'cat', $option );
        $query->set('posts_per_page',1);
    }
}
add_action( 'pre_get_posts', 'my_home_category' );
?>