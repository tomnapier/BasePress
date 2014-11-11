<?php
/* Register Post Thumbnails
=================================================== */
add_theme_support( 'post-thumbnails');
add_image_size( 'header', 1600, 600, true );
add_image_size( 'slider', 1600, 440, true );
add_image_size( 'square', 440, 440, true );
add_image_size( 'archive', 320, 200, true );
add_image_size( 'single', 460, 295, true );

/* Allow SVGs
=================================================== */
function cc_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

/* New Post Thumbnmail
=================================================== */
function new_post_thumbnail( $s = "thumbnail" ){
    global $post;

    echo get_new_post_thumbnail( $post->ID, $s );

}

function get_new_post_thumbnail( $id, $s = "thumbnail" ){
    $size = get_image_size( $s );

    if( has_post_thumbnail( $id ) ) :
        return get_the_post_thumbnail( $id, $s );
    else :
        return '<img width="' . $size[0] . '" height="' . $size[1] . '" src="http://placehold.it/' . $size[0] . 'x' . $size[1] . '" class="attachment-' . $s . ' wp-post-image" alt="' . get_the_title( $id ) . '">';
    endif;

}

function get_image_size( $s = "thumbnail" ){
    global $_wp_additional_image_sizes;

    $default = get_intermediate_image_sizes();
    if( in_array( $s, $default ) ) :
        $w = get_option( $s . '_size_w' );
        $h = get_option( $s . '_size_h' );
    endif;

    if( $_wp_additional_image_sizes[ $s ] ) :
        $w = $_wp_additional_image_sizes[ $s ][ 'width' ];
        $h = $_wp_additional_image_sizes[ $s ][ 'height' ];
    endif;

    return array( $w, $h );
}

function new_get_attachment_image( $id = null, $s = "thumbnail" ){
    $size = get_image_size( $s );

    if( $id ) :
        echo wp_get_attachment_image( $id, $s );
    else :
        echo '<img width="' . $size[0] . '" height="' . $size[1] . '" src="http://placehold.it/' . $size[0] . 'x' . $size[1] . '" class="attachment-' . $s . ' wp-post-image" alt="' . $post->post_title . '">';
    endif;

}
?>
