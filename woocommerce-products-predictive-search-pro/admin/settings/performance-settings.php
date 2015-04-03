<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Performance Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WC_Predictive_Search_Performance_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'performance-settings';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = '';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_predictive_search_performance_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	public function custom_types() {
		$custom_type = array( 'min_characters_yellow_message', 'time_delay_yellow_message' );
		
		return $custom_type;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		// add custom type
		foreach ( $this->custom_types() as $custom_type ) {
			add_action( $this->plugin_name . '_admin_field_' . $custom_type, array( $this, $custom_type ) );
		}
		
		$this->init_form_fields();
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Performance Settings successfully saved.', 'woops' ),
				'error_message'		=> __( 'Error: Performance Settings can not save.', 'woops' ),
				'reset_message'		=> __( 'Performance Settings successfully reseted.', 'woops' ),
			);
						
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'performance-settings',
			'label'				=> __( 'Performance', 'woops' ),
			'callback_function'	=> 'wc_predictive_search_performance_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wc_predictive_search_admin_interface;
		
		$output = '';
		$output .= $wc_predictive_search_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
			
			array(
            	'name' 		=> __( 'Search Performance Settings', 'woops' ),
                'type' 		=> 'heading',
				'desc'		=> __( "If you have a large site with 1,000's of products or an underpowered server use the settings below to tweak the search performance.", 'woops' ),
				'id'		=> 'predictive_search_performance_settings',
           	),
			array(  
				'name' 		=> __( "Charaters Before Query", 'woops' ),
				'desc' 		=> __("characters", 'woops'). '. ' .__( 'Number of Characters min 1, max 6', 'woops' ),
				'id' 		=> 'woocommerce_search_min_characters',
				'type' 		=> 'slider',
				'default'	=> 1,
				'min'		=> 1,
				'max'		=> 6,
				'increment'	=> 1
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container min_characters_yellow_message_container',
           	),
			array(
                'type' 		=> 'min_characters_yellow_message',
           	),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Query Time Delay', 'woops' ),
				'desc' 		=> __( 'milli seconds', 'woops'). '. ' .__( 'min 500, max 1,500', 'woops' ),
				'id' 		=> 'woocommerce_search_delay_time',
				'type' 		=> 'slider',
				'default'	=> 600,
				'min'		=> 500,
				'max'		=> 1500,
				'increment'	=> 100
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container time_delay_yellow_message_container',
           	),
			array(
                'type' 		=> 'time_delay_yellow_message',
           	),
		
        ));
	}
		
	public function min_characters_yellow_message( $value ) {
	?>
    	<tr valign="top" class="min_characters_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$min_characters_yellow_message = '<div>'. __( 'Number of characters that must be typed before the first search query. Setting 6 will decrease the number of queries  on your database by a factor of ~5 over a setting of 1.' , 'woops' ) .'</div><div>&nbsp;</div>
				<div style="clear:both"></div>
                <a class="min_characters_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woops' ).'</a>
                <a class="min_characters_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woops' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $min_characters_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .min_characters_yellow_message_container {
<?php if ( get_option( 'wc_ps_min_characters_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ps_min_characters_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".min_characters_yellow_message_dontshow", function(){
		$(".min_characters_yellow_message_tr").slideUp();
		$(".min_characters_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dontshow",
				option_name: 	"wc_ps_min_characters_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".min_characters_yellow_message_dismiss", function(){
		$(".min_characters_yellow_message_tr").slideUp();
		$(".min_characters_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dismiss",
				session_name: 	"wc_ps_min_characters_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
	
	public function time_delay_yellow_message( $value ) {
	?>
    	<tr valign="top" class="time_delay_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$time_delay_yellow_message = '<div>'. __( 'Time delay after a character is entered and query begins. Example setting 1,000 is 1 second after that last charcter is typed. If speed type a 10 letter word then first query is whole word not 1 query for each character. Reducing queries  to database by a factor of ~10.' , 'woops' ) .'</div><div>&nbsp;</div>
				<div style="clear:both"></div>
                <a class="time_delay_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woops' ).'</a>
                <a class="time_delay_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woops' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $time_delay_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .time_delay_yellow_message_container {
<?php if ( get_option( 'wc_ps_time_delay_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ps_time_delay_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".time_delay_yellow_message_dontshow", function(){
		$(".time_delay_yellow_message_tr").slideUp();
		$(".time_delay_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dontshow",
				option_name: 	"wc_ps_time_delay_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".time_delay_yellow_message_dismiss", function(){
		$(".time_delay_yellow_message_tr").slideUp();
		$(".time_delay_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dismiss",
				session_name: 	"wc_ps_time_delay_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
}

global $wc_predictive_search_performance_settings;
$wc_predictive_search_performance_settings = new WC_Predictive_Search_Performance_Settings();

/** 
 * wc_predictive_search_performance_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_performance_settings_form() {
	global $wc_predictive_search_performance_settings;
	$wc_predictive_search_performance_settings->settings_form();
}

?>