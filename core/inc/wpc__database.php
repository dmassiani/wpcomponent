<?php
class WPComponent__database
{


	public function total__wpc__content( $post_ID ){

		$args = array(
			'post_type'  	=> 'WPC__content'
			,'order_by'		=> 'ID'
			,'order'		=> 'ASC'
			,'post_parent'	=> $post_ID			
			,'posts_per_page'=>-1
			,'meta_key'		=> 'wpc__template'
		);
		$wpc_query = new WP_Query( $args );

		echo $wpc_query->found_posts;

	}


}