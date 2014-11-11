<?php
/*
* Example code showing how to hook WordPress to add fields to the taxonomny term edit screen.
* This example is meant to show how, not to be a drop in example.
* This example was written in response to this question:
*http://lists.automattic.com/pipermail/wp-hackers/2010-August/033671.html
* By:
*Mike Schinkel (http://mikeschinkel.com/custom-wordpress-plugins/)
* NOTE:
*This could easily become a plugin if it were fleshed out.
*A class with static methods was used to minimize the variables & functions added to the global namespace.
*wp_options was uses with one option be tax/term instead of via a serialize array because it aids in retrival
*if there get to be a large number of tax/terms types. A taxonomy/term meta would be the prefered but WordPress
*does not have one.
* This example is licensed GPLv2.
*/
 
class TaxonomyTermTypes {

    var $taxonomies = array();
    var $fields = array();

    function __construct( $taxonomies, $fields ) {
        $this->TaxonomyTermTypes( $taxonomies, $fields );
    }

    //This initializes the hooks to allow saving of the
    function TaxonomyTermTypes( $taxonomies, $fields ) {
        $this->taxonomies = $taxonomies;
        $this->fields = $fields;

        add_action( 'created_term', array( &$this, 'term_type_update' ), 10, 3 );
        add_action( 'edit_term', array( &$this, 'term_type_update' ), 10, 3 );

        $this->register_taxonomy( $taxonomies );
    }

    //This initializes the hooks to allow adding the dropdown to the form fields
    function register_taxonomy( $taxonomy ) {
        if ( !is_array( $taxonomy ) )
            $taxonomy = array( $taxonomy );

        foreach( $taxonomy as $tax_name ) {
            add_action( "{$tax_name}_add_form_fields", array( &$this, "add_form_fields" ) );
            add_action( "{$tax_name}_edit_form_fields", array( &$this, "edit_form_fields" ), 10, 2 );
        }
    }

    // This displays the selections. Edit it to retrieve
    function add_form_fields( $taxonomy ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script("jquery");
        wp_enqueue_script("rm_script", get_bloginfo('template_directory') . "/inc/styling/js/rm_script.js", false, null);

        foreach( $this->fields as $field ) {
            $field_id = $field['id'];
            $value = $term->$field_id;
            switch ( $field['type'] ) {
                case 'text': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><input name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php if ( $value != "" ) { echo stripslashes($value); } else { echo $field['std']; } ?>" />
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'textarea': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><textarea name="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" cols="" rows=""><?php if ( $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?></textarea>
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'select': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><select name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>">
                            <?php foreach ($field['options'] as $option) { ?>
                            <option <?php if ( $value == $option['value']) { echo 'selected="selected"'; } ?> value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option><?php } ?>
                        </select>
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case "checkbox": ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <?php if(get_option($field['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                        <td><input type="checkbox" name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>" value="true" <?php echo $checked; ?> />
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'colour': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><input name="tag-<?php echo $field['id']; ?>" class="color-picker" id="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php if (  $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?>" />
                        <small style="background-color: <?php if (  $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?>; height: 25px; width: 25px;"></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
            };
        }
    }

    // This a table row with the drop down for an edit screen
    function edit_form_fields($term, $taxonomy) {
        global $wpdb;
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script("jquery");
        wp_enqueue_script("rm_script", get_bloginfo('template_directory') . "/inc/styling/js/rm_script.js", false, null);

        foreach( $this->fields as $field ) {
            $field_id = $field['id'];
            $value = $term->$field_id;
            switch ( $field['type'] ) {
                case 'text': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><input name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php if ( $value != "" ) { echo stripslashes($value); } else { echo $field['std']; } ?>" />
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'textarea': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><textarea name="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" cols="" rows=""><?php if ( $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?></textarea>
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'select': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><select name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>">
                            <?php foreach ($field['options'] as $option) { ?>
                            <option <?php if ( $value == $option['value']) { echo 'selected="selected"'; } ?> value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option><?php } ?>
                        </select>
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case "checkbox": ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <?php if(get_option($field['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                        <td><input type="checkbox" name="tag-<?php echo $field['id']; ?>" id="tag-<?php echo $field['id']; ?>" value="true" <?php echo $checked; ?> />
                        <small><?php echo $field['desc']; ?></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
                case 'colour': ?>
                    <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="tag-<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label></th>
                        <td><input name="tag-<?php echo $field['id']; ?>" class="color-picker" id="tag-<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php if (  $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?>" />
                        <small style="background-color: <?php if (  $value != "") { echo stripslashes($value); } else { echo $field['std']; } ?>; height: 25px; width: 25px;"></small>
                        <div class="clearfix"></div></td>
                    </tr>
                <?php break;
            };
        }
    }

    // These hooks are called after adding and editing to save $_POST['tag-term']
    function term_type_update($term_id, $tt_id, $taxonomy) {
        global $wpdb;

        foreach( $this->fields as $field ) {
            $field_id = $field['id'];
            if ( isset( $_POST['tag-' . $field_id] ) ) {
                $wpdb->update( $wpdb->terms, array( $field_id => $_POST['tag-' . $field_id] ), array('term_id' => $term_id) );
            }
        }
    }
}

//This initializes the class.
//This should be called in your own code.
$tax_fields = array(
    array(
        'id' => 'tag_colour',
        'type' => 'colour',
        'label' => 'Colour',
        'std' => '#ffcc00'
    ),
    array(
        'id' => 'menu_order',
        'type' => 'text',
        'label' => 'Order',
        'std' => '0'
    )
);

new TaxonomyTermTypes( array( 'category' ), $tax_fields );

add_filter( "manage_edit-category_columns", "custom_column_header_function" );
add_action( "manage_category_custom_column",  "custom_populate_rows_function", 10, 3 );

function custom_column_header_function( $columns ){
    $columns = array (
        'cb'            => '<input type="checkbox" />',
        'cat_order'     => 'Order',
        'cat_name'      => 'Name',
        'description'   => 'Description',
        'slug'          => 'Slug',
        'posts'         => 'Posts'
    );

        return $columns;
}

function custom_populate_rows_function( $value, $column, $term_id ){
    global $post;

    $cat = get_term( $term_id, 'category' );
    $colour = $cat->tag_colour;
    $order = $cat->menu_order;

    switch ($column) {
        case "cat_name":
            $title .= '<strong><a style="color: '. $colour .';" class="row-title" href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?action=edit&amp;taxonomy=category&amp;tag_ID=' . $term_id . '&amp;post_type=post" title="Edit “' . $cat->name . '”">' . $cat->name . '</a></strong>';
            $title .= '<br>';
            $title .= '<div class="row-actions">';
            $title .= '<span class="edit"><a href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?action=edit&amp;taxonomy=category&amp;tag_ID=' . $term_id . '&amp;post_type=post">Edit</a> | </span>';
            $title .= '<span class="inline hide-if-no-js"><a href="#" class="editinline">Quick&nbsp;Edit</a> | </span>';
            $title .= '<span class="view"><a href="' . get_bloginfo( 'url' ) . '/?cat=' . $term_id . '">View</a></span>';
            $title .= '</div>';
            $title .= '<div class="hidden" id="inline_' . $term_id . '"><div class="name">' . $cat->name . '</div><div class="slug">' . $cat->slug . '</div><div class="parent">' . $cat->parent . '</div></div>';
            echo $title;
        break;
        case "cat_order":
            $output = '<strong><a style="color: '. $colour .';" href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?action=edit&amp;taxonomy=category&amp;tag_ID=' . $term_id . '&amp;post_type=post" title="Edit “' . $cat->name . '”">' . $order . ".</a></strong>";
            
            echo $output;
        break;
    }
}

function custom_column_register_sortable( $columns ) {
    $columns['cat_order'] = 'menu_order';
    return $columns;
}
add_filter( 'manage_edit-category_sortable_columns', 'custom_column_register_sortable' );

add_filter('get_terms_args', 'find_menu_orderby');
function edit_menu_orderby () {
    //This is a one-off, so that we don't disrupt queries that may not use menu_order.
    remove_filter('get_terms_orderby', 'edit_menu_orderby');
    return "menu_order";	
}

function find_menu_orderby ($args) {
    if ('menu_order' === $args['orderby']) {
        add_filter('get_terms_orderby', 'edit_menu_orderby');
    }
    return $args;
}

/* On activation, add custom database column
================================================== */
add_action( 'after_setup_theme', 'custom_cat_db' );
function custom_cat_db() {
    global $wpdb;

    $sql = "ALTER TABLE `{$wpdb->terms}` ADD  `menu_order` INT( 11 ) NOT NULL, AD  `tag_colour` VARCHAR( 7 ) NOT NULL;";
    $wpdb->query($sql);
}
?>