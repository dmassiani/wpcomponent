<?php
class wpcomponent_database
{

	public function __construct() {

	}

	public function wpcomponent_update_210() {

		// rename _wpc_structure to _wpcomponent_structure
		global $wpdb;
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key='_wpcomponent_structure' WHERE meta_key='_wpc_structure'");

	}

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
