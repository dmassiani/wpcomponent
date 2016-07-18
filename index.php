<?php
/**
 * @package WPComponent
 */
/*
Plugin Name: WordPress Component
Plugin URI: http://wpcomponent.com/
Description: WPComponent enable component for web artist.
Version: 2.1.3
Author: David Massiani
Author URI: http://davidmassiani.com
License: GPLv2 or later
Text Domain: wpcomponent
*/


// Folder name
define ( 'WPCOMPONENT_VERSION', '2.1.3' );
define ( 'WPCOMPONENT_FOLDER',  'wpcomponent' );

define ( 'WPCOMPONENT_URL', plugins_url('', __FILE__) );
define ( 'WPCOMPONENT_DIR', dirname(__FILE__) );
define ( 'WPCOMPONENT_DEFAULT_TEMPLATE', WPCOMPONENT_DIR .'/templates' );


if(!function_exists('log_it')){
 function log_it( $message ) {
   if( WP_DEBUG === true ){
     error_log('--------------------------');
     if( is_array( $message ) || is_object( $message ) ){
       error_log( print_r( $message, true ) );
     } else {
       error_log( $message );
     }
   }
 }
}


if( !class_exists('wpcomponent_kickstarter') ):

class wpcomponent_kickstarter
{

	public $templates = [];

    public $wpc_ajax;
    public $wpc_metabox;
    public $wpc_database;
    public $wpc_post;
    public $wpc_edit;
    public $wpc_structure;
    public $wpc_utility;
	public $wpc_templates;

	public function __construct(){

		add_action('init', array($this, 'init'), 1);

	}

    public function init(){

		load_plugin_textdomain( 'wpcomponent', false, plugin_basename( dirname( __FILE__ ) ) . '/core/lang' );

    	$this->wpcomponent_include_front_class();


    	if ( !is_admin() ) {

	        // init structure
	        $get_templates = new wpcomponent_structure();

    	}else{

    		$this->wpcomponent_include_admin_class();

    	// ===================================================
    	//
    	// add register plugins and styles
    	//
    	// ===================================================

    		$this->wpcomponent_register_plugins();
    		$this->wpcomponent_register_styles();

    	// =================================================
    	//
    	// instanciation
    	//
    	// =================================================

    		// init ajax
        	$this->wpc_ajax 			= new wpcomponent_ajax();
	    	// init metabox selector
	        $this->wpc_metabox 			= new wpcomponent_metabox();
	        // init database
	        $this->wpc_database 		= new wpcomponent_database();
	        // init post
	        $this->wpc_post 			= new wpcomponent_post();
	        // init edit
	        $this->wpc_edit 			= new wpcomponent_edit();
	        // init admin
	        $this->wpc_admin 			= new wpcomponent_admin();
	        // init checkup
	        $this->wpc_admin 			= new wpcomponent_checkup();

	        // action quand on post du contenu
	       	add_action( 'save_post', array( $this->wpc_post, 'wpcomponent_save' ) );
	       	// action qui ajoute le javascript
	       	add_action( 'admin_footer', array( $this, 'wpcomponent_removePageTemplate'), 10);
	       	// fonction appellé après l'installation d'un thème
	       	add_action( 'after_setup_theme', array( $this, 'wpcomponent_afterThemeActivation' ) );
	       	add_action( 'after_switch_theme', array( $this, 'wpcomponent_afterThemeActivation' ) );

	       	// maj 2.1
	       	$this->wpc_database->wpcomponent_update_210();

	    }

    }

    // remove wpcomponent template from template selector.

	public function wpcomponent_removePageTemplate() {
	    global $pagenow;
	    if ( in_array( $pagenow, array( 'post-new.php', 'post.php') ) && get_post_type() == 'page' ) { ?>
	        <script type="text/javascript">
	            (function($){
	                $(document).ready(function(){
	                    $('#page_template option[value^="wpcomponent"]').remove();
	                })
	            })(jQuery)
	        </script>
	    <?php
	    }
    	$this->wpcomponent_register_twitter();
	}

    public function wpcomponent_include_admin_class(){
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_admin.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_database.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_metabox.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_editors.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_ajax.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_post.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_edit.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_remover.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_checkup.php';
    }
    public function wpcomponent_include_front_class(){
		/**
		 * Load functions
		 *
		 *
		 */
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_content.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_utility.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc_structure.php';
    }

    static function wpcomponent_activation(){
		update_option( 'wpcomponent_setting_enable_inside_the_content', 'true' );
    }

    public function wpcomponent_afterThemeActivation(){

    }

    // ============================================================
    // Register JS plugins
    // ============================================================

    public function wpcomponent_register_plugins(){
		// register javascript
		$scripts = array();

		$scripts[] = array(
			'handle'	=> 'switchery',
			'src'		=> WPCOMPONENT_URL . '/front/js/switchery.js',
			'deps'		=> array('jquery')
		);

		$scripts[] = array(
			'handle'	=> 'wpcomponent-js',
			'src'		=> WPCOMPONENT_URL . '/front/js/wpcomponent.js',
			'deps'		=> array('jquery','switchery')
		);

		$scripts[] = array(
			'handle'	=> 'wpc-settings',
			'src'		=> WPCOMPONENT_URL . '/front/js/settings.js',
			'deps'		=> array('jquery','switchery')
		);

		foreach( $scripts as $script )
		{
			wp_localize_script($script['handle'], 'WPURLS', array( 'siteurl' => get_option('siteurl') ));
			wp_enqueue_script( $script['handle'], $script['src'], $script['deps'] );
		}
    }

    public function wpcomponent_register_twitter(){
    		?>
		<script>window.twttr = (function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0],
			t = window.twttr || {};
			if (d.getElementById(id)) return t;
			js = d.createElement(s);
			js.id = id;
			js.src = "https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js, fjs);

			t._e = [];
			t.ready = function(f) {
			t._e.push(f);
			};

			return t;
		}(document, "script", "twitter-wjs"));</script>
    		<?php
    }

    // ============================================================
    // Register CSS Styles
    // ============================================================

    public function wpcomponent_register_styles(){
		// register styles
		$styles = array();

		$styles[] = array(
			'handle'	=> 'wpcomponent-css',
			'src'		=> WPCOMPONENT_URL . '/front/css/wpcomponent.css'
		);

		foreach( $styles as $style )
		{
			wp_enqueue_style( $style['handle'], $style['src'] );
		}
    }

}
function wpcomponent()
{
	global $wpcomponent;

	if( !isset($wpcomponent) )
	{
		$wpcomponent = new wpcomponent_kickstarter();
	}

	return $wpcomponent;
}


// initialize
wpcomponent();

// hook qui appelle la fonction à l'activation du theme
register_activation_hook( __FILE__, array( 'wpcomponent_kickstarter' , 'wpcomponent_activation' ) );


endif; // class_exists check
