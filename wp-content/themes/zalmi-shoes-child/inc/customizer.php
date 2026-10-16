<?php
defined( 'ABSPATH' ) || exit;
add_action( 'customize_register', function ( $customizer ) {
 $customizer->add_section( 'zalmi_brand', array( 'title' => __( 'ZALMI SHOES', 'zalmi-shoes-child' ), 'priority' => 35 ) );
 $customizer->add_setting( 'zalmi_announcement', array( 'default' => 'NEW SEASON COLLECTION — EXPLORE NOW', 'sanitize_callback' => 'sanitize_text_field' ) );
 $customizer->add_control( 'zalmi_announcement', array( 'label' => __( 'Announcement (leave empty to hide)', 'zalmi-shoes-child' ), 'section' => 'zalmi_brand', 'type' => 'text' ) );
 $customizer->add_setting( 'zalmi_accent', array( 'default' => '#D65A31', 'sanitize_callback' => 'sanitize_hex_color' ) );
 $customizer->add_control( new WP_Customize_Color_Control( $customizer, 'zalmi_accent', array( 'label' => __( 'Accent color', 'zalmi-shoes-child' ), 'section' => 'zalmi_brand' ) ) );
} );
add_action( 'wp_enqueue_scripts', function () {
 $accent = sanitize_hex_color( get_theme_mod( 'zalmi_accent', '#D65A31' ) );
 if ( $accent ) { wp_add_inline_style( 'zalmi-global', ':root{--zalmi-accent:' . $accent . ';}' ); }
}, 40 );
