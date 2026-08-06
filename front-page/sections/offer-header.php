<?php
/**
 * Offer Landing Page Header
 * Same design as site-header but stripped to: Logo | Currency selector | CTA button
 * Nav links removed — keeps focus on one action.
 *
 * Variables expected from template-offer-landing.php:
 *   $offer_cta_text      (string)
 *   $offer_checkout_url  (string)
 */

// ── Language detection (copied from header.php) ────────────────────────────
$site_slug = '';
// Method 1: Polylang
if (function_exists('pll_current_language')) {
    $pll_lang = pll_current_language('slug');
    if (!empty($pll_lang) && $pll_lang !== 'en') {
        $site_slug = $pll_lang;
    }
}
// Method 2: Multisite
if (empty($site_slug) && is_multisite() && function_exists('get_blog_details')) {
    $blog_details = get_blog_details();
    if ($blog_details && !empty($blog_details->path)) {
        $site_slug = trim($blog_details->path, '/');
    }
}
// Method 3: REQUEST_URI fallback
if (empty($site_slug)) {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path_parts = explode('/', trim($request_uri, '/'));
    $first = isset($path_parts[0]) ? $path_parts[0] : '';
    if (in_array($first, ['sv', 'no', 'dk', 'fi', 'is'])) {
        $site_slug = $first;
    }
}

$site_language_map = [
    'sv' => ['flag' => '🇸🇪', 'name' => 'Svenska'],
    'no' => ['flag' => '🇳🇴', 'name' => 'Norsk'],
    'dk' => ['flag' => '🇩🇰', 'name' => 'Dansk'],
    'fi' => ['flag' => '🇫🇮', 'name' => 'Suomi'],
    'is' => ['flag' => '🇮🇸', 'name' => 'Íslenska'],
];
$default_flag = '🇺🇸';
$default_name = 'English';
if (isset($site_language_map[$site_slug])) {
    $default_flag = $site_language_map[$site_slug]['flag'];
    $default_name = $site_language_map[$site_slug]['name'];
}
?>

<header class="site-header offer-header" id="site-header">
    <div class="container nav-container">

        <!-- Logo -->
        <a href="<?php echo home_url('/'); ?>" class="logo">
            <?php
            // Same responsive set as front-page/sections/header.php; see the note there.
            $iptv_offer_logo_dir = get_template_directory_uri() . '/images/logo/';
            $iptv_offer_logo_2x  = $iptv_offer_logo_dir . 'light-logo-230x69.png';
            $iptv_offer_logo_3x  = $iptv_offer_logo_dir . rawurlencode('light logo 500_150.png');
            ?>
            <img src="<?php echo esc_url($iptv_offer_logo_2x); ?>"
                srcset="<?php echo esc_url($iptv_offer_logo_2x); ?> 230w, <?php echo esc_url($iptv_offer_logo_3x); ?> 500w"
                sizes="115px" width="500" height="150" alt="Nordic IPTV"
                class="logo-img" fetchpriority="high">
        </a>

        <!-- Right side: currency + CTA -->
        <div class="nav-right">
            <!-- Language selector (same markup as header.php so currency.js works) -->
            <div class="country-selector" id="countrySelector">
                <button class="country-btn" onclick="toggleCountryDropdown()">
                    <span class="country-flag" id="selectedFlag"><?php echo $default_flag; ?></span>
                    <span class="country-code" id="selectedCode"><?php echo $default_name; ?></span>
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                        <path d="M1 3L5 7L9 3" stroke="currentColor" stroke-width="1.5" fill="none" />
                    </svg>
                </button>
                <div class="country-dropdown" id="countryDropdown">
                    <div class="country-option" data-currency="usd" data-symbol="$" data-flag="🇺🇸">
                        <span class="country-flag">🇺🇸</span><span>English</span>
                    </div>
                    <div class="country-option" data-currency="sek" data-symbol="kr" data-flag="🇸🇪">
                        <span class="country-flag">🇸🇪</span><span>Svenska</span>
                    </div>
                    <div class="country-option" data-currency="nok" data-symbol="kr" data-flag="🇳🇴">
                        <span class="country-flag">🇳🇴</span><span>Norsk</span>
                    </div>
                    <div class="country-option" data-currency="dkk" data-symbol="kr" data-flag="🇩🇰">
                        <span class="country-flag">🇩🇰</span><span>Dansk</span>
                    </div>
                    <div class="country-option" data-currency="eur" data-symbol="€" data-flag="🇫🇮">
                        <span class="country-flag">🇫🇮</span><span>Suomi</span>
                    </div>
                    <div class="country-option" data-currency="isk" data-symbol="kr" data-flag="🇮🇸">
                        <span class="country-flag">🇮🇸</span><span>Íslenska</span>
                    </div>
                </div>
            </div>

            <!-- CTA — same pulse style as page buttons -->
            <a href="<?php echo esc_url($offer_checkout_url); ?>" class="nav-btn offer-cta-btn offer-header__cta">
                <?php echo esc_html($offer_cta_text); ?>
            </a>
        </div>

        <!-- Mobile toggle -->
        <button class="mobile-menu-toggle" onclick="toggleOfferMobileMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Minimal mobile menu: only currency + CTA -->
<div class="mobile-menu" id="offer-mobile-menu">
    <button class="mobile-menu-close" onclick="toggleOfferMobileMenu()">&times;</button>
    <div class="mobile-language-selector">
        <span class="mobile-language-label">Language</span>
        <div class="mobile-language-options">
            <button class="mobile-lang-btn" data-currency="usd" onclick="redirectToRegion('usd')">🇺🇸 English</button>
            <button class="mobile-lang-btn" data-currency="sek" onclick="redirectToRegion('sek')">🇸🇪 Svenska</button>
            <button class="mobile-lang-btn" data-currency="nok" onclick="redirectToRegion('nok')">🇳🇴 Norsk</button>
            <button class="mobile-lang-btn" data-currency="dkk" onclick="redirectToRegion('dkk')">🇩🇰 Dansk</button>
            <button class="mobile-lang-btn" data-currency="eur" onclick="redirectToRegion('eur')">🇫🇮 Suomi</button>
            <button class="mobile-lang-btn" data-currency="isk" onclick="redirectToRegion('isk')">🇮🇸 Íslenska</button>
        </div>
    </div>
    <a href="<?php echo esc_url($offer_checkout_url); ?>" class="nav-btn offer-cta-btn" style="margin-top:1rem;"
        onclick="toggleOfferMobileMenu()">
        <?php echo esc_html($offer_cta_text); ?>
    </a>
</div>

<style>
    /* Strip nav links, keep everything else identical to site-header */
    .offer-header .nav-links {
        display: none !important;
    }

    /* CTA in header — smaller than page CTAs, no full-width pulse override */
    .offer-header__cta {
        font-size: 0.875rem !important;
        padding: 10px 22px !important;
        width: auto !important;
        /* cancel the mobile full-width rule */
        animation: offerCtaPulse 2.5s ease-in-out infinite;
    }
</style>

<script>
    function toggleOfferMobileMenu() {
        var menu = document.getElementById('offer-mobile-menu');
        if (menu) menu.classList.toggle('open');
    }
</script>