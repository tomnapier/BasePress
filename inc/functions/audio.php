<?php /* Audio Functions */

function soundurl($link, $title){
	$xml = "http://soundcloud.com/oembed?format=xml&url=" . $link;
	$response = @file_get_contents($xml);
	if (preg_match('#^HTTP/... 4..#', $http_response_header[0])) {
	}
	$load = simplexml_load_string($response);
	if (!$load){
		$att = 'href="'. $link .'" title="'. $title .'" target="_blank"';
	}else{
		$audio = $load->html;
		$tags = explode('"', $audio);
		foreach($tags as $tag){
			if (strpos($tag,'http') !== false) {
				$src = $tag;
			}
		}
		$att = 'href="'. $src .'" class="soundcloud" title="'. $title .'" data-fancybox-type="iframe" target="_blank"';
		$img = $load->{'thumbnail-url'};
	}

	$return = array( $att, $img );
	return $return;
	
}; ?>