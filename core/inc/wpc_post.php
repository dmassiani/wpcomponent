<?php

// ******************************************************
//
// Enregistrement et update d'un post
//
// ******************************************************

/*

au premier enregistrement
wordpress créé deux enregistrement, un post de type publish, un autre de type revision

*/

class wpcomponent_post
{


	public function wpcomponent_save( $post_id ) {


			if( !empty( $_POST['wpcomponent_post_'] )
			&& !empty( $_POST['wpcomponent_template_'] )
			&& !empty( $_POST['wpcomponent_type_'] )
			&& !empty( $_POST['wpcomponent_folder_type_'] )
			&& !empty( $_POST['wpcomponent_folder_'] )
			&& !empty( $_POST['wpcomponent_slug_'] )
			&& !empty( $_POST['metabox_id'] )){

				$wpc_metabox 		= $_POST['metabox_id'];
				$wpc_posts 			= $_POST['wpcomponent_post_'];
				$wpc_templates 		= $_POST['wpcomponent_template_'];
				$wpc_folder_types	= $_POST['wpcomponent_folder_type_'];
				$wpc_folder 		= $_POST['wpcomponent_folder_'];
				$wpc_types 			= $_POST['wpcomponent_type_'];
				$wpc_files 			= $_POST['wpcomponent_file_'];
				$wpc_slugs 			= $_POST['wpcomponent_slug_'];
				$wpc_images 		= $_POST['wpcomponent_image_id'];
				$wpc_slug_ID 		= $_POST['wpcomponent_slug_ID'];

				// if( !empty( $_POST['wpcomponent_disable_'] ) ):
				// 	$wpc_disables = $_POST['wpcomponent_disable_'];
				// endif;

				if( !empty( $_POST['wpcomponent_title_'] ) ):
					$wpc_titles = $_POST['wpcomponent_title_'];
				endif;

				if( !empty( $_POST['wpcomponent_link_'] ) ):
					$wpc_links = $_POST['wpcomponent_link_'];
				endif;

				if( !empty( $_POST['wpcomponent_option_'] ) ):
					$wpc_options = $_POST['wpcomponent_option_'];
				endif;

				if( !empty( $_POST['wpcomponent_optionnumber'] ) ):
					$wpc_optionsnumber 	= $_POST['wpcomponent_optionnumber'];
				endif;

				if( !empty( $_POST['wpcomponent_optionselect_'] ) ):
					$wpc_optionsselect 	= $_POST['wpcomponent_optionselect_'];
				endif;

				if( !empty( $_POST['wpcomponent_optionswitch_'] ) ):
					$wpc_optionsswitch 	= $_POST['wpcomponent_optionswitch_'];
				endif;

				$user_ID = get_current_user_id();


				if ( false !== wp_is_post_revision( $post_id ) ) {
				    return;
				}

				// Check if our nonce is set.
				if ( ! isset( $_POST['wpcomponent_nonce'] ) ) {
					return;
				}

				// Verify that the nonce is valid.
				if ( ! wp_verify_nonce( $_POST['wpcomponent_nonce'], 'wpc_editor' ) ) {
					return;
				}

				// If this is an autosave, our form has not been submitted, so we don't want to do anything.
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
				}


				// Check the user's permissions.
				if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

					if ( ! current_user_can( 'edit_page', $post_id ) ) {
						return;
					}

				} else {

					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return;
					}
				}

				// if( isset( $wpc_posts ) && count( $wpc_posts ) != 0 ){
				if( isset( $wpc_metabox ) && count( $wpc_metabox ) != 0 ){

					$wpc_structure = new wpcomponent_structure();
							
					remove_action( 'save_post', array( $this, 'wpcomponent_save' ) );

					global $wpdb;


					$update_content = false;
					$update_meta = true;

					
					$container = '';
					$container_cache = '';
					$file = '';
					$file_cache = '';
					$template = '';
					$template_cache = '';

					$types = [];

					$status = get_post_status( $post_id );

					$i 					= 0;
					$i_title 			= 0;
					$i_option 			= 0;
					$i_optionnumber 	= 0;
					$i_optionselect 	= 0;
					$i_optionswitch 	= 0;
					$i_link 			= 0;
					$i_image 			= 0;

					$metas = [];

					// new
					$key_element = 0;

					foreach ($wpc_metabox as $key_meta => $metabox) {

							$folder_type= $wpc_folder_types[ $key_meta ];
							$folder 	= $wpc_folder[ $key_meta ];
							$file 		= $wpc_files[ $key_meta ];
							$template 	= $wpc_templates[ $key_meta ];
							$disable = 'off';
							unset($meta_content);

							// on récupère la structure de la metabox grace au nom du fichier
							$metabox_structure = $wpc_structure->wpcomponent_get_fileStructure( $folder_type, $folder, $file );

							// pour chaque element de la structure on retrouve sa data
							// les elements sont théoriquement dans l'ordre.

							foreach( $metabox_structure as $key => $element ):

								// $key_element représente le numéro de l'element dans la page

								// on indique que par défaut ce n'est pas un update
								$update_content = false;

								// on regénere les data du post
								$wpc_newpost = array(
								  	'post_status'    	=> $status
								  	,'post_type'      	=> 'wpc_content'
								  	,'ping_status'    	=> 'closed'
								  	,'post_author'		=> $user_ID
								  	,'comment_status' 	=> 'closed'
								);

								// s'il s'agit d'un update
								$keyTrimed = trim($wpc_slug_ID[ $key_element ]);

								if( !empty( $keyTrimed ) ){
									
									// on indique à wordpress un ID pour signifier d'updater
									// $wpc_newpost['ID'] = $wpc_ID[ $key_element ];
									$wpc_newpost['slug_ID'] = $wpc_slug_ID[ $key_element ];
									$update_content = true;

								}

								// gestion du contenu en fonction du type
								switch ( $element->type ) {
									case 'image':
										$wpc_newpost['post_content'] = $wpc_images[ $i_image ];
										$i_image++;
										break;

									case 'editor':
										$wpc_newpost['post_content'] = $_POST[ $wpc_posts[ $key_element ] ];
										break;

									case 'title':
										$wpc_newpost['post_content'] = $wpc_titles[ $i_title ];
										$i_title++;
										break;

									case 'link':
										$wpc_newpost['post_content'] = $wpc_links[ $i_link ];
										$i_link++;
										break;

									case 'option':
										// ici les valeurs sont correctes
										$wpc_newpost['post_content'] = $wpc_options[ $i_option ];
										$i_option++;
										break;

									case 'option-number':
										// ici les valeurs sont correctes
										$wpc_newpost['post_content'] = $wpc_optionsnumber[ $i_optionnumber ];
										$i_optionnumber++;
										break;

									case 'option-switch':
										// ici les valeurs sont correctes
										$wpc_newpost['post_content'] = $wpc_optionsswitch[ $i_optionswitch ];
										$i_optionswitch++;
										break;

									case 'option-select':
										// ici les valeurs sont correctes
										$wpc_newpost['post_content'] = $wpc_optionsselect[ $i_optionselect ];
										$i_optionselect++;
										break;
								}


								if( $update_content === false){

									$update_meta = true;
									$slug_id = add_post_meta( $post_id, $element->slug, $wpc_newpost['post_content'] );

								}else{


									$slug_id = $wpc_newpost['slug_ID'];

									$content = $wpc_newpost['post_content'];

									/**
									 * this is an update BUT
									 * I can add many options in template, I need to make this post meta
									 *
									 */
									$meta = $wpdb->query("SELECT meta_id FROM $wpdb->postmeta WHERE meta_id=$slug_id");
									if( $meta == 0 ){
										$slug_id = add_post_meta( $post_id, $element->slug, $wpc_newpost['post_content'] );
									}else{
										$wpdb->query("UPDATE $wpdb->postmeta SET meta_value='".$content."' WHERE meta_id=$slug_id");
									}

								}


								$meta_content[] = array(
									'type' 		=> $element->type,
									'slug' 		=> $element->slug,
									'slug_ID' 	=> $slug_id
								);

								$key_element++;

							endforeach;

							/**
							 * Disable option
							 *
							 *
							 */
							if( isset($_POST['wpcomponent_disable_' . $wpc_metabox[$key_meta] ] ) 
								&& $_POST['wpcomponent_disable_' . $wpc_metabox[$key_meta] ] === 'on' ){
								$disable = 'on';
							}


							$metas[] = array( 
								'file' 			=> $file, 
								'folder_type' 	=> $folder_type, 
								'folder' 		=> $folder, 
								'template' 		=> $template, 
								'container' 	=> $metabox, 
								'content' 		=> $meta_content,
								'disable'		=> $disable
							);



					}

					if( $update_meta === true ):
						// il y a eu un nouvel enregistrement

						update_post_meta( $post_id, '_wpcomponent_structure', $metas );

					else:

						add_post_meta( $post_id, '_wpcomponent_structure', $metas, true );

					endif;

				}

			}// fin d'empty

	}

}