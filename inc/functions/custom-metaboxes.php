<?php

/**
 * Render 'Opening Times' custom field type
 *
 * @since 0.1.0
 *
 * @param array  $field              The passed in `CMB2_Field` object
 * @param mixed  $value              The value of this field escaped.
 *                                   It defaults to `sanitize_text_field`.
 *                                   If you need the unescaped value, you can access it
 *                                   via `$field->value()`
 * @param int    $object_id          The ID of the current object
 * @param string $object_type        The type of object you are working with.
 *                                   Most commonly, `post` (this applies to all post-types),
 *                                   but could also be `comment`, `user` or `options-page`.
 * @param object $field_type_object  The `CMB2_Types` object
 */

function cmb2_render_callback_for_opening_times( $field, $value, $object_id, $object_type, $field_type_object ) {

    // make sure we specify each part of the value we need.
    $value = wp_parse_args( $value, array(
        'open'       => '',
        'close'      => '',
    ) );
?>
    <div>

        <p class="cmb2-metabox-description">Opening</p>

        <?php echo $field_type_object->input( array(
            'name'  => $field_type_object->_name( '[open]' ),
            'id'    => $field_type_object->_id( '_open' ),
            'value' => $value['open'],
            'type'  => 'time'
        ) ); ?>


        <p class="cmb2-metabox-description">Closing</p>
        <?php echo $field_type_object->input( array(
            'name'  => $field_type_object->_name( '[close]' ),
            'id'    => $field_type_object->_id( '_close' ),
            'value' => $value['close'],
            'type'  => 'time'
        ) ); ?>

    </div>

<?php }

add_action( 'cmb2_render_opening_times', 'cmb2_render_callback_for_opening_times', 10, 5 );

/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB2 options array
 */
function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => 10,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }

    return $post_options;
}

?>