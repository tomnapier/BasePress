<?php

/* Register the custom post type
================================ */

function custom_post_type() {

    $labels = array(
        'name'                => _x( 'Post Types', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Post Type', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Post Type', 'text_domain' ),
        'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
        'all_items'           => __( 'All Items', 'text_domain' ),
        'view_item'           => __( 'View Item', 'text_domain' ),
        'add_new_item'        => __( 'Add New Item', 'text_domain' ),
        'add_new'             => __( 'Add New', 'text_domain' ),
        'edit_item'           => __( 'Edit Item', 'text_domain' ),
        'update_item'         => __( 'Update Item', 'text_domain' ),
        'search_items'        => __( 'Search Item', 'text_domain' ),
        'not_found'           => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'post_type', 'text_domain' ),
        'description'         => __( 'Post Type Description', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
        'taxonomies'          => array( 'category', 'post_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'post_type', $args );

}

// Hook into the 'init' action
add_action( 'init', 'custom_post_type', 0 );


/* Add custom update messages
============================= */
add_filter( 'post_updated_messages', 'post_type_updated_messages' );

function post_type_updated_messages( $messages ) {
    $post             = get_post();
    $post_type        = get_post_type( $post );
    $post_type_object = get_post_type_object( $post_type );

    $messages['post_type'] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => __( 'Post Type updated.', 'your-plugin-textdomain' ),
        2  => __( 'Custom field updated.', 'your-plugin-textdomain' ),
        3  => __( 'Custom field deleted.', 'your-plugin-textdomain' ),
        4  => __( 'Post Type updated.', 'your-plugin-textdomain' ),
        /* translators: %s: date and time of the revision */
        5  => isset( $_GET['revision'] ) ? sprintf( __( 'Post Type restored to revision from %s', 'your-plugin-textdomain' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6  => __( 'Post Type published.', 'your-plugin-textdomain' ),
        7  => __( 'Post Type saved.', 'your-plugin-textdomain' ),
        8  => __( 'Post Type submitted.', 'your-plugin-textdomain' ),
        9  => sprintf(
            __( 'Post Type scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain' ),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i', 'your-plugin-textdomain' ), strtotime( $post->post_date ) )
        ),
        10 => __( 'Post Type draft updated.', 'your-plugin-textdomain' )
    );

    if ( $post_type_object->publicly_queryable ) {
        $permalink = get_permalink( $post->ID );

        $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Post Type', 'your-plugin-textdomain' ) );
        $messages[ $post_type ][1] .= $view_link;
        $messages[ $post_type ][6] .= $view_link;
        $messages[ $post_type ][9] .= $view_link;

        $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
        $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Post Type', 'your-plugin-textdomain' ) );
        $messages[ $post_type ][8]  .= $preview_link;
        $messages[ $post_type ][10] .= $preview_link;
    }

    return $messages;
}

/* Add custom dashboard columns
=============================== */

add_filter( 'manage_edit-post_type_columns', 'extra_post_type_columns' );
add_action( 'manage_post_type_posts_custom_column', 'post_type_column_content', 10, 2 );

function extra_post_type_columns( $columns ) {

    $columns = array(
        "cb"                  => "<input type=\"checkbox\" />",
        "title"               => "Title",
        "post_type_thumbnail" => 'Featured Image',
        "comments"            => '<span title="Comments" class="comment-grey-bubble"></span>',
        "date"                => "Date"
    );

    return $columns;

}

function post_type_column_content( $column ) {
    global $post;
    $parent = $post->ID;

    if( $post->post_parent != 0 ) $parent = $post->post_parent;
    $custom = get_post_custom($parent);

    switch ($column) {

        case "post_type_thumbnail":
            $thumb  = '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' . $post->ID . '&amp;action=edit" title="Edit “' . $post->post_title . '”">';
            $thumb .= (has_post_thumbnail( $post->ID ))? get_the_post_thumbnail( $post->ID, array( 70, 70 ) ) : null;
            $thumb .= '</a>';
            echo $thumb;
        break;

    }
}

/* Register custom taxonomy
=========================== */

function custom_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Taxonomies', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Taxonomy', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Taxonomy', 'text_domain' ),
        'all_items'                  => __( 'All Items', 'text_domain' ),
        'parent_item'                => __( 'Parent Item', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
        'new_item_name'              => __( 'New Item Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Item', 'text_domain' ),
        'edit_item'                  => __( 'Edit Item', 'text_domain' ),
        'update_item'                => __( 'Update Item', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
        'search_items'               => __( 'Search Items', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'taxonomy', array( 'post' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'custom_taxonomy', 0 );


/* Custom Query
================================================= */

function order_post_type( $query ) {

    if ( is_post_type_archive( 'post_type' ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
    }

}

add_action( 'pre_get_posts', 'order_post_type' );

?>