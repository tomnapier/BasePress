<?php
/* Add Customiser
=================================================== */
function tcx_register_theme_customizer( $wp_customize ) {
    $colors = array();
    $shortname = 'iw';

    $colors[] = array(
        'label' => "Primary Colour",
        "slug" => $shortname."_primary",
        "default" => "#008CBA");

    $colors[] = array(
        'label' => "Secondary Colour",
        "slug" => $shortname."_secondary",
        "default" => "#e9e9e9");

    $colors[] = array(
        'label' => "Alert Colour",
        "slug" => $shortname."_alert",
        "default" => "#f04124");

    $colors[] = array(
        'label' => "Success Colour",
        "slug" => $shortname."_success",
        "default" => "#43AC6A");

    $colors[] = array(
        'label' => "Warning Colour",
        "slug" => $shortname."_warning",
        "default" => "#f08a24");

    $colors[] = array(
        'label' => "Body Text Colour",
        "slug" => $shortname."_body_colour",
        "default" => "#222222");

    $colors[] = array(
        'label' => "Header Text Colour",
        "slug" => $shortname."_head_colour",
        "default" => "#222222");

    foreach( $colors as $color ) {
        // SETTINGS
        $wp_customize->add_setting(
            $color['slug'], array(
                'default' => $color['default'],
                'type' => 'option', 
                'capability' => 
                'edit_theme_options'
            )
        );

        // CONTROLS
        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                $color['slug'], 
                array('label' => $color['label'], 
                'section' => 'colors',
                'settings' => $color['slug'])
            )
        );
    }
}

add_action( 'customize_register', 'tcx_register_theme_customizer' );

/* Add Custom Background
=================================================== */
// $args = array(
//     'default-color' => 'fff',
//     'default-image' => get_bloginfo( 'template_url' ) . '/img/bg_wallpaper.jpg',
// );
// add_theme_support( 'custom-background', $args );

$headers = array(
    'flex-height'       => true,
    'flex-width'        => true,
    'default-image'     => get_bloginfo( 'template_url' ) . '/img/logo-blue.png',
    'default-color'     => '222',
);
add_theme_support( 'custom-header', $headers ); ?>