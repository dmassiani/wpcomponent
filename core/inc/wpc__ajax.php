<?php

// ******************************************************
//
// Register ajax
//
// ******************************************************

class WPComponent__ajax
{


    public function __construct(){

        add_action("wp_ajax_WPComponent__getNewBox", array( $this, "WPComponent__getNewBox") );
        add_action("wp_ajax_nopriv_WPComponent__getNewBox", array( $this, "WPComponent__getNewBox") );

        add_action("wp_ajax_WPComponent__deleteElements", array( $this, "WPComponent__deleteElements") );
        add_action("wp_ajax_nopriv_WPComponent__deleteElements", array( $this, "WPComponent__deleteElements") );

        add_action("wp_ajax_WPComponent__getPosts_byType", array( $this, "WPComponent__getPosts_byType") );
        add_action("wp_ajax_nopriv_WPComponent__getPosts_byType", array( $this, "WPComponent__getPosts_byType") );

        add_action("wp_ajax_WPComponent__checkup", array( $this, "WPComponent__checkup") );
        add_action("wp_ajax_nopriv_WPComponent__checkup", array( $this, "WPComponent__checkup") );

    }


    // ===================================================================
    // fonction qui retourne le nombre de post mch__content
    // ===================================================================

    public function WPComponent__getTotalStoryPost(){

        $db = new WPComponent__database();
        echo $db->total__wpc__content();
        die();

    }

    public function WPComponent__getPosts_byType(){


        $type = $_POST['type'];

        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'name',
            'order'            => 'ASC',
            'post_type'        => $type,
            'post_status'      => 'publish'
        );
        $posts_array = get_posts( $args ); 

        $options = '<option>'. __('Select post') .'</option>';

        foreach ( $posts_array as $post_type ) {
            $options.= '<option value="' . $post_type->ID . '">' . $post_type->post_title . '</option>';
        }

        echo $options;

    }

    // ===================================================================
    // fonction qui retourne l'editeur wordpress !
    // ===================================================================

    public function WPComponent__getNewBox(){

        $editeur = new WPComponent__editors();

        $editeur->folder_type = $_POST['type'];
        $editeur->folder = $_POST['folder'];
        $editeur->file = $_POST['file'];
        $editeur->ajax = true;
        $editeur->n__metabox = $_POST['n__metabox'];

        $editeur->getNewBox();

        die();

    }

    // ===================================================================
    // delete all element of metabox
    // ===================================================================

    public function WPComponent__deleteElements(){

        $remover = new WPComponent__remover();

        $remover->elements = $_POST['elements'];
        $remover->parent = $_POST['parent'];

        $remover->WPComponent__remove__elements();

        die();

    }

    // ===================================================================
    // send checkup
    // ===================================================================

    public function WPComponent__checkup(){

        $checkup = new WPComponent__checkup();
        $checkup->init();

        die();

    }


}
