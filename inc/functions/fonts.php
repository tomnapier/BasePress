<?php
/* Create the font list
================================================= */
$font_list = array(
	array(
		'label'	=> 'Abel',
		'value'	=> 'Abel',
		'serif'	=> 'sans-serif',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Alegreya',
		'value'	=> 'Alegreya:400italic,700italic,400,700',
		'serif'	=> 'sans-serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Alfa Slab One',
		'value'	=> 'Alfa+Slab+One',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Arvo',
		'value'	=> 'Arvo:400,700,400italic,700italic',
		'serif'	=> 'serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Berkshire Swash',
		'value'	=> 'Berkshire+Swash',
		'serif'	=> 'serif',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Droid Sans',
		'value'	=> 'Droid+Sans:400,700',
		'serif'	=> 'sans-serif',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Josefin Slab',
		'value'	=> 'Josefin+Slab:300,700,300italic,700italic',
		'serif'	=> 'serif',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Knewave',
		'value'	=> 'Knewave',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Lato',
		'value'	=> 'Lato:300,700,300italic,700italic',
		'serif'	=> 'sans-serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Londrina Solid',
		'value'	=> 'Londrina+Solid',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Lobster',
		'value'	=> 'Lobster',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Montserrat',
		'value'	=> 'Montserrat:400,700',
		'serif'	=> 'sans-serif',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Open Sans',
		'value'	=> 'Open+Sans:300italic,700italic,300,700',
		'serif'	=> 'sans-serif',
		'type'	=> array( 'body', 'head' )
	),
	array(
		'label'	=> 'Open Sans Condensed',
		'value'	=> 'Open+Sans+Condensed:300,300italic,700',
		'serif'	=> 'sans-serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Oxygen',
		'value'	=> 'Oxygen:700,300',
		'serif'	=> 'sans-serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Pacifico',
		'value'	=> 'Pacifico',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Patua One',
		'value'	=> 'Patua+One',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'Permanent Marker',
		'value'	=> 'Permanent+Marker',
		'serif'	=> 'cursive',
		'type'	=> 'head'
	),
	array(
		'label'	=> 'PT Serif',
		'value'	=> 'PT+Serif:400,700,400italic,700italic',
		'serif'	=> 'serif',
		'type'	=> 'body'
	),
	array(
		'label'	=> 'Rokkitt',
		'value'	=> 'Rokkitt:700',
		'serif'	=> 'serif',
		'type'	=> 'head'
	)
);

/* Create a list from the list
================================================= */
function get_font_list( $type = 'body' ){
	global $font_list;

	$fonts = array();
	$i = 1;
	$extra = array(
		'label'	=> "Please choose from below",
		'value'	=> ''
	);


	foreach ( $font_list as $font ) {
		if( !is_array( $font['type'] ) && $font['type'] == $type ){
			$fonts[$i] = array(
				'label'	=> $font['serif'] . ": " . $font['label'],
				'value'	=> $font['label'],
			);
			$i++;
		} elseif( is_array( $font['type'] ) && in_array( $type, $font['type'] ) ){
			$fonts[$i] = array(
				'label'	=> $font['serif'] . ": " . $font['label'],
				'value'	=> $font['label'],
			);
			$i++;
		}
	}

	array_unshift( $fonts, $extra );

	return $fonts;
}

/* Get Font Family
================================================= */
function get_font_family( $font ){
	global $font_list;

	$family = "";


	foreach ( $font_list as $single ) {
		if($font == $single['label']){
			$family = '"'.$single['label'].'", '.$single['serif'];
		}
	}

	return $family;
}

/* Get Font CSS
================================================= */
function get_font_css( $font ){
	global $font_list;

	$css = "";


	foreach ( $font_list as $single ) {
		if($font == $single['label']){
			$css = $single['value'];
		}
	}

	return $css;
} ?>