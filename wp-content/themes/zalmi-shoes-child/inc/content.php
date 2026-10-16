<?php
defined( 'ABSPATH' ) || exit;
add_shortcode( 'zalmi_categories', function () {
 if ( ! class_exists( 'WooCommerce' ) ) { return '<p>' . esc_html__( 'The collection will be available when WooCommerce is configured.', 'zalmi-shoes-child' ) . '</p>'; }
 return do_shortcode( '[product_categories number="6" columns="3" hide_empty="1" parent="0"]' );
} );
add_shortcode( 'zalmi_collection', function ( $attributes ) {
 if ( ! class_exists( 'WooCommerce' ) ) { return '<p>' . esc_html__( 'The collection is being prepared.', 'zalmi-shoes-child' ) . '</p>'; }
 $attributes = shortcode_atts( array( 'type' => 'featured' ), $attributes, 'zalmi_collection' );
 $query = '[products limit="8" columns="4" visibility="featured"]';
 if ( 'new' === $attributes['type'] ) { $query = '[products limit="4" columns="4" orderby="date" order="DESC"]'; }
 if ( 'sale' === $attributes['type'] ) { $query = '[sale_products limit="8" columns="4"]'; }
 if ( 'best' === $attributes['type'] ) {
  // A positive sale count is checked explicitly; demo products are never ranked as bestsellers.
  $has_sales = (bool) get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => 'total_sales', 'meta_value' => 0, 'meta_compare' => '>', 'meta_type' => 'NUMERIC' ) );
  $query = $has_sales ? '[best_selling_products limit="4" columns="4"]' : '[products limit="4" columns="4" orderby="date"]';
  return ( $has_sales ? '' : '<p>' . esc_html__( 'Explore the collection — bestseller rankings will appear once sales are available.', 'zalmi-shoes-child' ) . '</p>' ) . do_shortcode( $query );
 }
 return do_shortcode( $query );
} );
