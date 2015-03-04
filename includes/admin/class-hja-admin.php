<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Pages Class
 *
 * Handles generic Admin functionailties
 *
 * @package Javascript Html and Text Adder
 * @since 1.0.0
 */

class Hja_Admin_Pages {

	public $model, $scripts;

	public function __construct()	{		
		// constructor code
	}
	
	/**
	 * Includes Javascript and Css files
	 *
	 * @package Javascript Html and Text Adder
	 * @since 1.0.0
	 */
	public function hja_include_files($hook){
	
		if($hook == "widgets.php"){
			
			wp_register_script('hja-script', HJA_PLUGIN_URL . '/includes/js/hja-widget.js');
			wp_enqueue_script('hja-script');
			
			wp_register_script('hja-awquicktag', HJA_PLUGIN_URL . '/includes/js/awQuickTag.js');
			wp_enqueue_script('hja-awquicktag');
			
			wp_register_style('hja-style', HJA_PLUGIN_URL . '/includes/css/hja-widget.css');
			wp_enqueue_style('hja-style');
			
		}
	}
	
	
	/**
	 * Add code to footer
	 *
	 * @package Javascript Html and Text Adder
	 * @since 1.0.0
	 */
	function hja_admin_footer(){
		
		global $pagenow, $post;
		
		if($pagenow == "widgets.php"){
		
			echo '<span style="display:none" class="hjaUrl">' . HJA_PLUGIN_URL . '</span>';
			echo '<div class="hjaWindow">
				<span class="hjaOverlayClose"></span>
				<h3 class="hjaWinHead">Preview</h3>
				<iframe id="hjaIframe" name="hjaIframe" src="about:blank"></iframe>
				If the script is not working, try it in <a href="http://jsfiddle.com" target="_blank">jsfiddle</a>
			</div>';
			
		}
	
	}
	
	/**
	 * Adding Hooks
	 *
	 * @package Javascript Html and Text Adder
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'hja_include_files' ) ); 
		
		add_action( 'admin_footer', array( $this, 'hja_admin_footer' ) ); 
	}

}
?>