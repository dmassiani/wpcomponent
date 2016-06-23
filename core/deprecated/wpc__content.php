<?php
// ******************************************************
//
// Gestion des contenus
//
// ******************************************************


function get_illustration( $slug = false, $size = false ){

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
	    $image = wp_get_attachment_image_src( $the__chapter->post_content, $size );

	    if( $image ) {
	        echo $image[0];
	    }

	endif;
	
}

function the_illustration( $slug = false, $size = false ){

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
		echo wp_get_attachment_image( $the__chapter->post_content, $size );

	endif;

}

function the_chapter_title( $slug = false ){

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
		echo $the__chapter->post_content;
	endif;

}

function get_chapter( $slug = false ){

	global $post;
	global $wpc__stories;

	if( empty( $wpc__stories ) ) define_stories();
	if( empty( $wpc__stories ) ) return;

	if( $slug === false ) return;

	return;
}

function the_chapter( $slug = false ){

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
		echo apply_filters('the_content', $the__chapter->post_content );
	endif;

}

function the_option( $slug = false, $echo = false ){

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

function the_link( $slug = false, $echo = false ){

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
