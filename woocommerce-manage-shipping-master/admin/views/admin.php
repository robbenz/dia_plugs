<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * Woocommerce Manage Shipping
 *
 * @package   woocommerce-manage-shipping
 * @author    Niels Donninger <niels@donninger.nl>
 * @license   GPL-2.0+
 * @link      http://donninger.nl
 * @copyright 2013 Donninger Consultancy
 * 
 * FIX FIX:
 update fay6zf1_woocommerce_order_itemmeta set meta_key="_purchased" where meta_key="purchased";
update fay6zf1_woocommerce_order_itemmeta set meta_key="_shipped" where meta_key="shipped";
 */
 
 $url = get_permalink() . "?page=" . $_GET["page"];
?>
<style type="text/css">
 .large {
	 font-size: 20px;
 }
 
 .ready {
	 
 }
 
 .shipped {
	 
 }
 
 td {
 }
</style>
<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function(){
			jQuery("input[type='checkbox']").click(function(){
				//PART 1: save/undo shipping metadata on order item level
				var url = '';
				var thisCheck = jQuery(this);
				jQuery( "#result_" +thisCheck.val() ).html("<img src='<?php echo plugins_url($this->plugin_slug . '/admin/assets/ajax-loader.gif') ?>'/>");
				if (thisCheck.is (':checked')) {
					url = "<?php echo $url ?>&" + thisCheck.attr('name') + "=" +thisCheck.val();
					console.log('calling ' +url);
					//alert(url);
					jQuery.get( url, function( data ) {
					  jQuery( "#result_" +thisCheck.val() ).html(thisCheck.attr('name').substr(0,thisCheck.attr('name').indexOf('_')));
					});				
				} else {
					url = "<?php echo $url ?>&undo=1&" + thisCheck.attr('name') + "=" +thisCheck.val();
					console.log('calling ' +url);
					jQuery.get( url, function( data ) {
					  jQuery( "#result_" +thisCheck.val() ).html(thisCheck.attr('name').substr(0,thisCheck.attr('name').indexOf('_')) + ' undone ');
					});
				}

				//PART 2: if all order items are shipped, complete order
				var orderClass = thisCheck.attr('class');
				//only look at the 'shipping' checkboxes
				if(orderClass.substr(0,4)=='ship') {
					var allIsShipped = true;
					jQuery("."+orderClass).each(function(){
						//alert(jQuery(this).is (':checked'));
						if(!jQuery(this).is (':checked')) {
							allIsShipped = false;
							//alert(jQuery(this).val() +" is not checked");
						}
					});
					var orderId = orderClass.substring(5); //order id is after 12th position in orderClass
					//alert(orderId);
					if(allIsShipped) { //all items checked: all items sent
						//save order status to 'completed'					
						url = "<?php echo $url ?>&complete_order=" +orderId;
						jQuery.get( url, function() {
							//hiding order rows
							jQuery(".order_" +orderId).css("background","green");
							jQuery(".order_" +orderId).hide(1000);
							console.log(url);
						});
					}
				}
			});
		});
		//]]>
</script>
<div id="result"></div>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- @TODO: Provide markup for your options page here. -->
	<?php
	$orders = $this->get_orders();
	echo "<table>";
	foreach($orders as $order) {
		//when using sequential order numbers, do this:
		$order_number = get_post_meta($order->id, "_order_number", true);
		if(!$order_number){ $order_number = $order->id; }

		echo "<thead class=\"order_{$order->id}\">";
		echo "<tr class=\"\"><td colspan=\"10\"><hr style=\"height: 4px; background: black\"/></td></tr>\n";
		echo "<tr><td></td><td></td><td></td><td></td><td>Stock</td><td>Shipped</td><td>Ready</td><td>Purchased</td></tr>";
		echo "</thead>\n";
		echo "<tbody class=\"order_{$order->id}\">";
		echo "<tr class=\"\">";
		echo "<td colspan=\"5\">";
		echo "<a href=\"" . get_edit_post_link($order->id) . "\"><h2>";
		echo "Order #{$order_number} - ";
		echo $order->billing_first_name . " " . $order->billing_last_name . " - ";
		echo $order->order_date;
		echo "</h2></a>";
		echo "</td>";
		echo "</tr>";
		
		$items = $order->get_items();
		foreach($items as $item_id => $item) {
			$meta = array();
			$shipped = false;
			$purchased = false;
			$ready = false;
			
			$shipped = $item["item_meta"]["_shipped"][0];
			$purchased = $item["item_meta"]["_purchased"][0];
			$ready = $item["item_meta"]["_ready"][0];
			
			//echo $item_id . " "; var_dump($item); echo "<hr/>";
			
			$product = new WC_Product($item["item_meta"]["_product_id"][0]);
			$variation = new WC_Product_Variation($item["item_meta"]["_variation_id"][0]);
			echo "<tr class=\"\"><td colspan=\"10\"><hr/></td></tr>\n";
			echo "<tr class=\"\">";
			echo "<td>&nbsp;</td><td class=\"large\"><strong>";
			echo $item["item_meta"]["_qty"][0];
			echo " x </strong></td>";
			echo "<td><a href=\"" . get_edit_post_link($item["item_meta"]["_product_id"][0]) . "\">" . $item["name"] . "</a></td>";
			echo "<td>";
			foreach($item["item_meta"] as $att => $value) {
				if(substr($att,0,1) != "_") {
					$meta [] = "<span title=\"$att\">" . $value[0] . "</span>";
				}
			}
			echo implode($meta, " / ");
			//var_dump($item["item_meta"]);
			echo "</td>";
			echo "<td><strong>";
			if($variation->exists()) { 
				echo $variation->get_stock_quantity();
			} else {
				echo $product->get_stock_quantity();
			}
			echo "</strong></td>";
			
			echo "<td><input type=\"checkbox\" class=\"ship_{$order->id}\" value=\"{$item_id}\" name=\"ship_order_item\"";
			if($shipped){ echo " title=\"$shipped\" checked=\"true\""; }
			echo "/>";
			echo "</td>";
			echo "<td><input type=\"checkbox\" class=\"ready_{$order->id}\" value=\"{$item_id}\" name=\"ready_order_item\"";
			if($ready){ echo " title=\"$ready\" checked=\"true\""; }
			echo "/>";
			echo "</td>";
			echo "<td><input type=\"checkbox\" class=\"purchased_{$order->id}\" value=\"{$item_id}\" name=\"purchased_order_item\"";
			if($purchased){ echo " title=\"$purchased\" checked=\"true\""; }
			echo "/>";
			echo "</td>";
			echo "<td><div id=\"result_{$item_id}\">";
			echo "</div></td>";
			echo "</tr>\n";
		}
		echo "</tbody>\n";
	}
	echo "</table>\n";
?>
</div>
