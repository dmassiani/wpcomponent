<?php

// ******************************************************
//
// Extract manager
//
// ******************************************************


function the_component( $slug = false, $size = false ) {

	return get_component( $slug, true, $size );

}

function get_component( $slug = false, $echo = false, $size = false ) {

	global $post;
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc__current__wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_slugID = $element['slug_ID'];

	        $type = $element['type'];

	        switch( $type ) {

	        	case 'image' :
	        		return get_wpc_illustration( $slug, $echo, $size );
	        		break;

	        	case 'editor' :
	        		return get_wpc_chapter( $slug, $echo );
	        		break;

	        	case 'title' :
	        		return get_wpc_title( $slug, $echo );
	        		break;

	        	case 'option' :
	        		return get_wpc_option( $slug, $echo );
	        		break;

	        	case 'link' :
	        		return get_wpc_link( $slug, $echo );
	        		break;

	        }

	        break;

	    }

	}

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
function get_wpc_illustration( $slug = false, $echo = false, $size = false ){

	global $post;
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc__current__wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_slugID = $element['slug_ID'];
	        break;

	    }

	}

	if( !empty( $the_chapter_slugID ) ):

		$the__chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );


		if( $echo ){
			
			echo wp_get_attachment_image( $the__chapter->meta_value, $size );

		}else{
			$image = wp_get_attachment_image_src( $the__chapter->meta_value, $size );

			if( $image ) {
			    echo $image[0];
			}
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
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc__current__wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):

		$data = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		$wpc__data = apply_filters('the_content', $data->meta_value );

		if( $echo ){
			echo $wpc__data;
		}else{
			return $wpc__data;
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
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc__current__wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}



	if( !empty( $the_chapter_slugID ) ):
		$data = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			echo $data->meta_value;
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
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc__current__wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):
		$the__chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			echo $the__chapter->meta_value;
		}else{
			return $the__chapter->meta_value;
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
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc__current__wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_slugID = $element['slug_ID'];
			break;

		}

	}

	if( !empty( $the_chapter_slugID ) ):
		$the__chapter = get_metadata_by_mid ( 'post' , $the_chapter_slugID );
		if( $echo ){
			echo get_permalink( $the__chapter->meta_value);
		}else{
			return get_permalink( $the__chapter->meta_value);
		}
	endif;

}

function define_stories(){

	global $post;
	global $wpc__stories;

	if( empty( $post ) || !is_numeric( $post->ID ) || is_admin() ) return;

	/**
	 * @since 2.0.1
	 * update _wpc_content to _wpc_structure
	 * upgrade use _wpc_structure
	 *
	 *
	 */

	$metas = get_post_meta( $post->ID, '_wpc_content', true );
	if ( empty( $metas ) ):
		$metas = get_post_meta( $post->ID, '_wpc_structure', true );
	endif;

	if( ! empty( $metas ) ):

		$wpc__stories = [];

		foreach ($metas as $key => $metabox):

			$file	  		= $metas[ $key ]['file'];
			$folder_type	= $metas[ $key ]['folder_type'];
			$folder	  		= $metas[ $key ]['folder'];
			$contents 		= $metas[ $key ]['content'];

			$wpc__stories[] = array(
				'folder_type' 		=> $folder_type,
				'folder' 			=> $folder,
				'file' 				=> $file,
				'contents' 			=> $contents
			);

		endforeach;

	endif;

}


// fonction public d'affichage des histoires complètes
function the_wpc() {

	// after custom query perhaps we need to reset global post
	wp_reset_postdata();
	
	global $post;
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	foreach ($wpc__stories as $key => $wpc):


		$wpc__current__wpc = $wpc;

    	if( $wpc['folder_type'] === 'plugin' ){
    		$folder = WPC_DEFAULT_TEMPLATE .'/'. $wpc['folder'];
    	}else{
    		// $folder = get_template_directory() . '/' . WPC_FOLDER .'/'. $wpc['folder'];
    		$folder = get_stylesheet_directory() . '/' . WPC_FOLDER .'/'. $wpc['folder'];
    	}

		// remove php extension
		$info = pathinfo( $folder .'/'. $wpc['file'] );
		$name = $info['filename'];
		$folder = $wpc['folder'];
		$folder_type = $wpc['folder_type'];

		echo get_wpc_template( $name, $folder, $folder_type );

	endforeach;

}

function get_wpc_template( $wpc__name, $folder, $folder_type ){

	global $post;
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;
	if( empty( $wpc__name ) ) return;


	if( $folder_type === 'theme' ){

		return get_template_part(  'wpcomponent/' . $folder .'/'. $wpc__name );
	}else{

		return wpcomponent__locate__template( $wpc__name, WPC_DIR . '/templates/' . $folder .'/' );
	}

}

// extra function to load template in plugin folder
/**
*Extend WP Core get_template_part() function to load files from the within Plugin directory defined by PLUGIN_DIR_PATH constant
* * Load the page to be displayed 
* from within plugin files directory only 
* * @uses wpcomponent__load__template() function 
* * @param $slug * @param null $name 
*/ 

function wpcomponent__locate__template($slug, $location,  $name = null) {

	do_action("wpcomponent__locate__template_{$slug}", $slug, $name);

	$templates = array();
	if (isset($name))
	    $templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	wpcomponent__load__template($templates, $location, true, false);

}

/* Extend locate_template from WP Core 
* Define a location of your plugin file dir to a constant in this case = PLUGIN_DIR_PATH 
* Note: PLUGIN_DIR_PATH - can be any folder/subdirectory within your plugin files 
*/ 

function wpcomponent__load__template($template_names, $location, $load = false, $require_once = true ) 
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
