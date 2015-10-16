<?php 
class WCUF_Option
{
	public function __construct()
	{
	}
	public function get_fields_meta_data()
	{
		$fields =  get_option( 'wcuf_files_fields_meta' );
		//WPML
		if (isset($fields) && class_exists('SitePress'))
		{
			global $sitepress;
			foreach($fields as $key => $extra_field)
			{
				if($sitepress->get_default_language() !== ICL_LANGUAGE_CODE)
				{
					$fields[$key]['title'] =      			    apply_filters( 'wpml_translate_single_string', $extra_field['id'], 'woocommerce-files-upload',  'wcuf_'.$extra_field['id'].'_title', ICL_LANGUAGE_CODE  );
					$fields[$key]['description'] = 				apply_filters( 'wpml_translate_single_string', $extra_field['id'], 'woocommerce-files-upload',  'wcuf_'.$extra_field['id'].'_description', ICL_LANGUAGE_CODE  );
					$fields[$key]['message_already_uploaded'] =  apply_filters( 'wpml_translate_single_string', $extra_field['id'], 'woocommerce-files-upload',  'wcuf_'.$extra_field['id'].'_already_uploaded', ICL_LANGUAGE_CODE  );
				}
				
			}
		}
		return  $fields;
	}
	public function get_order_uploaded_files_meta_data($order_id)
	{
		return get_post_meta($order_id, '_wcst_uploaded_files_meta');
	}
	public function get_option($option_name)
	{
		return get_option($option_name);
	}
	public function update_option($field_name, $field_data )
	{
		//WPML managment
		if($field_name == 'wcuf_files_fields_meta' && class_exists('SitePress'))
			foreach($field_data as $file_meta)
			{
				//Register new string
				do_action( 'wpml_register_single_string', 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_title', $file_meta['title'] );
				do_action( 'wpml_register_single_string', 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_description', $file_meta['description'] );
				do_action( 'wpml_register_single_string', 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_already_uploaded', $file_meta['message_already_uploaded'] );
			}
			
		return update_option( $field_name, $field_data );
	}
	public function delete_option($field_name )
	{
		if($field_name == 'wcuf_files_fields_meta'  && class_exists('SitePress') && function_exists ( 'icl_unregister_string' ))
		{
			$fields =  get_option( 'wcuf_files_fields_meta' );
			foreach($fields as $file_meta)
			{
				icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_title' );
				icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_description' );
				icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_already_uploaded' );
			}
		}
		return delete_option( $field_name);
	}
	public function unregister_wpml_strings($ids)
	{
		$fields =  get_option( 'wcuf_files_fields_meta' );
		$rearranged_array = array();
		foreach($fields as $field)
		{
			$rearranged_array[$field['id']] = $field;
		}
		
		if(class_exists('SitePress') && function_exists ( 'icl_unregister_string' ))
			foreach($ids as $id => $id_to_delete)
			{
				if($id_to_delete && isset($rearranged_array[$id]))
				{
					icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_title' );
					icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_description' );
					icl_unregister_string ( 'woocommerce-files-upload', 'wcuf_'.$file_meta['id'].'_already_uploaded' );
				}
			}
	}
}
?>