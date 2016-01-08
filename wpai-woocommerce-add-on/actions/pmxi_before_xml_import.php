<?php
function pmwi_pmxi_before_xml_import( $import_id )
{
	delete_option('wp_all_import_' . $import_id . '_parent_product');	
}