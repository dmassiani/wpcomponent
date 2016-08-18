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


			if( !empty( $_POST['wpcomponent_post_'] ) && is_array($_POST['wpcomponent_post_'])
			&& !empty( $_POST['wpcomponent_template_'] ) && is_array($_POST['wpcomponent_template_'])
			&& !empty( $_POST['wpcomponent_type_'] ) && is_array($_POST['wpcomponent_type_'])
			&& !empty( $_POST['wpcomponent_folder_type_'] ) && is_array($_POST['wpcomponent_folder_type_'])
			&& !empty( $_POST['wpcomponent_folder_'] ) && is_array($_POST['wpcomponent_folder_'])
			&& !empty( $_POST['wpcomponent_slug_'] ) && is_array($_POST['wpcomponent_slug_'])
			&& !empty( $_POST['metabox_id'] ) && is_array($_POST['metabox_id'])){

				$wpc_metabox 		= array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['metabox_id']);
				$wpc_posts 			= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_post_']);
				$wpc_templates 		= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_template_']);
				$wpc_folder_types	= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_folder_type_']);
				$wpc_folder 		= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_folder_']);
				$wpc_types 			= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_type_']);
				$wpc_files 			= array_map( array( "wpcomponent_utility", "sanitizeArrayFilesName" ), $_POST['wpcomponent_file_']);
				$wpc_slugs 			= array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_slug_']);
				$wpc_slug_ID 		= array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_slug_ID']);

				if( !empty($_POST['wpcomponent_image_id'])){
					$wpc_images  = array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_image_id']);
				}

				if( !empty( $_POST['wpcomponent_title_'] ) && is_array($_POST['wpcomponent_title_']) ){
					$wpc_titles = array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_title_']);
				}

				if( !empty( $_POST['wpcomponent_link_'] ) && is_array($_POST['wpcomponent_link_']) ){
					$wpc_links = array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_link_']);
				}

				if( !empty( $_POST['wpcomponent_id_'] ) && is_array($_POST['wpcomponent_id_']) ){
					$wpc_ids = array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_id_']);
				}

				if( !empty( $_POST['wpcomponent_number_'] ) && is_array($_POST['wpcomponent_number_']) ){
					$wpc_numbers = array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_number_']);
				}

				if( !empty( $_POST['wpcomponent_option_'] ) && is_array($_POST['wpcomponent_option_']) ){
					$wpc_options = array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_option_']);
				}

				if( !empty( $_POST['wpcomponent_optionnumber_'] ) && is_array($_POST['wpcomponent_optionnumber_']) ){
					$wpc_optionsnumber = array_map( array( "wpcomponent_utility", "sanitizeArrayInt" ), $_POST['wpcomponent_optionnumber_']);
				}

				if( !empty( $_POST['wpcomponent_optionselect_'] ) && is_array($_POST['wpcomponent_optionselect_']) ){
					$wpc_optionsselect = array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_optionselect_']);
				}

				if( !empty( $_POST['wpcomponent_optionswitch_'] ) && is_array($_POST['wpcomponent_optionswitch_']) ){
					$wpc_optionsswitch = array_map( array( "wpcomponent_utility", "sanitizeArrayTextFields" ), $_POST['wpcomponent_optionswitch_']);
				}

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
					$i_id 				= 0;
					$i_image 			= 0;
					$i_number 			= 0;
					$i_options 			= 0;
					$i_fields			= 0;

					$metas = [];
					$resultsArray = array();

					// foreach ($metabox_structure as $id => $key) {
					//     $resultsArray[$key] = array(
					//         'wpc_slugs'  => $wpc_slugs[$id],
					//         'metabox_structure' => $metabox_structure[$id]
					//     );
					// }

					// log_it($resultsArray);

					// new
					$key_element = 0;

					foreach ($wpc_metabox as $key_meta => $metabox) {

							$folder_type 	= $wpc_folder_types[ $key_meta ];
							$folder 		= $wpc_folder[ $key_meta ];
							$file 			= $wpc_files[ $key_meta ];
							$template 		= $wpc_templates[ $key_meta ];
							$disable 		= 'off';
							$i_editor		= 0;

							unset($meta_content);

							// on récupère la structure de la metabox grace au nom du fichier
							$metabox_structure = $wpc_structure->wpcomponent_get_fileStructure( $folder_type, $folder, $file );

							$structureData = array_slice($wpc_slugs, $key_element, count($metabox_structure));
							$startKey = $key_element;

							foreach( $metabox_structure as $key => $element ):

								$sectionArrayKey = array_search($element->slug, $structureData);
								$key = $sectionArrayKey + $startKey;

								if( isset( $wpc_slug_ID[ $key ] ) && is_int( $wpc_slug_ID[ $key ] ) ){
									$update_content = false;
								}else{
									$update_content = true;
								}

								// on regénere les data du post
								$wpc_newpost = array();

								// gestion du contenu en fonction du type
								switch ( $element->type ) {
									case 'image':
										$wpc_newpost['post_content'] = $wpc_images[ $i_image ];
										$i_image++;
										$i_fields++;
										break;

									case 'editor':
										$wpc_newpost['post_content'] = wp_kses_post( $_POST[ $wpc_posts[ $key ] ] );
										$i_editor++;
										$i_fields++;
										break;

									case 'title':
										$wpc_newpost['post_content'] = $wpc_titles[ $i_title ];
										$i_title++;
										$i_fields++;
										break;

									case 'link':
										$wpc_newpost['post_content'] = $wpc_links[ $i_link ];
										$i_link++;
										$i_fields++;
										break;

									case 'id':
										$wpc_newpost['post_content'] = $wpc_ids[ $i_id ];
										$i_id++;
										$i_fields++;
										break;

									case 'number':
										$wpc_newpost['post_content'] = $wpc_numbers[ $i_number ];
										$i_number++;
										$i_fields++;
										break;

									case 'option':
										$wpc_newpost['post_content'] = $wpc_options[ $i_option ];
										$i_option++;
										$i_options++;
										break;

									case 'option-number':
										$wpc_newpost['post_content'] = $wpc_optionsnumber[ $i_optionnumber ];
										$i_optionnumber++;
										$i_options++;
										break;

									case 'option-switch':
										if( isset( $wpc_optionsswitch )
											&& isset( $wpc_optionsswitch[ $i_optionswitch ] ) ){
											$wpc_newpost['post_content'] = 'on';
										}else{
											$wpc_newpost['post_content'] = 'off';
										}
										$i_optionswitch++;
										$i_options++;
										break;

									case 'option-select':
										$wpc_newpost['post_content'] = $wpc_optionsselect[ $i_optionselect ];
										$i_optionselect++;
										$i_options++;
										break;
								}


								if( $update_content === false){

									$update_meta = true;
									$slug_id = add_post_meta( $post_id, $element->slug, $wpc_newpost['post_content'] );

								}else{

									$slug_id = $wpc_slug_ID[ $key ];
									$content = $wpc_newpost['post_content'];

									/**
									 * this is an update BUT
									 * I can add many options in template, I need to make this post meta
									 *
									 */
									$meta = $wpdb->query("SELECT meta_id FROM $wpdb->postmeta WHERE meta_id='".$slug_id."'");


									if( $meta == 0 ){

										$slug_id = add_post_meta( $post_id, $element->slug, $content );

									}else{

										$wpdb->query("UPDATE $wpdb->postmeta SET meta_value='".$content."' WHERE meta_id='".$slug_id."'");
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

			}

	}

}
