<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package _s
 */

global $shortname;

?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">

        <div class="site-info">
                <div class="map">
                   
                </div>
            <div class="row">

                <div class="contact-info">
                  
                </div>
                

                <div class="site-map">
                    <?php wp_nav_menu(array( 'theme_location' => 'site-map', 'container' => '', 'depth' => 1 ));?>
                </div>

            </div>
        </div>

        <div class="site-copyright">
            <ul>
                <li><p>&copy; <?php echo the_date('Y'); ?> <a href="<?php bloginfo('url');?>" rel="index"><?php bloginfo('name'); ?></a></p></li>
                 <?php if( get_option( 'iw_privacy' ) ) { ?><li><a href="<?php echo get_permalink( get_option( $shortname . '_privacy' ) ); ?>"><?php echo get_the_title( get_option( $shortname . '_privacy'  ) ); ?></a></li><?php } ?>
                 <?php if( get_option( 'iw_cookie' ) ) { ?><li><a href="<?php echo get_permalink( get_option( $shortname . '_cookie' ) ); ?>"><?php echo get_the_title( get_option( $shortname . '_cookie'  ) ); ?></a></li><?php } ?>
                <li><p><?php _e('Design by <a href="http://www.inkandwater.co.uk/" target="_blank" rel="bookmark">Ink &amp; Water</a>');?></p></li>
            </ul>
        </div><!-- .site-copyright -->

    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
