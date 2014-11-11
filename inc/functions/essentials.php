<?php
/* Make theme available for translation. Translations can be filed in the /languages/ directory
=================================================== */
load_theme_textdomain( 'your-theme', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable($locale_file) )
    require_once($locale_file);

/* Get the page number
=================================================== */
function get_page_number() {
    if (get_query_var('paged')) {
        print ' | ' . __( 'Page ' , 'your-theme') . get_query_var('paged');
    }
} // end get_page_number

/* For category lists on category archives: Returns other categories except the current one (redundant)
=================================================== */
function cats_meow($glue) {
    $current_cat = single_cat_title( '', false );
    $separator = "\n";
    $cats = explode( $separator, get_the_category_list($separator) );
    foreach ( $cats as $i => $str ) {
        if ( strstr( $str, ">$current_cat<" ) ) {
            unset($cats[$i]);
            break;
        }
    }
    if ( empty($cats) )
        return false;

        return trim(join( $glue, $cats ));
} // end cats_meow

/* For tag lists on tag archives: Returns other tags except the current one (redundant)
=================================================== */
function tag_ur_it($glue) {
    $current_tag = single_tag_title( '', '',    false );
    $separator = "\n";
    $tags = explode( $separator, get_the_tag_list( "", "$separator", "" ) );
    foreach ( $tags as $i => $str ) {
        if ( strstr( $str, ">$current_tag<" ) ) {
            unset($tags[$i]);
            break;
        }
    }
    if ( empty($tags) )
        return false;

    return trim(join( $glue, $tags ));
} // end tag_ur_it

/* Produces an avatar image with the hCard-compliant photo class
=================================================== */
function commenter_link() {
    $commenter = get_comment_author_link();
    if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
        $commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
    } else {
        $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
    }
    $avatar_email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 48 ) );
    $adate = get_comment_date("Y-m-d\TH:i:sP");
    $today = time();
    $today = date(DATE_ATOM,$today);
    $vdate = dateDifference($adate, $today);
    $date = '<span class="date">' . $vdate . '</span>';

    echo '<div class="profile">' . $avatar . '</div><div class="comment-author">' . $commenter . $date . '</div>';
} // end commenter_link

/* Custom callback to list pings
=================================================== */
function custom_pings($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
        <div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'your-theme'),
            get_comment_author_link(),
            get_comment_date(),
            get_comment_time() );
            edit_comment_link(__('Edit', 'your-theme'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
    <?php if ($comment->comment_approved == '0') _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'your-theme') ?>
        <div class="comment-content">
            <?php comment_text() ?>
        </div>
<?php } // end custom_pings

/* Featured Image Class
=================================================== */
function add_featured_image_body_class( $classes ) {
    global $post;

    
    if ( is_category() && get_option( "mlas_default_img" ) || get_post_type() == 'post' && get_option( "mlas_default_img" ) ) {
        $classes[] = 'has-featured-image';
    }

    if ( isset ( $post->ID ) && get_the_post_thumbnail( $post->ID ) && is_page() ) {
        $classes[] = 'has-featured-image';
    }

    return $classes;
}
add_filter( 'body_class', 'add_featured_image_body_class' );

/* Get Attribute
=================================================== */
function get_attribute( $embed, $key ){
    $embed = str_replace( array('<iframe ', '</iframe>', '/>' ), "", $embed);
    $options = explode( '" ', trim( $embed ) );
    $code = array();

    foreach( $options as $option ){
        $attribute = explode( '="', trim( $option ) );
        $code[ trim( $attribute[0] ) ] = $attribute[1];
    }

    if( array_key_exists( $key, $code ) ) {
        $attrs = $code;
    } else {
        foreach( $code as $a => $b ) {
            if( $a == 'style' ){
                $attrs = array();
                $attr = explode( ";", $b );
                foreach( $attr as $value ){
                    $css = explode( ":", $value );
                    $attrs[ trim( $css[0] ) ] = $css[1];
                };
            }
        }
    }

    echo $attrs[ $key ];
}

/* Breadcrumbs
=================================================== */
function the_breadcrumb() {
    $output = "";
    if ( !is_front_page() ) {

        $output .= '<ul class="breadcrumbs">';
        $output .= '<li><a href="' . get_option('home') . '"><span class="dashicons dashicons-admin-home"></span></a></li>';

        if ( is_category() || is_single() ){
            $cats = get_the_category( );

            foreach( $cats as $cat ){
                $output .= '<li' . ( ( is_category() )? ' class="current"' : '><a href="' . get_category_link( $cat->term_id ) . '"' ) . '>' . $cat->name . ( ( !is_category() )? '</a>' : null ) . '</li>';
            }

        } elseif ( is_archive() || is_single() ){
            if ( is_day() ) {
                $output .= sprintf( __( '%s', 'text_domain' ), get_the_date() );
            } elseif ( is_month() ) {
                $output .= sprintf( __( '%s', 'text_domain' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'text_domain' ) ) );
            } elseif ( is_year() ) {
                $output .= sprintf( __( '%s', 'text_domain' ), get_the_date( _x( 'Y', 'yearly archives date format', 'text_domain' ) ) );
            } elseif( !is_post_type_archive( 'post' ) ){
                $type = get_post_type();
                $obj = get_post_type_object( $type );
                $output .= '<li class="current">' . $obj->labels->name . '</li>';
            } else {
                $output .= __( 'Blog Archives', 'text_domain' );
            }
        }

        if( is_single() && !is_singular( array( 'post', 'page', 'attachment' ) ) ) {
            $type = get_post_type();
            $obj = get_post_type_object( $type );
            $output .= '<li><a href="' . get_post_type_archive_link( get_post_type() ) . '">' . $obj->labels->name . '</a></li>';
        }

        if ( is_single() ) {
            $output .= '<li class="current">' . get_the_title() . '</li>';
        }

        if ( is_page() ) {
            global $post;
            if ( $post->post_parent ){
                $output .= '<li><a href="' . get_permalink( $post->post_parent ) . '">' . get_the_title($post->post_parent) . '</a></li>';
            }
            $output .= '<li class="current">' . get_the_title() . '</li>';
        }

        if ( is_home() ){
            global $post;
            $page_for_posts_id = get_option('page_for_posts');
            if ( $page_for_posts_id ) { 
                $post = get_page($page_for_posts_id);
                setup_postdata($post);
                $output .= get_the_title();
                rewind_posts();
            }
        }

        $output .= '</ul>';
    }

    echo $output;
}

/* Add custom taxonomy to post_class()
======================================= */
function taxonomy_id_class( $classes ) {
    global $post;

    if( get_post_type( $post->ID ) == 'game' ) {
        $term = 'style';
    } elseif( get_post_type( $post->ID ) == 'location' ) {
        $term = 'type';
    } elseif( get_post_type( $post->ID ) == 'post' ) {
        $term = 'category';
    }
    $taxonomies = get_the_terms( $post->ID , $term );

    if( $taxonomies ) :
        foreach( $taxonomies as $taxonomy )
            $classes[] = $taxonomy->taxonomy . '-' . $taxonomy->slug;
    endif;

    return $classes;
}
add_filter('post_class', 'taxonomy_id_class');

?>
