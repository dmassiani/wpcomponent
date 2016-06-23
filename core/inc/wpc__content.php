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

	        $the_chapter_ID = $element['ID'];
	        $type = $element['type'];

	        switch( $type ) {

	        	case 'image' :
	        		return get_wpc_illustration( $slug, $size, $echo );
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
function get_wpc_illustration( $slug = false, $size = false, $echo = false ){

	global $post;
	global $wpc__stories;
	global $wpc__current__wpc;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc__current__wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_ID = $element['ID'];

	    }

	}

	if( !empty( $the_chapter_ID ) ):
		
		$the__chapter = get_post( $the_chapter_ID );
		if( $echo ){
			$image = wp_get_attachment_image_src( $the__chapter->post_content, $size );

			if( $image ) {
			    echo $image[0];
			}
		}else{
			echo wp_get_attachment_image( $the__chapter->post_content, $size );
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

			$the_chapter_ID = $element['ID'];
			break;

		}

	}

	if( !empty( $the_chapter_ID ) ):

		$the__chapter = get_post( $the_chapter_ID );
		$wpc__data = apply_filters('the_content', $the__chapter->post_content );

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

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the__chapter = get_post( $the_chapter_ID );
		if( $echo ){
			echo $the__chapter->post_content;
		}else{
			return $the__chapter->post_content;
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

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the__chapter = get_post( $the_chapter_ID );
		if( $echo ){
			echo $the__chapter->post_content;
		}else{
			return $the__chapter->post_content;
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

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the__chapter = get_post( $the_chapter_ID );
		if( $echo ){
			echo get_permalink( $the__chapter->post_content);
		}else{
			return get_permalink( $the__chapter->post_content);
		}
	endif;

}

function define_stories(){

	global $post;
	global $wpc__stories;

	if( empty( $post ) || !is_numeric( $post->ID ) || is_admin() ) return;

	$metas = get_post_meta( $post->ID, '_wpc_content', true );

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
