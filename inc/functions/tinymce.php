<?php
/*  Custom Styles */

/* Apply styles to the visual editor
========================================================= */
add_filter('mce_css', 'tuts_mcekit_editor_style');
function tuts_mcekit_editor_style($url) {

    if ( !empty($url) )
        $url .= ',';

    // Retrieves the plugin directory URL
    // Change the path here if using different directories
    $url .= trailingslashit( get_bloginfo('template_url') ) . 'style.css';

    return $url;
}

/* Add "Styles" drop-down
========================================================= */
add_filter( 'mce_buttons_2', 'tuts_mce_editor_buttons' );

function tuts_mce_editor_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    array_unshift( $buttons, 'columnselect' );
    return $buttons;
}

/* Add styles/classes to the "Styles" drop-down
========================================================= */
add_filter( 'tiny_mce_before_init', 'tuts_mce_before_init' );

function tuts_mce_before_init( $settings ) {
    $style_formats = array(
        array(
            'title' => 'Button',
            'selector' => 'a',
            'classes' => 'button'
        ),
        array(
            'title' => 'Button Group',
            'selector' => 'ul',
            'classes' => 'button-group',
        ),
        array(
            'title' => 'Lead Paragraph',
            'selector' => 'p',
            'classes' => 'lead',
        ),
        array(
            'title' => 'Red Header',
            'selector' => 'h4',
            'classes' => 'red'
        ),
        array(
            'title' => 'Serif Section',
            'inline' => 'span',
            'classes' => 'serif',
        ),
        array(
            'title' => 'Label',
            'inline' => 'span',
            'classes' => 'label',
        ),
        array(
            'title' => 'Inline List',
            'selector' => 'ul',
            'classes' => 'inline-list',
        ),
        array(
            'title' => 'List Circle',
            'selector' => 'ul',
            'classes' => 'circle',
        ),
        array(
            'title' => 'List Disc',
            'selector' => 'ul',
            'classes' => 'disc',
        ),
        array(
            'title' => 'List Square',
            'selector' => 'ul',
            'classes' => 'square',
        ),
        array(
            'title' => 'Alert Box',
            'block' => 'div',
            'classes' => 'alert-box',
            'wrapper' => true
        ),
        array(
            'title' => 'Panel',
            'block' => 'div',
            'wrapper' => true,
            'classes' => 'panel',
        ),
        array(
            'title' => 'Responsive Video',
            'block' => 'div',
            'wrapper' => true,
            'classes' => 'flex-video widescreen',
        )
    );

    $settings['style_formats'] = json_encode( $style_formats );
    $settings['column_formats'] = json_encode( $style_formats );
    return $settings;
}
?>
