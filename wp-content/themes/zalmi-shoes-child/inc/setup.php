<?php
defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', function () {
 load_child_theme_textdomain( 'zalmi-shoes-child', get_stylesheet_directory() . '/languages' );
 add_theme_support( 'woocommerce' );
 add_theme_support( 'wc-product-gallery-zoom' );
 add_theme_support( 'wc-product-gallery-lightbox' );
 add_theme_support( 'wc-product-gallery-slider' );
} );
add_action( 'astra_header_before', function () {
 $message = get_theme_mod( 'zalmi_announcement', __( 'NEW SEASON COLLECTION — EXPLORE NOW', 'zalmi-shoes-child' ) );
 if ( $message ) { echo '<div class="zalmi-announcement">' . esc_html( $message ) . '</div>'; }
} );
add_action( 'admin_notices', function () {
 if ( ! current_user_can( 'activate_plugins' ) ) { return; }
 if ( ! class_exists( 'WooCommerce' ) || ! did_action( 'elementor/loaded' ) ) {
  echo '<div class="notice notice-warning"><p>' . esc_html__( 'ZALMI SHOES: install and activate WooCommerce and Elementor Free for the complete storefront. Astra must remain installed as the parent theme.', 'zalmi-shoes-child' ) . '</p></div>';
 }
} );
