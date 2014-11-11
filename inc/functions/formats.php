<?php /* Add Post Formats
=================================================== */
add_filter('single_template', 'single_template_terms');
function single_template_terms($template) {
	foreach( (array) wp_get_object_terms(get_the_ID(), get_taxonomies(array('public' => true, '_builtin' => true))) as $term ) {
		$url = str_replace('post-','',$term->slug);
		if ( file_exists(TEMPLATEPATH . "/single-{$url}.php") )
			return TEMPLATEPATH . "/single-{$url}.php";
	}
	return $template;
} 

add_theme_support('post-formats', array(
	'audio',
	'gallery',
	'image',
	'video',
));
add_post_type_support( 'gallery', 'post-formats' );