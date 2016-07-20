<?php

// ******************************************************
//
// Remover des contenus
//
// ******************************************************

class wpcomponent_remover
{


    public $elements;
    public $parent;
    public $meta_elements;

	public function wpcomponent_remove_elements() {

		global $wpdb;

		if( !empty( $this->elements ) ){

			$elements = explode( ',', trim(urldecode($this->elements)) );

			foreach ($elements as $key => $element) {

				/**
				 * $element représente l'id du meta à supprimer
				 *
				 */
				$meta = get_metadata_by_mid ( 'post' , $element );

			    // [meta_id] => 1435
			    // [post_id] => 1441
			    // [meta_key] => anchor
			    // [meta_value] => anchor test

				$wpdb->query(
					$wpdb->prepare(
						"
				        DELETE FROM $wpdb->postmeta
						WHERE post_id = %d
						AND meta_id = %s
						",
					    $meta->post_id, $meta->meta_id
				        )
				);

			}

			$this->meta_elements;
			$this->wpcomponent_update_parentMeta();

			echo 'done';

		}// fin d'empty

	}

	public function wpcomponent_update_parentMeta(){

		$post_id = $this->parent;
		// on retrouve la meta pour le post
		$metas = get_post_meta( $post_id, '_wpcomponent_structure', true );
		$elements = explode( ',', trim(urldecode($this->elements)) );

		// pour chaque metabox
		foreach ($metas as $key_metabox => $metabox):

			$contents = $metabox['content'];
			$log_key = $key_metabox;

			// on retrouve la partie contenu :

			// pour chaque contenu :
			foreach ($contents as $key_content => $content):
				// si l'id est égale à un content('id)')

				foreach ($elements as $key_element => $id):

					if( (int) $id === (int) $content['slug_ID'] ){
						$remover_key = $log_key;
					}

				endforeach;


			endforeach;
		endforeach;

		unset($metas[$remover_key]);
		$metas = array_values($metas);
		foreach ($metas as $key_metabox => $metabox):
			$metas[$key_metabox]['container'] = ( $key_metabox + 1 ) * 1000;
		endforeach;

		// pour chaque meta on reinitialise le code metabox

		// on update les metas
		update_post_meta( $post_id, '_wpcomponent_structure', $metas );

		echo 'done';
	}

}
