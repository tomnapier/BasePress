<?php
//*******************************************************************************//
// Declare Theme Name
//*******************************************************************************//
$themename = get_bloginfo('name');
$shortname = "iw";

//*******************************************************************************//
//    Generate a list of WordPress categories
//*******************************************************************************//
$extra = array(
        'label'    => "Please choose from below",
        'value'    => ''
);

//*******************************************************************************//
//    List all options
//*******************************************************************************//


//*******************************************************************************//
//    Display Options in Admin Panel
//*******************************************************************************//
// Create Panel
function mytheme_add_admin() {
    global $themename, $shortname, $options;
    if ( $_GET['page'] == basename(__FILE__) ) {
        if ( 'save' == $_REQUEST['action'] ) {
            foreach ( $options as $value ) {
                update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
            foreach ($options as $value) {
                if( isset( $_REQUEST[ $value['id'] ] ) ) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); }
                else { delete_option( $value['id'] ); }
            }
            header("Location: admin.php?page=options.php&saved=true");
            die;
        }
        else if( 'reset' == $_REQUEST['action'] ) {
            foreach ($options as $value) {
                delete_option( $value['id'] ); }
            header("Location: admin.php?page=options.php&reset=true");
            die;
        }
    }
    add_menu_page('Theme Options', 'Theme Options', 'edit_pages', basename(__FILE__), 'mytheme_admin', '', 3);
}

// Make it look good
function mytheme_add_init() {
    global $pagenow;
    if( "admin.php" == $pagenow ) {
        $file_dir = get_bloginfo('template_directory');
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script("jquery");
        wp_enqueue_media();
        wp_enqueue_script('media-upload');
        wp_enqueue_script("wp-color-picker");
        wp_enqueue_style("functions", $file_dir."/inc/styling/css/functions.css", false, "1.0", "all");
        wp_enqueue_script("rm_script", $file_dir."/inc/styling/js/rm_script.js", false, "1.0");
    }
}


$options = array (

    array(
        "name" => $themename." Options",
        "type" => "title" ),

    /* Styling options
    =================================================== */
    array(
        "name" => "Styling Options",
        "type" => "section" ),
    array( "type" => "open"),
    array(
        "name" => "Gutter Width",
        "desc" => "Width in em",
        "id" => $shortname."_column",
        "type" => "text",
        "std" => "1.875"),
    array(
        "name" => "Max Width",
        "desc" => "Width in em",
        "id" => $shortname."_width",
        "type" => "text",
        "std" => "62.5"),
    array(
        "name" => "Border Radius",
        "desc" => "Width in pxm",
        "id" => $shortname."_radius",
        "type" => "text",
        "std" => "3"),
    array(
        "name" => "Primary Colour",
        "desc" => "",
        "id" => $shortname."_primary",
        "type" => "colour",
        "std" => "#008CBA"),
    array(
        "name" => "Secondary Colour",
        "desc" => "",
        "id" => $shortname."_secondary",
        "type" => "colour",
        "std" => "#e7e7e7"),
    array(
        "name" => "Alert Colour",
        "desc" => "",
        "id" => $shortname."_alert",
        "type" => "colour",
        "std" => "#f04124"),
    array(
        "name" => "Success Colour",
        "desc" => "",
        "id" => $shortname."_success",
        "type" => "colour",
        "std" => "#43AC6A"),
    array(
        "name" => "Warning Colour",
        "desc" => "",
        "id" => $shortname."_warning",
        "type" => "colour",
        "std" => "#f08a24"),
    array(
        "name" => "Body font colour",
        "desc" => "",
        "id" => $shortname."_body_colour",
        "type" => "colour",
        "std" => "#222"),
    array(
        "name" => "Header font colour",
        "desc" => "",
        "id" => $shortname."_head_colour",
        "type" => "colour",
        "std" => "#222"),
    array( "type" => "close"),

    /* Home Page options
    =================================================== */
    array(
        "name" => "Home Page Options",
        "type" => "section"),
    array( "type" => "open"),
    array(
        "name" => "Welcome Line",
        "desc" => "The first thing people will see after the slider",
        "id" => $shortname."_home_welcome",
        "type" => "text",
        "std" => ""),
    array(
        "name" => "Intro Text",
        "desc" => "Home page intro paragraph",
        "id" => $shortname."_home_intro",
        "type" => "textarea",
        "std" => ""),
    array(
        "name" => "Featured Category",
        "desc" => "Category to be featured below the introduction",
        "id" => $shortname."_home_cat",
        "type" => "term_list",
        "term" => "category"),
    array(
        "name" => "Twitter Feed",
        "desc" => "The embed code from twitter for your twitter feed",
        "id" => $shortname."_home_tweets",
        "type" => "textarea"),
    array(
        "name" => "Slider Speed",
        "desc" => "Length of time in millseconds each slide is on screen",
        "id" => $shortname."_slider_speed",
        "std" => "10000",
        "type" => 'text' ),
    array(
        "name" => "Slider Animation Speed",
        "desc" => "Speed in milliseconds of the actual animation",
        "id" => $shortname."_slider_anim",
        "std" => "500",
        "type" => 'text' ),
    array(
        "name" => "Slider Bullets",
        "desc" => "Display bullets on home slider",
        "id" => $shortname."_slider_bullets",
        "type" => 'checkbox' ),
    array(
        "name" => "Slide Numbers",
        "desc" => "Display slide number on slider",
        "id" => $shortname."_slider_num",
        "type" => 'checkbox' ),
        
    array( "type" => "close"),

    /* Contact options
    =================================================== */
    array( "name" => "Contact Details",
           "type" => "section"),
    array( "type" => "open"),
     array(
        "name" => "Phone Number",
        "desc" => "",
        "id" => $shortname."_contact_phone",
        "type" => "text" ),
        
     array(
        "name" => "Fax Number",
        "desc" => "",
        "id" => $shortname."_contact_fax",
        "type" => "text" ),
        
      array(
        "name" => "Email",
        "desc" => "",
        "id" => $shortname."_contact_email",
        "type" => "text" ),
    array( "type" => "close"),

     /* Social Media options
    =================================================== */

    array(                                   
            "name" => "Social Media Links",
            "type" => "section"),
        array( "type" => "open"),
            array(
                "name" => "Facebook",
                "desc" => "",
                "id" => $shortname."_social_fb",
                "type" => "text",
                "std" => ""),
            array(
                "name" => "Twitter",
                "desc" => "",
                "id" => $shortname."_social_tw",
                "type" => "text",
                "std" => ""),
            array(
                "name" => "LinkedIn",
                "desc" => "",
                "id" => $shortname."_social_li",
                "type" => "text",
                "std" => ""),

    array( "type" => "close"),                                   

    /* Footer options
    =================================================== */
    array( "name" => "Footer",
        "type" => "section"),
    array( "type" => "open"),
    array( 
        "name" => "Privacy Policy Page",
        "desc" => "",
        "id" => $shortname."_privacy",
        "type" => "post_list",
        "post_type" => "page"),
    array( 
        "name" => "Cookie Policy Page",
        "desc" => "",
        "id" => $shortname."_cookie",
        "type" => "post_list",
        "post_type" => "page"),
    array( 
        "name" => "Footer contact info",
        "desc" => "Enter contact info",
        "id" => $shortname."_footer_contact",
        "type" => "textarea",
        "std" => ""),
    array( 
        "name" => "Google Analytics Code",
        "desc" => "You can paste your Google Analytics or other tracking code in this box. This will be automatically added to the footer.",
        "id" => $shortname."_ga_code",
        "type" => "textarea",
        "std" => ""),
    array( "type" => "close")
);

// Render Panel
function mytheme_admin() {
    global $themename, $shortname, $options;
    $i = 0;
    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

    $count = 0;
    foreach ($options as $value) {
        if ($value['type'] == 'section') $count++;
    };
    $half = round( $count / 2 );
    $true = false; ?>

    <div id="theme-options" class="wrap">
        <h2>Theme Options</h2>
        <div id="welcome-panel" class="welcome-panel">
            <div class="welcome-panel-content">
                <h3>Welcome to the <?php echo $themename; ?> theme options!</h3>
                <p class="about-description">We've got some options here to customise the look of your website:</p>
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h4>1. Get Started</h4>
                        <p>Each section is labelled according to what part of the website you are changing, click on the arrows next to the header to hide/show more options.</p>
                    </div>
                    <div class="welcome-panel-column">
                        <h4>2. Next Steps</h4>
                        <p>Once you've made your changes, click on the save button and then your changes will appear live on the website.</p>
                    </div>
                    <div class="welcome-panel-column welcome-panel-last">
                        <h4>3. Last Bits</h4>
                        <p>There are further customisations you can perform, <a class="hide-if-no-customize" href="<?php bloginfo( 'url'); ?>/wp-admin/customize.php" target="_blank">click here</a> or alternatively <a href="mailto:<?php bloginfo( 'admin_email' ); ?>">email the admin</a>. <a href="http://wordpress.org/plugins/wordpress-seo/" target="_blank">Click here</a> for a great SEO plugin to help your search engine optimisation.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="dashboard-widgets-wrap">
            <form id="dashboard-widgets" class="metabox-holder" method="post">
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <?php foreach ($options as $value) { switch ( $value['type'] ) {
                case "open": //Get Options values
                break;
                case "close": ?>
                            <input name="save" type="submit" value="Save changes" class="button button-primary" />
                        </div>
                    </div>
                <?php break;
                case "title": ?>
                <?php break;
                case 'text': ?>
                <div class="rm_input rm_text">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'textarea': ?>
                <div class="rm_input rm_textarea">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'select': ?>
                <div class="rm_input rm_select">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $option) { ?>
                        <option <?php if (get_settings( $value['id'] ) == $option['value']) { echo 'selected="selected"'; } ?> value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option><?php } ?>
                    </select>
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'post_list':
                $items = get_posts( array (
                    'post_type'    => $value['post_type'],
                    'posts_per_page' => -1
                )); ?>
                <div class="rm_input rm_select">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                            <option value="">Select One</option>
                            <?php foreach($items as $item) {
                                echo '<option value="'.$item->ID.'"',get_settings( $value['id'] ) == $item->ID ? ' selected="selected"' : '','>'.$item->post_type.': '.$item->post_title.'</option>';
                            } // end foreach ?>
                        </select>
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'term_list':
                $items = get_terms( $value['term'], 'orderby=count&hide_empty=0' ); ?>
                <div class="rm_input rm_select">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                            <option value="">Select One</option>
                            <?php foreach($items as $item) {
                                echo '<option value="'.$item->term_id.'"',get_settings( $value['id'] ) == $item->term_id ? ' selected="selected"' : '','>'.$item->taxonomy.': '.$item->name.'</option>';
                            } // end foreach ?>
                        </select>
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case "checkbox": ?>
                <div class="rm_input rm_checkbox">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                    <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'colour': ?>
                <div class="rm_input rm_colour">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <input name="<?php echo $value['id']; ?>" class="color-picker" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
                    <small style="background-color: <?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>; height: 25px; width: 25px;"></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'slider': ?>
                <div id="slider-<?php echo $value['id']; ?>" class="rm_input rm_slider">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <div class="slide"></div>
                    <input name="<?php echo $value['id']; ?>" class="amount" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" readonly />
                    <small><?php echo $value['desc']; ?></small>
                    <script>
                        jQuery(document).ready(function($) {
                            $( "#slider-<?php echo $value['id']; ?> .slide" ).slider({
                                value: <?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo '100'; } ?>,
                                min: 0,
                                max: 100,
                                step: 5,
                                slide: function( event, ui ) {
                                    $( "#slider-<?php echo $value['id']; ?> .amount" ).val( ui.value );
                                }
                            });
                            $( "#slider-<?php echo $value['id']; ?> .amount" ).val( $( "#slider-<?php echo $value['id']; ?> .slide" ).slider( "value" ) );
});
                    </script>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case 'image': ?>
                <div class="rm_input rm_upload">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <div class="uploader">
                        <a class="button" id="<?php echo $value['id']; ?>_button" href="#"><i class="icon16 icon-media"></i> <?php if ( get_settings( $value['id'] ) != "") { echo "Change image"; } else { echo "Upload/Choose image"; } ?></a>
                        <input type="text" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" hidden />
                        <div id="<?php echo $value['id']; ?>_preview" class="background">
                            <img src="<?php if ( get_settings( $value['id'] ) != "") {
                                    $url = wp_get_attachment_image_src( stripslashes( get_settings( $value['id'] ) ), 'thumbnail' );
                                    echo $url[0];
                                } else {
                                    echo $value['std']; } ?>" />
                        </div>
                    </div>
                    <small><?php echo $value['desc']; ?></small>
                    <div class="clearfix"></div>
                </div>
                <?php break;
                case "section": $i++;
                if ( $i > $half && $true == false ){ 
                    echo'</div></div><div id="postbox-container-2" class="postbox-container"><div id="side-sortables" class="meta-box-sortables ui-sortable">';
                    $true = true;
                } ?>
                <div class="rm_section postbox">
                    <div class="handlediv" title="Click to toggle"><br></div>
                    <h3 class="hndle">
                        <span><span class="hide-if-no-js"><?php echo $value['name']; ?></span> <span class="hide-if-js"><?php echo $value['name']; ?></span></span>
                    </h3>

                    <div class="inside">
            <?php break; } } ?>
                    </div>
                </div><!-- .post-box-container -->
                <input type="hidden" name="action" value="save" />
            </form>
            <form method="post">
                <p class="submit">
                    <input name="reset" type="submit" value="Reset All" class="button" />
                    <input type="hidden" name="action" value="reset" />
                </p>
            </form>
        </div>
    </div><!-- #theme-options -->
<?php }

add_action( 'admin_init', 'mytheme_add_init', 10 );
add_action( 'admin_menu', 'mytheme_add_admin', 100 ); ?>
