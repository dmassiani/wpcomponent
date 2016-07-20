<?php


// ******************************************************
//
// Administration
//
// ******************************************************


class wpcomponent_admin
{

	protected $option_name = 'wpcomponent';

	public function __construct(){

		add_action('admin_menu', array( $this, 'wpcomponent_init' ) );

        add_action("wp_ajax_wpcomponent_setSetting", array( $this, "wpcomponent_setSetting") );
        add_action("wp_ajax_nopriv_wpcomponent_setSetting", array( $this, "wpcomponent_setSetting") );

	}

	public function wpcomponent_init(){
		add_submenu_page(
			'options-general.php',
			'WPComponent',
			'WPComponent',
			'manage_options',
			'wpcomponent',
			array( $this, 'wpcomponent_render' )
			// plugins_url( '../front/image/wpcomponent-icon-16.png' , dirname(_FILE_) ),
			// 89
		);

		// prepare settings fields

		// ---------------------------------------------------
		// Export
		// ---------------------------------------------------

		if ( isset($_POST) && isset($_GET['action']) && $_GET['action'] === 'export') {

				$filename = '.zip';
				$realName = 'wpcomponent' . $filename;
				// parent : ( coded first )
				$rootPath = realpath( get_template_directory() . '/wpcomponent/' );
				// child : ( coded last )
				// $rootPath = realpath( get_stylesheet_directory() . '/wpcomponent/' );


				$zip = new ZipArchive;
				$res = $zip->open( $rootPath . $filename, ZipArchive::CREATE && ZipArchive::OVERWRITE );
				$files = new RecursiveIteratorIterator(
				    new RecursiveDirectoryIterator($rootPath),
				    RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ($files as $name => $file)
				{
				    // Skip directories (they would be added automatically)
				    if (!$file->isDir())
				    {
				        // Get real and relative path for current file
				        $filePath = $file->getRealPath();
				        $relativePath = substr($filePath, strlen($rootPath) + 1);

				        // Add current file to archive
				        $zip->addFile($filePath, $relativePath);
				    }
				}

				$zip->close();

				$zip = get_stylesheet_directory() . '/' . $realName;

				header("Content-type: application/zip");

				header("Content-Disposition: attachment; filename=".basename($zip));
				header("Content-Length: ". filesize($zip));
				ob_clean();
				flush();
				readfile($zip);
				unlink($zip);
		}

		// ---------------------------------------------------
		// Import
		// ---------------------------------------------------


		if ( isset( $_FILES )
			&& !empty( $_FILES )
			&& !empty($_FILES['wpcomponent_file_import']['tmp_name'])
			) {

			// $destination = get_template_directory();
			$destination = get_stylesheet_directory();


			if (empty($_FILES['wpcomponent_file_import']['tmp_name'])) {
				echo __('no files', 'wpcomponent');
				return;
			}

			if (!current_user_can('publish_pages') || !current_user_can('publish_posts')) {
				echo __('no can', 'wpcomponent');
				return;
			}

			$file = $_FILES['wpcomponent_file_import']['tmp_name'];
			$this->stripBOM($file);

			$zip = new ZipArchive;
			if ($zip->open($file) === TRUE) {

				$zip->deleteName('__MACOSX/');

     			if( $zip->getNameIndex(1) === 'wpcomponent/' ){
     				// il s'agit bien d'un dossier wpcomponent, on le décompresse telle quel
			    	$zip->extractTo( $destination );
     			}else{
     				// on estime qu'il s'agit d'un dossier de macro-template
     				// et on l'upload dans le dossier wpcomponent
			    	$zip->extractTo( $destination . '/wpcomponent/' );
     			}

			    $zip->close();
			}

		}

	}

	public function wpcomponent_getSettings(){

		$option_name = get_option($this->option_name);
		$options = [];

		$available_options = [
			'wpcomponent_setting_disable_mt_css',
			'wpcomponent_setting_disable_mt_js',
			'wpcomponent_setting_disable_plugin_component',
			'wpcomponent_setting_enable_inside_the_content'
		];

		// je vais chercher chaque post type
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

            $available_options[] = 'wpcomponent_setting_enable_' . $screen;

		}

		foreach( $available_options as $option ){

			$options[ $option ] = get_option( $option );

		}

		return $options;

	}

	public function wpcomponent_setSetting(){


		$option_name = $this->option_name;
		$option = $_POST['option'];
		$value = $_POST['value'];
		$done = update_option( $option, $value );
		die();

	}

	public function wpcomponent_render(){
		$options = $this->wpcomponent_getSettings();
    ?>
    <div class="wpc_admin wpc_admin--wrapper">

	    <header class="wpc_admin--header">
	    	<h1>
	    		<img src="<?php echo plugins_url( '../front/image/wpcomponent.svg' , dirname(__FILE__) ) ?>" alt="WPComponent">
	    		WP Component
	    	</h1>
	    </header>

	    <nav class="wpc_admin--nav">
	    	<ul>
	    		<li>
	    			<h2>
		    			<a class="active" href="general">
		    				<?php _e('General', 'wpcomponent') ?>
		    			</a>
	    			</h2>
	    		</li>
	    		<li>
	    			<h2>
		    			<a href="importexport">
		    				<?php _e('Import/Export', 'wpcomponent') ?>
		    			</a>
	    			</h2>
	    		</li>
	    		<!-- current dev -->
<!-- 	    		<li>
	    			<h2>
		    			<a href="checkup">
		    				Checkup
		    			</a>
	    			</h2>
	    		</li> -->
	    	</ul>
	    </nav>

	    <main class="wpc_admin--main">
	    	<!-- General options -->
			<div class="wpc_admin_tabs wpc_admin_tabs--general">

				<div class="wpc_admin--card">
					<h2><?php _e('Content type enable', 'wpcomponent') ?></h2>


					<?php
						// je vais chercher chaque post type
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
				    ?>
					<h4>
						<?php _e('Enable for', 'wpcomponent')?> <?=$screen?>
						<?php $checked = ( $options['wpcomponent_setting_enable_' . $screen ] != 'false') ? 'checked' : ''; ?>
						<input type="checkbox" name="wpcomponent_setting_enable_<?=$screen?>" value="<?php echo $options['wpcomponent_setting_enable_' . $screen ]; ?>" class="js-switch-settings" <?=$checked?> />
					</h4>
				    <?php


				        }
					?>

				</div>


				<div class="wpc_admin--card">
					<h2><?php _e('Components selector', 'wpcomponent') ?></h2>
					<h4>
						<?php _e('Disable Default Components', 'wpcomponent') ?>
						<?php $checked = ( $options['wpcomponent_setting_disable_plugin_component'] === 'true' ) ? 'checked' : ''; ?>
						<input type="checkbox" name="wpcomponent_setting_disable_plugin_component" value="<?php echo $options['wpcomponent_setting_disable_plugin_component']; ?>" class="js-switch-settings" <?=$checked?> />
					</h4>
				</div>

				<div class="wpc_admin--card">
					<h2><?php _e('Using the_content()', 'wpcomponent') ?></h2>
					<h4>
						<?php _e('Using the_wpc() inside the_content()', 'wpcomponent') ?>
						<?php $checked = ( $options['wpcomponent_setting_enable_inside_the_content'] === 'true' ) ? 'checked' : ''; ?>
						<input type="checkbox" name="wpcomponent_setting_enable_inside_the_content" value="<?php echo $options['wpcomponent_setting_enable_inside_the_content']; ?>" class="js-switch-settings" <?=$checked?> />
					</h4>
				</div>


<!-- 				<div class="wpc_admin--card">
					<h2>Macro-Template capabilities</h2>
					<h4>
						Can enable CSS
						<?php $checked = ( $options['wpcomponent_setting_disable_mt_css'] == 'true') ? 'checked' : ''; ?>
						<input type="checkbox" name="wpcomponent_setting_disable_mt_css" value="<?php echo $options['wpcomponent_setting_disable_mt_css']; ?>" class="js-switch-settings" <?=$checked?> />
					</h4>
					<h4>
						Can enable JavaScript
						<?php $checked = ( $options['wpcomponent_setting_disable_mt_js'] == 'true') ? 'checked' : ''; ?>
						<input type="checkbox" name="wpcomponent_setting_disable_mt_js" value="<?php echo $options['wpcomponent_setting_disable_mt_js']; ?>" class="js-switch-settings" <?=$checked?> />
					</h4>
				</div> -->
				<div class="wpc_admin--card">
					<h4><?php _e('Say to world how WPComponent is awesome!', 'wpcomponent') ?></h4>
					<a class="twitter-share-button"
					  href="https://twitter.com/intent/tweet?text=Check%20this%20modular%20@WordPress%20content%20builder!&amp;url=http://www.wpcomponent.com">
					Tweet</a>
				</div>
			</div>


			<!-- Import Export options -->
			<div class="wpc_admin_tabs wpc_admin_tabs--importexport">

				<!-- Import CARD -->
				<div class="wpc_admin--card">

					<h2><?php _e('Import your Components', 'wpcomponent') ?></h2>

				    <form method="post" enctype="multipart/form-data" name="import" action="admin.php?page=wpcomponent&amp;tabs=importexport&amp;action=import">
				        <!-- File input -->
				        <p><label for="wpcomponent_file_import"><?php _e('Upload file:', 'wpcomponent')?></label><br/>
				        <input name="wpcomponent_file_import" id="wpcomponent_file_import" type="file" value="" /></p>
						<p><?php _e('Must be a Zip file of your "wpcomponent" folder', 'wpcomponent')?><br/>
						<?php _e('or a Components folder', 'wpcomponent')?></p>
				        <p class="submit"><input type="submit" class="button button-blue" name="submit" value="<?php _e('Import', 'wpcomponent')?>" /></p>
				    </form>

				</div>
				<!-- // Import CARD -->


				<!-- Export CARD -->
				<div class="wpc_admin--card">
					<h2><?php _e('Export current used theme "wpcomponent" folder.', 'wpcomponent')?></h2>
				    <form method="post" name="export" enctype="multipart/form-data" action="admin.php?page=wpcomponent&amp;tabs=importexport&amp;action=export">
				        <input type="submit" class="button" name="submit" value="<?php _e('Export', 'wpcomponent')?>" />
				    </form>
				</div>
				<!-- // Export CARD -->
			</div>

			<!-- Checkup current dev -->
<!-- 			<div class="wpc_admin_tabs wpc_admin_tabs--checkup">

				<div class="wpc_admin--card">
					<h2>Checkup</h2>
					<a href="#" class="button button-blue" id="wpc-start">
						Start
					</a>
					<div class="wpc_admin--reponse">

					</div>
				</div>

			</div> -->

	    </main>

    </div>
    <?php
	}

    /**
     * Delete BOM from UTF-8 file.
     *
     * @param string $fname
     * @return void
     */
    public function stripBOM($fname) {
        $res = fopen($fname, 'rb');
        if (false !== $res) {
            $bytes = fread($res, 3);
            if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
                // $this->log['notice'][] = 'Getting rid of byte order mark...';
                fclose($res);

                $contents = file_get_contents($fname);
                if (false === $contents) {
                    trigger_error('Failed to get file contents.', E_USER_WARNING);
                }
                $contents = substr($contents, 3);
                $success = file_put_contents($fname, $contents);
                if (false === $success) {
                    trigger_error('Failed to put file contents.', E_USER_WARNING);
                }
            } else {
                fclose($res);
            }
        } else {
            // $this->log['error'][] = 'Failed to open file, aborting.';
        }
    }

}
