<?php
#######################################################################################
# Colour Functions
#######################################################################################
add_filter('query_vars', 'add_new_var_to_wp');
function add_new_var_to_wp($public_query_vars) {
    $public_query_vars[] = 'theme_custom';
    //my_theme_custom_var is the name of the custom query variable that is created and how you reference it in the call to the file
    return $public_query_vars;
}
add_action('template_redirect', 'my_theme_css_display');
function my_theme_css_display(){
    $css = get_query_var('theme_custom');
    if ($css == 'css'){
        include_once (TEMPLATEPATH . '/css/styling.php');
        exit;  //This stops WP from loading any further
    }
}

/* HEX to RGB
=================================================== */
function hex_to_rgb( $hex ) {
    $hex = ereg_replace("#", "", $hex);
    $color = array();

    if(strlen($hex) == 3) {
        $color['r'] = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $color['g'] = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $color['b'] = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    }
    else if(strlen($hex) == 6) {
        $color['r'] = hexdec(substr($hex, 0, 2));
        $color['g'] = hexdec(substr($hex, 2, 2));
        $color['b'] = hexdec(substr($hex, 4, 2));
    }

    return $color;
}

/* Find out transparent colour
=================================================== */
function wp_alpha_colour( $hex, $alpha ) {
    $hex = ereg_replace("#", "", $hex);
    $color = array();

    if(strlen($hex) == 3) {
        $color['r'] = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $color['g'] = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $color['b'] = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    }
    else if(strlen($hex) == 6) {
        $color['r'] = hexdec(substr($hex, 0, 2));
        $color['g'] = hexdec(substr($hex, 2, 2));
        $color['b'] = hexdec(substr($hex, 4, 2));
    }

    if($alpha > 1 ) { $alpha = $alpha / 100; }
    $final = "rgba(".$color['r'].",".$color['g'].",".$color['b']."," . $alpha . ")";
    return $final;
}

/* Find the Opposite Colour
=================================================== */
function wp_opposite_colour( $hex ) {

    $hexcode = ereg_replace("#", "", $hex);

    $redhex  = substr($hexcode,0,2);
    $greenhex = substr($hexcode,2,2);
    $bluehex = substr($hexcode,4,2);

    // $var_r, $var_g and $var_b are the three decimal fractions to be input to our RGB-to-HSL conversion routine

    $var_r = (hexdec($redhex)) / 255;
    $var_g = (hexdec($greenhex)) / 255;
    $var_b = (hexdec($bluehex)) / 255;

    // Output is HSL equivalent as $h, $s and $l — these are again expressed as fractions of 1, like the input values

    $var_min = min($var_r,$var_g,$var_b);
    $var_max = max($var_r,$var_g,$var_b);
    $del_max = $var_max - $var_min;

    $l = ($var_max + $var_min) / 2;

    if ($del_max == 0) {
        $h = 0;
        $s = 0;
    } else {
        if ($l < 0.5) {
            $s = $del_max / ($var_max + $var_min);
        } else {
            $s = $del_max / (2 - $var_max - $var_min);
        };

        $del_r = ((($var_max - $var_r) / 6) + ($del_max / 2)) / $del_max;
        $del_g = ((($var_max - $var_g) / 6) + ($del_max / 2)) / $del_max;
        $del_b = ((($var_max - $var_b) / 6) + ($del_max / 2)) / $del_max;

        if ($var_r == $var_max) {
            $h = $del_b - $del_g;
        } elseif ($var_g == $var_max) {
            $h = (1 / 3) + $del_r - $del_b;
        } elseif ($var_b == $var_max) {
            $h = (2 / 3) + $del_g - $del_r;
        };

        if ($h < 0) {
            $h += 1;
        };

        if ($h > 1) {
            $h -= 1;
        };
    };

    // Calculate the opposite hue, $h2
    $h2 = $h + 0.5;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    if ($s == 0) {
        $r = $l * 255;
        $g = $l * 255;
        $b = $l * 255;
    } else {
        if ($l < 0.5) {
            $var_2 = $l * (1 + $s);
        } else {
            $var_2 = ($l + $s) - ($s * $l);
        };

        $var_1 = 2 * $l - $var_2;
        $r = 255 * hue_2_rgb($var_1,$var_2,$h2 + (1 / 3));
        $g = 255 * hue_2_rgb($var_1,$var_2,$h2);
        $b = 255 * hue_2_rgb($var_1,$var_2,$h2 - (1 / 3));
    };

    $rhex = sprintf("%02X",round($r));
    $ghex = sprintf("%02X",round($g));
    $bhex = sprintf("%02X",round($b));

    $rgbhex = $rhex.$ghex.$bhex;

    return "#".$rgbhex;
};

function hue_2_rgb($v1,$v2,$vh) {
    if ($vh < 0) { $vh += 1; };
    if ($vh > 1) { $vh -= 1; };
    if ((6 * $vh) < 1) { return ($v1 + ($v2 - $v1) * 6 * $vh); };
    if ((2 * $vh) < 1) { return ($v2); };
    if ((3 * $vh) < 2) { return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6)); };

    return ($v1);
};

/* RGB to HSL
=================================================== */
function rgb_to_hsl( $r, $g, $b ) {
    $oldR = $r;
    $oldG = $g;
    $oldB = $b;

    $r /= 255;
    $g /= 255;
    $b /= 255;

    $max = max( $r, $g, $b );
    $min = min( $r, $g, $b );

    $h;
    $s;
    $l = ( $max + $min ) / 2;
    $d = $max - $min;

        if( $d == 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( 2 * $l - 1 ) );

        switch( $max ){
                case $r: 
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 ); 
                    break;

                case $g: 
                    $h = 60 * ( ( $b - $r ) / $d + 2 ); 
                    break;

                case $b: 
                    $h = 60 * ( ( $r - $g ) / $d + 4 ); 
                    break;
            }
    }

    return array( "h" => $h, "s" => $s, "l" => $l );
}
/* HSL to RGB
=================================================== */
function hsl_to_rgb( $h, $s, $l ){
    $r; 
    $g; 
    $b;

    $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
    $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
    $m = $l - ( $c / 2 );

    if ( $h < 60 ) {
        $r = $c;
        $g = $x;
        $b = 0;
    } else if ( $h < 120 ) {
        $r = $x;
        $g = $c;
        $b = 0;            
    } else if ( $h < 180 ) {
        $r = 0;
        $g = $c;
        $b = $x;
    } else if ( $h < 240 ) {
        $r = 0;
        $g = $x;
        $b = $c;
    } else if ( $h < 300 ) {
        $r = $x;
        $g = 0;
        $b = $c;
    } else {
        $r = $c;
        $g = 0;
        $b = $x;
    }

    $r = ( $r + $m ) * 255;
    $g = ( $g + $m ) * 255;
    $b = ( $b + $m  ) * 255;

    return array( "r" => $r, "g" => $g, "b" => $b );
}

/* Find out if it dark or light
=================================================== */
function dark_or_light( $hex, $output = false ){
    $rgb = hex_to_rgb($hex);

    $hsl = rgb_to_hsl($rgb['r'],$rgb['g'],$rgb['b']);
    if ($hsl['l'] >= 0.6){
        $newl += 0.3;
    } else {
        $return = true;
        $newl -= 0.3;
    }
    if ($newl >= 1){ $newl = 1; } elseif ($newl <= 0) { $newl = 0; }

    $new_rgb = hsl_to_rgb ($hsl['h'],$hsl['s'],$newl);
    $colour = 'rgb(' . round($new_rgb['r']) . ',' . round($new_rgb['g']) . ',' . round($new_rgb['b']) . ')';

    if( $output ) { return $colour; } else { return $return; }
}

/* Hover State
=================================================== */
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Format the hex color string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Get decimal values
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));

    // Adjust number of steps and keep it inside 0 to 255
    $r = max(0,min(255,$r + $steps));
    $g = max(0,min(255,$g + $steps));  
    $b = max(0,min(255,$b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#'.$r_hex.$g_hex.$b_hex;
}
?>