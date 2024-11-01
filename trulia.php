<?php
/*
Plugin Name: Trulia
Plugin URI: http://www.seodenver.com/trulia/
Description: Easily add Trulia maps to your sidebar or embed Trulia.com real estate maps in your content.
Author: Katz Web Services, Inc.
Version: 1.0.1
Author URI: http://www.katzwebservices.com
*/

/*
Copyright 2012 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(class_exists('WP_Widget') && function_exists('register_widget')) {
	
	add_action( 'widgets_init', 'load_kws_trulia_widget' );
	
	function load_kws_trulia_widget() {
		register_widget( 'Trulia' );
	}
	
	class Trulia extends WP_Widget {
		
		var $version = '1.0.1';
		
	 	function Trulia() {
	    	$control_options = array('width'=>400);
	        $widget_options = array('description'=>'Add and configure a Trulia.com map with real estate listings.', 'classname' => 'trulia');
	        parent::WP_Widget(false, $name = 'Trulia', $widget_options, $control_options);    
	    	
	    	$this->url = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__));    
	        $this->defaults = array(
					'map_header_text' => __('Distinctive Properties'),
				    'city_state' => 'Bethesda, MD',
				    'is_shortcode' => false,
				    'size' => 'large',
				    'map_background_color' => '#B5CF5A',
				    'map_text_color' => '#000000',
				    'active_swatch_picker' => 'background',
				    'logo_color' => 'grn',
				    'map_width' => 350,
	    	        'map_height' => 380,
	    	        'map_iframe_height' => 329,
	    	        'map_iframe_width' => 340,
	    	        'auth_key' => '5fa9ac',
	    	        'user_hash' =>'fd63b6',
	    	        'site_ids' => '0',
	    	        'map_view' => 'map',
	    	        'latitude' => '',
	    	        'longitude' => '',
	    	        'map_custom_height' => '',
	    	        'refresh_rate' => 0,
	    	        'rotate' => 0,
	    	        'refresh' => 0,
	    	        'map_city' => '',
	    	        'map_state' => '', 
	    	        'map_zip' => '',
	    	        'terms' => true,
	    	        'zip' => '',
	    	        'text' => '',
	    	        'color' => '',
	    	        'header' => '',
	    	        'title' => '',
	    	        'bg' => '',
	    	        'background' => '',
	    	        'city' => '',
	    	        'state' => '',
	    	        'link' => true,
	    	        'footer' => false,
	    	        'saved' => false,
	    	        'align' => 'center'
			);
			
			add_action('wp_ajax_trulia_locations', array($this, 'ajax_trulia_locations'));    
	        add_action('wp_print_styles', array(&$this, 'print_styles'));
	        add_action('wp_print_footer_scripts', array(&$this, 'print_scripts'));
	        
	        add_action('admin_print_styles-widgets.php', array(&$this, 'widget_styles'));
	        add_action('admin_print_scripts-widgets.php', array(&$this, 'widget_scripts'), 9999);
	        
	        // Implement the shortcodes
	        add_shortcode('trulia', array(&$this, 'shortcode'));
			add_shortcode('Trulia', array(&$this, 'shortcode'));
	    }
		
		
		function ajax_trulia_locations() {
			if(!isset($_GET['q'])) { die(); }
			$results = wp_remote_retrieve_body(wp_remote_get('http://pipes.yahoo.com/pipes/pipe.run?_id=8dabccf63e82aad7c57dce9bac3454e1&_render=json&max=20&location_type=city|state|country|zipCode&q='.urlencode($_GET['q'])));
			
			if($results && !is_wp_error($results)) {
				$results = json_decode($results);
				
			    foreach ($results->value->items as $result) {
			        echo $result->display . "\n";
			    }
		    }
			
			die();
		}
		
		function remove_whitespace($content = null) {
			return trim(preg_replace('/\s+/ism', ' ', $content))."\n";
		}
				
		function widget_js() {
			global $pagenow;
			if(is_admin() && $pagenow == 'widgets.php') {
			}
		}
		
		function widget_styles() {
			wp_enqueue_style('trulia', $this->url.'/trulia.css');
		}
		
		function print_scripts() {
			// For future use
			return;
		}
		
		function print_styles() {
			
			// For future use
			return;
 		}
 		
 		function widget_scripts() {
			wp_enqueue_script('trulia', $this->url.'/trulia.js',array('jquery','suggest'), '1.0', true);
 		}
 	 	 
 	 	function shortcode($atts = array()) {
 	 		global $post; // prevent before content
			if(!is_admin()) {

				$settings = shortcode_atts($this->defaults, $atts);
				
				if(!empty($settings['zip'])) {
					$settings['map_zip'] = $settings['zip'];
					unset($settings['zip']);
				}
				if(!empty($settings['city'])) {
					$settings['map_city'] = $settings['city'];
					unset($settings['city']);
				}
				if(!empty($settings['state'])) {
					$settings['map_state'] = $settings['state'];
					unset($settings['state']);
				}
				if(!empty($settings['rotate'])) {
					$settings['refresh_rate'] = $settings['rotate'];
					unset($settings['rotate']);
				}
				if(!empty($settings['refresh'])) {
					$settings['refresh_rate'] = $settings['refresh'];
					unset($settings['refresh']);
				}
				
				if(!empty($settings['background']) || !empty($settings['bg'])) {
					$settings['map_background_color'] = empty($settings['background']) ? $settings['bg'] : $settings['background'];
					unset($settings['bg'],$settings['background']);
				}
				
				if(!empty($settings['title']) || !empty($settings['header'])) {
					$settings['map_header_text'] = empty($settings['title']) ? $settings['header'] : $settings['title'];
					unset($settings['title'], $settings['header']);
				}
				
				if(!empty($settings['text']) || !empty($settings['color'])) {
					$settings['map_text_color'] = empty($settings['color']) ? $settings['text'] : $settings['color'];
					unset($settings['color'], $settings['text']);
				}
				
				$settings['is_shortcode'] = true;
				
				return $this->generateCode($settings);
			}
		}
	
	    function update($new_instance, $old_instance) {
			return $new_instance;
	    }
	    
	    function widget($args, $instance) {      
	        $output = '';
	        extract( $args );
	        $settings = shortcode_atts($this->defaults, $instance);
	       	extract($settings);
	       	
	    	#if($hide === 'yes' || $show_widget === 'no' || empty($code)) { return; }
			
			$output .= $before_widget;
            $output .= "\n\t".$this->generateCode($settings)."\n\t";
			$output .=  $after_widget; 
			
			$output = apply_filters('trulia_widget', $output);
			echo $output;
			return;
	    }
	    
	    function process_color($color) {
    		$color = str_replace('#', '', $color);
			// HEX
			if(preg_match('/#?[0-9A-Fa-f]{6}/ism', $color)) {
				$color = str_replace('#', '', $color);
				$color = strtolower('#'.$color);
			}
			// Named
			else {
				$color = strtolower($color);
			}
			return $color;
	    }
	     
	    function form($instance = array()) {
	    	$settings = shortcode_atts($this->defaults, $instance); 
	    	
			extract($settings);
			
			?>
	<div class="trulia_container">
	      	    

 			<div class="clear stuffbox">
	 			<div class="widget-top"><div class="widget-title"><h4 rel="#trulia-settings-<?php echo $this->number; ?>"><?php _e('Select your TruliaMap Location', 'trulia'); ?><span class="cc-arrow in-widget-title"></span></h4></div></div>
	 			<div class="section" id="trulia-settings-<?php echo $this->number; ?>">
					<?php
						$this->make_textfield($city_state, 'city_state', 'city_state', '<strong>Enter City, State, or Zip</strong> <span class="howto">San Francisco, CA <em>or</em> 94103</span>'); 
					?>
	            </div>
            </div>
            
			<div class="clear stuffbox">
			
 			<div class="widget-top"><div class="widget-title"><h4 rel="#trulia-design-<?php echo $this->number; ?>"><?php _e('Customize your TruliaMap', 'trulia'); ?><span class="cc-arrow in-widget-title"></span></h4></div></div>
 			<div class="section" id="trulia-design-<?php echo $this->number; ?>">
 			
					
		        	<?php
						$this->make_textfield($map_header_text, 'map_header_text', 'map_header_text', '<strong>Map Title:</strong>'); 
					?>
	                
	                <p>
	                    <label style="display:block;padding-bottom:0.25em;" for="<?php echo $this->get_field_id('size'); ?>"><strong><?php _e('Map Size:', 'trulia'); ?></strong></label>
	                    <select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" style="display:block;">
	                        <option value="panoramic" <?php selected($size, 'panoramic'); ?>>
	                            <?php _e('Panoramic (800x230)', 'trulia'); ?>
	                        </option>
	
	                        <option value="large" <?php selected($size, 'large'); ?>>
	                            <?php _e('Large (350x380)', 'trulia'); ?>
	                        </option>
	
	                        <option value="small" <?php selected($size, 'small'); ?>>
	                            <?php _e('Small (230x380)', 'trulia'); ?>
	                        </option>
	                    </select>
	                </p>
	
	                <p style="margin-bottom:0.25em;"><label><strong><?php _e('Map Type', 'trulia'); ?></strong></label></p>
	
	                    <?php 
	                    	$this->make_radio($map_view, 'mm_view_map','map_view', 'map', '<span>Map</span>');
	                    	$this->make_radio($map_view, 'mm_view_sat','map_view', 'sat', '<span>Satellite</span>');
	                    	$this->make_radio($map_view, 'mm_view_hyb','map_view', 'hyb', '<span>Hybrid</span>');
	                    ?>
	                    
					<div class="clear"></div>	                        
	                <p>
	                	<label for="<?php echo $this->get_field_id('refresh_rate'); ?>"><strong><?php _e('Slideshow Speed', 'trulia'); ?></strong></label>
	                	<span class="howto"><?php _e('Automatically animate through all of your newest listings.', 'trulia'); ?></span>
	                	<select id="<?php echo $this->get_field_id('refresh_rate'); ?>" name="<?php echo $this->get_field_name('refresh_rate'); ?>">
	                        <option value="0" <?php selected($refresh_rate, 0); ?>>
	                            <?php _e('Don\'t animate', 'trulia'); ?>
	                        </option>
							
							<option value="5" <?php selected($refresh_rate, 5); ?>>
	                            <?php _e('Show new property every 5 seconds', 'trulia'); ?>
	                        </option>
							
							<option value="10" <?php selected($refresh_rate, 10); ?>>
	                            <?php _e('Show new property every 10 seconds', 'trulia'); ?>
	                        </option>
							
	                        <option value="15" <?php selected($refresh_rate, 15); ?>>
	                            <?php _e('Show new property every 15 seconds', 'trulia'); ?>
	                        </option>
	
	                        <option value="20" <?php selected($refresh_rate, 20); ?>>
	                            <?php _e('Show new property every 20 seconds', 'trulia'); ?>
	                        </option>
	
	                        <option value="30" <?php selected($refresh_rate, 30); ?>>
	                            <?php _e('Show new property every 30 seconds', 'trulia'); ?>
	                        </option>
	
	                        <option value="45" <?php selected($refresh_rate, 45); ?>>
	                            <?php _e('Show new property every 45 seconds', 'trulia'); ?>
	                        </option>
	
	                        <option value="60" <?php selected($refresh_rate, 60); ?>>
	                            <?php _e('Show new property every 60 seconds', 'trulia'); ?>
	                        </option>
	                    </select>
					</p>
	
	                <div class="palette">
	                    <p style="margin-bottom:0.25em;">
	                    	<label><strong><?php _e('Color Choices', 'trulia'); ?></strong></label>
	                    	<span class="howto"><?php _e('Choose the background color and text color for the map.', 'trulia'); ?></span>
	                    </p>
	
	                    <table class="color_table" cellpadding="0" cellspacing="0">
	                        <tr>
	                            <td align="right" style="padding-right:5px;"><label class="background_label"<?php if(!isset($active_swatch_picker) || $active_swatch_picker == 'background') { echo '  style="font-weight:bold; font-size:.9em;"'; } ?>><?php _e('Background', 'trulia'); ?></label></td>
	
	                            <td class="colorpick_bg_cell"></td>
	                            <td class="swatch_picker_cell background_picker_cell<?php if(!isset($active_swatch_picker) || $active_swatch_picker == 'background') { echo ' active_swatch_picker'; } ?>" style="border:thick solid #fff;">
	                            	<span class="swatch_picker background_picker" style="background-color:<?php _e($map_background_color); ?>;"><span class="pixel"></span></span>
	                            </td>
	
	                            <td rowspan="7" id="color_box_content_cell">
	                                <div id="color_box_content">
	                                    <span class='swatch blk' style='background-color:#000000'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#5D5D5D;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#C0CAC8;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#92ABC4;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#BCCFE3;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch wht' style='background-color:#EFF0F1;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch wht' style='background-color:#FFFFFF;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#92A2A0'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#C5C9B2;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#D9DDCC;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#E0DED3;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#E9E7E0;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#F3F2EA;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#F6F2EF;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch' style='background-color:#526131;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch grn' style='background-color:#829B29'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch grn' style='background-color:#B5CF5A;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch grn' style='background-color:#C2DA6D;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch grn' style='background-color:#CEDF7D;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch grn' style='background-color:#D7E497;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch gry' style='background-color:#E6EBB1;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#2F5F8B'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#1A89C5;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#62A1D1;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#7BC0E6;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#92D5FA;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#B9E4FB;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch blu' style='background-color:#D8EDFC;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch red' style='background-color:#7C4336'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch org' style='background-color:#DF7E00;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch org' style='background-color:#F1AA3F;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch org' style='background-color:#F8D249;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch org' style='background-color:#FDE576;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch yel' style='background-color:#FEF9C0;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch wht' style='background-color:#FEFDE7;'>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class='swatch wht' style='background-color:#FEFDE7;'>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                                </div>
	                            </td>
	
	                            <td rowspan="7">&nbsp;&nbsp;</td>
	                        </tr>
	
	                        <tr>
	                            <td align="right" style="padding-right:5px;"><label class="text_label"<?php if($active_swatch_picker == 'text') { echo '  style="font-weight:bold; font-size:.9em;"'; } ?>>Text</label></td>
							
								<td class="colorpick_bg_cell"></td>
	                            <td class="swatch_picker_cell text_picker_cell<?php if($active_swatch_picker == 'text') { echo ' active_swatch_picker'; } ?>" style="border:thick solid #fff;">
	                            	<span class="swatch_picker text_picker" style="background-color:<?php _e($map_text_color); ?>;"><span class="pixel"></span></span>
	                            </td>
	                        </tr>
	
	                        <tr>
	                            <td scope="col"></td>
	
	                            <td id="op_label_color_cell"><span>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	                        </tr>
	
	                        <tr>
	                            <td scope="col"></td>
	
	                            <td id="op_label_color_cell"><span>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	                        </tr>
	
	                        <tr>
	                            <td scope="col"></td>
	
	                            <td id="op_label_color_cell"><span>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	                        </tr>
	
	                        <tr>
	                            <td scope="col"></td>
	
	                            <td id="op_label_color_cell"><span>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	                        </tr>
	                    </table>
	                </div><!--Color Selector-->
	              
		            	<input type="hidden" class="active_swatch_picker" value="<?php _e($active_swatch_picker); ?>" id="<?php echo $this->get_field_id('active_swatch_picker'); ?>" name="<?php echo $this->get_field_name('active_swatch_picker'); ?>" />
		            	<input type="hidden" value="0" id="<?php echo $this->get_field_id('sid'); ?>" name="<?php echo $this->get_field_name('sid'); ?>" /> 
		            	<input type="hidden" class="map_text_color" value="<?php _e($map_text_color); ?>" id="<?php echo $this->get_field_id('map_text_color'); ?>" name="<?php echo $this->get_field_name('map_text_color'); ?>" />
		            	<input type="hidden" class="map_background_color" value="<?php _e($map_background_color); ?>" id="<?php echo $this->get_field_id('map_background_color'); ?>" name="<?php echo $this->get_field_name('map_background_color'); ?>" /> 
		            	<input type="hidden" class="logo_color" value="<?php _e($logo_color); ?>" id="<?php echo $this->get_field_id('logo_color'); ?>" name="<?php echo $this->get_field_name('logo_color'); ?>" /> 
		            	<input type="hidden" value="0" id="<?php echo $this->get_field_id('site_ids'); ?>" name="<?php echo $this->get_field_name('site_ids'); ?>" />
		            	<input type="hidden" value="1" id="<?php echo $this->get_field_id('saved'); ?>" name="<?php echo $this->get_field_name('saved'); ?>" />
		            	<input type="hidden" value="0" name="<?php echo $this->get_field_name('terms'); ?>" />

		   	</div>
		   	</div> 					
		   	
		   	<div class="clear stuffbox">
		   	
		   	<div class="widget-top"><div class="widget-title"><h4 rel="#trulia-preview-<?php echo $this->number; ?>">TruliaMap Preview<span class="cc-arrow in-widget-title"></span></h4></div></div>
 			<div class="section" id="trulia-preview-<?php echo $this->number; ?>"> 				
	 			 	<?php $this->generateCode($settings, true); ?>
			</div>
			            </div>
			<div class="clear termsdiv">
 					<?php if($saved && empty($terms)) { ?>
 						<div style="background-color: rgb(255, 235, 232);border-color: rgb(204, 0, 0);-webkit-border-bottom-left-radius: 3px 3px;-webkit-border-bottom-right-radius: 3px 3px;-webkit-border-top-left-radius: 3px 3px;-webkit-border-top-right-radius: 3px 3px;border-style: solid;border-width: 1px;margin: 5px 0px 15px;padding: 10px 0.6em 0;"><div class="wrap"><p><?php _e('Agreeing to the Terms &amp; Conditions is required for the Trulia widget to display on the website.', 'trulia'); ?></p></div></div><?php 
 					} ?>
                    <h4><label for="<?php echo $this->get_field_id('terms'); ?>"<?php if($saved && empty($terms)) { ?> class="truliaError"<?php }?>><input name="<?php echo $this->get_field_name('terms'); ?>" id="<?php echo $this->get_field_id('terms'); ?>" type="checkbox" value="1" <?php checked($terms, '1'); ?> /> <span>I agree to <a target="_blank" href="http://www.trulia.com/terms">Trulia Terms &amp; Conditions</a> of use</span></span> <span style="color:red;">(Required)</span></label></h4>
            </div>
	</div>
	           <?php
		}

  
	    function generateCode($settings, $echo = false) {
	    	extract($settings);
	    	if(!is_admin() && empty($terms)) { return '<!-- Trulia: you must agree to the terms and conditions of use. -->'; }
			$size = trim(strtolower($size));
			
			switch($size) {
	    		case 'panoramic': 
	    			$map_width = 800;
	    			$map_height = 230;
	    			break;
	    		case 'small':
	    			$map_width = 230;
	    			$map_height = 380;
	    			break;
	    		case 'custom':
	    			break;
	    		case 'large':
	    		default: 
	    			$map_width = 350;
	    			$map_height = 380;
	    			break;
	    	}
	    	
	    	$city_state = trim(rtrim($city_state));
	    	
	    	// If there's a ZIP code, don't set city or state.
	    	if(preg_match('/^\d{5}([\-]\d{4})?$/', $city_state)) {
	    		$map_zip = $city_state;	
	    		$map_state = $map_city = '';
	    	} elseif(!empty($map_zip) || !empty($zip)) {
	    		$map_state = $map_city = '';
	    		if(!empty($zip)) {
	    			$map_zip = $zip;
	    		}
	    		$map_zip = trim(rtrim($map_zip));
	    	} elseif(!empty($map_city) && !empty($map_state)) {
	    		$map_state = trim(rtrim($map_state));
	    		$map_city = trim(rtrim($map_city));
	    	} else {
	    		$map_zip = '';
	    		@list($map_city, $map_state) = explode(',', $city_state);
	    		$map_state = trim(rtrim($map_state));
	    		$map_city = trim(rtrim($map_city));
	    	}
	    	
	    	if($align == 'center' || $align == 'left' || $align == 'right' || $align == 'none' ) {
	    		$align = 'align'.$align;
	    	} else {
	    		$align = 'aligncenter';
	    	}
	    	
	    	$map_background_color = $this->process_color($map_background_color);
	    	$map_text_color = $this->process_color($map_text_color);
	    	$map_iframe_width = $map_width - 10;
	    	$map_iframe_height = $map_height - 50;
	    	
	    	if($is_shortcode) {
	    		$number = rand(5000,100000);
	    	} else {
	    		$number = $this->number;
	    	}
	    	
	    	$iframecode = '<iframe style="border:none; padding:0px; width:'.$map_iframe_width.'px; height:'.$map_iframe_height.'px;" frameborder="0" width="'.$map_iframe_width.'" height="'.$map_iframe_height.'" scrolling="no" src="http://truliamap.trulia.com/truliamapapi/?sid=&cv=&v='.$map_view.'&d=source&lat='.$latitude.'&lng='.$longitude.'&s='.$size.'&r='.$refresh_rate.'&city='.$map_city.'&state='.$map_state.'&uid=&zip='.$map_zip.'">Your browser is not compatible with this Trulia map.</iframe>';
	    	
	    	$iframecode = apply_filters('trulia_iframe', $iframecode);
	    	
	    	$output = '
	    	<div class="truliamap '.$align.'" style="font-family:arial,verdana,sans-serif;text-align:center; width:'.$map_width.'px; background-color:'.$map_background_color.';">
		   		<h3 class="truliamap_header" style="color:'.$map_text_color.';font-weight:bold;font-size:14px;margin:0; padding:.25em;">'.urldecode($map_header_text).'</h3>
		   		<div id="truliamap_map_'.$number.'">'.$iframecode.'</div>
		   		<div style="margin:0 5px;text-align:left;">
		   			<div style="float:left;width:'.($map_iframe_width - 89).'px; margin:0; padding:0; font-size:10px; line-height:31px;">';
		   			
							$output .= apply_filters('trulia_link', $this->attr($city_state));
		   	
		   	$output .= '</div>';
		   	$output .= apply_filters('trulia_logo', '<a href="http://wordpress.org/extend/plugins/trulia/" class="trulia_logo" style="width:78px; height:35px; padding:0px; margin:0px;border-width:0px; background: url('.$this->url.'/images/logo-'.$logo_color.'.gif) left top no-repeat; text-align:left; text-indent:-9999px; overflow:hidden; float:right; outline:0; border: none;" title="Trulia for WordPress">Trulia for WordPress</a>');
		   	$output .= '
			   	</div>
			   	<div style="clear:both;"></div>
			</div>
';
			$output = apply_filters('trulia_map', $output);
			if($echo) { echo $output; return; } else { return $output; }
	    }
	    
	    function make_textfield($setting = '', $fieldid = '', $fieldname='', $title = '', $error = '') {
			$input = '';
			$fieldnameOrig = $fieldname;
		    $fieldid = $this->get_field_id($fieldid);
		    $fieldname = $this->get_field_name($fieldname);
		    if(!empty($error)) {
		    	 $input .= '<div style="background-color: rgb(255, 235, 232);border-color: rgb(204, 0, 0);-webkit-border-bottom-left-radius: 3px 3px;-webkit-border-bottom-right-radius: 3px 3px;-webkit-border-top-left-radius: 3px 3px;-webkit-border-top-right-radius: 3px 3px;border-style: solid;border-width: 1px;margin: 5px 0px 15px;padding: 10px 0.6em 0;"><div class="wrap"><label for="'.$fieldid.'">'.wpautop($error).'</label></div></div>';
		    }
		    
			$input .= '
			<p class="'.$fieldid.'">
				<label for="'.$fieldid.'">'.__(wptexturize($title)).'
				<input type="text" class="widefat '.sanitize_title($fieldnameOrig).'" id="'.$fieldid.'" name="'.$fieldname.'" value="'.$setting.'"/>
				</label>
			</p>';
			
			echo $input;
		}    
		function make_checkbox($setting = '', $fieldid = '', $fieldname='', $title = '') {
			$fieldid = $this->get_field_id($fieldid);
		    $fieldname = $this->get_field_name($fieldname);
		    
			$checkbox = '
			<p class="'.$fieldid.'">
				<input type="hidden" name="'.$fieldname.'" value="no" />
				<input type="checkbox" id="'.$fieldid.'" name="'.$fieldname.'" value="yes"'.checked($setting, 'yes', false).' class="checkbox" />
				<label for="'.$fieldid.'">'.__(wptexturize($title)).'</label>
			</p>';
		    echo $checkbox;
		}
		function make_radio($setting = '', $fieldid = '', $fieldname='', $value = '', $title = '') {
			$fieldnameOrig = $fieldname;
			$fieldid = $this->get_field_id($fieldid);
		    $fieldname = $this->get_field_name($fieldname);
		    
			$checkbox = '
			<p class="'.$fieldid.' '.sanitize_title($fieldnameOrig).'">
				<input type="radio" id="'.$fieldid.'" name="'.$fieldname.'" value="'.$value.'"'.checked($setting, $value, false).' class="radio" />
				<label for="'.$fieldid.'">'.__($title).'</label>
			</p>';
		    echo $checkbox;
		}
		
		function is_valid_url($location, $default = '') {
			return $location;
	    	if(preg_match('/^(http\:\/\/|https\:\/\/)(([a-z0-9]([-a-z0-9]*[a-z0-9]+)?){1,63}\.)+[a-z]{2,6}/ism', $location) && parse_url($location)) {
	    		return $location;
	    	}
	    	if(empty($default)) { return false; } else { return $default; }
	    }
		
	    function attr($city_state = '') {
			global $post, $pagenow;// prevents calling before <HTML>
			if(($post && !is_admin()) || (is_admin() && defined('DOING_AJAX')) || (isset($pagenow) && $pagenow == 'widgets.php')) {
				$url = 'http://www.katzwebservices.com/development/attribution.php?site='.htmlentities(substr(get_bloginfo('url'), 7)).'&from=trulia&location='.urlencode($city_state).'&version='.$this->version;
				// > 2.8
				if(function_exists('fetch_feed')) {
					include_once(ABSPATH . WPINC . '/feed.php');
					if ( !$rss = fetch_feed($url) ) { return $default; }
					if(!is_wp_error($rss)) {
						// This list is only missing 'style', 'id', and 'class' so that those don't get stripped.
						// See http://simplepie.org/wiki/reference/simplepie/strip_attributes for more information.
						$strip = array('bgsound','expr','onclick','onerror','onfinish','onmouseover','onmouseout','onfocus','onblur','lowsrc','dynsrc');
						$rss->strip_attributes($strip);
						$rss->set_cache_duration(60*60*24*60); // Fetch every 60 days
						$rss_items = $rss->get_items(0, 1);	
						foreach ( $rss_items as $item ) {
							return str_replace(array("\n", "\r"), ' ', $item->get_description());
						}
					}
					return $default;
				} else { // < 2.8
					require_once(ABSPATH . WPINC . '/rss.php');
					if(!function_exists('fetch_rss')) { return $default; }
					if ( !$rss = fetch_rss($url) ) {
						return $default;
					}
					$items = 1;
					if ((!is_wp_error($rss) && !empty($rss)) && is_array( $rss->items ) && !empty( $rss->items ) ) {
						$rss->items = array_slice($rss->items, 0, $items);
						foreach ($rss->items as $item ) {
							if ( isset( $item['description'] ) && is_string( $item['description'] ) )
								$summary = $item['description'];
							$desc = str_replace(array("\n", "\r"), ' ', $summary);
							$summary = '';
							return $desc;
						}
					}
					return $default;
				}
			}
		}
	    
	} 	
}

?>