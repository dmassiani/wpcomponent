<?php

// ******************************************************
//
// Extract manager
//
// ******************************************************

/**
 * Extract the_wpcomponent index the content
 *
 *
 */
function the_wpcomponent_inside_the_content( $content ) {
	if( get_option( 'wpcomponent_setting_enable_inside_the_content' ) == 'true' ){	
		ob_start();
		get_wpc();
		$wpcomponent_content = ob_get_contents();
		ob_end_clean();
		return $content . $wpcomponent_content;
	}else{
		return $content;
	}
}
add_filter( 'the_content', 'the_wpcomponent_inside_the_content', 6 );


/**
 * extract component
 * $slug is string
 * $size for image
 * $echo is for when we need to return image or src
 */
function the_wpcomponent( $slug = false, $size = false, $echo = false ) {
	echo get_wpcomponent( $slug, $size, $echo );
}

function get_wpcomponent( $slug = false, $size = false, $echo = true ) {

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc_current_wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_slugID = $element['slug_ID'];

	        $type = $element['type'];

	        switch( $type ) {

	        	case 'image' :
	        		$wpcomponent_content = get_wpc_illustration( $slug, $size, $echo );
	        		break;

	        	case 'editor' :
	        		$wpcomponent_content = get_wpc_chapter( $slug  );
	        		break;

	        	case 'title' :
	        		$wpcomponent_content = get_wpc_title( $slug );
	        		break;

	        	case 'option' :
	        		$wpcomponent_content = get_wpc_option( $slug  );
	        		break;

	        	case 'link' :
	        		$wpcomponent_content = get_wpc_link( $slug  );
	        		break;

	        }

	        break;

	    }

	}

	return $wpcomponent_content;


}


// ******************************************************
//
// Gestion des contenus
//
// ******************************************************

/**
 * get illustration
 *
 *
 */
function get_wpc_illustration( $slug = false, $size = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc_current_wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_slugID = $element['slug_ID'];
	        break;

	    }

	}

	if( !empty( $the_chapter_slugID ) ):

		$the_chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );


		if( $echo ){
			
			$image = wp_get_attachment_image_src( $the_chapter->meta_value, $size );

			if( $image ) {
			    return $image[0];
			}

		}else{
			return wp_get_attachment_image( $the_chapter->meta_value, $size );
		}

	endif;
	
}

/**
 * get chapter
 *
 *
 */
function get_wpc_chapter( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):

		$data = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		// $wpc_data = apply_filters('the_content', $data->meta_value );
		$wpc_data = wpautop( $data->meta_value );

		if( $echo ){
			return $wpc_data;
		}else{
			return $wpc_data;
		}

	endif;

}

/**
 * get title
 *
 *
 */
function get_wpc_title( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}



	if( !empty( $the_chapter_slugID ) ):
		$data = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			return $data->meta_value;
		}else{
			return $data->meta_value;
		}
	endif;

}

/**
 * get option
 *
 *
 */
function get_wpc_option( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):
		$the_chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			return $the_chapter->meta_value;
		}else{
			return $the_chapter->meta_value;
		}
	endif;

}

/**
 * get link
 *
 *
 */
function get_wpc_link( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):
		$the_chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			return get_permalink( $the_chapter->meta_value);
		}else{
			return get_permalink( $the_chapter->meta_value);
		}
	endif;

}

function define_wpc_stories(){

	global $post;
	global $wpc_stories;

	if( empty( $post ) || !is_numeric( $post->ID ) || is_admin() ) return;

	/**
	 * @since 2.0.1
	 * update _wpc_content to _wpcomponent_structure
	 * upgrade use _wpcomponent_structure
	 *
	 *
	 */
	$metas = get_post_meta( $post->ID, '_wpc_content', true );

	if ( empty( $metas ) ):
		$metas = get_post_meta( $post->ID, '_wpcomponent_structure', true );
	endif;

	if( ! empty( $metas ) ):

		$wpc_stories = [];

		foreach ($metas as $key => $metabox):

			$file	  		= $metas[ $key ]['file'];
			$folder_type	= $metas[ $key ]['folder_type'];
			$folder	  		= $metas[ $key ]['folder'];
			$disable 		= $metas[ $key ]['disable'];
			$contents 		= $metas[ $key ]['content'];

			if( $disable === 'off' ){				
				$wpc_stories[] = array(
					'folder_type' 		=> $folder_type,
					'folder' 			=> $folder,
					'file' 				=> $file,
					'contents' 			=> $contents
				);
			}

		endforeach;

	endif;

}


// fonction public d'affichage des histoires complètes
function the_wpc() {

	echo get_wpc();

}
// fonction public d'affichage des histoires complètes
function get_wpc() {

	// after custom query perhaps we need to reset global post
	wp_reset_postdata();
	
	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;
	$wpcomponent_content = '';

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;

	foreach ($wpc_stories as $key => $wpc):


		$wpc_current_wpc = $wpc;

    	if( $wpc['folder_type'] === 'plugin' ){
    		$folder = WPCOMPONENT_DEFAULT_TEMPLATE .'/'. $wpc['folder'];
    	}else{
    		// $folder = get_template_directory() . '/' . WPCOMPONENT_FOLDER .'/'. $wpc['folder'];
    		$folder = get_stylesheet_directory() . '/' . WPCOMPONENT_FOLDER .'/'. $wpc['folder'];
    	}

		// remove php extension
		$info = pathinfo( $folder .'/'. $wpc['file'] );
		$name = $info['filename'];
		$folder = $wpc['folder'];
		$folder_type = $wpc['folder_type'];

		$wpcomponent_content.= get_wpcomponent_template( $name, $folder, $folder_type );

	endforeach;

	return $wpcomponent_content;

}

function get_wpcomponent_template( $wpc_name, $folder, $folder_type ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_wpc_stories();
	if( empty( $wpc_stories ) ) return;
	if( empty( $wpc_name ) ) return;


	if( $folder_type === 'theme' ){

		return get_template_part(  'wpcomponent/' . $folder .'/'. $wpc_name );
	}else{

		return wpcomponent_locate_template( $wpc_name, WPCOMPONENT_DIR . '/templates/' . $folder .'/' );
	}

}

// extra function to load template in plugin folder
/**
*Extend WP Core get_template_part() function to load files from the within Plugin directory defined by PLUGIN_DIR_PATH constant
* * Load the page to be displayed 
* from within plugin files directory only 
* * @uses wpcomponent_load_template() function 
* * @param $slug * @param null $name 
*/ 

function wpcomponent_locate_template($slug, $location,  $name = null) {

	do_action("wpcomponent_locate_template_{$slug}", $slug, $name);

	$templates = array();
	if (isset($name))
	    $templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	wpcomponent_load_template($templates, $location, true, false);

}

/* Extend locate_template from WP Core 
* Define a location of your plugin file dir to a constant in this case = PLUGIN_DIR_PATH 
* Note: PLUGIN_DIR_PATH - can be any folder/subdirectory within your plugin files 
*/ 

function wpcomponent_load_template($template_names, $location, $load = false, $require_once = true ) 
{ 
	$located = ''; 
	foreach ( (array) $template_names as $template_name ) { 
		if ( !$template_name ) continue; 

		/* search file within the PLUGIN_DIR_PATH only */ 
		if ( file_exists( $location . $template_name)) { 
			$located = $location . $template_name; 
			break; 
		} 
	}

	if ( $load && '' != $located )
	    load_template( $located, $require_once );

	return $located;
}
