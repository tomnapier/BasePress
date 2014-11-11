<?php
/* Shortcodes
=================================================== */
function my_gallery_func($atts) {
    global $post;
    extract(shortcode_atts(array(  
        "skip" => '0',
        "thumb" => false
    ), $atts));
    $start .= '<div class="my-gallery alignright">';
    $first = false;
    $exclude_ids = explode(",", $skip);

    $query_images_args = array(
        'post_parent'        => $post->ID,
        'post_type'            => 'attachment',
        'post_mime_type'    =>'image',
        'post_status'        => 'inherit',
        'posts_per_page'    => -1,
        'post__not_in'        => $exclude_ids
    );
    
    $i = 1;
    $query_images = new WP_Query( $query_images_args );
    $images = array();
    $total = count($query_images->posts);

    $body = '<div class="clear">';
    foreach ( $query_images->posts as $image) {
        $id = $image->ID;
        $img = wp_get_attachment_url( $id );
        $square = wp_get_attachment_image_src( $id, 'my-gallery' );
        $size = getimagesize($img); 
        $width = $size[0]; 
        $height = $size[1]; 
        $aspect = $height / $width;
        if ($thumb){
            if($thumb == $id){
                $first = true;
            }
        } else {
            if ($i == 1){ $first = true; }
        }
        if ($first == true) {
            $button = '<a id="gallery-' . $i . '" href="' . $img . '" class="button full-width fancybox" rel="gallery1" title="'. $image->post_title .'">Visit the Gallery</a>';
            $start .= '<img src="'.$square[0].'" class="thumbnail" />';
            $first = false;
        } else {
            $body .= '<a id="gallery-' . $i . '" href="' . $img . '" rel="gallery1" title="'. $image->post_title .'"></a>';
        }

        if ($i == $total && $button == ""){
            $button = 'Error';
        }
        $i++;
    }
    $output = $start . $body . '</div>' . $button . '</div>';
    return $output;
}
add_shortcode('my_gallery', 'my_gallery_func');

/* Removes automatic formatting with [raw][/raw]
=================================================== */
function gw_formatter($content) {
    $new_content = '';
    $pattern_full = '{(\[raw\].*?\[/raw\])}is';
    $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($pieces as $piece) {
        if (preg_match($pattern_contents, $piece, $matches)) {
            $new_content .= $matches[1];
        } else {
            $new_content .= wptexturize(wpautop($piece));
        }
    }

    return $new_content;
}
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'gw_formatter', 99);

/* Columns
=================================================== */
function gw_columns($atts, $content = null, $code) {
    extract(shortcode_atts(array(
        'large'     => '3',
        'medium'    => '2',
        'small'     => '1'
    ), $atts));

    $l = round(12 / $large);
    $m = round(12 / $medium);
    $s = round(12 / $small);

    if (!preg_match_all("/(.?)\[(column)\b(.*?)(?:(\/))?\](?:(.+?)\[\/column\])?(.?)/s", $content, $matches)) {
        return do_shortcode($content);
    } else {
        for($i = 0; $i < count($matches[0]); $i++) {
            $matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
        }

        for($i = 0; $i < count($matches[0]); $i++) {
            $output .= '<div class="large-'.$l.' medium-'.$m.' small-'.$s.' columns">';
            $output .= do_shortcode( trim($matches[5][$i]) );
            $output .= '</div>';
            if( ( $i + 1 ) % $large == 0 ) $output .= '</div><div class="row">';
        }

        return '<div class="row">' . $output . '</div>';
    }
}
add_shortcode('columns', 'gw_columns');

/* Rows
=================================================== */
function gw_row($atts, $content = null, $code) {
    if(!empty($content)){
        return '<div class="row">' . do_shortcode($content) . '</div>';
    }
}
add_shortcode('row', 'gw_row');

/* Tabs shortcode
=================================================== */
function gw_tabs($atts, $content = null, $code) {
    extract(shortcode_atts(array(
        'style' => false,
        'history' => false
    ), $atts));
    
    if (!preg_match_all("/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches)) {
        return do_shortcode($content);
    } else {
        for($i = 0; $i < count($matches[0]); $i++) {
            $matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
        }

        $tabtitles = '<ul class="tabs vertical" data-tab>';
        $tabcontent = '<div class="tabs-content vertical">';

        for($i = 0; $i < count($matches[0]); $i++) {
            $tabtitles .= '<li class="tab-title' . ( ($i == 0 )? ' active' : null ) . '"><a href="#tab-' . strtolower(str_replace(" ", "_", trim($matches[3][$i]['title']))) . '">' . $matches[3][$i]['title'] . '</a></li>';
            $tabcontent .= '<div id="tab-' . strtolower(str_replace(" ", "_", trim($matches[3][$i]['title']))) . '" class="content' . ( ($i == 0 )? ' active' : null ) . '">' . do_shortcode(trim($matches[5][$i])) . '</div>';
        }

        $tabtitles .= '</ul>';
        $tabcontent .= '</div>';

        return '<div class="section-container tabs">' . $tabtitles . $tabcontent . '</div>';
    }
}
add_shortcode('tabs', 'gw_tabs');

/* Accordion shortcode
=================================================== */
function gw_accordion($atts, $content = null, $code) {
    extract(shortcode_atts(array(
        'style' => false,
        'history' => false
    ), $atts));
    
    if (!preg_match_all("/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches)) {
        return do_shortcode($content);
    } else {
        for($i = 0; $i < count($matches[0]); $i++) {
            $matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
        }

        for($i = 0; $i < count($matches[0]); $i++) {
            $output .= '<dd class="accordion-navigation">';
            $output .= '<a href="#' . strtolower(str_replace(" ", "_", trim($matches[3][$i]['title']))) . '">' . $matches[3][$i]['title'] . '</a>';
            $output .= '<div id="' . strtolower(str_replace(" ", "_", trim($matches[3][$i]['title']))) . '" class="content">' . do_shortcode(trim($matches[5][$i])) . '</div>';
            $output .= '</dd>';
        }

        return '<dl class="accordion" data-accordion>' . $output . '</dl>';
    }
}
add_shortcode('accordion', 'gw_accordion');

/* All Posts
=================================================== */
function gw_all_posts( $atts, $content = null ) {
    extract(shortcode_atts(array(
        'type'        => 'post',
        'number'    => get_option( 'posts_per_page' )
    ), $atts));

    $args = array( 'post_type' => $type, 'posts_per_page' => $number );
    if( $type != 'post' ){ 
        $args = array( 'order' => 'ASC', 'orderby' => 'menu_order' ) + $args;
    }

    $the_query = new WP_Query($args);
    $output = "";
    while ( $the_query->have_posts() ) : $the_query->the_post();

        $post_type = ( 'post' == $type )? 'single' : $type;
        $output .= load_template_part( 'content', $post_type );

    endwhile;

    $output .= '<div class="view-more"><a href="' . get_post_type_archive_link( $type ) .'" class="button radius secondary">View More</a>';

    return '<div class="post-list type-' . $type . ' row">' . $output . '</div>';
}
add_shortcode('all_posts', 'gw_all_posts');

function load_template_part( $template_name, $part_name = null ) {
    ob_start();
    get_template_part( $template_name, $part_name );
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}


/* New Gallery Shortcode
========================================= */
remove_shortcode( 'gallery' );
add_shortcode( 'gallery', 'iw_gallery_shortcode' );

function iw_gallery_shortcode( $attr ) {
    $post = get_post();

    static $instance = 0;
    $instance++;

    if ( ! empty( $attr['ids'] ) ) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if ( empty( $attr['orderby'] ) )
            $attr['orderby'] = 'post__in';
        $attr['include'] = $attr['ids'];
    }

    /**
     * Filter the default gallery shortcode output.
     *
     * If the filtered output isn't empty, it will be used instead of generating
     * the default gallery template.
     *
     * @since 2.5.0
     *
     * @see gallery_shortcode()
     *
     * @param string $output The gallery output. Default empty.
     * @param array  $attr   Attributes of the gallery shortcode.
     */
    $output = apply_filters( 'post_gallery', '', $attr );
    if ( $output != '' )
        return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    $html5 = current_theme_supports( 'html5', 'gallery' );
    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'slider'     => false,
        'id'         => $post ? $post->ID : 0,
        'itemtag'    => $html5 ? 'figure'     : 'li',
        'icontag'    => $html5 ? 'div'        : 'span',
        'captiontag' => $html5 ? 'figcaption' : 'dd',
        'columns'    => 4,
        'size'       => 'square',
        'include'    => '',
        'exclude'    => '',
        'link'       => 'file'
    ), $attr, 'gallery'));

    $small = floor( $columns / 2 );
    ( $slider )? $size = 'slider' : null;

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $icontag = tag_escape($icontag);
    $valid_tags = wp_kses_allowed_html( 'post' );
    if ( ! isset( $valid_tags[ $itemtag ] ) )
        $itemtag = 'li';
    if ( ! isset( $valid_tags[ $captiontag ] ) )
        $captiontag = 'dd';
    if ( ! isset( $valid_tags[ $icontag ] ) )
        $icontag = 'span';

    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';

    $size_class = sanitize_html_class( $size );
    $gallery_div = ( $slider )? "<ul id='$selector' class='gallery galleryid-{$id} gallery-orbit' data-orbit>" : "<ul id='$selector' class='gallery galleryid-{$id} small-block-grid-{$small} large-block-grid-{$columns} clearing-thumbs' data-clearing>";

    /**
     * Filter the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default gallery shortcode CSS styles.
     * @param string $gallery_div   Opening HTML div container for the gallery shortcode output.
     */
    $output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        if ( ! empty( $link ) && 'file' === $link )
            $image_output = str_replace( "<a", "<a class='th'", wp_get_attachment_link( $id, $size, false, false ) );
        elseif ( ! empty( $link ) && 'none' === $link )
            $image_output = wp_get_attachment_image( $id, $size, false );
        else
            $image_output = wp_get_attachment_link( $id, $size, true, false );

        $image_meta  = wp_get_attachment_metadata( $id );

        $orientation = '';
        if ( isset( $image_meta['height'], $image_meta['width'] ) )
            $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "<{$icontag} class='gallery-icon {$orientation}'>$image_output   </{$icontag}>";
        $output .= "</{$itemtag}>";
    }


    $output .= "
        </ul>\n";

    return $output;
}
 ?>