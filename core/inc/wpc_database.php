<?php
class wpcomponent_database
{


	public function total_wpcomponent_content( $post_ID ){

		$args = array(
			'post_type'  	=> 'WPC_content'
			,'order_by'		=> 'ID'
			,'order'		=> 'ASC'
			,'post_parent'	=> $post_ID			
			,'posts_per_page'=>-1
			,'meta_key'		=> 'wpc_template'
		);
		$wpc_query = new WP_Query( $args );

		echo $wpc_query->found_posts;

	}


}