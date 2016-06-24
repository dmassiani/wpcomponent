<?php
/**
 * @package WPComponent
 */
/*
Plugin Name: WordPress Component
Plugin URI: http://wpcomponent.com/
Description: WPComponent enable component for web artist.
Version: 2.0
Author: David Massiani
Author URI: http://davidmassiani.com
License: GPLv2 or later
Text Domain: WPComponent
*/


// Folder name
define ( 'WPC_VERSION', '2.1' );
define ( 'WPC_FOLDER',  'wpcomponent' );

define ( 'WPC_URL', plugins_url('', __FILE__) );
define ( 'WPC_DIR', dirname(__FILE__) );
define ( 'WPC_DEFAULT_TEMPLATE', WPC_DIR .'/templates' );


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


if( !class_exists('WPComponent__kickstarter') ):

class WPComponent__kickstarter
{

	public $templates = [];
	
    public $wpc__ajax;
    public $wpc__metabox;
    public $wpc__database;
    public $wpc__post;
    public $wpc__edit;
    public $wpc__structure;
    public $wpc__utility;

	public $name = "wpc__content";
	public $wpc__templates;

	public function __construct(){

		load_textdomain('WPComponent', WPC_DIR . 'core/lang/wpc-' . get_locale() . '.mo');

		add_action('init', array($this, 'init'), 1);

	}

    public function init(){

		register_post_type( $this->name ,
			array(
				'labels' => array(
				'name' => __( $this->name ),
				'singular_name' => __( $this->name )
			),
			'public' => false,
			'has_archive' => false,
			)
		);
		
	    remove_post_type_support( $this->name, 'title' );


    	$this->WPComponent__include__front__class();


    	if ( !is_admin() ) {

	        // init structure
	        $get__templates = new WPComponent__structure();

    	}else{

    		$this->WPComponent__include__admin__class();

    	// ===================================================
    	// 
    	// add register plugins and styles
    	// 
    	// ===================================================

    		$this->WPComponent__register__plugins();
    		$this->WPComponent__register__styles();

    	// =================================================
    	//
    	// instanciation
    	//
    	// =================================================

    		// init ajax
        	$this->wpc__ajax 			= new WPComponent__ajax();
	    	// init metabox selector
	        $this->wpc__metabox 		= new WPComponent__metabox();
	        // init database
	        $this->wpc__database 		= new WPComponent__database();
	        // init post
	        $this->wpc__post 			= new WPComponent__post();
	        // init edit
	        $this->wpc__edit 			= new WPComponent__edit();
	        // init admin
	        $this->wpc__admin 			= new WPComponent__admin();
	        // init checkup
	        $this->wpc__admin 			= new WPComponent__checkup();

	        // action quand on post du contenu
	       	add_action( 'save_post', array( $this->wpc__post, 'WPComponent__save' ) );
	       	// action qui ajoute le javascript
	       	add_action( 'admin_footer', array( $this, 'WPComponent__removePageTemplate'), 10);
	       	// fonction appellé après l'installation d'un thème
	       	add_action( 'after_setup_theme', array( $this, 'WPComponent__afterThemeActivation' ) );
	       	add_action( 'after_switch_theme', array( $this, 'WPComponent__afterThemeActivation' ) );

	    }

    }

    // remove wpcomponent template from template selector.

	public function WPComponent__removePageTemplate() {
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
    	$this->WPComponent__register__twitter();
	}

    public function WPComponent__include__admin__class(){
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__admin.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__database.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__metabox.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__editors.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__ajax.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__post.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__edit.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__remover.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__checkup.php';
    }
    public function WPComponent__include__front__class(){
    	/**
    	 * Load deprecated functions
    	 * this is removed in next version
    	 */
		include_once plugin_dir_path(__FILE__). '/core/deprecated/wpc__content.php';
		
		/**
		 * Load functions
		 *
		 *
		 */
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__content.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__utility.php';
		include_once plugin_dir_path(__FILE__). '/core/inc/wpc__structure.php';
    }

    static function WPComponent__activation(){

    }

    public function WPComponent__afterThemeActivation(){

    }

    // ============================================================
    // Register JS plugins
    // ============================================================

    public function WPComponent__register__plugins(){
		// register javascript 
		$scripts = array();

		$scripts[] = array(
			'handle'	=> 'switchery',
			'src'		=> WPC_URL . '/front/js/switchery.js',
			'deps'		=> array('jquery')
		);

		$scripts[] = array(
			'handle'	=> 'wpcomponent-js',
			'src'		=> WPC_URL . '/front/js/wpcomponent.js',
			'deps'		=> array('jquery','switchery')
		);

		$scripts[] = array(
			'handle'	=> 'settings',
			'src'		=> WPC_URL . '/front/js/settings.js',
			'deps'		=> array('jquery','switchery')
		);

		foreach( $scripts as $script )
		{
			wp_localize_script($script['handle'], 'WPURLS', array( 'siteurl' => get_option('siteurl') ));
			wp_enqueue_script( $script['handle'], $script['src'], $script['deps'] );
		}
    }

    public function WPComponent__register__twitter(){
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

    public function WPComponent__register__styles(){
		// register styles
		$styles = array();
		
		$styles[] = array(
			'handle'	=> 'wpcomponent-css',
			'src'		=> WPC_URL . '/front/css/wpcomponent.css'
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
		$wpcomponent = new WPComponent__kickstarter();
	}
	
	return $wpcomponent;
}


// initialize
wpcomponent();

// hook qui appelle la fonction à l'activation du theme
register_activation_hook( __FILE__, array( 'WPComponent__kickstarter' , 'WPComponent__activation' ) );


endif; // class_exists check