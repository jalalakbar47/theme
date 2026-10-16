<?php
defined( 'ABSPATH' ) || exit;
add_action( 'wp_enqueue_scripts', function () {
 $base = get_stylesheet_directory_uri();
 wp_enqueue_style( 'zalmi-global', $base . '/assets/css/global.css', array( 'astra-theme-css' ), '1.0.0' );
 if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
  wp_enqueue_style( 'zalmi-commerce', $base . '/assets/css/commerce.css', array( 'zalmi-global' ), '1.0.0' );
 }
 wp_enqueue_script( 'zalmi-interactions', $base . '/assets/js/interactions.js', array(), '1.0.0', true );
} , 30 );
