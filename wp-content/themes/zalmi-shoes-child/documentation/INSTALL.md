# Installation and editing

## Prerequisites
Use a private local/staging WordPress installation, HTTPS for staging, PHP 8.1+ and a database supported by your selected WordPress/WooCommerce releases. PHP 8.3+ is recommended for a new installation; check the current official requirements before choosing versions. No version compatibility matrix has been runtime-tested in this workspace.

Back up an existing store's files and database. Each cloud task already runs in an isolated environment; use its existing checkout rather than creating a Git worktree.

1. Appearance → Themes → Add New: install **Astra** from WordPress.org. Keep it installed.
2. Plugins → Add New: install and activate **WooCommerce** and **Elementor Website Builder** (free). Follow WooCommerce onboarding with actual business information; leave live gateways disabled.
3. Appearance → Themes → Add New → Upload: upload `zalmi-shoes-child.zip`, then activate **ZALMI SHOES CHILD**.
4. Create pages using the templates below or use the optional WP-CLI bootstrap.
5. Review all draft copy, images, links, policies, shipping zones, tax decisions and payment settings before enabling purchases.

## Elementor import (manual)
Elementor → Templates → Saved Templates → Import Templates: import the individual JSON files from `elementor-templates`. Create a page, Edit with Elementor, choose the folder icon → My Templates → Insert. Use the Elementor Full Width page layout so Astra retains header/footer. Set Home in Settings → Reading.

These files use native Elementor containers, heading, text, image, button and shortcode widgets, with no paid widgets. Import/edit/export must still be verified on your installed Elementor version. The homepage illustration URL assumes WordPress is at the domain root and the theme directory has its original name. Select `assets/images/studio-shoe.png` in the Media Library instead when installed in a subdirectory. WP-CLI setup resolves this URL automatically.

Shortcodes display real catalog data; empty stores have no product cards. Add actual images and publish products before checking the catalog. Hero, category layout, collections, story and newsletter text can be rearranged and edited in Elementor. Product card details remain managed in WooCommerce.

## Optional automated starter content
Use WP-CLI on a backed-up **local or staging** installation. Set `WP_ENVIRONMENT_TYPE` to `local` or `staging` in your local configuration (do not commit credentials). From the WordPress root:

```sh
wp theme install astra
wp plugin install woocommerce elementor --activate
wp theme activate zalmi-shoes-child
wp zalmi setup
# Only for a NEW demonstration store, with explicit sample-product opt-in:
wp zalmi setup --demo --new-store
```

Run only installation commands needed for missing dependencies; don't reinstall/overwrite existing installations. The importer refuses production. It preserves pages with existing slugs and products with existing demo SKUs. It creates draft pages, native WooCommerce core pages if missing, hierarchical categories, a menu without replacing assigned menus, and optionally four draft variable sample products with six variations each. Sample prices are demonstrative and products have no fabricated ratings or reviews. Upload product images yourself. Repeated runs skip existing demo SKUs and pages.

`--new-store` configures PKR/Pakistan and the new homepage only once, and refuses when published catalog products or orders exist. The imported homepage remains a draft until reviewed and published. No shipping rates, taxes, bank details or payment gateways are enabled by this command. Review the created core WooCommerce pages and their associations under WooCommerce → Settings → Advanced.

## Header, footer and navigation
Appearance → Customize → Header Builder: set the site title/logo, primary menu, search, WooCommerce account and cart components available in your Astra version. Set a dynamic cart count with Astra's native WooCommerce cart element. Appearance → Menus: assign ZALMI Primary to the primary location and add Men/Women/Kids product-category links. Categories are nested by audience to avoid ambiguous duplicate URLs. Add submenu entries for the desired footwear categories. Astra handles keyboard/mobile navigation.

Appearance → Customize → Footer Builder: configure brand copy, Quick Links, Customer Service, and verified Contact/Social links. Link published About, Contact, FAQ, Size Guide, Shipping, Returns, Privacy, Terms and Order Tracking pages. A custom four-column footer is **not** automatically installed. No fictional contact details or social links are generated. Remove unused components instead of using `#` links.

Appearance → Customize → ZALMI SHOES: announcement and accent color. Astra Global Typography/Colors and Elementor Site Settings: set Inter/Poppins if locally hosted, dark #111111, soft #F7F7F5, accent #D65A31, white background. System fonts work without external font downloads. Elementor's explicit widget colors can override theme CSS.

## Catalog, cart and checkout
Products → Add New: enter real title, description, SKU, image/gallery, categories, price and stock. For variable products, choose Variable Product, add global Size/Color attributes under Products → Attributes, enable Used for variations, generate variations and set each variation's price/stock/image. Size ranges belong to each product. Native WooCommerce controls enforce selection and calculate cart totals.

Products → Categories: set real descriptions and category images. Mark selected products Featured for the homepage. New Arrivals uses date order; Best Sellers uses sales popularity only when sales exist, otherwise labels a fallback collection. Sale uses real sale products. Native WooCommerce renders genuine ratings when present. No testimonial or social feed is fabricated.

Shop filters submit GET parameters to actual WooCommerce queries. Use global attributes `pa_size` and `pa_color` to expose the size/color controls. WooCommerce owns price limits, ordering, search, counts and pagination. Test these against your actual catalog and current WooCommerce version.

WooCommerce → Settings → General: PKR/Pakistan as appropriate. Shipping: configure genuine zones/rates; no rates are assumed. Tax: consult your requirements before enabling rules. Payments: configure COD/bank transfer only when approved; never enter credentials into theme source. Advanced: verify Cart, Checkout, My Account, Terms and endpoints. Native Cart/Checkout pages are preserved, including blocks where selected by WooCommerce.

WooCommerce → Orders manages orders; Users and WooCommerce customer tools manage customers. Accounts & Privacy controls registration and data retention. Native password reset requires working mail delivery. Verify mail with a staging mail catcher before testing.

## Forms and optional plugins
No extra plugin is required for core commerce. Configure a maintained form plugin (for example Contact Form 7) and real mail delivery before replacing Contact's inactive explanatory content with a form shortcode. Connect a subscription provider with consent/privacy controls before adding a newsletter form. These integrations are intentionally not pretended to work. Wishlist, quick view, comparison and social feeds are omitted.

Elementor Free edits pages, not WooCommerce Theme Builder layouts. Astra and WooCommerce control header/footer, archives, product, account, cart and checkout. Elementor Pro is optional and not bundled; use licensed Theme Builder widgets if separately installed. No paid features were activated.

## Launch gate
Finish all runtime checks in REPORT.md, replace all draft policies/sample data, configure verified contact information and photography, verify HTTPS and mail, back up and review deployment separately. This package does not publish a site or enable financial processing.
