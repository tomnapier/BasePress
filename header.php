<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content"> */ 

global $shortname;

?>

    <!DOCTYPE html>
    <!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
    <!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
    <!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
    <!--[if (gte IE 10)|!(IE)]><!--><html lang="en"> <!--<![endif]-->

    <head>

        <!-- Meta
        ================================================== -->
        <meta charset="<?php echo get_option('blog_charset'); ?>">

        <title>

        </title>

        <meta name="description" content="<?php bloginfo('description'); ?>">
        <meta name="author" content="<?php $user = get_user_by( "email", get_bloginfo('admin_email') ); echo $user->display_name ?>">

        <!-- Viewport
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- CSS
        ================================================== -->
       
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
        <link rel='stylesheet' type='text/css' href="<?php bloginfo('url'); ?>/?theme_custom=css">

        <!-- Custom CSS
        ================================================== -->


        <!-- Favicons
        ================================================== -->
        <link rel="shortcut icon" href="<?php echo get_option( $shortname . '_favicon'); ?>">
        <link rel="apple-touch-icon" href="<?php echo get_option( $shortname . '_icon_56'); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_option( $shortname . '_icon_72'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_option( $shortname . '_icon_114'); ?>">

        <!-- WordPress
        ================================================== -->
        <?php wp_head(); ?>

    </head>

    <body <?php body_class( $body ); ?>>
            
        <div id="page" class="hfeed site">

            <header id="masthead" class="site-header" role="banner">
                    
                    <div class="site-branding">
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    </div>

                    <nav id="site-navigation" class="site-navigation main-navigation" role="navigation">
                        <?php  wp_nav_menu( array( 'theme_location' => 'main-menu', 'container' => '' ) ); ?>
                    </nav><!-- #site-navigation -->

                    <div id="mobile-menu">
                        <a href="#site-navigation" class="menu-button"><span class="menu-icon"></span></a>
                    </div>

            </header><!-- #masthead -->

            <div id="content" class="site-content row">
