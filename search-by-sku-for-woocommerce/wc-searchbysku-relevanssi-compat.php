<?php
add_filter('the_posts', 'variation_query');

function variation_query($posts, $query = false) {

    if (is_search() && !is_admin()) {
        $ignoreIds = array(0);
        foreach($posts as $post) {
            $ignoreIds[] = $post->ID;
        }
        //get_search_query does sanitization
        $matchedSku = get_parent_post_by_sku(get_search_query(), $ignoreIds);

        if ($matchedSku) {
          foreach($matchedSku as $product_id) {
            $posts[] = get_post($product_id->post_id);
          }
        }
        return $posts;
      }
      return $posts;
    }

function get_parent_post_by_sku($sku, $ignoreIds) {
    global $wpdb, $wp_query, $post;

    $dia_search_terms = get_post_meta( get_the_ID(), 'dia_search_extra_terms', true );
    $search_mft_part_number = get_post_meta( get_the_ID(), 'dia_product_mft_part_number', true );


    $results = array();
    //Search for the sku of a variation and return the parent.
    $ignoreIdsForMySql = implode(",", $ignoreIds);
    $variationsSql =
          "SELECT p.post_parent as post_id FROM $wpdb->posts as p
          join $wpdb->postmeta pm
          on p.ID = pm.post_id
          and pm.meta_key='_sku'
          and pm.meta_value LIKE '%$sku%'
          join $wpdb->postmeta visibility
          on p.post_parent = visibility.post_id
          and visibility.meta_key = '_visibility'
          and visibility.meta_value <> 'hidden'
          where 1
          AND p.post_parent <> 0
          and p.ID not in ($ignoreIdsForMySql)
          and p.post_status = 'publish'
          group by p.post_parent";
    $variations = $wpdb->get_results($variationsSql);

    foreach($variations as $post) {
        $ignoreIds[] = $post->post_id;
    }

    //If not variation try a regular product sku
    $ignoreIdsForMySql = implode(",", $ignoreIds);

    $regularProductsSql =
        "SELECT p.ID as post_id FROM $wpdb->posts as p
        join $wpdb->postmeta pm
        on p.ID = pm.post_id
        and  pm.meta_key='_sku'
        AND pm.meta_value LIKE '%$sku%'
        join $wpdb->postmeta visibility
        on p.ID = visibility.post_id
        and visibility.meta_key = '_visibility'
        and visibility.meta_value <> 'hidden'
        where 1
        and (p.post_parent = 0 or p.post_parent is null)
        and p.ID not in ($ignoreIdsForMySql)
        and p.post_status = 'publish'
        group by p.ID";
    $regular_products = $wpdb->get_results($regularProductsSql);

    $dia_searchProductsSql =
      "SELECT p.ID as post_id FROM $wpdb->posts as p
      join $wpdb->postmeta pm
      on p.ID = pm.post_id
      and  pm.meta_key='dia_search_extra_terms'
      AND pm.meta_value LIKE '%$dia_search_terms%'
      join $wpdb->postmeta visibility
      on p.ID = visibility.post_id
      and visibility.meta_key = '_visibility'
      and visibility.meta_value <> 'hidden'
      where 1
      and (p.post_parent = 0 or p.post_parent is null)
      and p.ID not in ($ignoreIdsForMySql)
      and p.post_status = 'publish'
      group by p.ID";
    $dia_products = $wpdb->get_results($dia_searchProductsSql);

    $dia_mftProductsSql =
      "SELECT p.ID as post_id FROM $wpdb->posts as p
      join $wpdb->postmeta pm
      on p.ID = pm.post_id
      and  pm.meta_key='dia_product_mft_part_number'
      AND pm.meta_value LIKE '%$search_mft_part_number%'
      join $wpdb->postmeta visibility
      on p.ID = visibility.post_id
      and visibility.meta_key = '_visibility'
      and visibility.meta_value <> 'hidden'
      where 1
      and (p.post_parent = 0 or p.post_parent is null)
      and p.ID not in ($ignoreIdsForMySql)
      and p.post_status = 'publish'
      group by p.ID";
    $dia_mft_products = $wpdb->get_results($dia_mftProductsSql);


    $results = array_merge($variations, $regular_products, $dia_products, $dia_mft_products);

    $wp_query->found_posts += sizeof($results);

    return $results;
}
