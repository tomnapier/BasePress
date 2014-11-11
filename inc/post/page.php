<?php
#######################################################################################
# Page Meta Box
#######################################################################################

/* Add the Meta Box
======================================== */
/*function add_page_meta_box() {
    add_meta_box(
        'page_options_box', // $id
        'Page Options', // $title 
        'page_meta_box', // $callback
        'page', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_page_meta_box');*/

/* Metabox loop
======================================== */
/*$prefix = 'page_';
$page_meta_fields = array(

    array(
        'label'     => 'Show Children',
        'desc'      => 'Do you want to show the children below page content?',
        'id'        => $prefix.'child',
        'type'      => 'checkbox'
    )
);*/

/* The Callback
======================================== */
/*function page_meta_box() {
    global $page_meta_fields, $post;

    $name = basename(__FILE__);

    create_meta_box( $page_meta_fields, get_post_type(), $name );
}*/

/* Save the Data
======================================== */
/*function save_page_meta($post_id) {
    global $page_meta_fields;

    // verify nonce
    if ( !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename(__FILE__) ) ){
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($page_meta_fields as $field) {
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
add_action('save_post', 'save_page_meta');*/
?>