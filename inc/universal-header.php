<!-- Universal Header - Includes front-page header for consistency -->
<?php
/**
 * Universal Header
 * 
 * This file includes the front-page header to ensure
 * consistent navigation across all pages.
 */
include get_template_directory() . '/front-page/sections/header.php';
?>

<!-- Header JavaScript for scroll effects and mobile menu -->
<script>
    // Header JavaScript - Mobile menu and scroll effects
    function toggleMobileMenu() {
        document.getElementById('mobile-menu').classList.toggle('active');
    }

    function toggleCountryDropdown() {
        const dropdown = document.getElementById('countryDropdown');
        if (dropdown) dropdown.classList.toggle('active');
    }

    // Close mobile menu when a link is clicked
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu) {
            mobileMenu.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') {
                    toggleMobileMenu();
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.country-selector')) {
                const dropdown = document.getElementById('countryDropdown');
                if (dropdown) dropdown.classList.remove('active');
            }
        });
    });

    // Header scroll effect - makes header sticky on scroll
    window.addEventListener('scroll', function () {
        const header = document.getElementById('site-header');
        if (header) {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });

    // Redirect to the chosen language (syncs with currency.js).
    //
    // The argument is a Polylang slug. A currency code still resolves, through
    // window.nordictvLangSlug — but it cannot tell Finnish from German, which
    // is why the switcher markup names languages now.
    function redirectToRegion(key) {
        // Remember the choice for next visit. window.nordictvLang is printed
        // by inc/language-preference.php, which also reads the cookie back.
        const cfg = window.nordictvLang;
        const slug = window.nordictvLangSlug ? window.nordictvLangSlug(key) : key;
        if (!slug) return;

        if (cfg && cfg.cookie) {
            document.cookie = cfg.cookie + '=' + encodeURIComponent(slug) +
                ';path=/;max-age=' + (cfg.days * 24 * 60 * 60) + ';samesite=lax';
        }

        // This page's counterpart in the chosen language, not the language root.
        let target = window.nordictvLangUrl && window.nordictvLangUrl(slug);
        if (!target) {
            target = window.location.origin + (slug === 'en' ? '/' : '/' + slug + '/');
        }

        let isRoot = false;
        try {
            isRoot = /^\/([a-z]{2}\/)?$/.test(new URL(target, window.location.origin).pathname);
        } catch (e) {
            isRoot = false;
        }
        if (isRoot) {
            target += (target.indexOf('?') === -1 ? '?' : '&') + 'nolangredirect=1';
        }

        window.location.href = target;
    }
</script>