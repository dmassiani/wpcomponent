<?php

// ******************************************************
//
// Register ajax
//
// ******************************************************

class wpcomponent_ajax
{


    public function __construct(){

        add_action("wp_ajax_wpcomponent_getNewBox", array( $this, "wpcomponent_getNewBox") );
        add_action("wp_ajax_nopriv_wpcomponent_getNewBox", array( $this, "wpcomponent_getNewBox") );

        add_action("wp_ajax_wpcomponent_deleteElements", array( $this, "wpcomponent_deleteElements") );
        add_action("wp_ajax_nopriv_wpcomponent_deleteElements", array( $this, "wpcomponent_deleteElements") );

        add_action("wp_ajax_wpcomponent_getPosts_byType", array( $this, "wpcomponent_getPosts_byType") );
        add_action("wp_ajax_nopriv_wpcomponent_getPosts_byType", array( $this, "wpcomponent_getPosts_byType") );

        add_action("wp_ajax_wpcomponent_checkup", array( $this, "wpcomponent_checkup") );
        add_action("wp_ajax_nopriv_wpcomponent_checkup", array( $this, "wpcomponent_checkup") );

    }


    // ===================================================================
    // fonction qui retourne le nombre de post mch_content
    // ===================================================================

    public function wpcomponent_getTotalStoryPost(){

        $db = new wpcomponent_database();
        echo $db->total_wpc_content();
        die();

    }

    public function wpcomponent_getPosts_byType(){


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

    public function wpcomponent_getNewBox(){

        $editeur = new wpcomponent_editors();

        $editeur->folder_type = $_POST['type'];
        $editeur->folder = $_POST['folder'];
        $editeur->file = $_POST['file'];
        $editeur->ajax = true;
        $editeur->n_metabox = $_POST['n_metabox'];

        $editeur->getNewBox();

        die();

    }

    // ===================================================================
    // delete all element of metabox
    // ===================================================================

    public function wpcomponent_deleteElements(){

        $remover = new wpcomponent_remover();

        $remover->elements = $_POST['elements'];
        $remover->parent = $_POST['parent'];

        $remover->wpcomponent_remove_elements();

        die();

    }

    // ===================================================================
    // send checkup
    // ===================================================================

    public function wpcomponent_checkup(){

        $checkup = new wpcomponent_checkup();
        $checkup->init();

        die();

    }


}
