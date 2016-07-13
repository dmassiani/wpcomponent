<?php


// ******************************************************
//
// Generation des metabox à l'ouverture d'un update de post
//
// ******************************************************


class wpcomponent_edit
{

	public function __construct(){
		add_action( 'edit_form_after_editor', array( $this, 'wpcomponent_getdata' ) );
	}

	public function wpcomponent_getdata( $post ) {


		// metabox represente les metabox par groupe de template
		$metabox = [];
		$metabox_contenu = [];
		$metabox_structure = []; // structure ID des elements pour la suppression


		// ====================================================================
		//
		// on récupère les templates disponibles
		//
		// ====================================================================
		$wpc_structure = new wpcomponent_structure();
		$editeur = new wpcomponent_editors();
		$editeur->update = true;

		// on récupère les metas
		// Les metas sont tout les champs wpcomponent
		// --------------------------------------------------
		/*

			L'ordre des metas correspond à l'ordre des containers.
			C'est à dire qu'un user peut tout à fait modifier l'ordre 
			en inversant les metabox.

		*/
		// --------------------------------------------------
		$data = get_post_meta( $post->ID, '_wpcomponent_structure', true );
		// global $post;



		if( ! empty( $data ) ):

			$editeur->postID = $post->ID;


			foreach ($data as $key => $metabox):

				// --------------------------------------------------
				/*
					Les variables globales nécessaires aux métabox
					globales = pour la metabox encapsulante
				*/
				// --------------------------------------------------
				$editeur->template 		= $metabox['template'];
				$editeur->folder_type 	= $metabox['folder_type'];
				$editeur->folder 		= $metabox['folder'];
				$editeur->file 			= $metabox['file'];
				$editeur->disable 		= $metabox['disable'];
				$editeur->options 		= null;

				// --------------------------------------------------
				/*
					$fileSlugs représente la structure SLUG du fichier concerné
				*/
				// --------------------------------------------------		
				$fileStructure 		= $wpc_structure->wpcomponent_getFileSlugs( 
					$metabox['folder_type'], 
					$metabox['folder'], 
					$metabox['file'] 
				); 

				// --------------------------------------------------
				/*
					Metabox représente les données d'une metabox WPComponent
				*/
				// --------------------------------------------------

				$dataContent 	= $metabox['content'];
				$container 		= 1000 * ( $key + 1 );
				$contentStructure = [];

				foreach ($metabox['content'] as $i => $content):

					$contentStructure[ $content['slug'] ] = [
						// 'ID' 			=> $content['ID'],
						'slug_ID' 		=> $content['slug_ID'],
						'type' 			=> $content['type']
					];

				endforeach;

				// --------------------------------------------------
				/*
					On ouvre une nouvelle metabox
				*/
				// --------------------------------------------------
				$editeur->openMetaBox( $key );

			
				// --------------------------------------------------
				/*
					Pour chaque slug du fichier on va chercher la data correspondante.
				*/
				// --------------------------------------------------

				foreach( $fileStructure as $keyS => $slug ):


					// --------------------------------------------------
					/*
						Les variables des champs
					*/
					// --------------------------------------------------

					$editeur->slug 				= $slug;
					$editeur->container_id 		= "wpc_editor_" . ( $container + $keyS + 1 );
					$editeur->name 				= $wpc_structure->wpcomponent_getNameFileSlug( $editeur->folder_type, $editeur->folder, $editeur->file, $editeur->slug );
					$slugType 					= $wpc_structure->wpcomponent_slugType( $editeur->folder_type, $editeur->folder, $editeur->file, $editeur->slug );


					$dataID = false;
					$dataType = false;
					$dataI = false;
					$currentSlug = NULL;

					/**
					 * Reset des datas
					 *
					 */
					$editeur->content 		= '';
					$editeur->slug_ID = '';


					if( isset( $contentStructure[ $slug ] ) ){

						// $post = get_post( $contentStructure[ $slug ]['ID'] );


						$data = get_metadata_by_mid ( 'post' , $contentStructure[ $slug ]['slug_ID'] );

						// $editeur->ID 			= $post->ID;
						$editeur->slug_ID 		= $contentStructure[ $slug ]['slug_ID'];
						$editeur->content 		= $data->meta_value;
						$metabox_structure[] 	= $contentStructure[ $slug ]['slug_ID'];
						$editeur->elementsRemove = implode(',', $metabox_structure);

					}
					// else : new fields : no structure, no ID


					switch ( $slugType ) {
						case 'image':
							$editeur->images_id = $editeur->content;
							$editeur->getNewImage();
							break;

						case 'editeur':
							$editeur->getNewEditor();
							break;

						case 'title':
							$editeur->getNewTitle();
							break;

						case 'link':
							$editeur->getNewLink();
							break;

						case 'number':
							$editeur->getNewNumber();
							break;

						case 'option':
							$editeur->getNewOption();
							break;

						case 'option-number':
							$editeur->getNewOption('number');
							break;

						case 'option-switch':
							$editeur->getNewOption('switch');
							break;

						case 'option-select':
							$editeur->getNewOption('select');
							break;

						default:
							$editeur->getNewEditor();
					}

				endforeach;

				// --------------------------------------------------
				/*
					On ferme la metabox
				*/
				// --------------------------------------------------
				$editeur->closeMetaBox();
				$metabox_structure = [];
					

			endforeach;

		endif;


	}

}