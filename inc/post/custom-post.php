<?php
#######################################################################################
# teams Custom Post Types
#######################################################################################
global $iwcustom;
if ($iwcustom == "") { $iwcustom = array(); }
array_push($iwcustom, "team" );

// 1. Custom Post Type Registration
add_action( 'init', 'create_team_type', 0  );
function create_team_type() {
    $args = array(
        'labels' => custom_post_type_labels( 'Team Member','Team' ),
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'capability_type' => 'post',
        'hierarchical' => false,
        'has_archive' => true,
        'taxonomies' => array('style'),
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'can_export' => true
    );
    register_post_type( 'team', $args );
}

// A helper function for generating the labels
function team_labels( $singular, $plural = '' ) {
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

// 4. Customize Update Messages
add_filter('post_updated_messages', 'team_updated_messages');
function team_updated_messages( $messages ) {
    global $post, $post_ID;

    $messages['teams'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __('team updated. <a href="%s">View team</a>'), esc_url( get_permalink($post_ID) ) ),
            2 => __('Custom field updated.'),
            3 => __('Custom field deleted.'),
            4 => __('team updated.'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf( __('team restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __('team published. <a href="%s">View team</a>'), esc_url( get_permalink($post_ID) ) ),
            7 => __('team saved.'),
            8 => sprintf( __('team submitted. <a target="_blank" href="%s">Preview team</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
            9 => sprintf( __('team scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview team</a>'),
            // translators: Publish box date format, see php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => sprintf( __('team draft updated. <a target="_blank" href="%s">Preview team</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}

/* Customise Columns
======================================= */
add_filter( 'manage_edit-team_columns', 'my_extra_team_columns' );
add_action( 'manage_team_posts_custom_column', 'my_team_column_content', 10, 2 );

function my_extra_team_columns( $columns ) {
    $columns = array(
        "cb"                => "<input type=\"checkbox\" />",
        "title"             => "Title",
        "team_type"       => 'Type',
        "team_thumbnail"    => 'Thumb',
        "comments"          => '<span title="Comments" class="comment-grey-bubble"></span>',
        "date"              => "Date"
    );
    return $columns;
}

function my_team_column_content( $column ) {
    global $post;
    $parent = $post->ID;

    if( $post->post_parent != 0 ) $parent = $post->post_parent;
    $custom = get_post_custom($parent);
    $colour = $custom['team_colour'][0];

    switch ($column) {
       case 'team_type' :
            /* Get the genres for the post. */
            $terms = get_the_terms( $post_id, 'type' );
            /* If terms were found. */
            if ( !empty( $terms ) ) {
                $out = array();
                /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                foreach ( $terms as $term ) {
                    $out[] = sprintf( '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'type' => $term->slug ), 'edit.php' ) ),
                        esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'type', 'display' ) )
                    );
                }
                /* Join the terms, separating them with a comma. */
                echo join( ', ', $out );
            }
            /* If no terms were found, output a default message. */
            else {
                _e( 'Not set' );
            }
        break;
        case "team_thumbnail":
            $thumb  = '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' . $post->ID . '&amp;action=edit" title="Edit “' . $post->post_title . '”">';
            $thumb .= (has_post_thumbnail( $post->ID ))? get_the_post_thumbnail( $post->ID, array( 70, 70 ) ) : '<img src="http://placehold.it/70&text=No+Thumb">';
            $thumb .= '</a>';
            echo $thumb;
        break;
    }
}


/* Add Filters
======================================= */

/* Create Taxonomy
======================================= */
add_action( 'init', 'create_team_taxonomies', 0 );

function create_team_taxonomies() {
    global $wp_rewrite;

    // Create Style
    $labels = array(
        'name'              => _x( 'Team Types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Team Type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Team Types' ),
        'all_items'         => __( 'All Team Types' ),
        'parent_item'       => __( 'Parent Team Type' ),
        'parent_item_colon' => __( 'Parent Team Type:' ),
        'edit_item'         => __( 'Edit Team Type' ),
        'update_item'       => __( 'Update Team Type' ),
        'add_new_item'      => __( 'Add New Team Type' ),
        'new_item_name'     => __( 'New team Type Name' ),
        'menu_name'         => __( 'Team Type' ),
    );

    $args = array(
        'hierarchical'      => true,
        'query_var'         => 'team_type',
        'rewrite'           => $rewrite['category'],
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'labels'            => $labels,
    );

    register_taxonomy( 'type', 'team', $args );
}


/* Make sure we set a valid category.
================================================== */
function set_team_categories( $post_id ) {
    if ( 'team' != $_POST['post_type'] ) {
        return;
    }
    $taxonomy = $_POST['tax_input'];
    if ( $taxonomy['style'][1] ){
        return;
    }

    // If this is a revision, get real post ID
    if ( $parent_id = wp_is_post_revision( $post_id ) ) 
        $post_id = $parent_id;

    // Get default category ID from options
    $defaultcat = get_option( 'iw_team_style' );

    // unhook this function so it doesn't loop infinitely
    remove_action( 'save_post', 'set_team_categories' );

    // update the post, which calls save_post again
    $cat_ids = array( $defaultcat );
    $cat_ids = array_map( 'intval', $cat_ids );
    $cat_ids = array_unique( $cat_ids );
    wp_set_object_terms( $post_id, $cat_ids, 'style', true );

    // re-hook this function
    add_action( 'save_post', 'set_team_categories' );
}
add_action( 'save_post', 'set_team_categories' );


/* Add Meta Box
======================================= */

function add_team_meta_box() {
    add_meta_box(
        'team_options_box', // $id
        'Team Member Information', // $title 
        'team_meta_box', // $callback
        'team', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_team_meta_box');

/* Metabox loop
======================================== */
$prefix = 'team_';
$team_meta_fields = array(

     array(
        'label'     => 'Position',
        'desc'      => '',
        'id'        => $prefix.'position',
        'type'      => 'text'
    ),
    array(
        'label'     => 'Contact',
        'desc'      => '',
        'id'        => $prefix.'contact',
        'type'      => 'text'
    )
);

/* The Callback
======================================== */
function team_meta_box() {
    global $team_meta_fields, $post;

    $name = basename(__FILE__);

    create_meta_box( $team_meta_fields, get_post_type(), $name );
}

/* Save the Data
======================================== */
function save_team_meta($post_id) {
    global $team_meta_fields;

    // verify nonce
    if ( !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename(__FILE__) ) ){
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('team' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($team_meta_fields as $field) {
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
add_action('save_post', 'save_team_meta');

/* Custom Query
================================================= */
function order_team( $query ) {
    if ( is_post_type_archive( 'team' ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'order_team' );
?>