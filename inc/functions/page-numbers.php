<?php
#######################################################################################
# Page Numbers
#######################################################################################

function wp_page_numbers_check_num($num) {
	return ($num%2) ? true : false;
}

function wp_page_numbers_page_of_page($max_page, $paged, $page_of_page_text, $page_of_of) {
	$pagingString = "";
	if ( $max_page > 1) {
		$pagingString .= '<li class="page_info">';
		if($page_of_page_text == "")
			$pagingString .= 'Page ';
		else
			$pagingString .= $page_of_page_text . ' ';
		
		if ( $paged != "" )
			$pagingString .= $paged;
		else
			$pagingString .= 1;
		
		if($page_of_of == "")
			$pagingString .= ' of ';
		else
			$pagingString .= ' ' . $page_of_of . ' ';
		$pagingString .= floor($max_page).'</li>';
	}
	return $pagingString;
}

function wp_page_numbers_prevpage($paged, $max_page, $prevpage) {
	if( $max_page > 1 && $paged > 1 ) { $class = "arrow"; } else { $class = "arrow unavailable"; }
	$pagingString = '<li class="'.$class.'"><a href="'.get_pagenum_link($paged-1). '">'.$prevpage.'</a></li>';
	return $pagingString;
}

function wp_page_numbers_left_side($max_page, $limit_pages, $paged, $pagingString) {
	$pagingString = "";
	$page_check_max = false;
	$page_check_min = false;
	if($max_page > 1)
	{
		for($i=1; $i<($max_page+1); $i++)
		{
			if( $i <= $limit_pages )
			{
				if ($paged == $i || ($paged == "" && $i == 1))
					$pagingString .= '<li class="current"><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
				else
					$pagingString .= '<li><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
				if ($i == 1)
					$page_check_min = true;
				if ($max_page == $i)
					$page_check_max = true;
			}
		}
		return array($pagingString, $page_check_max, $page_check_min);
	}
}

function wp_page_numbers_middle_side($max_page, $paged, $limit_pages_left, $limit_pages_right) {
	$pagingString = "";
	$page_check_max = false;
	$page_check_min = false;
	for($i=1; $i<($max_page+1); $i++)
	{
		if($paged-$i <= $limit_pages_left && $paged+$limit_pages_right >= $i)
		{
			if ($paged == $i)
				$pagingString .= '<li class="active_page"><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
			else
				$pagingString .= '<li><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
				
			if ($i == 1)
				$page_check_min = true;
			if ($max_page == $i)
				$page_check_max = true;
		}
	}
	return array($pagingString, $page_check_max, $page_check_min);
}

function wp_page_numbers_right_side($max_page, $limit_pages, $paged, $pagingString) {
	$pagingString = "";
	$page_check_max = false;
	$page_check_min = false;
	for($i=1; $i<($max_page+1); $i++)
	{
		if( ($max_page + 1 - $i) <= $limit_pages )
		{
			if ($paged == $i)
				$pagingString .= '<li class="active_page"><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
			else
				$pagingString .= '<li><a href="'.get_pagenum_link($i). '">'.$i.'</a></li>'."\n";
				
			if ($i == 1)
			$page_check_min = true;
		}
		if ($max_page == $i)
			$page_check_max = true;
		
	}
	return array($pagingString, $page_check_max, $page_check_min);
}

function wp_page_numbers_nextpage($paged, $max_page, $nextpage) {
	if( $paged != "" && $paged < $max_page) { $class = "arrow"; } else { $class = "arrow unavailable"; }
	$pagingString = '<li class="'.$class.'"><a href="'.get_pagenum_link($paged+1). '">'.$nextpage.'</a></li>'."\n";
	return $pagingString;
}

function wp_page_numbers($start = "", $end = "") {
	global $wp_query;
	global $max_page;
	global $paged;
	if ( !$max_page ) { $max_page = $wp_query->max_num_pages; }
	if ( !$paged ) { $paged = 1; }
	
	$settings = get_option('wp_page_numbers_array');
	$page_of_page = $settings["page_of_page"];
	$page_of_page_text = $settings["page_of_page_text"];
	$page_of_of = $settings["page_of_of"];
	
	$next_prev_text = $settings["next_prev_text"];
	$show_start_end_numbers = $settings["show_start_end_numbers"];
	$show_page_numbers = $settings["show_page_numbers"];
	
	$limit_pages = $settings["limit_pages"];
	$nextpage = $settings["nextpage"];
	$prevpage = $settings["prevpage"];
	$startspace = $settings["startspace"];
	$endspace = $settings["endspace"];
	
	if( $nextpage == "" ) { $nextpage = "&raquo;"; }
	if( $prevpage == "" ) { $prevpage = "&laquo;"; }
	if( $startspace == "" ) { $startspace = "..."; }
	if( $endspace == "" ) { $endspace = "..."; }
	
	if($limit_pages == "") { $limit_pages = "10"; }
	elseif ( $limit_pages == "0" ) { $limit_pages = $max_page; }
	
	if(wp_page_numbers_check_num($limit_pages) == true)
	{
		$limit_pages_left = ($limit_pages-1)/2;
		$limit_pages_right = ($limit_pages-1)/2;
	}
	else
	{
		$limit_pages_left = $limit_pages/2;
		$limit_pages_right = ($limit_pages/2)-1;
	}
	
	if( $max_page <= $limit_pages ) { $limit_pages = $max_page; }
	
	$pagingString = "<div id='wp_page_numbers' class='pagination-centered small-12 columns'>\n";
	$pagingString .= '<ul class="pagination">';
	
	if( ($paged) <= $limit_pages_left )
	{
		list ($value1, $value2, $page_check_min) = wp_page_numbers_left_side($max_page, $limit_pages, $paged, $pagingString);
		$pagingMiddleString .= $value1;
	}
	elseif( ($max_page+1 - $paged) <= $limit_pages_right )
	{
		list ($value1, $value2, $page_check_min) = wp_page_numbers_right_side($max_page, $limit_pages, $paged, $pagingString);
		$pagingMiddleString .= $value1;
	}
	else
	{
		list ($value1, $value2, $page_check_min) = wp_page_numbers_middle_side($max_page, $paged, $limit_pages_left, $limit_pages_right);
		$pagingMiddleString .= $value1;
	}
	if($next_prev_text != "no")
		$pagingString .= wp_page_numbers_prevpage($paged, $max_page, $prevpage);

		if ($page_check_min == false && $show_start_end_numbers != "no")
		{
			$pagingString .= "<li class=\"first_last_page\">";
			$pagingString .= "<a href=\"" . get_pagenum_link(1) . "\">1</a>";
			$pagingString .= "</li>\n<li  class=\"space\">".$startspace."</li>\n";
		}
	
	if($show_page_numbers != "no")
		$pagingString .= $pagingMiddleString;
	
		if ($value2 == false && $show_start_end_numbers != "no")
		{
			$pagingString .= "<li class=\"space\">".$endspace."</li>\n";
			$pagingString .= "<li class=\"first_last_page\">";
			$pagingString .= "<a href=\"" . get_pagenum_link($max_page) . "\">" . $max_page . "</a>";
			$pagingString .= "</li>\n";
		}
	
	if($next_prev_text != "no")
		$pagingString .= wp_page_numbers_nextpage($paged, $max_page, $nextpage);
	
	$pagingString .= "</ul>\n";
	
	$pagingString .= "<div style='float: none; clear: both;'></div>\n";
	$pagingString .= "</div>\n";
	
	if($max_page > 1)
		echo $start . $pagingString . $end;
}