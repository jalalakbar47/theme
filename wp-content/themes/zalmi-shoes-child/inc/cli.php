<?php
/** Explicit local/staging bootstrap: wp zalmi setup --demo */
defined( 'ABSPATH' ) || exit;
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { return; }
WP_CLI::add_command( 'zalmi setup', function ( $args, $flags ) {
 if ( 'local' !== wp_get_environment_type() && 'staging' !== wp_get_environment_type() ) { WP_CLI::error( 'Set WP_ENVIRONMENT_TYPE to local or staging. This importer refuses production.' ); }
 if ( ! class_exists( 'WooCommerce' ) || ! did_action( 'elementor/loaded' ) ) { WP_CLI::error( 'Activate WooCommerce and Elementor first.' ); }
 $created = array();
 foreach ( glob( get_stylesheet_directory() . '/elementor-templates/*.json' ) as $file ) {
  $slug = basename( $file, '.json' );
  if ( 'newsletter' === $slug ) { continue; }
  $existing = get_page_by_path( $slug );
  if ( $existing ) { WP_CLI::log( 'Preserved existing page: ' . $slug ); continue; }
  $template = json_decode( str_replace( '/wp-content/themes/zalmi-shoes-child/assets/images', esc_url_raw( get_stylesheet_directory_uri() . '/assets/images' ), file_get_contents( $file ) ), true );
  $id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_name' => $slug, 'post_title' => ucwords( str_replace( '-', ' ', $slug ) ) ), true );
  if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
  update_post_meta( $id, '_elementor_edit_mode', 'builder' );
  update_post_meta( $id, '_elementor_template_type', 'wp-page' );
  update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
  update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $template['content'] ) ) );
  update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
  $created[ $slug ] = $id;
 }
 // WooCommerce creates and associates only its missing core pages.
 WC_Install::create_pages();
 if ( ! get_page_by_path( 'order-tracking' ) ) {
  wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_name' => 'order-tracking', 'post_title' => 'Order Tracking', 'post_content' => '[woocommerce_order_tracking]' ) );
 }
 $categories = array( 'Men' => array( 'Sneakers', 'Formal Shoes', 'Running Shoes', 'Casual Shoes', 'Sandals' ), 'Women' => array( 'Sneakers', 'Flats', 'Heels', 'Casual Shoes', 'Sandals' ), 'Kids' => array( 'Boys Shoes', 'Girls Shoes', 'School Shoes', 'Sports Shoes' ) );
 foreach ( $categories as $parent => $children ) {
  $parent_slug = sanitize_title( $parent );
  $term = term_exists( $parent_slug, 'product_cat' );
  if ( ! $term ) { $term = wp_insert_term( $parent, 'product_cat', array( 'slug' => $parent_slug ) ); }
  if ( is_wp_error( $term ) ) { WP_CLI::error( $term->get_error_message() ); }
  foreach ( $children as $child ) {
   $slug = $parent_slug . '-' . sanitize_title( $child );
   if ( ! term_exists( $slug, 'product_cat' ) ) { wp_insert_term( $child, 'product_cat', array( 'slug' => $slug, 'parent' => (int) $term['term_id'] ) ); }
  }
 }
 if ( isset( $flags['demo'] ) ) {
  foreach ( array( 'size' => 'Size', 'color' => 'Color' ) as $slug => $label ) {
   if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) { wc_create_attribute( array( 'name' => $label, 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order' ) ); }
   if ( ! taxonomy_exists( 'pa_' . $slug ) ) { register_taxonomy( 'pa_' . $slug, array( 'product' ) ); }
  }
  foreach ( array( 'Everyday Runner', 'City Sneaker', 'Classic Oxford', 'Weekend Trainer' ) as $index => $name ) {
   $sku = 'ZALMI-DEMO-' . ( $index + 1 );
   if ( wc_get_product_id_by_sku( $sku ) ) { continue; }
   $product = new WC_Product_Variable();
   $product->set_name( '[DEMO] ' . $name ); $product->set_sku( $sku );
   $product->set_status( 'draft' ); $product->set_featured( true );
   $product->set_description( 'Sample product for local testing. Replace measurements, materials, prices and images with verified catalog data before publication.' );
   $attributes = array();
   foreach ( array( 'size' => array( '40', '41', '42' ), 'color' => array( 'Black', 'White' ) ) as $slug => $values ) {
    $ids = array();
    foreach ( $values as $value ) { $term = term_exists( $value, 'pa_' . $slug ); if ( ! $term ) { $term = wp_insert_term( $value, 'pa_' . $slug ); } if ( ! is_wp_error( $term ) ) { $ids[] = (int) $term['term_id']; } }
    $attribute = new WC_Product_Attribute(); $attribute->set_id( wc_attribute_taxonomy_id_by_name( $slug ) ); $attribute->set_name( 'pa_' . $slug ); $attribute->set_options( $ids ); $attribute->set_visible( true ); $attribute->set_variation( true ); $attributes[] = $attribute;
   }
   $product->set_attributes( $attributes );
   $category = term_exists( 'men', 'product_cat' );
   if ( $category ) { $product->set_category_ids( array( (int) $category['term_id'] ) ); }
   $id = $product->save();
   foreach ( array( '40', '41', '42' ) as $size ) { foreach ( array( 'black', 'white' ) as $color ) {
    $variation = new WC_Product_Variation(); $variation->set_parent_id( $id ); $variation->set_attributes( array( 'pa_size' => $size, 'pa_color' => $color ) ); $variation->set_regular_price( (string) ( 4500 + $index * 1000 ) ); $variation->set_manage_stock( true ); $variation->set_stock_quantity( 5 ); $variation->save();
   } }
   WC_Product_Variable::sync( $id );
  }
 }
 if ( isset( $flags['new-store'] ) ) {
  if ( get_option( 'zalmi_initialized' ) ) { WP_CLI::log( 'New-store settings already initialized; preserved.' ); }
  elseif ( wc_get_orders( array( 'limit' => 1, 'return' => 'ids' ) ) || wc_get_products( array( 'limit' => 1, 'status' => 'publish' ) ) ) { WP_CLI::error( 'Existing commerce data found; refusing new-store configuration.' ); }
  else {
   update_option( 'woocommerce_currency', 'PKR' ); update_option( 'woocommerce_default_country', 'PK' );
   update_option( 'blogname', 'ZALMI SHOES' );
   if ( isset( $created['home'] ) ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $created['home'] ); }
   update_option( 'zalmi_initialized', 1 );
  }
 }
 $menu = wp_get_nav_menu_object( 'ZALMI Primary' );
 if ( ! $menu ) {
  $menu_id = wp_create_nav_menu( 'ZALMI Primary' );
  if ( ! is_wp_error( $menu_id ) ) {
   foreach ( array( 'home' => 'Home', 'shop' => 'Shop', 'new-arrivals' => 'New Arrivals', 'best-sellers' => 'Best Sellers', 'sale' => 'Sale', 'contact-us' => 'Contact' ) as $slug => $label ) {
    $page = get_page_by_path( $slug );
    if ( $page ) { wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $label, 'menu-item-object-id' => $page->ID, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) ); }
   }
  }
 }
 WP_CLI::success( 'Starter content prepared. Review and publish drafts, assign the ZALMI Primary menu in Astra, add images, and configure shipping/tax/payment settings. No live payments enabled.' );
} );
