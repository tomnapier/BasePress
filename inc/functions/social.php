<?php
/* Twitter Stuff
================================================== */
function gw_twitter( $atts, $content = null ){
	extract(shortcode_atts(array(
		'scheme'		=> 'light',
		'color'			=> get_option( 'wv_colour_pri' ),
		'header'		=> 'true',
		'footer'		=> 'true',
		'border'		=> 'true',
		'transparent'	=> 'false',
		'limit'			=> 0
		
	), $atts));

	$output = '<a class="twitter-timeline" href="' . get_option( 'wv_tw' ) . '" data-widget-id="' . get_option( 'wv_tw_code' ) . '" data-theme="' . $scheme . '" data-link-color="' . $color . '" ';
	$output .= 'data-chrome="';
	$output .= ($header != 'true')? 'noheader ' : null;
	$output .= ($footer != 'true')? 'nofooter ' : null;
	$output .= ($border != 'true')? 'noborder ' : null;
	$output .= ($transparent == 'true')? 'transparent ' : null;
	$output .= '"';
	$output .= ($limit > 0)? ' data-tweet-limit="' . $limit . '"' : null;
	$output .= '></a>' . "\n";
	$output .= '<script>!function(d,s,id){' . "\n";
	$output .= 'var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';' . "\n";
	$output .= 'if(!d.getElementById(id)){' . "\n";
	$output .= 'js=d.createElement(s);' . "\n";
	$output .= 'js.id=id;' . "\n";
	$output .= 'js.src=p+"://platform.twitter.com/widgets.js";' . "\n";
	$output .= 'fjs.parentNode.insertBefore(js,fjs);' . "\n";
	$output .= '}}(document,"script","twitter-wjs");' . "\n";
	$output .= '</script>' . "\n";

	return $output;
}
add_shortcode( 'twitter', 'gw_twitter' );

/* Facebook Stuff
================================================== */
function facebook(){
	$output = '<div id="fb-root"></div>';
	$output .= '<script>(function(d, s, id) {' . "\n";
	$output .= 'var js, fjs = d.getElementsByTagName(s)[0];' . "\n";
	$output .= 'if (d.getElementById(id)) return;' . "\n";
	$output .= 'js = d.createElement(s); js.id = id;' . "\n";
	$output .= 'js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId='. get_option( 'wv_fb_code' ) .'";' . "\n";
	$output .= 'fjs.parentNode.insertBefore(js, fjs);' . "\n";
	$output .= '}(document, "script", "facebook-jssdk"));</script>';

	echo $output;
};

function gw_facebook( $atts, $content = null ){
	extract(shortcode_atts(array(
		'color'		=> 'dark',
		'width'		=> '213',
		'faces'		=> 'true',
		'header'	=> 'false',
		'stream'	=> 'true',
		'border'	=> 'false'
		
	), $atts));

	$output = '<div class="fb-like-box" data-href="' . get_option( 'wv_fb' ) . '" data-colorscheme="'.$color.'" data-width="'.$width.'" data-show-faces="'.$faces.'" data-header="'.$header.'" data-stream="'.$stream.'" data-show-border="'.$border.'"></div>';
	return $output;
}
add_shortcode( 'facebook', 'gw_facebook' );

/* Instagram Stuff
----------------------------------------------------------------*/
function gw_instagram( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'number'	=> 3
	), $atts));

	$url = "https://api.instagram.com/v1/users/" . get_option("wv_photos_id") . "/media/recent/?access_token=".get_option("wv_photos_author")."&count=".$number;
	
	$result = fetchData($url);
	
	$result = json_decode($result);
	$total = count($result->data);

	$i = 1;

	

	$output .= '<ul class="small-block-grid-2 large-block-grid-2">';
	foreach ($result->data as $post) {
		$output .= '<li id="instagram-'.$i.'">';
		$output .= '<a class="th radius instagram-unit" target="blank" href="'.$post->link.'">';
		$output .= '<img src="'.$post->images->low_resolution->url.'" alt="'.$post->caption->text.'" width="100%" height="auto" />';
		$output .= '</a>';
		$output .= '</li>';
		$i++;
	}
	$output .= '</ul>';

	return $output;
}
add_shortcode('instagram', 'gw_instagram');

function fetchData($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$result = curl_exec($ch);
	curl_close($ch); 
	return $result;
} ?>