<?php

add_theme_support( 'post-formats', array( 'audio', 'gallery', 'video' ) );

/* Custom comments
=================================================== */
function custom_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth; ?>

    <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
        <?php commenter_link(); ?>
    <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'your-theme') ?>
        <div class="comment-content">
            <?php comment_text() ?>
        </div>
        <?php if($args['type'] == 'all' || get_comment_type() == 'comment') :
            comment_reply_link(array_merge($args, array(
                'reply_text' => __('Reply','your-theme'), 
                'login_text' => __('Log in to reply.','your-theme'),
                'depth' => $depth,
                'before' => '<div class="comment-reply-link">', 
                'after' => '</div>'
        )));
    endif; ?>
        <div class="clear"></div>
    </li>
<?php }

/* Excerpt Styling
=================================================== */
//remove_filter('the_excerpt', 'wpautop');                        /* Remove Styling */

function custom_excerpt_length( $length ) {                        /* Excerpt Length */
    return 35;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {                            /* Excerpt More */
    global $post;

    $output = ( get_post_type() != 'team' )? '...<a href="' . get_permalink( $post->ID ) . '" class="read-more">Read More</a>' : '...<a href="#" data-reveal-id="team-' . $post->ID . '" class="read-more team-more">Read More</a>';
    return $output;
}
add_filter('excerpt_more', 'new_excerpt_more');

/* Content Styling
=================================================== */
function first_paragraph($content){
    return preg_replace('/<p([^>]+)?>/', '<p$1 class="lead">', $content, 1);
}
add_filter('the_content', 'first_paragraph', 100);

/* Excerpt on pages
=================================================== */
add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
}

function custom_excerpt( $id = '', $link = false ) {

    $content = get_post( $id );
    $text = $content->post_content;

    $text = do_shortcode( $text );
    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]>', $text);
    $excerpt_length = apply_filters('excerpt_length', 55);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

    $end = ( $link )? '<a href="' . get_permalink( $id ) . '" rel="bookmark">Read more</a>' : null;

    return wpautop( apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt ) . $end );
}

/* Excerpt on pages
=================================================== */
function related_posts( $postid, $taxonomy ){

    $found_none = '<h2>No related posts found!</h2>';
    $param_type = $taxonomy; // e.g. tag__in, category__in, but genre__in will NOT work

    $post_types = get_post_types( array( 'public' => true ), 'names' );
    $tax_args = array( 'orderby' => 'none' );

    $tags = wp_get_post_terms( $postid, $taxonomy, $tax_args );

    if ( $tags ) {
        foreach ( $tags as $tag ) {
            $args=array(
                "$param_type"      => $tag->slug,
                'post__not_in'     => array($post->ID),
                'post_type'        => $post_types,
                'showposts'        =>-1,
                'caller_get_posts' =>1
            );

            $my_query = null;
            $my_query = new WP_Query( $args );
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post(); ?>
                    <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
                    <?php $found_none = '';
                endwhile;
            }
        }
    }
    if ( $found_none ) {
        $output = $found_none;
    }

    return $output;
    // copy it back
    wp_reset_query(); // to use the original query again
}

/* Related
=================================================== */
function get_related_posts( $post_id, $number = 3, $taxonomy = 'post_tag' ) {

    $related_ids = false;

    $post_ids = array();
    // get tag ids belonging to $post_id
    $tag_ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

    if ( $tag_ids ) {
        // get all posts that have the same tags
        $tag_posts = get_posts(
            array(
                'post_type'      => get_post_type( $post_id ),
                'posts_per_page' => -1, // return all posts
                'fields'         => 'ids',
                'post__not_in'   => array( $post_id ), // exclude $post_id from results
                'tax_query'      => array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'id',
                        'terms'    => $tag_ids
                    )
                )
            )
        );

        // loop through posts with the same tags
        if ( $tag_posts ) {
            $score = array();
            $i = 0;
            foreach ( $tag_posts as $tag_post ) {
                // get tags for related post
                $terms = wp_get_post_terms( $tag_post, $taxonomy, array( 'fields' => 'ids' ) );
                $total_score = 0;

                foreach ( $terms as $term ) {
                    if ( in_array( $term, $tag_ids ) ) {
                        ++$total_score;
                    }
                }

                if ( $total_score > 0 ) {
                    $score[$i]['ID'] = $tag_post;
                    // add number $i for sorting
                    $score[$i]['score'] = array( $total_score, $i );
                }
                ++$i;
            }

            // sort the related posts from high score to low score
            uasort( $score, 'sort_tag_score' );
            // get sorted related post ids
            $related_ids = wp_list_pluck( $score, 'ID' );
            // limit ids
            $related_ids = array_slice( $related_ids, 0, (int) $number );
        }
    }
    return $related_ids;
}

function sort_tag_score( $item1, $item2 ) {
    if ( $item1['score'][0] != $item2['score'][0] ) {
        return $item1['score'][0] < $item2['score'][0] ? 1 : -1;
    } else {
        return $item1['score'][1] < $item2['score'][1] ? -1 : 1; // ASC
    }
}

/* Limit Words
============================================ */
function string_limit_words($string, $word_limit){
    $words = explode(' ', $string, ($word_limit + 1));
    if(count($words) > $word_limit)
        array_pop($words);
    return implode(' ', $words);
}
function aasort (&$array, $key , $key2 = null) {
    $sorter = array();
    $ret = array();
    reset($array);

    foreach ($array as $ii => $va) {
        $value = $va[$key];
        if ($key2) { $value .= "." . $va[$key2]; }
        $sorter[$ii] = $value;
    }
    asort($sorter);

    foreach ($sorter as $ii => $va) {
        $ret[$ii] = $array[$ii];
    }

    $array = $ret;
}

/* Get Background Color
============================================ */
function get_background( $url, $x, $y ){
    $path_parts = pathinfo( $url );

    if( $path_parts['extension'] == 'jpg' ){ $im = imagecreatefromjpeg( $url ); }
    elseif( $path_parts['extension'] == 'png' ){ $im = imagecreatefrompng( $url ); }
    elseif( $path_parts['extension'] == 'gif' ){ $im = imagecreatefromgif( $url ); }
    else { return; }

    $x--; $y--;

    $rgb = imagecolorat( $im, $x, $y );
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    $colour = 'rgb(' . $r .', ' . $g .', ' . $b . ')';

    //$colour = average_colour( $im, $x, $y );

    return '<style>body { background-color: ' . $colour . '; background-image: url(' . $url . '); }</style>';
}

function average_colour( $img, $w, $h ) {
    $r = $g = $b = 0;
    for($y = 0; $y < $h; $y++) {
        for($x = 0; $x < $w; $x++) {
            $rgb = imagecolorat($img, $x, $y);
            $r += $rgb >> 16;
            $g += $rgb >> 8 & 255;
            $b += $rgb & 255;
        }
    }
    $pxls = $w * $h;
    $r = dechex(round($r / $pxls));
    $g = dechex(round($g / $pxls));
    $b = dechex(round($b / $pxls));
    if(strlen($r) < 2) {
        $r = 0 . $r;
    }
    if(strlen($g) < 2) {
        $g = 0 . $g;
    }
    if(strlen($b) < 2) {
        $b = 0 . $b;
    }
    return "#" . $r . $g . $b;
}

/* Home Page Posts
============================================ */
function one_post_on_homepage( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $cats = get_option( 'iw_home_cat' );
        $query->set( 'posts_per_page', 1 );
        $query->set( 'cat', $cats );
    }
}
add_action( 'pre_get_posts', 'one_post_on_homepage' );

/* Home Page Posts
============================================ */
function category_id_class($classes) {
    global $post;

    $taxonomies = get_object_taxonomies( get_post_type() );

    foreach( $taxonomies as $taxonomy ) : 
        $terms = get_the_terms( $post->ID, $taxonomy );

        if( $terms ) :

            foreach ( $terms as $category ) :
                $classes[] = $taxonomy . '-' . $category->term_id;
            endforeach;
        endif;
    endforeach;

        return $classes;
}
add_filter('post_class', 'category_id_class');
?>