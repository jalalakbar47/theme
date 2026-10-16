<?php
defined( 'ABSPATH' ) || exit;
/** GET-only catalog filtering; WooCommerce owns pagination, sorting and purchasing. */
function zalmi_filter_value( $key ) {
 return isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
}
add_filter( 'loop_shop_columns', function () { return 4; } );
function zalmi_catalog_filters() {
 if ( ! is_shop() && ! is_product_taxonomy() ) { return; }
 echo '<details class="zalmi-filters"><summary>' . esc_html__( 'Filter your collection', 'zalmi-shoes-child' ) . '</summary><form method="get"><div class="zalmi-filter-fields">';
 echo '<input type="hidden" name="post_type" value="product">';
 foreach ( array( 's' => __( 'Search', 'zalmi-shoes-child' ), 'min_price' => __( 'Minimum price', 'zalmi-shoes-child' ), 'max_price' => __( 'Maximum price', 'zalmi-shoes-child' ) ) as $key => $label ) {
  echo '<label>' . esc_html( $label ) . '<input name="' . esc_attr( $key ) . '" type="' . ( 's' === $key ? 'search' : 'number' ) . '" ' . ( 's' === $key ? '' : 'min="0" step="any"' ) . ' value="' . esc_attr( zalmi_filter_value( $key ) ) . '"></label>';
 }
 foreach ( array( 'product_cat' => __( 'Category', 'zalmi-shoes-child' ), 'pa_size' => __( 'Size', 'zalmi-shoes-child' ), 'pa_color' => __( 'Color', 'zalmi-shoes-child' ) ) as $taxonomy => $label ) {
  if ( ! taxonomy_exists( $taxonomy ) ) { continue; }
  $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
  if ( is_wp_error( $terms ) || ! $terms ) { continue; }
  $key = 'product_cat' === $taxonomy ? 'product_cat' : 'zalmi_' . $taxonomy;
  echo '<label>' . esc_html( $label ) . '<select name="' . esc_attr( $key ) . '"><option value="">' . esc_html__( 'All', 'zalmi-shoes-child' ) . '</option>';
  foreach ( $terms as $term ) { echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( zalmi_filter_value( $key ), $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>'; }
  echo '</select></label>';
 }
 echo '<label><span>' . esc_html__( 'Availability', 'zalmi-shoes-child' ) . '</span><select name="zalmi_stock"><option value="">' . esc_html__( 'Any stock', 'zalmi-shoes-child' ) . '</option><option value="instock" ' . selected( zalmi_filter_value( 'zalmi_stock' ), 'instock', false ) . '>' . esc_html__( 'In stock', 'zalmi-shoes-child' ) . '</option></select></label>';
 echo '<label><span>' . esc_html__( 'Offers', 'zalmi-shoes-child' ) . '</span><select name="zalmi_sale"><option value="">' . esc_html__( 'All products', 'zalmi-shoes-child' ) . '</option><option value="1" ' . selected( zalmi_filter_value( 'zalmi_sale' ), '1', false ) . '>' . esc_html__( 'On sale', 'zalmi-shoes-child' ) . '</option></select></label>';
 if ( zalmi_filter_value( 'orderby' ) ) { echo '<input type="hidden" name="orderby" value="' . esc_attr( zalmi_filter_value( 'orderby' ) ) . '">'; }
 echo '<button type="submit">' . esc_html__( 'Apply filters', 'zalmi-shoes-child' ) . '</button><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Clear filters', 'zalmi-shoes-child' ) . '</a></div></form></details>';
}
add_action( 'woocommerce_before_shop_loop', 'zalmi_catalog_filters', 15 );
add_action( 'woocommerce_no_products_found', 'zalmi_catalog_filters', 5 );
add_action( 'woocommerce_product_query', function ( $query ) {
 if ( is_admin() ) { return; }
 $tax_query = $query->get( 'tax_query' );
 foreach ( array( 'pa_size', 'pa_color' ) as $taxonomy ) {
  $value = sanitize_title( zalmi_filter_value( 'zalmi_' . $taxonomy ) );
  if ( $value && taxonomy_exists( $taxonomy ) ) { $tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $value ); }
 }
 $query->set( 'tax_query', $tax_query );
 if ( 'instock' === zalmi_filter_value( 'zalmi_stock' ) ) {
  $meta_query = $query->get( 'meta_query' );
  $meta_query[] = array( 'key' => '_stock_status', 'value' => 'instock' );
  $query->set( 'meta_query', $meta_query );
 }
 if ( '1' === zalmi_filter_value( 'zalmi_sale' ) ) {
  $sale_ids = wc_get_product_ids_on_sale();
  $existing = $query->get( 'post__in' );
  $query->set( 'post__in', $existing ? array_values( array_intersect( $existing, $sale_ids ) ) ?: array( 0 ) : ( $sale_ids ?: array( 0 ) ) );
 }
} );
add_action( 'woocommerce_single_product_summary', function () {
 echo '<p class="zalmi-service-links"><a href="' . esc_url( home_url( '/size-guide/' ) ) . '">' . esc_html__( 'Size guide', 'zalmi-shoes-child' ) . '</a> · <a href="' . esc_url( home_url( '/shipping-information/' ) ) . '">' . esc_html__( 'Shipping information', 'zalmi-shoes-child' ) . '</a> · <a href="' . esc_url( home_url( '/returns-exchanges/' ) ) . '">' . esc_html__( 'Returns & exchanges', 'zalmi-shoes-child' ) . '</a></p>';
}, 35 );
