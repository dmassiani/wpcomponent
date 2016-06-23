<?php


// ******************************************************
//
// Generation des metabox à l'ouverture d'un update de post
//
// ******************************************************


class WPComponent__edit
{

	public function __construct(){
		add_action( 'edit_form_after_editor', array( $this, 'WPComponent__getdata' ) );
	}

	public function WPComponent__getdata( $post ) {


		// metabox represente les metabox par groupe de template
		$metabox = [];
		$metabox_contenu = [];
		$metabox__structure = []; // structure ID des elements pour la suppression


		// ====================================================================
		//
		// on récupère les templates disponibles
		//
		// ====================================================================
		$wpc__structure = new WPComponent__structure();
		$editeur = new WPComponent__editors();
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
		$data = get_post_meta( $post->ID, '_wpc_content', true );



		if( ! empty( $data ) ):


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
				$editeur->postID 		= $post->ID;
				$editeur->options 		= null;
				// --------------------------------------------------
				/*
					$fileSlugs représente la structure SLUG du fichier concerné
				*/
				// --------------------------------------------------		
				$fileStructure 		= $wpc__structure->WPComponent__getFileSlugs( 
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
						'ID' => $content['ID'],
						'type' => $content['type']
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
					$editeur->container__id 	= "wpc__editor__" . ( $container + $keyS + 1 );
					$editeur->name 				= $wpc__structure->WPComponent__getNameFileSlug( $editeur->folder_type, $editeur->folder, $editeur->file, $editeur->slug );
					$slugType 					= $wpc__structure->WPComponent__slugType( $editeur->folder_type, $editeur->folder, $editeur->file, $editeur->slug );


					$dataID = false;
					$dataType = false;
					$dataI = false;
					$currentSlug = NULL;


					$editeur->ID 			= '';
					$editeur->content 		= '';


					if( isset( $contentStructure[ $slug ] ) ){

						$post = get_post( $contentStructure[ $slug ]['ID'] );

						$editeur->ID 			= $post->ID;
						$editeur->content 		= $post->post_content;
						$metabox__structure[] 	= $post->ID;
						$editeur->elementsRemove = implode(',', $metabox__structure);

					}

					switch ( $slugType ) {
						case 'image':
							$editeur->images__id = $editeur->content;
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
				$metabox__structure = [];
					

			endforeach;

		endif;


	}

}