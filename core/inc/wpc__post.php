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

class WPComponent__post
{


	public function WPComponent__save( $post_id ) {


			if( !empty( $_POST['wpc__post__'] )
			&& !empty( $_POST['wpc__template__'] )
			&& !empty( $_POST['wpc__type__'] )
			&& !empty( $_POST['wpc__folder_type__'] )
			&& !empty( $_POST['wpc__folder__'] )
			&& !empty( $_POST['wpc__slug__'] )
			&& !empty( $_POST['metabox__id'] )){

				$wpc__posts 		= $_POST['wpc__post__'];
				$wpc__templates 	= $_POST['wpc__template__'];
				$wpc__folder_types= $_POST['wpc__folder_type__'];
				$wpc__folder 		= $_POST['wpc__folder__'];
				$wpc__types 		= $_POST['wpc__type__'];
				$wpc__files 		= $_POST['wpc__file__'];
				$wpc__slugs 		= $_POST['wpc__slug__'];
				$wpc__metabox 	= $_POST['metabox__id'];
				$wpc__images 		= $_POST['wpc__image__id'];
				$wpc__ID 			= $_POST['wpc__ID'];
				

				if( !empty( $_POST['wpc__title__'] ) ):
					$wpc__titles 		= $_POST['wpc__title__'];
				endif;

				if( !empty( $_POST['wpc__link__'] ) ):
					$wpc__links 		= $_POST['wpc__link__'];
				endif;

				if( !empty( $_POST['wpc__option__'] ) ):
					$wpc__options 	= $_POST['wpc__option__'];
				endif;

				if( !empty( $_POST['wpc__optionnumber'] ) ):
					$wpc__optionsnumber 	= $_POST['wpc__optionnumber'];
				endif;

				if( !empty( $_POST['wpc__optionselect__'] ) ):
					$wpc__optionsselect 	= $_POST['wpc__optionselect__'];
				endif;

				if( !empty( $_POST['wpc__optionswitch__'] ) ):
					$wpc__optionsswitch 	= $_POST['wpc__optionswitch__'];
				endif;

				$user_ID = get_current_user_id();


				if ( false !== wp_is_post_revision( $post_id ) ) {
				    return;
				}

				// Check if our nonce is set.
				if ( ! isset( $_POST['wpcomponent__nonce'] ) ) {
					return;
				}

				// Verify that the nonce is valid.
				if ( ! wp_verify_nonce( $_POST['wpcomponent__nonce'], 'wpc__editor' ) ) {
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

				// if( isset( $wpc__posts ) && count( $wpc__posts ) != 0 ){
				if( isset( $wpc__metabox ) && count( $wpc__metabox ) != 0 ){

					$wpc__structure = new WPComponent__structure();
							
					remove_action( 'save_post', array( $this, 'WPComponent__save' ) );
					// remove_action( 'save_post', array( $this, 'WPComponent__savedata' ) );

					// on a du post alors on y va :)
					// on boucle sur les wpc__post

					$update__content = false;
					$update__meta = true;

					
					$container = '';
					$container__cache = '';
					$file = '';
					$file__cache = '';
					$template = '';
					$template_cache = '';

					$types = [];

					$status = get_post_status( $post_id );

					$i 					= 0;
					$i__title 			= 0;
					$i__option 			= 0;
					$i__optionnumber 	= 0;
					$i__optionselect 	= 0;
					$i__optionswitch 	= 0;
					$i__link 			= 0;
					$i__image 			= 0;

					$metas = [];

					// new
					$key__element = 0;

					foreach ($wpc__metabox as $key__meta => $metabox) {

							$folder_type= $wpc__folder_types[ $key__meta ];
							$folder 	= $wpc__folder[ $key__meta ];
							$file 		= $wpc__files[ $key__meta ];
							$template 	= $wpc__templates[ $key__meta ];
							unset($meta__content);

							// on récupère la structure de la metabox grace au nom du fichier
							$metabox__structure = $wpc__structure->WPComponent__get__fileStructure( $folder_type, $folder, $file );

							// pour chaque element de la structure on retrouve sa data
							// les elements sont théoriquement dans l'ordre.

							foreach( $metabox__structure as $key => $element ):

								// $key__element représente le numéro de l'element dans la page

								// on indique que par défaut ce n'est pas un update
								$update__content = false;

								// on regénere les data du post
								$wpc__newpost = array(
								  	'post_status'    	=> $status
								  	,'post_type'      	=> 'wpc__content'
								  	,'ping_status'    	=> 'closed'
								  	,'post_author'		=> $user_ID
								  	,'comment_status' 	=> 'closed'
								);

								// s'il s'agit d'un update
								$keyTrimed = trim($wpc__ID[ $key__element ]);
								if( !empty( $keyTrimed ) ){

									// on indique à wordpress un ID pour signifier d'updater
									$wpc__newpost['ID'] = $wpc__ID[ $key__element ];
									$update__content = true;

								}


								// gestion du contenu en fonction du type
								switch ( $element->type ) {
									case 'image':
										$wpc__newpost['post_content'] = $wpc__images[ $i__image ];
										$i__image++;
										break;

									case 'editor':
										$wpc__newpost['post_content'] = $_POST[ $wpc__posts[ $key__element ] ];
										break;

									case 'title':
										$wpc__newpost['post_content'] = $wpc__titles[ $i__title ];
										$i__title++;
										break;

									case 'link':
										$wpc__newpost['post_content'] = $wpc__links[ $i__link ];
										$i__link++;
										break;

									case 'option':
										// ici les valeurs sont correctes
										$wpc__newpost['post_content'] = $wpc__options[ $i__option ];
										$i__option++;
										break;

									case 'option-number':
										// ici les valeurs sont correctes
										$wpc__newpost['post_content'] = $wpc__optionsnumber[ $i__optionnumber ];
										$i__optionnumber++;
										break;

									case 'option-switch':
										// ici les valeurs sont correctes
										$wpc__newpost['post_content'] = $wpc__optionsswitch[ $i__optionswitch ];
										$i__optionswitch++;
										break;

									case 'option-select':
										// ici les valeurs sont correctes
										$wpc__newpost['post_content'] = $wpc__optionsselect[ $i__optionselect ];
										$i__optionselect++;
										break;
								}


								if( $update__content === false){
									$wpc__id = wp_insert_post( $wpc__newpost );
									$update__meta = true;


								}else{
									wp_update_post( $wpc__newpost );
									$wpc__id = $wpc__newpost['ID'];
								}



								$meta__content[] = array(
									'ID' => $wpc__id,
									'type' => $element->type,
									'slug' => $element->slug
								);

								$key__element++;

							endforeach;


							$metas[] = array( 
								'file' 			=> $file, 
								'folder_type' 	=> $folder_type, 
								'folder' 		=> $folder, 
								'template' 		=> $template, 
								'container' 	=> $metabox, 
								'content' 		=> $meta__content
							);



					}

					if( $update__meta === true ):
						// il y a eu un nouvel enregistrement

						update_post_meta( $post_id, '_wpc_content', $metas );

					else:

						add_post_meta( $post_id, '_wpc_content', $metas, true );

					endif;

				}

			}// fin d'empty

	}

}