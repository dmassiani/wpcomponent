<?php
class wpcomponent_metabox
{

    public function __construct()
    {
        load_textdomain('wpcomponent', WPCOMPONENT_DIR . 'lang/wpcomponent-' . get_locale() . '.mo');
        add_action( 'add_meta_boxes', array($this, 'wpcomponent_addMetaBox_Sidebar') );
    }

    public function wpcomponent_addMetaBox_Sidebar(){

        $screens = array( 'post', 'page' );

        $args = array(
            'public'   => true,
            '_builtin' => false
        );

        $output = 'objects'; // names or objects

        $post_types = get_post_types( $args, $output );

        foreach ( $post_types  as $post_type ) {


            $screens[] = $post_type->name;

        }

        foreach ( $screens as $screen ) {

            $option_filter = 'wpcomponent_setting_enable_';

            if( get_option( $option_filter . $screen ) != 'false' ){
                add_meta_box(
                    'wpc_selector',
                    __( 'Add a component', 'wpcomponent' ),
                    array($this, 'wpcomponent_addMetaBox_Sidebar_callback'),
                    $screen,
                    'side',
                    'core'
                );
            }

        }

    }

    public function wpcomponent_addMetaBox_Sidebar_callback(){

        // --------------------------------------------------------------------
        // fonction qui affiche la meta dans la sidebar
        // --------------------------------------------------------------------

        // note :
        // structure des folders :
        // plugin : templates/**nom**
        // themes : wpcomponent/**nom**

        // les templates à la racine du dossier ne seront pas lus

        $wpc_structure = new wpcomponent_structure();
        $theme_template = $wpc_structure->wpcomponent_register_Theme_folder();
        $plugin_template = $wpc_structure->wpcomponent_register_Plugin_folder();

        // ici on pourra aller chercher tout les templates inclut dans le dossier default du plugin

            // on parcourt les templates et on les affiche
        ?>
        <select id="wpcomponent_folder_selector">
            <?php

                // on lit en premier les dossiers du thème
                // puis les folders du plugins
                    // dans les folders il y a aura toujours le dossier default

                if( !empty($theme_template) ){
                    foreach ($theme_template as $key => $value) {
                            // log_it($value);

                        if( gettype($value) === "array" ){
            ?>
            <option value="wpcomponent-theme-<?=$key?>"><?=$key?></option>
            <?php

                        }

                    }
                }

            ?>
            <?php

                // on lit en premier les dossiers du thème
                // puis les folders du plugins
                    // dans les folders il y a aura toujours le dossier default

                foreach ($plugin_template as $key => $value) {

                    if( gettype($value) === "array" ){

                        // log_it($value);

                        if( get_option( 'wpcomponent_setting_disable_mt_plugin' ) != 'true' ){

            ?>
            <option value="wpcomponent-plugin-<?=$key?>">Plugin <?=$key?></option>
            <?php

                        }

                    }

                }

            ?>
        </select>
        <?php
            $i=0;
            $foundThemeTemplate = false;

            if( is_array($theme_template) ){

                $foundThemeTemplate = true;

                foreach ($theme_template as $key_parent => $value) {

                    if( gettype($value) === "array" ){

                        if($i === 0)$class=' class="first"';
                        else $class='';

                        ?>
        <ol id="wpcomponent-theme-<?=$key_parent?>"<?=$class?>>
                        <?php

                        foreach ($value as $template) {

                            $template = json_decode($template);

                            $elements = [];
                            foreach( $template->elements as $key => $element ):
                                $elements[] = $element->type;
                            endforeach;
                            $structure = implode(',', $elements);

                            $elements = [];
                            foreach( $template->elements as $key => $element ):
                                $elements[] = $element->slug;
                            endforeach;
                            $slugs = implode(',', $elements);

                    ?>

                            <li>
                                <a href="#"
                                data-type="theme"
                                data-folder="<?=$key_parent?>"
                                data-file="<?=$template->file?>"
                                data-name="<?=$template->name?>"
                                data-structure="<?=$structure?>"
                                data-slugs="<?=$slugs?>"
                                title="<?=$template->description?>">
                                    <h4><?=$template->name?></h4>
                                </a>
                            </li>

                    <?php
                        }

                    ?>
        </ol>
                    <?php
                    $i++;
                    }

                }

            }

            if( is_array( $plugin_template ) ){

                foreach ($plugin_template as $key_parent => $value) {

                    if( gettype($value) === "array" ){

                        if( get_option( 'wpcomponent_setting_disable_mt_plugin' ) != 'true' ){

                            if( $foundThemeTemplate === false ){
                                $foundThemeTemplate = true;
                                $class=' class="first"';
                            }else{
                                $class='';
                            }

                        ?>
        <ol id="wpcomponent-plugin-<?=$key_parent?>"<?=$class?>>
                        <?php

                        foreach ($value as $template) {

                            $template = json_decode($template);


                            $elements = [];
                            foreach( $template->elements as $key => $element ):
                                $elements[] = $element->type;
                            endforeach;
                            $structure = implode(',', $elements);

                            $elements = [];
                            foreach( $template->elements as $key => $element ):
                                $elements[] = $element->slug;
                            endforeach;
                            $slugs = implode(',', $elements);

                    ?>

                            <li>
                                <a href="#"
                                data-type="plugin"
                                data-folder="<?=$key_parent?>"
                                data-file="<?=$template->file?>"
                                data-name="<?=$template->name?>"
                                data-structure="<?=$structure?>"
                                data-slugs="<?=$slugs?>">
                                    <h4><?=$template->name?></h4>
                                    <p>
                                        <?=$template->description?>
                                    </p>
                                </a>
                            </li>

                    <?php
                        }

                    ?>
        </ol>
                    <?php
                        }
                    }

                }

            }

        ?>

        <?php

    }

}
