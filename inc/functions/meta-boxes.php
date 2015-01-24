<?php
#######################################################################################
# Create Meta Boxes
#######################################################################################

/* Custom Post Labels
======================================================== */
function custom_post_type_labels( $singular, $plural = '' ) {
    if( $plural == '') $plural = $singular .'s';
    
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

/* Call scripts first
======================================================== */
function custom_scripts(){
    global $screen_id;

    if( is_admin() ) {
        wp_enqueue_media();

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('media-upload');
    
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );

        wp_register_style( 'jquery-ui', get_bloginfo( 'template_url' ) . '/inc/styling/css/jquery-ui-1.10.4.custom.min.css' );
        wp_enqueue_style( 'jquery-ui' );

        if ( 'post' == get_current_screen() -> base ) {
            wp_enqueue_script('custom-js', get_template_directory_uri() . '/inc/styling/js/custom-js.js' );
            wp_enqueue_script('time-js', get_template_directory_uri() . '/inc/styling/js/timepicker.js');
        }
    }
}
add_action('admin_enqueue_scripts', 'custom_scripts');

/* Create Meta Box
======================================================== */
function create_meta_box( $meta_fields, $post_type, $name ) {
    global $post;

    /* Nonce for verification
    ======================================================== */
    echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( $name ).'" />';
    
    
    /* Begin the field table and loop
    ======================================================== */
    echo '<table class="form-table">';
    foreach ($meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {
                    // text
                    case 'text':
                        echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // textarea
                    case 'textarea':
                        echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox
                    case 'checkbox':
                        echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
                                <label for="'.$field['id'].'">'.$field['desc'].'</label>';
                    break;
                    // select
                    case 'select':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                        foreach ($field['options'] as $option) {
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // selectgroup
                    case 'selectgroup':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                        $selectgroup = '';
                        foreach ($field['options'] as $option) {
                            if ( $option['group'] != $selectgroup ) { echo (($selectgroup != '' )? '</optgroup>' : null ) . '<optgroup label="' . $option['group'] . '">'; $selectgroup = $option['group']; }
                            $option['value'] = strtolower( $option['label'] );
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // radio
                    case 'radio':
                        foreach ( $field['options'] as $option ) {
                            echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox_group
                    case 'checkbox_group':
                        foreach ($field['options'] as $option) {
                            echo '<input type="checkbox" value="'.$option['value'].'" name="'.$field['id'].'[]" id="'.$option['value'].'"',$meta && in_array($option['value'], $meta) ? ' checked="checked"' : '',' /> 
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
                    break;
                    // tax_select
                    case 'tax_select':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">
                                <option value="">Select One</option>'; // Select One
                        $terms = get_terms($field['id'], 'get=all');
                        $selected = wp_get_object_terms($post->ID, $field['id']);
                        foreach ($terms as $term) {
                            if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug)) 
                                echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>'; 
                            else
                                echo '<option value="'.$term->slug.'">'.$term->name.'</option>'; 
                        }
                        $taxonomy = get_taxonomy($field['id']);
                        echo '</select><br /><span class="description"><a href="'.get_bloginfo('home').'/wp-admin/edit-tags.php?taxonomy='.$field['id'].'">Manage '.$taxonomy->label.'</a></span>';
                    break;
                    // post_list
                    case 'post_list':
                    $items = get_posts( array (
                        'post_type'    => $field['post_type'],
                        'posts_per_page' => -1
                    ));
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">
                                <option value="">Select One</option>'; // Select One
                            foreach($items as $item) {
                                echo '<option value="'.$item->ID.'"',$meta == $item->ID ? ' selected="selected"' : '','>'.$item->post_type.': '.$item->post_title.'</option>';
                            } // end foreach
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    case 'term_list':
                    $items = get_terms( $field['term'], 'orderby=count&hide_empty=0' );
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">
                                <option value="">Select One</option>'; // Select One
                            foreach($items as $item) {
                                echo '<option value="'.$item->term_id.'"',$meta == $item->term_id ? ' selected="selected"' : '','>'.$item->taxonomy.': '.$item->name.'</option>';
                            } // end foreach
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // date
                    case 'date':
                        echo '<input type="text" class="datepicker" name="'.$field['id'].'" id="'.$field['id'].'" value="'. (($meta)?date('Y/m/d',$meta): null) .'" size="30" />
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // slider
                    case 'slider':
                    $value = ( $meta != '' )? $meta : '0';
                        echo '<div id="'.$field['id'].'-slider" class="jquery-slider" data-min="'.$field['min'].'" data-max="'.$field['max'].'" data-step="'.$field['step'].'" style="border-color: #eee; max-width: 300px;"></div>
                                <input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="' . $meta . '" size="5" />
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // image
                    case 'image':
                        $image = get_template_directory_uri().'/inc/styling/img/image.png';    
                        echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
                        if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium');    $image = $image[0]; }
                        echo    '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />
                                    <img src="'.$image.'" class="custom_preview_image" alt="" /><br />
                                        <input class="custom_upload_image_button" type="button" value="Choose Image" />
                                        <small>&nbsp;<a href="#" class="custom_clear_image_button">Remove Image</a></small>
                                        <br clear="all" /><span class="description">'.$field['desc'].'</span>';
                    break;

                    // repeatable
                    case 'repeatable':
                        echo '<a class="repeatable-add button" href="#">+</a>
                                <ul id="'.$field['id'].'-repeatable" class="page_repeatable">';
                        $i = 0;
                        if ($meta) {
                            foreach($meta as $row) {
                                echo '<li><span class="sort hndle">|||</span>
                                            <input type="text" name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" value="'.$row.'" size="30" />
                                            <a class="repeatable-remove button" href="#">-</a></li>';
                                $i++;
                            }
                        } else {
                            echo '<li><span class="sort hndle">|||</span>
                                        <input type="text" name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" value="" size="30" />
                                        <a class="repeatable-remove button" href="#">-</a></li>';
                        }
                        echo '</ul>
                            <span class="description">'.$field['desc'].'</span>';
                    break;
                } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}

?>