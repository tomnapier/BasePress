<?php
#######################################################################################
# Slider Custom Post Types
#######################################################################################

/* Custom Post Type Registration
======================================== */
add_action( 'init', 'create_slide_type', 0  );
function create_slide_type() {
    $args = array(
        'labels' => custom_post_type_labels( 'Slides', 'Slider' ),
        'public' => true,
        'menu_icon' => 'dashicons-images-alt2',
        'capability_type' => 'post',
        'hierarchical' => false,
        'has_archive' => true,
        'supports' => array(
            'title',
            'thumbnail',
            'page-attributes'
        ),
        'can_export' => true
    );
    register_post_type( 'slide', $args );
}

/* A helper function for generating the labels
======================================== */
function slide_labels( $singular, $plural = '' ) {
    if( $plural == '') $plural = $singular . 's';
    
    return array(
        'name' => _x( $plural, 'post type general name' ),
        'singular_name' => _x( $singular, 'post type singular name' ),
        'add_new' => __( 'Add New' ),
        'add_new_item' => __( 'Add New '. $singular ),
        'edit_item' => __( 'Edit '. $singular ),
        'new_item' => __( 'New '. $singular ),
        'view_item' => __( 'View '. $singular ),
        'search_items' => __( 'Search '. $plural ),
        'not_found' =>  __( 'No '. $plural .' found' ),
        'not_found_in_trash' => __( 'No '. $plural .' found in Trash' ), 
        'parent_item_colon' => ''
    );
}

/* Customize Update Messages
======================================== */
add_filter('post_updated_messages', 'slide_updated_messages');
function slide_updated_messages( $messages ) {
    global $post, $post_ID;

    $messages['slides'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __('Slider updated. <a href="%s">View slide</a>'), esc_url( get_permalink($post_ID) ) ),
            2 => __('Custom field updated.'),
            3 => __('Custom field deleted.'),
            4 => __('Slider updated.'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf( __('Slider restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __('Slider published. <a href="%s">View slide</a>'), esc_url( get_permalink($post_ID) ) ),
            7 => __('Slider saved.'),
            8 => sprintf( __('Slider submitted. <a target="_blank" href="%s">Preview slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
            9 => sprintf( __('Slider scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview slide</a>'),
            // translators: Publish box date format, see php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => sprintf( __('Slider draft updated. <a target="_blank" href="%s">Preview slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}

/* Add the Meta Box
======================================== */
function add_slide_meta_box() {
    add_meta_box(
        'slide_options_box', // $id
        'Slider Options', // $title 
        'slide_meta_box', // $callback
        'slide', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_slide_meta_box');

/* Metabox loop
======================================== */
$prefix = 'slide_';
$slide_meta_fields = array(

    array(
        'label'     => 'New Window',
        'desc'      => '',
        'id'        => $prefix.'target',
        'type'      => 'checkbox'
    ),

    array(
        'label'     => 'Linked Post',
        'desc'      => '',
        'id'        => $prefix.'post',
        'type'      => 'post_list',
        'post_type' => array( 'post','project','service', 'page' ),
    ),
    
     array(
        'label'     => 'Slide Heading',
        'desc'      => '',
        'id'        => $prefix.'heading',
        'type'      => 'text'
    ),

   array(
        'label'     => 'Slide Caption',
        'desc'      => '',
        'id'        => $prefix.'caption',
        'type'      => 'text'
    ),

    array(
        'label'     => 'External Link',
        'desc'      => '',
        'id'        => $prefix.'link',
        'type'      => 'textarea'
    )
);

/* The Callback
======================================== */
function slide_meta_box() {
    global $slide_meta_fields, $post;

    $name = basename(__FILE__);

    create_meta_box( $slide_meta_fields, get_post_type(), $name );
}

/* Save the Data
======================================== */
function save_slide_meta($post_id) {
    global $slide_meta_fields;

    // verify nonce
    if ( !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename(__FILE__) ) ){
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('slide' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($slide_meta_fields as $field) {
        if($field['type'] == 'tax_select') continue;
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // enf foreach

    // save taxonomies
    $post = get_post($post_id);
    $category = $_POST['category'];
    wp_set_object_terms( $post_id, $category, 'category' );
}
add_action('save_post', 'save_slide_meta');

/* Customise Columns
======================================= */
add_filter('manage_edit-slide_columns', 'my_extra_slide_columns');
add_action('manage_slide_posts_custom_column', 'my_slide_column_content', 10, 2 );
add_action( 'pre_get_posts', 'my_slide_orderby' );

function my_extra_slide_columns($columns) {
    $columns = array(
        "cb"                => "<input type=\"checkbox\" />",
        "title"             => "Title",
        "slide_heading"     => "Slide Heading",
        "slide_caption"     => "Slide Caption",
        "slide_image"       => "Image",
        "author"            => "Author",
        "date"              => "Date"
    );
    return $columns;
}

function my_slide_column_content( $column ) {
    global $post;
    $parent = $post->ID;
    
    $meta = get_post_custom();
    
    switch ($column) {
        case "slide_heading":
             echo $meta['slide_heading'][0];
        break;
        case "slide_caption":
             echo $meta['slide_caption'][0];
        break;
        case "slide_image":
            $thumb  = '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' . $post->ID . '&amp;action=edit" title="Edit “' . $post->post_title . '”">';
            $thumb .= get_the_post_thumbnail( $post->ID, 'thumbnail' );
            $thumb .= '</a>';
            echo $thumb;
        break;
    }
}
function my_slide_orderby( $query ) {
    if( ! is_admin() )
        return;
    if ($query->query_vars[post_type] == "slide"){
        $query->set('order','ASC');
        $query->set('orderby','menu_order');
    }
}
?>