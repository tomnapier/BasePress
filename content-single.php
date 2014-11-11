<?php
/**
 * @package _s
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

        <div class="entry-meta">
            <?php _s_posted_on(); ?>
            <div class="meta-bookmark">
                <span class="permalink"><?php the_permalink(); ?></span>
            </div>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php the_content(); ?>
        <?php edit_post_link( __( 'Edit', '_s' ), '<span class="edit-link">', '</span>' ); ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php
            /* translators: used between list items, there is a space after the comma */
            $category_list = get_the_category_list( __( ', ', '_s' ) );

            /* translators: used between list items, there is a space after the comma */
            $tag_list = get_the_tag_list( '', __( ', ', '_s' ) );

            if ( ! _s_categorized_blog() ) {
                // This blog only has 1 category so we just need to worry about tags in the meta text
                if ( '' != $tag_list ) {
                    $meta_text = __( 'This entry was tagged %2$s.', '_s' );
                } else {
                    $meta_text = __( '', '_s' );
                }

            } else {
                // But this blog has loads of categories so we should probably display them here
                if ( '' != $tag_list ) {
                    $meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', '_s' );
                } else {
                    $meta_text = __( 'This entry was posted in %1$s.', '_s' );
                }

            } // end check for categories on this blog

            printf(
                $meta_text,
                $category_list,
                $tag_list,
                get_permalink()
            );
        ?>
        
        
        <?php previous_post_link('<span class="prev-link"><p>Previous: %link</p></span>'); ?>

        <?php next_post_link('<span class="next-link"><p>Next: %link</p></span>'); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

