<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content"> */ ?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
    <head>

        <!-- Basic Page Needs
        ================================================== -->
        <meta charset="<?php echo get_option('blog_charset'); ?>">
        <title><?php
                if ( is_single() ) { bloginfo('name'); print ' | '; single_post_title(); }
                elseif ( is_home() || is_front_page() ) { bloginfo('name'); print ' | '; bloginfo('description'); get_page_number(); }
                elseif ( is_page() ) { bloginfo('name'); print ' | '; single_post_title(''); }
                elseif ( is_search() ) { bloginfo('name'); print ' | Search results for ' . wp_specialchars($s); get_page_number(); }
                elseif ( is_404() ) { bloginfo('name'); print ' | Not Found'; }
                else { bloginfo('name'); print ' | '; wp_title('|'); get_page_number(); }
        ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
        <meta name="author" content="<?php $user = get_user_by( "email", get_bloginfo('admin_email') ); echo $user->display_name ?>">

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- CSS
        ================================================== -->
       
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
        <link rel='stylesheet' type='text/css' href="<?php bloginfo('url'); ?>/?theme_custom=css">
    
        <?php if ( get_option('iw_custom_css') != "") { ?>
        <!-- Custom CSS
        ================================================== -->
        <style type="text/css"><?php echo get_option('iw_custom_css'); ?></style>
        <?php } ?>

        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/ie.css">
        <![endif]-->

        <!-- Favicons
        ================================================== -->
        <link rel="shortcut icon" href="<?php echo get_option('iw_favicon'); ?>">
        <link rel="apple-touch-icon" href="<?php echo get_option('iw_icon_56'); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_option('iw_icon_72'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_option('iw_icon_114'); ?>">

        <!-- WordPress
        ================================================== -->
        <?php wp_head(); ?>

    </head>

    <body <?php body_class( $body ); ?>>
            
        <div id="page" class="hfeed site">

            <header id="masthead" class="site-header" role="banner">
                    
                    <div class="site-branding">
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                        <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
                    </div>

                    <div id="mobile-menu">
                        <a href="#site-navigation" class="mobile-btn"><span class="dashicons dashicons-menu"></span></a>
                    </div>

                    <nav id="site-navigation" class="site-navigation main-navigation" role="navigation">
                        <?php  wp_nav_menu( array( 'theme_location' => 'main-menu', 'container' => '' ) ); ?>
                    </nav><!-- #site-navigation -->


    </header><!-- #masthead -->

                <?php get_template_part( 'slider' ); ?>

                <div class="intro">
                    <h2><?php echo get_option( 'iw_home_welcome' ); ?></h2>
                    <p><?php echo get_option( 'iw_home_intro' ); ?></p>
                </div>

            <div id="content" class="site-content row">