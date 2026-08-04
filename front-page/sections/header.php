<!-- Header Section -->
<?php
// Detect current subsite for default currency/flag
$site_slug = '';

// Method 1: Polylang language detection (most reliable on this site)
if (function_exists('pll_current_language')) {
    $pll_lang = pll_current_language('slug');
    if (!empty($pll_lang) && $pll_lang !== 'en') {
        $site_slug = $pll_lang;
    }
}

// Method 2: WordPress Multisite blog path
if (empty($site_slug) && is_multisite() && function_exists('get_blog_details')) {
    $blog_details = get_blog_details();
    if ($blog_details && !empty($blog_details->path)) {
        $site_slug = trim($blog_details->path, '/');
    }
}

// Method 3: Fallback to REQUEST_URI parsing
if (empty($site_slug)) {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path_parts = explode('/', trim($request_uri, '/'));
    $first_segment = isset($path_parts[0]) ? $path_parts[0] : '';
    if (in_array($first_segment, array('sv', 'no', 'dk', 'fi', 'is'))) {
        $site_slug = $first_segment;
    }
}

// Map subsite to its language label (flag + native language name)
$site_language_map = array(
    'sv' => array('flag' => '🇸🇪', 'name' => 'Svenska'),
    'no' => array('flag' => '🇳🇴', 'name' => 'Norsk'),
    'dk' => array('flag' => '🇩🇰', 'name' => 'Dansk'),
    'fi' => array('flag' => '🇫🇮', 'name' => 'Suomi'),
    'is' => array('flag' => '🇮🇸', 'name' => 'Íslenska'),
);

// Get default based on current subsite (main site = English)
$default_flag = '🇺🇸';
$default_name = 'English';
if (isset($site_language_map[$site_slug])) {
    $default_flag = $site_language_map[$site_slug]['flag'];
    $default_name = $site_language_map[$site_slug]['name'];
}

// Polylang filters home_url() only when the path is empty or '/'. Anything with
// a path or fragment — home_url('/#features') — comes back as the English URL,
// which is why every anchor link in this nav pointed at the English front page
// from /no/, /sv/ and the rest while the labels were correctly translated.
$nav_home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
$nav_home = trailingslashit($nav_home);

// The guide's real slug. home_url('/user-guide/') was a 404 in every language.
$nav_guide = function_exists('iptv_page_url')
    ? iptv_page_url('iptv-guide-setup-apps-devices-tips', $nav_home)
    : $nav_home;
?>
<header class="site-header" id="site-header">
    <div class="container nav-container">
        <a href="<?php echo esc_url($nav_home); ?>" class="logo">
            <?php // Light (white) logo: the bar is dark on every page. ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/logo/light logo 500_150.png" alt="Nordic IPTV"
                class="logo-img">
        </a>
        <?php if (has_nav_menu('primary')): ?>
            <?php wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => 'nav',
                'container_class' => 'nav-links',
                'menu_class' => '',
                'fallback_cb' => false,
            )); ?>
        <?php else: ?>
            <nav class="nav-links">
                <a href="<?php echo esc_url($nav_home); ?>"><?php echo esc_html(iptv_text('nav_link_home', 'Home')); ?></a>
                <a href="<?php echo esc_url($nav_home . '#features'); ?>"><?php echo esc_html(iptv_text('nav_link_features', 'Features')); ?></a>
                <a href="<?php echo esc_url($nav_home . '#pricing'); ?>"><?php echo esc_html(iptv_text('nav_link_pricing', 'Pricing')); ?></a>
                <!-- Blog lives in the footer only. -->
                <a href="<?php echo esc_url($nav_guide); ?>"><?php echo esc_html(iptv_text('nav_link_guide', 'User Guide')); ?></a>
                <a href="<?php echo esc_url($nav_home . '#contact'); ?>"><?php echo esc_html(iptv_text('nav_link_contact', 'Contact')); ?></a>
                <a href="https://panel.nordictv.io/login"><?php echo esc_html(iptv_text('nav_link_account', 'My Account')); ?></a>
            </nav>
        <?php endif; ?>
        <div class="nav-right">
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
            <a href="<?php echo esc_url($nav_home . '#pricing'); ?>" class="nav-btn nav-btn-primary"><?php echo esc_html(iptv_text('nav_cta_label', 'Get Access Now')); ?></a>
        </div>
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu">
    <button class="mobile-menu-close" onclick="toggleMobileMenu()">&times;</button>

    <?php if (has_nav_menu('primary')): ?>
        <?php wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => '',
            'fallback_cb' => false,
        )); ?>
    <?php else: ?>
        <a href="<?php echo esc_url($nav_home); ?>"><?php echo esc_html(iptv_text('nav_link_home', 'Home')); ?></a>
        <a href="<?php echo esc_url($nav_home . '#features'); ?>"><?php echo esc_html(iptv_text('nav_link_features', 'Features')); ?></a>
        <a href="<?php echo esc_url($nav_home . '#pricing'); ?>"><?php echo esc_html(iptv_text('nav_link_pricing', 'Pricing')); ?></a>
        <!-- Blog lives in the footer only. -->
        <a href="<?php echo esc_url($nav_guide); ?>"><?php echo esc_html(iptv_text('nav_link_guide', 'User Guide')); ?></a>
        <a href="<?php echo esc_url($nav_home . '#contact'); ?>"><?php echo esc_html(iptv_text('nav_link_contact', 'Contact')); ?></a>
        <a href="https://panel.nordictv.io/login"><?php echo esc_html(iptv_text('nav_link_account', 'My Account')); ?></a>
    <?php endif; ?>

    <!-- Language Selector in Mobile Menu -->
    <div class="mobile-language-selector">
        <span class="mobile-language-label"><?php echo esc_html(iptv_text('nav_region_label', 'Language')); ?></span>
        <div class="mobile-language-options">
            <button class="mobile-lang-btn" data-currency="usd" onclick="redirectToRegion('usd')">🇺🇸 English</button>
            <button class="mobile-lang-btn" data-currency="sek" onclick="redirectToRegion('sek')">🇸🇪 Svenska</button>
            <button class="mobile-lang-btn" data-currency="nok" onclick="redirectToRegion('nok')">🇳🇴 Norsk</button>
            <button class="mobile-lang-btn" data-currency="dkk" onclick="redirectToRegion('dkk')">🇩🇰 Dansk</button>
            <button class="mobile-lang-btn" data-currency="eur" onclick="redirectToRegion('eur')">🇫🇮 Suomi</button>
            <button class="mobile-lang-btn" data-currency="isk" onclick="redirectToRegion('isk')">🇮🇸 Íslenska</button>
        </div>
    </div>

    <a href="<?php echo esc_url($nav_home . '#pricing'); ?>" class="nav-btn nav-btn-primary" style="margin-top:1rem;"
        onclick="toggleMobileMenu()"><?php echo esc_html(iptv_text('nav_cta_label', 'Get Access Now')); ?></a>
</div>