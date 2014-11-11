<?php /* Video Functions */

/* Get YouTube video ID from URL
=================================================== */
function youtube_id($url) {
	$pattern = 
		'%^# Match any youtube URL
		(?:https?://)?		# Optional scheme. Either http or https
		(?:www\.)?			# Optional www subdomain
		(?:					# Group host alternatives
			youtu\.be/		# Either youtu.be,
		| youtube\.com		# or youtube.com
			(?:				# Group path alternatives
			/embed/			# Either /embed/
			| /v/			# or /v/
			| /watch\?v=	# or /watch\?v=
			)				# End path alternatives.
		)					# End host alternatives.
		([\w-]{10,12})		# Allow 10-12 for 11 char youtube id.
		$%x'
		;
	$result = preg_match($pattern, $url, $matches);
	if (false !== $result) {
		return $matches[1];
	}
	return false;
}

/* Get Vimeo video ID from URL
=================================================== */
function vimeo_id($url) {
	$result = preg_match('/(\d+)/', $url, $matches);
	if (false !== $result) {
		return $matches[1];
	}
	return false;
}

/* Get Video Info
=================================================== */
function video_info($url) {
	// Handle Youtube
	if (strpos($url, "youtube.com")) {
		$url = parse_url($url);
		$vid = parse_str($url['query'], $output);
		$video_id = $output['v'];
		$data['video_type'] = 'youtube';
		$data['video_id'] = $video_id;
		$xml = simplexml_load_file("http://gdata.youtube.com/feeds/api/videos?q=$video_id");

		foreach ($xml->entry as $entry) {
			// get nodes in media: namespace
			$media = $entry->children('http://search.yahoo.com/mrss/');
			
			// get video player URL
			$attrs = $media->group->player->attributes();
			$watch = $attrs['url']; 
			
			// get video thumbnail
			$data['thumb_1'] = $media->group->thumbnail[0]->attributes(); // Thumbnail 1
			$data['thumb_2'] = $media->group->thumbnail[1]->attributes(); // Thumbnail 2
			$data['thumb_3'] = $media->group->thumbnail[2]->attributes(); // Thumbnail 3
			$data['thumb_large'] = $media->group->thumbnail[0]->attributes(); // Large thumbnail
			$data['tags'] = $media->group->keywords; // Video Tags
			$data['cat'] = $media->group->category; // Video category
			$attrs = $media->group->thumbnail[0]->attributes();
			$thumbnail = $attrs['url']; 
			
			// get <yt:duration> node for video length
			$yt = $media->children('http://gdata.youtube.com/schemas/2007');
			$attrs = $yt->duration->attributes();
			$data['duration'] = $attrs['seconds'];
			
			// get <yt:stats> node for viewer statistics
			$yt = $entry->children('http://gdata.youtube.com/schemas/2007');
			$attrs = $yt->statistics->attributes();
			/*$viewCount = $attrs['viewCount'];
			 $data['views'] = $viewCount = $attrs['viewCount']; 
			$data['title']=$entry->title;
			$data['info']=$entry->content;
			
			// get <gd:rating> node for video ratings
			$gd = $entry->children('http://schemas.google.com/g/2005'); 
			if ($gd->rating) {
				$attrs = $gd->rating->attributes();
				$data['rating'] = $attrs['average']; 
			} else { $data['rating'] = 0;} */
		} // End foreach
	} // End Youtube

	// Handle Vimeo
	else if (strpos($url, "vimeo.com")) {
		$video_id=explode('vimeo.com/', $url);
		$video_id=$video_id[1];
		$data['video_type'] = 'vimeo';
		$data['video_id'] = $video_id;
		$xml = simplexml_load_file("http://vimeo.com/api/v2/video/$video_id.xml");
			
		foreach ($xml->video as $video) {
			$data['id']=$video->id;
			$data['title']=$video->title;
			$data['info']=$video->description;
			$data['url']=$video->url;
			$data['upload_date']=$video->upload_date;
			$data['mobile_url']=$video->mobile_url;
			$data['thumb_small']=$video->thumbnail_small;
			$data['thumb_medium']=$video->thumbnail_medium;
			$data['thumb_large']=$video->thumbnail_large;
			$data['user_name']=$video->user_name;
			$data['urer_url']=$video->urer_url;
			$data['user_thumb_small']=$video->user_portrait_small;
			$data['user_thumb_medium']=$video->user_portrait_medium;
			$data['user_thumb_large']=$video->user_portrait_large;
			$data['user_thumb_huge']=$video->user_portrait_huge;
			$data['likes']=$video->stats_number_of_likes;
			$data['views']=$video->stats_number_of_plays;
			$data['comments']=$video->stats_number_of_comments;
			$data['duration']=$video->duration;
			$data['width']=$video->width;
			$data['height']=$video->height;
			$data['tags']=$video->tags;
		} // End foreach
	} // End Vimeo
	
	// Set false if invalid URL
	else { $data = false; }

	return $data;
}

/* YouTube Comments
=================================================== */
function ytcomments($id) {
	$videoId = $id;
	// get the feed
	$url="http://gdata.youtube.com/feeds/api/videos/{$videoId}/comments";
	$comments = simplexml_load_file($url);

	$output = '<ul id="comments">' . "\n";
	foreach($comments->entry as $comment) {

		/* Get IMG */
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($comment->author->uri);
		$x = $xmlDoc->documentElement;
		$searchNode = $x->getElementsByTagName( "thumbnail" );
		foreach ($searchNode AS $item){
			$img = $item->getAttribute('url');
		}

		/* User details */
		$name = $comment->author->name;
		$user = $comment->author->uri;
		$user = str_replace('gdata', 'www', $user);
		$user = str_replace('feeds/api/users/', '', $user);

		/* Content */
		$adate = $comment->published;
		$today = time();
		$today = date(DATE_ATOM,$today);
		$vdate = dateDifference($adate, $today);

		$date = date('d M Y', strtotime($adate));
		$text = $comment->content;
		$text = nicetext($text);

		/* Render Output */
		$output .= '<li class="comment">' . "\n";
		$output .= '<a href="' . $user . '" target="_blank" class="profile"><img src="' . $img . '" alt="' . $name . '" width="48" /></a>' . "\n";
		$output .= '<div class="comment-author">';
		$output .= '<a href="' . $user . '" target="_blank">' . $name . '</a>';
		$output .= '<span class="date">' . $vdate . '</span>';
		$output .= '</div>' . "\n";
		$output .= '<div class="comment-content">' . $text . '</div>' . "\n";
		$output .= '<div class="clear"></div>' . "\n";
		$output .= '</li>';
	}
	$output .= '</ul>';
	return $output;
}

/* Date Diff
=================================================== */
function dateDifference($startDate, $endDate) { 
	$startDate = strtotime($startDate); 
	$endDate = strtotime($endDate); 
	if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate) 
		return "Error"; 
		
	$years = date('Y', $endDate) - date('Y', $startDate);
	$months = date('m', $endDate) - date('m', $startDate);
	if ($months < 0)  { 
		$months += 12; 
		$years--;
	}

	$offsets = array();
	if ($years > 0)
		$offsets[] = $years . (($years == 1) ? ' year' : ' years');
	if ($months > 0)
		$offsets[] = $months . (($months == 1) ? ' month' : ' months');
	$offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

	$days = $endDate - strtotime($offsets, $startDate);
	$days = date('z', $days);

	$weeks = 0;
	if($days > 7) {
		$week = $days / 7;
		$weeks = substr($week, 0, 1);
		$days = $days - (7 * $weeks);
	}

	if ($days > 0) {
		$offsets = '+' . $days . (($days == 1) ? ' day' : ' days');
	} else {
		$offsets = "now";
	}

	$hours = $endDate - strtotime($offsets, $startDate);
	$hours = date('G', $hours);

	if ($hours > 0) {
		$offsets = '+' . $hours . (($hours == 1) ? ' hour' : ' hours');
	} else {
		$offsets = "now";
	}

	$minutes = $endDate - strtotime($offsets, $startDate);
	$minutes =  intval(date('i', $minutes));

	$text = $years . (($years == 1) ? ' year ago' : ' years ago');
	if($years <= 0) {
		$text = $months . (($months == 1) ? ' month ago' : ' months ago');
		if($months <= 0 ) {
			$text = $weeks . (($weeks == 1) ? ' week ago' : ' weeks ago');
			if ($weeks <= 0) {
				$text = $days . (($days == 1) ? ' day ago' : ' days ago');
				if ($days <= 0) {
					$text = $hours . (($hours == 1) ? ' hour ago' : ' hours ago');
					if ($hours <= 0) {
						$text = $minutes . (($minutes == 1) ? ' minute ago' : ' minutes ago');
					}
				}
			}
		}
	}
	return $text; 
}
function nicetext($text) {
	$text = str_replace("\r\n","\n",$text);
	$paragraphs = preg_split("/[\n]{2,}/",$text);
	foreach ($paragraphs as $key => $p) {
		$paragraphs[$key] = "<p>".str_replace("\n","<br />",$paragraphs[$key])."</p>";
	}
	$text = implode("", $paragraphs);

	return $text;
} ?>