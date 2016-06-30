<?php

// ******************************************************
//
// Register ajax
//
// ******************************************************

class WPComponent_ajax
{


    public function __construct(){

        add_action("wp_ajax_WPComponent_getNewBox", array( $this, "WPComponent_getNewBox") );
        add_action("wp_ajax_nopriv_WPComponent_getNewBox", array( $this, "WPComponent_getNewBox") );

        add_action("wp_ajax_WPComponent_deleteElements", array( $this, "WPComponent_deleteElements") );
        add_action("wp_ajax_nopriv_WPComponent_deleteElements", array( $this, "WPComponent_deleteElements") );

        add_action("wp_ajax_WPComponent_getPosts_byType", array( $this, "WPComponent_getPosts_byType") );
        add_action("wp_ajax_nopriv_WPComponent_getPosts_byType", array( $this, "WPComponent_getPosts_byType") );

        add_action("wp_ajax_WPComponent_checkup", array( $this, "WPComponent_checkup") );
        add_action("wp_ajax_nopriv_WPComponent_checkup", array( $this, "WPComponent_checkup") );

    }


    // ===================================================================
    // fonction qui retourne le nombre de post mch_content
    // ===================================================================

    public function WPComponent_getTotalStoryPost(){

        $db = new WPComponent_database();
        echo $db->total_wpc_content();
        die();

    }

    public function WPComponent_getPosts_byType(){


        $type = $_POST['type'];

        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'name',
            'order'            => 'ASC',
            'post_type'        => $type,
            'post_status'      => 'publish'
        );
        $posts_array = get_posts( $args ); 

        $options = '<option>'. __('Select post', 'wpcomponent') .'</option>';

        foreach ( $posts_array as $post_type ) {
            $options.= '<option value="' . $post_type->ID . '">' . $post_type->post_title . '</option>';
        }

        echo $options;

    }

    // ===================================================================
    // fonction qui retourne l'editeur wordpress !
    // ===================================================================

    public function WPComponent_getNewBox(){

        $editeur = new WPComponent_editors();

        $editeur->folder_type = $_POST['type'];
        $editeur->folder = $_POST['folder'];
        $editeur->file = $_POST['file'];
        $editeur->ajax = true;
        $editeur->n__metabox = $_POST['n_metabox'];

        $editeur->getNewBox();

        die();

    }

    // ===================================================================
    // delete all element of metabox
    // ===================================================================

    public function WPComponent_deleteElements(){

        $remover = new WPComponent_remover();

        $remover->elements = $_POST['elements'];
        $remover->parent = $_POST['parent'];

        $remover->WPComponent_remove_elements();

        die();

    }

    // ===================================================================
    // send checkup
    // ===================================================================

    public function WPComponent_checkup(){

        $checkup = new WPComponent_checkup();
        $checkup->init();

        die();

    }


}
