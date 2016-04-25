<?php
/*
Plugin Name: WooCommerce PDF & Print
Plugin URI: 
Description: Adds PDF, Doc and Print buttons on WooCommerce Product Page. Allows print and save product information as PDF and MS Doc file.
Version: 1.5.0
Author: gVectors Team
Author URI: http://www.gvectors.com
*/


if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/'. basename (WP_CONTENT_URL) );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . basename (WP_CONTENT_URL) );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	  
define('WPP_FOLDER', dirname(__FILE__) .'/' );
define('WPP_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('WPP', 'wpp_main'); 

////////////////////////////////////////////////////////////////////////////////////////
require( WPP_FOLDER . 'functions.php' );

	add_action( "woocommerce_product_meta_end", "wpp_saving_buttons", 1982 );
	
	if( isset($_GET[ 'wpp_export' ]) ) {
		add_action('wp', 'wpp_export');
	}
#################################################################################################################
################################# INSTALLATION ##################################################################

function wpp_install () {

   global $wpdb;
   global $wpp_db_version;
   define( 'WPP_PREFIX' , $wpdb->prefix);
   $version = '1.0.1';
   
   $wpp_options = array(     'wpp_onoff_saving_manager',
   							 'wpp_onoff_saving_doc',
							 'wpp_onoff_saving_pdf',
							 'wpp_onoff_saving_print',
							 
							 'wpp_save_doc_icon_url',
							 'wpp_save_pdf_icon_url',
							 'wpp_save_print_icon_url',
							 
							 'wpp_save_doc_template',
							 'wpp_save_doc_button_url', 
							 'wpp_save_doc_button_text',
							 'wpp_save_doc_button_location', 
							 'wpp_doc_t_title',
							 'wpp_doc_t_image',
							 'wpp_doc_t_excerpt',
							 'wpp_doc_t_date',
							 'wpp_doc_t_md',
							 
							 'wpp_save_pdf_button_url', 
							 'wpp_save_pdf_button_text',
							 'wpp_save_pdf_button_location', 
							 
							 'wpp_save_print_button_url', 
							 'wpp_save_print_button_text',
							 'wpp_save_print_button_location', 
							 
							 'wpp_saving_align', 
							 'wpp_saving_type',
							 'wpp_saving_position', 
							 'wpp_saving_location_postend', 
							 'wpp_saving_location_custom',
							 
							 'wpp_save_doc_button_type',
							 'wpp_save_pdf_button_type', 
							 'wpp_save_print_button_type', 
							 
							 'wpp_onoff_print_manager',
							 'wpp_print_location_postend',
							 'wpp_print_location_custom',
							 'wpp_print_type',
							 'wpp_print_app',
							 'wpp_pt_title',
							 'wpp_pt_image',
							 'wpp_pt_excerpt',
							 'wpp_pt_date',
							 'wpp_pt_md',
							 'wpp_pt_links',
							 'wpp_pt_header',
							 'wpp_pt_head_date',
							 'wpp_pt_head_site',
							 'wpp_pt_head_url' );
   
   //------------- Adding Options ----------------------------//
	
	if( get_option('wpp_installed') == '' ){ 
		foreach ( $wpp_options as $wpp_ ) { add_option( $wpp_ , 1 ); }
		update_option( 'wpp_installed', '1.0.0' );
		update_option( 'wpp_saving_position', 0 );
		update_option( 'wpp_save_doc_button_url', '100/doc-m.png' );
		update_option( 'wpp_save_doc_icon_url', 'doc.gif' );
		update_option( 'wpp_save_doc_button_text', 'Save as Word Document' );
		update_option( 'wpp_save_pdf_button_url', '100/pdf-m.png' );
		update_option( 'wpp_save_pdf_icon_url', 'pdf.gif' );
		update_option( 'wpp_save_pdf_button_text', 'Save as PDF' );
		update_option( 'wpp_save_print_button_url', '100/print-m.png' );
		update_option( 'wpp_save_print_icon_url', 'print.gif' );
		update_option( 'wpp_save_print_button_text', 'Print this Product' );
		update_option( 'wpp_onoff_save_follow', 1 ); 
		update_option( 'wpp_save_text_align', 'left' ); 
		update_option( 'wpp_save_pdf_img_show', 1 ); 
		update_option( 'wpp_save_pdf_rus', 0 ); 
		update_option( 'wpp_save_pdf_img_max_width', '500' );
		update_option( 'wpp_save_doc_img_max_width', '500' ); 
		update_option( 'wpp_save_print_img_max_width', '500' );
	}
	if( version_compare( get_option('wpp_installed'), '1.2.0') < 0 ){
		update_option( 'wpp_installed', '1.2.0' ); //
	}
	if( version_compare( get_option('wpp_installed'), '1.2.3') < 0 ){
		update_option( 'wpp_installed', '1.2.3' ); //
	}
	if( version_compare( get_option('wpp_installed'), '1.3.1') < 0 ){
		update_option( 'wpp_installed', '1.3.1' ); //
	}
	if( version_compare( get_option('wpp_installed'), '1.3.2') < 0 ){
		update_option( 'wpp_installed', '1.3.2' ); //
	}
	if( version_compare( get_option('wpp_installed'), '1.3.3') < 0 ){
		update_option( 'wpp_installed', '1.3.3' ); //
		update_option( 'wpp_ph_sku', 'SKU' ); 
		update_option( 'wpp_ph_in_stock', 'In stock' ); 
		update_option( 'wpp_ph_rating', 'Rating' ); 
		update_option( 'wpp_ph_prod_cats', 'Product Categories' ); 
		update_option( 'wpp_ph_prod_tags', 'Product Tags' ); 
		update_option( 'wpp_ph_prod_page', 'Product Page' ); 
		update_option( 'wpp_ph_prod_vars', 'Product Variants' ); 
		update_option( 'wpp_ph_prod_summ', 'Product Summary' ); 
		update_option( 'wpp_ph_prod_desc', 'Product Description' ); 
		update_option( 'wpp_ph_prod_attr', 'Product Attributes' ); 
		update_option( 'wpp_ph_prod_gall', 'Product Gallery' ); 
		update_option( 'wpp_ph_print_post', 'Print this Product' ); 
		update_option( 'wpp_ph_dimensions', 'Dimensions' ); 
		update_option( 'wpp_ph_weight', 'Weight' ); 
		update_option( 'wpp_ph_price', 'Price' ); 
		update_option( 'wpp_ph_prod_date', 'Product added date' ); 
		update_option( 'wpp_ph_prod_mod_date', 'Product modified date' ); 
		update_option( 'wpp_ph_real_price', 'Real Price' ); 
		update_option( 'wpp_ph_curr_sale_price', 'Current Sale Price' ); 
		update_option( 'wpp_ph_exp_date', 'Export date' ); 
		update_option( 'wpp_ph_links', 'Links' ); 
		update_option( 'wpp_ph_page_exported_from', 'Product data have been exported from' );
	}
	if( version_compare( get_option('wpp_installed'), '1.4.0') < 0 ){
		update_option( 'wpp_installed', '1.4.0' );
		
		update_option( 'wpp_T_logo', '' ); 
		update_option( 'wpp_T_featured_image', '1' ); 
		update_option( 'wpp_T_price_html', '1' ); 
		update_option( 'wpp_T_sku', '1' ); 
		update_option( 'wpp_T_stock', '1' ); 
		update_option( 'wpp_T_rating', '1' ); 
		update_option( 'wpp_T_categories', '1' ); 
		update_option( 'wpp_T_tags', '1' ); 
		update_option( 'wpp_T_permalink', '1' ); 
		update_option( 'wpp_T_variants', '1' ); 
		update_option( 'wpp_T_summary', '1' ); 
		update_option( 'wpp_T_description', '1' ); 
		update_option( 'wpp_T_attribute', '1' ); 
		update_option( 'wpp_T_custom_tab', '1' ); 
		update_option( 'wpp_T_gallery', '1' ); 
	}
	if( version_compare( get_option('wpp_installed'), '1.4.1') < 0 ){
		update_option( 'wpp_installed', '1.4.1' );
	}
	if( version_compare( get_option('wpp_installed'), '1.4.2') < 0 ){
		update_option( 'wpp_installed', '1.4.2' );
	}
	if( version_compare( get_option('wpp_installed'), '1.4.3') < 0 ){
		update_option( 'wpp_installed', '1.4.3' );
	}
	if( version_compare( get_option('wpp_installed'), '1.5.0') < 0 ){
		update_option( 'wpp_installed', '1.5.0' );
	}
				
}

add_action('admin_init', 'wpp_install', 2);


function wpp_css() { echo "<link rel='stylesheet' href='".WPP_PATH ."css/wpp.css' type='text/css' />"; }
add_action('admin_head','wpp_css');

#####################################################################################################################
################################### ADMIN OPTIONS  ##################################################################
//- Top Level Menu -//
$wpp_menu_array [0]['parent_file'] ='wpp_main';
$wpp_menu_array [0]['parent_menu_title'] = 'WooPP';
$wpp_menu_array [0]['parent_menu_icon'] = WPP_PATH.'img/mini.png';
$wpp_menu_array [0]['parent_level'] = 'manage_options';
$wpp_menu_array [0]['parent_page_title'] = 'ProfProjects - WooCommerce PDF &amp; Print';
//- Sub Menu Overview -//
$wpp_menu_array [0]['page']['wpp_main']['page_menu_title'] = 'General';
$wpp_menu_array [0]['page']['wpp_main']['page_title'] = 'WooCommerce PDF &amp; Print by ProfProjects';
$wpp_menu_array [0]['page']['wpp_main']['page_header'] = __( 'WooCommerce PDF &amp; Print - General Settings');
$wpp_menu_array [0]['page']['wpp_main']['page_screen_custom_icon'] = WPP_PATH.'img/icon.png';
$wpp_menu_array [0]['page']['wpp_main']['page_screen_icon'] = 'options-general';
$wpp_menu_array [0]['page']['wpp_main']['page_level'] = 'manage_options';
$wpp_menu_array [0]['page']['wpp_main']['page_file'] = 'wpp_main' ;
$wpp_menu_array [0]['page']['wpp_main']['page_column_number'] = 2;
$wpp_menu_array [0]['page']['wpp_main']['page_include_file_top'] = 'overview.php';
$wpp_menu_array [0]['page']['wpp_main']['page_include_file_bottom'] = 'footer.php'; 
$wpp_menu_array [0]['page']['wpp_main']['page_type'] = 'admin_simple';
//- Sub Menu Saving Manager -//
$wpp_menu_array [0]['page']['wpp_saving']['page_menu_title'] = 'Saving Manager';
$wpp_menu_array [0]['page']['wpp_saving']['page_title'] = 'WooCommerce PDF &amp; Print - Saving Manager';
$wpp_menu_array [0]['page']['wpp_saving']['page_header'] = __( 'Save as PDF &amp; Word Document Manager');
$wpp_menu_array [0]['page']['wpp_saving']['page_screen_custom_icon'] = WPP_PATH.'img/icon.png';
$wpp_menu_array [0]['page']['wpp_saving']['page_screen_icon'] = 'options-general';
$wpp_menu_array [0]['page']['wpp_saving']['page_level'] = 'manage_options';
$wpp_menu_array [0]['page']['wpp_saving']['page_file'] = 'wpp_saving' ;
$wpp_menu_array [0]['page']['wpp_saving']['page_column_number'] = 1;
$wpp_menu_array [0]['page']['wpp_saving']['page_include_file_top'] = 'saving.php';
$wpp_menu_array [0]['page']['wpp_saving']['page_include_file_bottom'] = 'footer.php';
$wpp_menu_array [0]['page']['wpp_saving']['page_type'] = 'admin_box';
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['doc_save']['contentbox_id'] = 'cb_' . mt_rand(1,1000000);
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['doc_save']['contentbox_title'] = 'Word Document Save Options' ;
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['doc_save']['contentbox_data'] = '' ;
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['pdf_save']['contentbox_id'] = 'cb_' . mt_rand(1,1000000);
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['pdf_save']['contentbox_title'] = 'PDF Save Options' ;
$wpp_menu_array [0]['content']['wpp_saving']['contentbox']['pdf_save']['contentbox_data'] = '' ;
//- Sub Menu Print Manager -//
$wpp_menu_array [0]['page']['wpp_print']['page_menu_title'] = 'Print Manager';
$wpp_menu_array [0]['page']['wpp_print']['page_title'] = 'WooCommerce PDF &amp; Print - Print Manager';
$wpp_menu_array [0]['page']['wpp_print']['page_header'] = __( 'Print Manager');
$wpp_menu_array [0]['page']['wpp_print']['page_screen_custom_icon'] = WPP_PATH.'img/icon.png';
$wpp_menu_array [0]['page']['wpp_print']['page_screen_icon'] = 'options-general';
$wpp_menu_array [0]['page']['wpp_print']['page_level'] = 'manage_options';
$wpp_menu_array [0]['page']['wpp_print']['page_file'] = 'wpp_print' ;
$wpp_menu_array [0]['page']['wpp_print']['page_column_number'] = 1;
$wpp_menu_array [0]['page']['wpp_print']['page_include_file_top'] = 'print.php';
$wpp_menu_array [0]['page']['wpp_print']['page_include_file_bottom'] = 'footer.php';
$wpp_menu_array [0]['page']['wpp_print']['page_type'] = 'admin_box';
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_template']['contentbox_id'] = 'cb_' . mt_rand(1,1000000);
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_template']['contentbox_title'] = 'Print Template Settings' ;
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_template']['contentbox_data'] = '' ;
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_img']['contentbox_id'] = 'cb_' . mt_rand(1,1000000);
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_img']['contentbox_title'] = 'Print Buttons' ;
$wpp_menu_array [0]['content']['wpp_print']['contentbox']['print_img']['contentbox_data'] = '' ;

######################################################################################################################
############################################### - MENU CLASS - #######################################################
class wpp_admin_box {

	var $pn;
	var $pagehook;
	var $data_array;
	var $wpp_unsp = false;
	var $wpp_note;
	
	function wpp_admin_box ( $ex_array, $page_name ) {
		$this->data_array = $ex_array ;
		$this->pn = $page_name ;
	}
	
	function wpp_admin() {
		
		add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns' ), 10, 2);
		add_action('admin_menu',  array(&$this, 'on_admin_menu' )); 
	}
	
	function on_admin_menu() {
		add_menu_page($this->data_array['parent_page_title'], $this->data_array['parent_menu_title'] , $this->data_array['parent_level'], $this->data_array['parent_file'] , array(&$this, 'on_show_page'), $this->data_array['parent_menu_icon']);
		foreach($this->data_array['page'] as $name){
			if($name['page_file'] == $this->pn){
				$this->pagehook = add_submenu_page( $this->data_array['parent_file'] , $name['page_title'], $name['page_menu_title'], $name['page_level'], $name['page_file'], array(&$this, 'on_show_page' ));
			}
			else{
				add_submenu_page( $this->data_array['parent_file'] , $name['page_title'], $name['page_menu_title'], $name['page_level'], $name['page_file'], array(&$this, 'on_show_page' ));
			}
		}
		if( !empty($this->data_array['page'][$this->pn]) && $this->data_array['page'][$this->pn]['page_type'] == 'admin_box' ) {
			add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
		}
	}
	function on_screen_layout_columns($columns, $screen) {
		if ( $screen == $this->pagehook ){ 
			 $columns[ $this->pagehook ] = $this->data_array['page'][$this->pn]['page_column_number']; 
		}
		return $columns;
	}
	
	function on_load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		if( isset($this->data_array['content'][$this->pn]['sidebox']) && count($this->data_array['content'][$this->pn]['sidebox']) > 10) { 
			wp_die( __(' Number of sideboxes more then 10 !'));
		}
		$fn = 0;
		if( !empty($this->data_array['content'][$this->pn]['sidebox']) ){
			foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sid ){
				add_meta_box( $sid['sidebox_id'], $sid['sidebox_title'], array(&$this, 'sb_'.$fn),$this->pagehook, 'side', 'core');	
				$fn=$fn+1;				
			}
		}
	}
	
	function on_show_page() {
		global $screen_layout_columns;
		if( $this->data_array['page'][$this->pn]['page_type'] == 'admin_box' ) {	
			if( isset($this->data_array['content'][$this->pn]['contentbox']) && count($this->data_array['content'][$this->pn]['contentbox']) > 10 ) { 
				wp_die( __(' Number of contentbox more then 10 !'));
			}
			$fn = 0;
			if(!empty($this->data_array['content'][$this->pn]['contentbox'])) {
				foreach( $this->data_array['content'][$this->pn]['contentbox'] as $sid ){
					add_meta_box( $sid['contentbox_id'], $sid['contentbox_title'], array(&$this, 'cb_'.$fn),$this->pagehook, 'normal', 'core');
					$fn=$fn+1;				
				}
			}
		}
		
		?>
		
		<div id="wpp_wrap" class="wrap">
			<?php 
			if( !$this->data_array['page'][$this->pn]['page_screen_custom_icon'] ) {
				screen_icon($this->data_array['page'][$this->pn]['page_screen_icon']);
			}
			?>
			<h2>
			<?php 
			if( !empty($this->data_array['page'][$this->pn]['page_screen_custom_icon']) || $this->data_array['page'][$this->pn]['page_screen_custom_icon'] ) { 
				echo '<img src = "'.$this->data_array['page'][$this->pn]['page_screen_custom_icon'].'" align="absmiddle" style="background:#FFFFFF; border:#CCCCCC 1px solid; padding:1px;"> &nbsp;'; } 
			 _e( $this->data_array['page'][$this->pn]['page_header']) ?>
			 </h2>
			<?php 
			if(!empty($this->data_array['page'][$this->pn]) && $this->data_array['page'][$this->pn]['page_include_file_top'] ) { 
				include( WPP_FOLDER . $this->data_array['page'][$this->pn]['page_include_file_top'] ); 
			}
			?>
			<div id="poststuff" class="metabox-holder<?php echo $this->data_array['page'][$this->pn]['page_column_number'] == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<?php 
				if( !empty($this->data_array['page'][$this->pn]) && $this->data_array['page'][$this->pn]['page_type'] == 'admin_box' ) 
				{
		
					if($this->data_array['page'][$this->pn]['page_column_number'] == 2) 
					{
						?>
						<div id="side-info-column" class="inner-sidebar">
								<?php do_meta_boxes($this->pagehook , 'side', $data); ?>
						</div>
						
						<div id="post-body" class="has-sidebar">
							<div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes($this->pagehook , 'normal', $data); ?>
							</div>
						</div>
						<?php
					}
					else
					{
						@do_meta_boxes($this->pagehook , 'normal', $data);
						@do_meta_boxes($this->pagehook , 'side', $data);
					}
				?>
				<br class="clear"/>				
			</div>	
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook ; ?>');
			});
			//]]>
		</script>
			<?php
			if($this->data_array['page'][$this->pn]['page_include_file_bottom'] ) include( WPP_FOLDER . $this->data_array['page'][$this->pn]['page_include_file_bottom'] );
			}
			else {
				 if($this->data_array['page'][$this->pn]['page_include_file_bottom']) include( WPP_FOLDER . $this->data_array['page'][$this->pn]['page_include_file_bottom'] );
			}
	}

	function sb_0($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 0){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_1($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 1){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_2($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 2){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_3($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 3){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_4($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 4){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_5($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 5){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_6($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 6){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_7($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 7){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_8($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 8){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_9($data) {$i = 0;foreach( $this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 9){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function sb_10($data){$i = 0;foreach($this->data_array['content'][$this->pn]['sidebox'] as $sb => $sid ){if($i == 10){if($sid['sidebox_data']){echo $sid['sidebox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	
	function cb_0($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 0){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_1($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 1){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_2($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 2){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_3($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 3){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_4($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 4){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_5($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 5){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_6($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 6){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_7($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 7){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_8($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 8){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_9($data) {$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 9){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}
	function cb_10($data){$i = 0;foreach($this->data_array['content'][$this->pn]['contentbox'] as $cb => $sid ){if($i == 10){ if($sid['contentbox_data']){echo $sid['contentbox_data'];}else{include ( WPP_FOLDER . 'page_contents.php' );}}$i=$i+1;}}

}
if(is_admin()){
	$page = (isset($_GET['page'])) ? $_GET['page'] : false ;
	$wpp_admin_class = new wpp_admin_box( $wpp_menu_array[0], $page );
	$wpp_admin_class->wpp_admin();
}
?>