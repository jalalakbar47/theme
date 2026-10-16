<?php
/** ZALMI SHOES child theme bootstrap. */
defined( 'ABSPATH' ) || exit;
foreach ( array( 'setup', 'enqueue', 'customizer', 'woocommerce', 'content', 'cli' ) as $zalmi_module ) {
 require_once get_stylesheet_directory() . '/inc/' . $zalmi_module . '.php';
}
