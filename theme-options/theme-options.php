<?php 

/**
 * Plugin Name: Theme Settings Menu
 * Plugin URI: http://tom-napier.co.uk
 * Description: A simple theme settings menu to add extra info (such as twitter feeds, phone numbers, etc.)
 * Version: 1.0
 * Author: Tom Napier
 * Author URI: http://tom-napier.co.uk
 * License: GPL2
 */

/* ADD THE THEME SETTINGS PAGE
================================================================== */

function setup_theme_settings_menu() {

// settings_fields($option_group);
// register_setting($option_group, $option_name, $sanitize_callback=“”);
// unregister_setting($option_group, $option_name, $sanitize_callback=“”);
// add_settings_section($id, $title, $callback, $page);
// add_settings_field($id, $title, $callback, $page, $section, $args = array());
// do_settings_sections($page)
// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position )
// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

  add_menu_page('Theme Settings', 'Theme Settings', 'manage_options', 'theme-settings.php','theme_settings_page','',4.5);

}

add_action('admin_menu', 'setup_theme_settings_menu');

/* ENQUEUE STYLES
================================================================== */

// Register style sheet.
add_action( 'admin_enqueue_scripts', 'register_plugin_styles' );

function register_plugin_styles() {
  wp_register_style( 'stylesheet', plugins_url( '/css/theme-settings.css', __FILE__  ) );
  wp_enqueue_style( 'stylesheet' );
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
}


function theme_settings_page() {  


?>
    <div class="section panel">
      <h1><?php bloginfo('name');?> Theme Settings</h1>
      <form method="post" enctype="multipart/form-data" action="options.php">
        <?php 
          settings_fields('theme_settings'); 
        
          do_settings_sections('theme_settings.php');
        ?>
            <p class="submit">  
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />   
            </p>  
            
      </form>
      
    </div>
    <?php
}

/* Register the settings to use on the theme options page
================================================================== */

add_action( 'admin_init', 'register_settings' );

/* FUNCTION TO REGISTER THE SETTINGS
================================================================== */

function register_settings() {
    
    // Register the settings with Validation callback
    register_setting( 'theme_settings', 'theme_settings', 'validate_settings' );
    
    // Add settings section
    add_settings_section( 'contact_section', 'Contact Settings', 'display_section', 'theme_settings.php' );

    // Add settings section
    add_settings_section( 'home_section', 'Home Page Settings', 'display_section', 'theme_settings.php' );

    $pre = 'setting_';

    /* Home Page Settings 
    =============================== */

    $field_args = array(
      'type'      => 'text',
      'id'        => $pre. 'home_tagline',
      'desc'      => 'Home Page Tagline',
      'std'       => '',
      'label_for' => 'home_post',
    );

    add_settings_field( 'home_tagline', 'Home Page Tagline', 'display_setting', 'theme_settings.php', 'home_section', $field_args );
    // add_settings_field($id, $title, $callback, $page, $section, $args = array());

    $field_args = array(
      'type'      => 'post_list',
      'post_type' => 'page',
      'id'        => $pre. 'home_link',
      'desc'      => 'Home Page Tagline',
      'std'       => '',
      'label_for' => 'home_post',
    );

    add_settings_field( 'home_link', 'Home Page Link', 'display_setting', 'theme_settings.php', 'home_section', $field_args );


    $field_args = array(
      'type'      => 'post_list',
      'post_type' => 'portfolio',
      'id'        => $pre. 'home_post',
      'desc'      => 'Front Page Featured Post',
      'std'       => '',
      'label_for' => 'home_post',
    );

    add_settings_field( 'home_post', 'Front Page Featured Post', 'display_setting', 'theme_settings.php', 'home_section', $field_args );

    $field_args = array(
      'type'      => 'post_list',
      'post_type' => 'page',
      'id'        => $pre. 'home_contact',
      'desc'      => 'Front Page Contact Section',
      'std'       => '',
      'label_for' => 'home_contact',
    );

    add_settings_field( 'home_contact', 'Front Page Contact Section', 'display_setting', 'theme_settings.php', 'home_section', $field_args );

    /* CONTACT SECTION 
    =============================== */

    $field_args = array(
      'type'      => 'text',
      'id'        => $pre. 'phone',
      'desc'      => 'The main contact telephone number for the business',
      'std'       => '',
      'label_for' => 'textbox',
    );

    add_settings_field( 'phone', 'Phone Number', 'display_setting', 'theme_settings.php', 'contact_section', $field_args );

    $field_args = array(
      'type'      => 'text',
      'id'        => $pre. 'email',
      'desc'      => 'The main email address for the business',
      'std'       => '',
      'label_for' => 'textbox',
    );

    add_settings_field( 'email', 'Email Address', 'display_setting', 'theme_settings.php', 'contact_section', $field_args );

    $field_args = array(
      'type'      => 'textarea',
      'id'        => $pre. 'map',
      'desc'      => 'Paste the "src" attribute from a Google Map Embed Code here',
      'std'       => '',
      'label_for' => 'textbox',
    );

}

/* FUNCTION TO ADD EXTRA TEXT TO DISPLAY ON EACH SECTION
============================================================= */

function display_section( $section ) { 

}

/* FUNCTION TO DISPLAY THE SETTINGS ON THE PAGE
============================================================== 

* This is setup to be expandable by using a switch on the type variable.
* In future you can add multiple types to be display from this function,
* Such as checkboxes, select boxes, file upload boxes etc. */

function display_setting( $args ) {

    extract( $args );

    $option_name = 'theme_settings';

    $options = get_option( $option_name );

    switch ( $type ) {  

          case 'text':  
              echo "<input class='regular-text$class' type='text' id='$id' name='" . $option_name . "[$id]' value='$options[$id]' />";  
              echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
          break;  

        case 'textarea':  
              $options[$id] = stripslashes($options[$id]);  
              $options[$id] = esc_html( $options[$id]);  
              echo "<textarea id='$id' name='" . $option_name . "[$id]'>" . $options[$id] . "</textarea>";  
              echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
        break;

        case 'checkbox':  

              if ( $options[$id] ) { $checked = "checked=\"checked\""; } else { $checked = "";} 
              echo "<input type='checkbox' id='$id' name='" . $option_name . "[$id]' " . $checked . "/>";  
              echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  

        break;

        case 'post_list';

            $items = get_posts( array( 'posts_per_page' => -1, 'post_type' => $post_type ) );
            echo "<select id='$id' name='" . $option_name . "[$id]'><option>Select</option>";

            foreach ($items as $item) {
            if ( $options[$id] == $item->ID ) { $selected = "selected=\"selected\""; } else { $selected = "";} 
                echo "<option value=" . $item->ID . " ". $selected . ">" . $item->post_title  . "</option>";
            }

            echo "</select> <br/> <span class='description'>$desc</span>";

        break;

        case 'color';

            $options[$id] = stripslashes($options[$id]);  
            $options[$id] = esc_attr( $options[$id]);  
            echo "<input class='color-picker' type='text' id='$id' name='" . $option_name . "[$id]' value='$options[$id]' />";  
            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 
            ?>

            <script>
                jQuery(document).ready(function($){
                  $(".color-picker").iris({
                    palettes: ['#fff', '#d2d2d2', '#969696', '#6e6e6e', '#000', '#8cc83c']
                  });
                });
            </script>

    <?php
        break;
        
        case 'date';
            $options[$id] = stripslashes($options[$id]);  
            $options[$id] = esc_attr( $options[$id]);  
            echo "<input class='date-picker' type='text' id='$id' name='" . $option_name . "[$id]' value='$options[$id]' />";  
            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";?>
         
          <script type="text/javascript">
            jQuery(document).ready(function($) {
              $('.date-picker').datepicker({
                dateFormat : 'dd-mm-yy'
              });
            });
          </script>

    <?php 
        break;

        case 'image':
          $image = get_template_directory_uri().'/inc/image.jpg'; 
          echo "<span class='custom_default_image' style='display:none'>'" . $image . "'</span>";
          
          if ($options[$id]) { $image = wp_get_attachment_image_src($options[$id], 'medium'); $image = $image[0]; }
          
          echo  "<input id='$id' name='" . $option_name . "[$id]' type='hidden' class='custom_upload_image' value='$options[$id]' />";
          echo  "<img src='" . $image . "' class='custom_preview_image' alt='' /><br />";
          echo  "<input class='custom_upload_image_button' type='button' value='Choose Image' />";
          echo  "<small>&nbsp;<a href='#' class='custom_clear_image_button'>Remove Image</a></small>";
          echo  "<br clear='page' /><span class='description'>" . $desc . "</span>";
        break;

    }

}

/**
 * Callback function to the register_settings function will pass through an input variable
 * You can then validate the values and the return variable will be the values stored in the database.
 */

function validate_settings($input) {

  foreach($input as $key => $value) {

    $newinput[$key] = trim($value);

    // Check to see if the current option has a value. If so, process it.

        if( isset( $input[$key] ) ) {
         
            // Strip page HTML and PHP tags and properly handle quoted strings
            $newinput[$key] = strip_tags( stripslashes( $input[ $key ] ) );

        } // end if

  }

  return $newinput;

} ?>