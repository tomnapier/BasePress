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
                    // gallery
                    case 'gallery':
                        $post_id = $post->ID;
                        $loop = gallery_images( $post_id );
                        $text = ( empty( $loop ) )? 'Add Media' : 'Manage Gallery';

                        $return = '<div id="'.$field['id'].'-gallery">';
                        $intro    = '<p>';
                        $intro    .= '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/media-upload.php?post_id=' . $post_id .'&amp;type=image&amp;TB_iframe=1" id="add_media" class="button insert-media add_media" title="' . $text . '"><i class="icon16 icon-media"></i> ' . $text . '</a>';
                        $intro    .= '</p>';
                        $return .= $intro;

                        if( empty( $loop ) ) $return .= '<p>No images.</p>';

                        $gallery = gallery_display( $loop, $post_id );
                        $return .= $gallery;
                        $return .= "</div>";
                        echo $return;
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

/* Gallery Functions
========================================*/
function gallery_display( $loop, $id ) {
    $gallery = '<ul class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-' . $id . '">';
    foreach( $loop as $image ):
        $thumbnail    = wp_get_attachment_image_src( $image->ID, 'thumbnail' );

        $gallery .= '<li class="attachment save-ready">';
        $gallery .= '<div class="attachment-preview type-image">';
        $gallery .= '<div class="thumbnail"><div class="centered">';
        $gallery .= '<img src="' . $thumbnail[0] . '" alt="' . $image->post_title . '" rel="' . $image->ID . '" title="' . $image->post_content . '" draggable="false">';
        $gallery .= '</div></div>';
        $gallery .= '</div>';
        $gallery .= '</li>';

    endforeach;

    $gallery .= '</ul>';
    $gallery .= '<style>#__attachments-view-' . $id . ' { margin-top: 20px; } #__attachments-view-' . $id . ' .attachment-preview, #__attachments-view-' . $id . ' .attachment-preview .thumbnail { cursor: default; width: 120px; height: 120px; }</style>';

    return $gallery;
}

function gallery_images( $post_id ) {
    $args = array(
        'post_type'         => 'attachment',
        'post_status'       => 'inherit',
        'post_parent'       => $post_id,
        'post_mime_type'    => 'image',
        'posts_per_page'    => -1,
        'order'             => 'ASC',
        'orderby'           => 'menu_order',
    );
    $images = get_posts( $args );
    return $images;
}

/* Screen Help
========================================*/
add_action( 'contextual_help', 'wptuts_screen_help', 10, 3 );
function wptuts_screen_help( $contextual_help, $screen_id, $screen ) {

    // The add_help_tab function for screen was introduced in WordPress 3.3.
    if ( ! method_exists( $screen, 'add_help_tab' ) )
        return $contextual_help;

    global $hook_suffix;

    // List screen properties
    $variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
        . sprintf( '<li> Screen id : %s</li>', $screen_id )
        . sprintf( '<li> Screen base : %s</li>', $screen->base )
        . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
        . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
        . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
        . '</ul>';

    // Append global $hook_suffix to the hook stems
    $hooks = array(
        "load-$hook_suffix",
        "admin_print_styles-$hook_suffix",
        "admin_print_scripts-$hook_suffix",
        "admin_head-$hook_suffix",
        "admin_footer-$hook_suffix"
    );

    // If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
    if ( did_action( 'add_meta_boxes_' . $screen_id ) )
        $hooks[] = 'add_meta_boxes_' . $screen_id;

    if ( did_action( 'add_meta_boxes' ) )
        $hooks[] = 'add_meta_boxes';

    // Get List HTML for the hooks
    $hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';

    // Combine $variables list with $hooks list.
    $help_content = $variables . $hooks;

    // Add help panel
    $screen->add_help_tab( array(
        'id'     => 'wptuts-screen-help',
        'title' => 'Screen Information',
        'content' => $help_content,
    ));

    return $contextual_help;
}
?>