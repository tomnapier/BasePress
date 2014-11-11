<?php
/* Register Menus
=================================================== */
function register_my_menus() {
    register_nav_menus(
        array(
            'main-menu' => __( 'Main Menu' ),
            'site-map' => __( 'Site Map' )
        )
    );
}
add_action( 'init', 'register_my_menus' );

/* Home Menu
=================================================== */
function home_link( $title, $url, $target = "_self" ){

	if( is_front_page() && $target != "_blank" ) {
		$output = "#";
		$title = strtolower( $title );
		$title = str_replace( " ", "-", $title );
		$output .= $title;
	} else {
		$output = $url;
	}

	return $output;
}

/* Is Current
=================================================== */
function menu_current( $id, $url = null ){
	global $post;

	$currentURL = "http://".$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	$currentID = $post->ID;

	if (!is_home()) $cat = in_category( $id, $currentID );

	if ($currentID == $id || $currentURL == $url || $cat){
		$output = 'current-menu-item';
	}

	return ( $output )? $output : null;
}

/* Get Menu
=================================================== */
function get_menu( $menu_name = null, $home = false, $mobile_check = true ){

	//Regular Menu
	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {

		$i = 1;
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );
		$menu_media = '<ul id="menu-' . $menu_name . '" class="menu';
		$menu_media .= ($mobile_check == true)? ' hide-for-small">' : '">';
		$menu_mobile = '<select id="mobile-' . $menu_name . '" class="menu show-for-small">';
		$menu_mobile .= '<option value="">Go to...</option>';


		foreach ( (array) $menu_items as $key => $menu_item ) {
			$id = $menu_item->object_id;

			$title = $menu_item->title;
			$url = $menu_item->url;
			$target = $menu_item->target;
			$object = $menu_item->object;

			if( $object == 'page' ){
				$page = get_post( $id );
				$parent = ( $page->post_parent != 0 )? $page->post_parent : $id;
				$colour = get_post_meta( $parent, 'custom_colour', true );
			} else {
				$colour = "";
			}

			$classes = $menu_item->classes;
			$class = "menu-item menu-item-object-" . $object . " menu-item-type-" . $type . ' menu-item-' . $menu_item->ID;
			$class .= ( menu_current( $id, $url ) )? " " . menu_current( $id, $url ) : "";
			if ($classes[0] != "") $class .= ' '. implode( ' ', $classes );

			$link = $url;
			$menu_media .= '<li id="menu-item-' . $menu_item->ID . '" class="'. $class .'"><a href="' . $link . '" target="' . $target . '"><span class="colour" style="background-color: ' . $colour . ';"></span>' . $title . '</a>' . $after . '</li>';
			$menu_mobile .= '<option value="' . $link . '"'. $select .'>' . $title . '</option>';

			$i++;
		}
		$menu_media .= '</ul>';
		$menu_mobile .= '</select>';
	} else {
		$menu_media = '<ul class="menu hide-for-small"><li>Menu "' . $menu_name . '" not defined.</li></ul>';
		$menu_mobile = '<span class="menu show-for-small">Menu "' . $menu_name . '" not defined.</span>';
	}
							echo ($mobile_check == true)? $menu_media . $menu_mobile : $menu_media;
}

/* Custom Post Menu
=================================================== */
add_action( 'admin_init', 'add_menu_meta_box' );
add_action( 'admin_enqueue_scripts', 'metabox_script' );
add_action( 'wp_ajax_my-add-post-type-archive-links', 'ajax_add_post_type' );
add_filter( 'wp_setup_nav_menu_item', 'setup_archive_item' );
add_filter( 'wp_nav_menu_objects', 'maybe_make_current' );

function add_menu_meta_box() {
	add_meta_box(
		'post-type-archives',
		__('Post Types','my-post-type-archive-links'),
		'post_type_meta_box', // $callback
		'nav-menus',
		'side',
		'low' ); // $priority
}

function post_type_meta_box( ) {
	global $nav_menu_selected_id;

	$post_types = get_post_types(array('public'=>true,'_builtin'=>false), 'object'); ?>

	<!-- Post type checkbox list -->
	<ul id="post-type-archive-checklist">
		<?php $i = 1;
		foreach ($post_types as $type):?>
			<li>
				<label>
					<input type="checkbox" value ="<?php echo esc_attr($type->name); ?>" />
					<?php echo esc_attr($type->labels->name); ?>
				</label>
			</li>
		<?php endforeach;?>
	</ul><!-- /#post-type-archive-checklist -->

	<!-- 'Add to Menu' button -->
	<p class="button-controls" >
		<span class="add-to-menu" >
			<input type="submit" id="submit-post-type-archives" <?php disabled( $nav_menu_selected_id, 0 ); ?> value="<?php esc_attr_e('Add to Menu'); ?>" name="add-post-type-menu-item"  class="button-secondary submit-add-to-menu" />
		</span>
	</p>
<?php }


function metabox_script( $hook ) {
	if( 'nav-menus.php' != $hook )
		return;

	//On Appearance>Menu page, enqueue script: 
	wp_enqueue_script( 'my-post-type-archive-links_metabox', get_bloginfo( 'template_url' ) . '/inc/styling/js/metabox.js', array('jquery') );
	wp_localize_script( 'my-post-type-archive-links_metabox','MyPostTypeArchiveLinks', array('nonce'=>wp_create_nonce('my-add-post-type-archive-links')));
}

function ajax_add_post_type(){
	if ( ! current_user_can( 'edit_theme_options' ) )
		die('-1');

	check_ajax_referer('my-add-post-type-archive-links','posttypearchive_nonce');

	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	if(empty($_POST['post_types']))
		exit;

	//Create menu items and store IDs in array
	$item_ids = array();
	foreach ( ( array ) $_POST['post_types'] as $post_type ) {
		$post_type_obj = get_post_type_object( $post_type );

		if( !$post_type_obj )
			continue;

		$menu_item_data= array(
			'menu-item-title' => esc_attr($post_type_obj->labels->name),
			'menu-item-type' => 'post_type_archive',
			'menu-item-object' => esc_attr($post_type),
			'menu-item-url' => get_post_type_archive_link($post_type),
		);

		//Collect the items' IDs. 
		$item_ids[] = wp_update_nav_menu_item(0, 0, $menu_item_data );
	}

	//If there was an error die here
	if ( is_wp_error( $item_ids ) )
		die('-1');

	//Set up menu items
	foreach ( (array) $item_ids as $menu_item_id ) {
		$menu_obj = get_post( $menu_item_id );
		if ( ! empty( $menu_obj->ID ) ) {
			$menu_obj = wp_setup_nav_menu_item( $menu_obj );
			$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
			$menu_items[] = $menu_obj;
		}
	}

	//This gets the HTML to returns it to the menu
	if ( ! empty( $menu_items ) ) {
		$args = array(
			'after' => '',
			'before' => '',
			'link_after' => '',
			'link_before' => '',
			'walker' => new Walker_Nav_Menu_Edit,
		);
		echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
	}

	//Finally don't forget to exit
	exit;
}
	
function setup_archive_item($menu_item){
	if($menu_item->type !='post_type_archive')
		return $menu_item;

	$post_type = $menu_item->object;
	$menu_item->url =get_post_type_archive_link($post_type);

	return $menu_item;
}

function maybe_make_current($items){
	foreach ($items as $item){
		if('post_type_archive' != $item->type)
			continue;

		$post_type = $item->object;
		if(!is_post_type_archive($post_type)&& !is_singular($post_type))
			continue;

		//Make item current
		$item->current = true;
		$item->classes[] = 'current-menu-item';

		//Get menu item's ancestors:
		$_anc_id = (int) $item->db_id;
		$active_ancestor_item_ids=array();

		while(( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) && ! in_array( $_anc_id, $active_ancestor_item_ids )  ){
			$active_ancestor_item_ids[] = $_anc_id;
		}

		//Loop through ancestors and give them 'ancestor' or 'parent' class
		foreach ($items as $key=>$parent_item){
			$classes = (array) $parent_item->classes;

			//If menu item is the parent
			if ($parent_item->db_id == $item->menu_item_parent ) {
				$classes[] = 'current-menu-parent';
				$items[$key]->current_item_parent = true;
			}

			//If menu item is an ancestor
			if ( in_array(  intval( $parent_item->db_id ), $active_ancestor_item_ids ) ) {
				$classes[] = 'current-menu-ancestor';
				$items[$key]->current_item_ancestor = true;
			}

			$items[$key]->classes = array_unique( $classes );
		}
	}
	return $items;
}



/* Register Menus
=================================================== */
function sitemap( $menu_name, $columns = 6 ){
	$i = 1;

	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );
		$sixth = floor( count($menu_items) / $columns );
		$width = round( 12 / $columns );

		foreach ( (array) $menu_items as $key => $menu_item ) {
			$id = $menu_item->object_id;

			$title = $menu_item->title;
			$url = $menu_item->url;
			$target = $menu_item->target;
			$object = $menu_item->object;

			$parent = $menu_item->menu_item_parent;

			$classes = $menu_item->classes;
			$class = "menu-item menu-item-object-" . $object . " menu-item-type-" . $type . ' menu-item-' . $menu_item->ID;
			$class .= ( menu_current( $id, $url ) )? " " . menu_current( $id, $url ) : "";

			if ($i > $sixth && !$parent){
				$i = 1;
				$r++;
			}

			if ( $i == 1 ) {
				if($r != 0) { $menu_list .= "</ul></div>"; }
				$menu_list .= '<div class="medium-'. $width . ' columns"><ul>';
			}

			if ($parent) {
				if($child) {
					$child = true;
					$menu_list .= "</li>";
				} else {
					$menu_list .= "<ul>";
					$child = true;
				}
			} else {
				if($child && $i != 1){
					$child = false;
					$menu_list .= "</li></ul></li>";
				} else {
					$child = false;
					$menu_list .= "</li>";
				}
			}

			$menu_list .= '<li id="menu-item-' . $menu_item->ID . '" class="'. $class .'"><a href="' . $url . '" target="' . $target . '"><span class="colour" style="background-color: ' . $colour . ';"></span>' . $title . '</a>';
			$i ++;
		}
	} else {
		$menu_list = 'Menu "' . $menu_name . '" not defined.';
	}
	$menu_list .= '</li></ul></div>';
	if( $r < $columns ) $menu_list .= '<div class="medium-'. $width . ' columns"></div>';

	echo $menu_list;
}
?>
