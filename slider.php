<?php 

/* get metabox data */
global $post;
$slider = new WP_query( array( 'post_type' => 'slide', 'post_per_page' => 5 ) );


?>
<?php if ( $slider->have_posts() ) { ?>
     
     <div id="slider">
          <ul class="slider" data-orbit data-options="pause_on_hover:true; animation_speed:500;navigation_arrows:true;bullets:false;slide_number:false;">
     <?php $i = 1 ;?>
     <?php while ( $slider->have_posts() ) : $slider->the_post(); ?>
     <?php $url = wp_get_attachment_image_src(get_post_thumbnail_ID(), 'slider');
            $meta = get_post_custom(); 
          $heading  = ( $meta['slide_heading'][0] )? '<div class="slide-heading"><h2>' . $meta['slide_heading'][0] . '</h2></div>' : null;
          $caption  = ( $meta['slide_caption'][0] )? '<div class="slide-caption"><p>' . $meta['slide_caption'][0] . '</p></div>' : null;
          $post = ( $meta['slide_post'][0] )? '<div class="slide-link"><a href="' . get_permalink( $meta['slide_post'][0] ) . '">Read More</a></div>' : null;
          $link = ( $meta['slide_link'][0] )? '<div class="slide-link"><a href="' . get_permalink( $meta['slide_link'][0] ) . '">Read More</a></div>' : null;?>

            <li style="background:url(<?php echo $url[0]; ?>); background-size:cover;">
              <img src="<?php echo $url[0]; ?>" alt="slide<?php echo $i++ ;?>" />
              <?php if($heading || $caption) { ?>
                <div class="slide-container">
                    <?php echo $heading; ?>
                    <?php echo $caption; ?>
                    <?php echo $post; ?>
                    <?php echo $link; ?>
                </div>
              <?php } ?>
            </li>

     <?php endwhile; ?>
     
          </ul>
     </div>
      
<?php } else {
		$placeholder = wp_get_attachment_url(new_post_thumbnail( 'slider' ));
        echo '<img src="' . $placeholder . '" />';
	  };

wp_reset_postdata(); ?>
