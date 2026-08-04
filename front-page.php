<?php
/**
 * Template Name: Front Page
 * Description: Modular IPTV Landing Page - Each section is in a separate file
 */
get_header();

// Define file paths
$front_page_dir = get_template_directory() . '/front-page';
$css_dir = $front_page_dir . '/css';
$js_dir = $front_page_dir . '/js';
$sections_dir = $front_page_dir . '/sections';
?>

<style>
    <?php
    // Load CSS files
    $css_files = array(
        'variables',      // Old vars (Keep for Pricing/Reviews)
        'base',           // Old base (Keep for layout safety)
        'header',         // Existing Header
        'pricing',        // Existing Pricing
        'reviews',        // Existing Reviews
        'contact',        // Existing Contact form styling
        'footer',         // Existing Footer
        'responsive',     // Existing Responsive
        'redesign-theme', // Previous redesign (Overrides + New Sections)
        'cta',            // CTA Section Styles
        'activity-ticker', // Social proof notifications
        'design-v2',      // DESIGN V2 tokens (blue & white) - must come after the old layers
        'design-v2-sections' // DESIGN V2 section components
    );

    foreach ($css_files as $file) {
        $path = $css_dir . '/' . $file . '.css';
        if (file_exists($path)) {
            include $path;
        }
    }
    ?>
</style>

<?php
// Page-wide backdrop: a fixed, masked grid that sits behind every section.
// Rendered once here rather than inside a section, since it belongs to the page
// rather than to any one band of it. See .dv2-grid-wash in design-v2.css.
?>
<div class="dv2-grid-wash" aria-hidden="true"></div>

<?php
// Load all sections in order
// Order follows the "NordicTV Blue & White" design.
$sections = array(
    'header',           // Front-page header (source of truth for all headers)
    'hero',             // Backdrop hero with Trustpilot badge
    'content-showcase', // Channels panel + VOD panel
    'sports',           // Sports panel with mosaic
    'cta-bar',          // Full-width savings bar
    // Features sits directly above pricing and closes on a CTA into it, so the
    // capability pitch lands immediately before the plan configurator.
    'features',         // Eight capability cards
    'pricing',          // Device/duration configurator (WooCommerce)
    'steps',            // Onboarding panel - sits directly under pricing
    'unlock',           // Supported device chips
    'reviews',          // Score card + two-row review marquee
    'faq',              // Accordion
    'contact',          // Support cards
    'footer'
    // Not in this list (files and styles still exist - add the slug back to
    // render them again):
    //   'comparison'  - NordicTV vs. traditional services
    //   'dashboard'   - Member area promo
    //   'dark-cta'    - second CTA block; the journey panel closes this design
);

foreach ($sections as $section) {
    $path = $sections_dir . '/' . $section . '.php';
    if (file_exists($path)) {
        include $path;
    }
}

// Activity Ticker (Social Proof)
include $front_page_dir . '/partials/activity-ticker.php';
?>

<script>
    <?php
    // Load all JS files in order
    $js_files = array(
        'header',
        'currency',
        'carousels',
        'pricing',
        'hero-animation',   // New Hero Animation
        'reviews',          // Reviews carousel arrows (no autoplay)
        'activity-ticker'   // Social proof notifications
    );

    foreach ($js_files as $file) {
        $path = $js_dir . '/' . $file . '.js';
        if (file_exists($path)) {
            include $path;
        }
    }
    ?>
</script>

<?php get_footer(); ?>