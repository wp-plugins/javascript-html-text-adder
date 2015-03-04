<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'widgets_init', 'hja_load_widget' );

/**
 * Register the Widget
 *
 * @package Javascript Html and Text Adder
 * @since 1.0.0
 */
function hja_load_widget() {
	
	register_widget( 'Hja_Widget' );
}
/**
 * Widget Class
 *
 * @package Javascript Html and Text Adder
 * @since 1.0.0
 */

class Hja_Widget extends WP_Widget {
	
	## Initialize
	function Hja_Widget(){
		$widget_ops = array(
			'classname' => 'widget_html_javascript_adder',
			'description' => __("Insert Javascripts, HTML, Text, Shortcodes and other codes in the sidebar", 'hja')
		);
		
		$control_ops = array('width' => 480, 'height' => 500);
		parent::WP_Widget('html_javascript_adder', __('Javascript HTML Text Adder', 'hja'), $widget_ops, $control_ops);
		
		if( is_admin() ){
			
			// Get plugin options
			$hja = get_option('widget_html_javascript_adder');
		
			// Rename old array keys
			if(is_array($hja) && !isset($hja['_varscleaned'])){
				$toRep = array('hja_' , 'is_', 'diable_post');
				$repWith = array('', 'hide_', 'hide_in_posts');
				foreach($hja as $k=>$v){
					if(is_array($v)){
						foreach($v as $m=>$n){
							$old = $m;
							$new = str_replace($toRep, $repWith, $old);
							$hja[$k][$new] = $hja[$k][$old];
							unset($hja[$k][$old]);
						}
					}
				}
				$hja['_varscleaned'] = true;
				update_option('widget_html_javascript_adder', $hja);
			}
			
			// Include plugin version in options
			if( ( is_array($hja) && !isset($hja['_version']) ) || $hja['_version'] != HJA_VERSION ){
				$hja['_version'] = HJA_VERSION;
				update_option('widget_html_javascript_adder', $hja);
			}
		
		}
				
	}
	
	function page_check($instance){
		$hide_single = $instance['hide_single'];
		$hide_archive = $instance['hide_archive'];
		$hide_home = $instance['hide_home'];
		$hide_page = $instance['hide_page'];
		$hide_search = $instance['hide_search'];
		
		if (is_home() == 1 && $hide_home != 1){
			return true;
		
		}elseif (is_single() == 1 && $hide_single!= 1){
			return true;
		
		}elseif (is_page() == 1 && $hide_page != 1){
			return true;
		
		}elseif (is_archive() == 1 && $hide_archive != 1){
			return true;
		
		}elseif (is_tag() == 1 && $hide_archive != 1){
			return true;
		
		}elseif(is_search() == 1 && $hide_search != 1){
			return true;
		
		}else{
			return false;
		}
	}
	
	function admin_check($instance){
		$hide_admin = $instance['hide_admin'];
		
		if(current_user_can('level_10') && $hide_admin == 1){
			return true;
		}else{
			return false;
		}
	}
	
	function hide_post_check($instance){
		global $post;
		$hide_in_posts = $instance['hide_in_posts'];
		$splitId = explode(',', $hide_in_posts);
		
		if(is_page($splitId) || is_single($splitId)){
			return false;
		}else{
			return true;
		}
	}
	
	function show_post_check($instance){
		global $post;
		$show_in_posts = $instance['show_in_posts'];
		$splitId = explode(',', $show_in_posts);
		
		if(is_page($splitId) || is_single($splitId)){
			return true;
		}else{
			return false;
		}
	}
	
	function all_ok($instance){
		if($this->admin_check($instance)){
			return false;
		}else{
			if($instance['display_in'] == 'all'){
				return true;
			}elseif($instance['display_in'] == 'hide_only'){
				return (
					$this->page_check($instance) && 
					$this->hide_post_check($instance)
				);
			}elseif($instance['display_in'] == 'show_only'){
				return (
					$this->show_post_check($instance)
				);
			}else{
				return true;
			}
		}
	}
	
	## Display the Widget
	function widget($args, $instance){
		extract($args);
		
		// Check conditions
		if( $this->all_ok($instance) == false ){
			return '';
		}
		
		if(empty($instance['title'])){
			$title = '';
		}else{
			$title = $before_title . apply_filters('widget_title', $instance['title'], $instance, $this->id_base) . $after_title;
		}
		
		if(empty($instance['content'])){
			$content = '';
		}elseif($instance['add_para'] == 1){
			$content = wpautop($instance['content']);
		}else{
			$content = $instance['content'];
		}
		
		$content = do_shortcode($content);
			
		$before_content = "\n" . '<div class="hjawidget textwidget">' . "\n";
		$after_content = "\n" . '</div>' . "\n";
		
		$before_cmt = "\n<!-- Start - Javascript HTML Text Adder plugin v" . HJA_VERSION . " -->\n";
		$after_cmt =  "<!-- End - Javascript HTML Text Adder plugin v" . HJA_VERSION . " -->\n";
		
		## Output
		$output_content = 
			$before_cmt .
				$before_widget . 
					$title . 
					$before_content . 
						$content . 
					$after_content . 
				$after_widget.
			$after_cmt;
		
		## Print the output
		echo $output_content;
		
	}
	
	## Save settings
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['content'] = stripslashes($new_instance['content']);
		
		$instance['hide_single'] = $new_instance['hide_single'];
		$instance['hide_archive'] = $new_instance['hide_archive'];
		$instance['hide_home'] = $new_instance['hide_home'];
		$instance['hide_page'] = $new_instance['hide_page'];
		$instance['hide_search'] = $new_instance['hide_search'];
		
		$instance['add_para'] = $new_instance['add_para'];
		
		$instance['hide_admin'] = $new_instance['hide_admin'];
		$instance['hide_in_posts'] = $new_instance['hide_in_posts'];
		$instance['show_in_posts'] = $new_instance['show_in_posts'];
		
		$instance['display_in'] = $new_instance['display_in'];
		
		return $instance;
	}
  
	## HJA Widget form
	function form($instance){

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 'content' => '', 'hide_single'=> '0',
			'hide_archive' => '0', 'hide_home' => '0', 'hide_page' => '0',
			'hide_search' => '0', 'add_para' => '0', 'hide_admin' => '0', 
			'hide_in_posts' => '', 'show_in_posts' => '', 'display_in' => 'all'
		));
		
		$title = htmlspecialchars($instance['title']);
		$content = htmlspecialchars($instance['content']);
		
		$hide_single = $instance['hide_single'];
		$hide_archive = $instance['hide_archive'];
		$hide_home = $instance['hide_home'];
		$hide_page = $instance['hide_page'];
		$hide_search = $instance['hide_search'];
		
		$add_para = $instance['add_para'];
		
		$hide_admin = $instance['hide_admin'];
		$hide_in_posts = $instance['hide_in_posts'];
		$show_in_posts = $instance['show_in_posts'];
		
		$display_in = $instance['display_in'];
	?>
	
		<div class="section">
			<label><?php _e('Title', 'hja'); ?> :<br />
				<input id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" placeholder="Enter the title here"/>
			</label>
		</div>
		
		<div class="section">
			<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content :', 'hja'); ?></label>
			
			<ul class="hjaToolbar clearfix">
				<li><img src="<?php echo HJA_IMG_URL . '/edit-icon.png'; ?>" /> Toolbar
					<ul>
						<li editorId="<?php echo $this->get_field_id('content'); ?>">
							
							<span class="hjaTb" openTag="&lt;h1&gt;" closeTag="&lt;/h1&gt;">H1</span>
							<span class="hjaTb" openTag="&lt;h2&gt;" closeTag="</h2>">H2</span>
							<span class="hjaTb hjaTbSpace" openTag="&lt;h3&gt;" closeTag="&lt;/h3&gt;">H3</span>
							
							<span class="hjaTb" openTag="&lt;strong&gt;" closeTag="&lt;/strong&gt;">B</span>
							<span class="hjaTb" openTag="&lt;em&gt;" closeTag="&lt;/em&gt;">I</span>
							<span class="hjaTb" openTag="&lt;u&gt;" closeTag="&lt;/u&gt;">U</span>
							<span class="hjaTb hjaTbSpace" openTag="<s>" closeTag="</s>">S</span>
							
							<span class="hjaTb" openTag="&lt;a " closeTag="&lt;/a&gt;" action="a">Link</span>
							<span class="hjaTb" openTag="&lt;img " closeTag="/&gt;" action="img">Image</span>
							<span class="hjaTb" openTag="&lt;code&gt;" closeTag="&lt;/code&gt;">Code</span>
							<span class="hjaTb" openTag="&lt;p&gt;" closeTag="&lt;/p&gt;">P</span>
							<span class="hjaTb" openTag="&lt;ol&gt;" closeTag="&lt;/ol&gt;">OL</span>
							<span class="hjaTb" openTag="&lt;ul&gt;" closeTag="&lt;/ul&gt;">UL</span>
							<span class="hjaTb" openTag="&lt;li&gt;" closeTag="&lt;/li&gt;">LI</span>
							<span class="hjaTb" openTag="&lt;br/&gt;" closeTag="">Br</span>
						</li>
					</ul>
				</li>
				<li class="hjaTb-preview" editorId="<?php echo $this->get_field_id('content'); ?>"><img src="<?php echo HJA_IMG_URL . '/preview-icon.png'; ?>" /> Preview</li>
			</ul>
			
			<textarea rows="10" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="hjaContent" spellcheck="false" placeholder="Enter your HTML/Javascript/Plain text content here"><?php echo $content; ?></textarea>
		</div>
		
		<div class="section">
			<h3><?php _e("Settings", 'hja'); ?></h3>
			
			<label class="hjaAccord"><input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="all" <?php echo ($display_in == 'all') ? 'checked="checked"' : '' ; ?> /> <?php _e("Show in all pages", 'hja'); ?></label>
			
			<label class="hjaAccord"><input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="show_only" <?php echo ($display_in == 'show_only') ? 'checked="checked"' : '' ; ?> /> <?php _e("Show only in specific pages", 'hja'); ?></label>
			
			<div class="hjaAccordWrap" <?php echo ($display_in != 'show_only') ? 'style="display:none"' : '' ; ?>>
				<label><input id="<?php echo $this->get_field_id('show_in_posts'); ?>" type="text" name="<?php echo $this->get_field_name('show_in_posts'); ?>" value="<?php echo $show_in_posts; ?>" class="widefat hjaGetPosts"/></label>
				<span class="smallText"><?php _e("Post ID / name / title separated by comma", 'hja'); ?></span>
			</div> <!-- HJA ACCORD WRAP 2 -->
			
	
			<label class="hjaAccord"><input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="hide_only" <?php echo ($display_in == 'hide_only') ? 'checked="checked"' : '' ; ?> /> <?php _e("Hide only in specific pages", 'hja'); ?></label>
			
			<div class="hjaAccordWrap" <?php echo ($display_in != 'hide_only') ? 'style="display:none"' : '' ; ?>>
			
			<label><input id="<?php echo $this->get_field_id('hide_single'); ?>" type="checkbox"  name="<?php echo $this->get_field_name('hide_single'); ?>" value="1" <?php echo $hide_single == "1" ? 'checked="checked"' : ""; ?> /> <?php _e("Don't display in Posts page", 'hja'); ?></label>
			
			<label><input id="<?php echo $this->get_field_id('hide_page'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_page'); ?>" value="1" <?php echo $hide_page == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Pages", 'hja'); ?></label>
			
			<label><input id="<?php echo $this->get_field_id('hide_archive'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_archive'); ?>" value="1" <?php echo $hide_archive == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Archive or Tag page", 'hja'); ?></label>
			
			<label><input id="<?php echo $this->get_field_id('hide_home'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_home'); ?>" value="1" <?php echo $hide_home == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Home page", 'hja'); ?></label>
			
			<label><input id="<?php echo $this->get_field_id('hide_search'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_search'); ?>" value="1" <?php echo $hide_search == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Search page", 'hja'); ?></label><br />
			
			<label><?php _e("Don't show in posts", 'hja'); ?><br />
			<input id="<?php echo $this->get_field_id('hide_in_posts'); ?>" type="text" name="<?php echo $this->get_field_name('hide_in_posts'); ?>" value="<?php echo $hide_in_posts; ?>" class="widefat hjaGetPosts" placeholder="<?php _e("Post ID / name / title separated by comma", 'hja'); ?>"/></label>
				
				
			</div><!-- HJA Accord 3 -->
			
			<div class="hjaOtherOpts">
			<label><input id="<?php echo $this->get_field_id('add_para'); ?>" type="checkbox" name="<?php echo $this->get_field_name('add_para'); ?>" value="1" <?php echo $add_para == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Automatically add paragraphs", 'hja'); ?></label> &nbsp;
			
			<label><input id="<?php echo $this->get_field_id('hide_admin'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_admin'); ?>" value="1" <?php echo $hide_admin == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display to admin", 'hja'); ?></label>
			</div>

		</div>

		<?php	  
	}
}

?>