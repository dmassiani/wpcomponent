<?php
// ******************************************************
//
// Gestion des contenus
//
// ******************************************************


function get_illustration( $slug = false, $size = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';


	foreach( $wpc_current_wpc['contents'] as $key => $element ){

	    if( $element['slug'] === $slug ){

	        $the_chapter_ID = $element['ID'];

	    }

	}

	if( !empty( $the_chapter_ID ) ):

	    $the_chapter = get_post( $the_chapter_ID );
	    $image = wp_get_attachment_image_src( $the_chapter->post_content, $size );

	    if( $image ) {
	        echo $image[0];
	    }

	endif;
	
}

function the_illustration( $slug = false, $size = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;
	if( $size === false ) $size = 'full';

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		
		$the_chapter = get_post( $the_chapter_ID );
		echo wp_get_attachment_image( $the_chapter->post_content, $size );

	endif;

}

function the_chapter_title( $slug = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the_chapter = get_post( $the_chapter_ID );
		echo $the_chapter->post_content;
	endif;

}

function get_chapter( $slug = false ){

	global $post;
	global $wpc_stories;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	return;
}

function the_chapter( $slug = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the_chapter = get_post( $the_chapter_ID );
		echo apply_filters('the_content', $the_chapter->post_content );
	endif;

}

function the_option( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the_chapter = get_post( $the_chapter_ID );
		if( $echo ){
			echo $the_chapter->post_content;
		}else{
			return $the_chapter->post_content;
		}
	endif;

}

function the_link( $slug = false, $echo = false ){

	global $post;
	global $wpc_stories;
	global $wpc_current_wpc;

	if( empty( $wpc_stories ) ) define_stories();
	if( empty( $wpc_stories ) ) return;

	if( $slug === false ) return;

	foreach( $wpc_current_wpc['contents'] as $key => $element ){

		if( $element['slug'] === $slug ){

			$the_chapter_ID = $element['ID'];

		}

	}

	if( !empty( $the_chapter_ID ) ):
		$the_chapter = get_post( $the_chapter_ID );
		if( $echo ){
			echo get_permalink( $the_chapter->post_content);
		}else{
			return get_permalink( $the_chapter->post_content);
		}
	endif;

}
