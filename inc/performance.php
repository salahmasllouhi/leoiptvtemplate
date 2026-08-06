<?php
/**
 * Front-end performance
 *
 * Three jobs, all of them invisible: drop assets nothing on the page uses,
 * serve the two webfonts from this domain instead of Google's, and stop the
 * analytics tags from competing with the page for the first second of its life.
 *
 * Nothing here changes markup, copy or layout. The rules are deliberately
 * narrow — each one names the thing it removes and why it is safe to remove.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/* -------------------------------------------------------------------------
   Assets nothing on the page uses
   ------------------------------------------------------------------------- */

/**
 * Chaty registers the PicMo emoji picker for the inline chat form it can show.
 * Our widget only opens WhatsApp, Telegram and phone links, so the picker is
 * never constructed — it is 33.5KB of JavaScript for a code path that cannot
 * run here.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }

    foreach (array('chaty-picmo-js', 'chaty-picmo-latest-js') as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}, 100);

/**
 * jQuery Migrate exists to shim calls removed in jQuery 3 and to warn about
 * them in the console. Nothing on the front end needs it; wp-admin keeps it,
 * because that is where an untested plugin screen is most likely to want it.
 */
add_action('wp_default_scripts', function ($scripts) {
    if (is_admin() || empty($scripts->registered['jquery'])) {
        return;
    }

    $scripts->registered['jquery']->deps = array_diff(
        $scripts->registered['jquery']->deps,
        array('jquery-migrate')
    );
});

/* -------------------------------------------------------------------------
   Webfonts, served from this domain
   ------------------------------------------------------------------------- */

/**
 * The @font-face block for the self-hosted families, with the relative paths in
 * assets/css/fonts.css resolved against the theme directory.
 *
 * Inlined rather than enqueued: a stylesheet link to fonts.css would be one
 * more render-blocking request, and the file is under 1KB.
 *
 * @return string
 */
function nordictv_font_face_css()
{
    static $css = null;

    if ($css !== null) {
        return $css;
    }

    $path = get_template_directory() . '/assets/css/fonts.css';
    $css  = file_exists($path) ? (string) file_get_contents($path) : '';

    if ($css !== '') {
        $css = str_replace('../fonts/', get_template_directory_uri() . '/assets/fonts/', $css);
    }

    return $css;
}

/**
 * Preload the two latin faces and declare the families.
 *
 * The hero has no artwork, so the largest element above the fold is the
 * headline — which means the font is the LCP dependency, and preloading it is
 * the equivalent of preloading an LCP image. Only the `latin` subsets are
 * preloaded: every Nordic character the site sets, including ð and þ, lives in
 * U+0000-00FF, so `latin-ext` is fetched only if a page actually needs it.
 */
add_action('wp_head', function () {
    $css = nordictv_font_face_css();

    if ($css === '') {
        return;
    }

    $fonts = get_template_directory_uri() . '/assets/fonts/';

    foreach (array('sora-latin.woff2', 'space-grotesk-latin.woff2') as $file) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url($fonts . $file)
        );
    }

    echo '<style id="nordictv-fonts">' . $css . '</style>' . "\n";
}, 1);

/* -------------------------------------------------------------------------
   Analytics, after the page is usable
   ------------------------------------------------------------------------- */

/**
 * A one-shot "the visitor is here and the page is done" trigger.
 *
 * Both tags below queue their calls synchronously and only need their remote
 * script to flush that queue, so delaying the download costs no events — a
 * PageView fired at load is still a PageView when the script arrives. The
 * trigger fires on the first real interaction, or shortly after load if the
 * visitor does nothing, whichever comes first.
 */
add_action('wp_head', function () {
    ?>
    <script>
        window.nordictvWhenIdle = (function () {
            var queue = [];
            var fired = false;

            function run() {
                if (fired) {
                    return;
                }
                fired = true;

                events.forEach(function (name) {
                    window.removeEventListener(name, run, opts);
                });

                while (queue.length) {
                    queue.shift()();
                }
            }

            var events = ['pointerdown', 'keydown', 'touchstart', 'wheel'];
            var opts = { passive: true, capture: true };

            events.forEach(function (name) {
                window.addEventListener(name, run, opts);
            });

            window.addEventListener('load', function () {
                setTimeout(run, 2500);
            });

            return function (fn) {
                fired ? fn() : queue.push(fn);
            };
        })();
    </script>
    <?php
}, 0);

/**
 * Microsoft Clarity, deferred.
 *
 * The plugin prints its loader straight into wp_head. Replacing its action with
 * our own keeps the same project id and the same `clarity()` queue, and only
 * moves the moment the tag is fetched.
 */
add_action('wp_head', function () {
    if (!function_exists('clarity_add_script_to_header')) {
        return;
    }

    remove_action('wp_head', 'clarity_add_script_to_header');

    $project_id = get_option('clarity_project_id');

    if (empty($project_id)) {
        return;
    }
    ?>
    <script>
        (function (c, a, i) {
            c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
            window.nordictvWhenIdle(function () {
                var t = document.createElement('script');
                t.async = 1;
                t.src = 'https://www.clarity.ms/tag/' + i + '?ref=wordpress';
                document.head.appendChild(t);
            });
        })(window, 'clarity', <?php echo wp_json_encode((string) $project_id); ?>);
    </script>
    <?php
}, 2);

/**
 * Meta Pixel, deferred.
 *
 * Replaces the copy in functions.php. The stub and the PageView are kept
 * inline and synchronous, so the event is recorded at the same moment as
 * before; only fbevents.js waits.
 */
add_action('wp_head', function () {
    ?>
    <!-- Meta Pixel Code -->
    <script>
        !function (f, b, e, n) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
            };
            if (!f._fbq) f._fbq = n;
            n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
        }(window, document, 'script');
        fbq('init', '2000858220840148');
        fbq('track', 'PageView');
        window.nordictvWhenIdle(function () {
            var t = document.createElement('script');
            t.async = !0;
            t.src = 'https://connect.facebook.net/en_US/fbevents.js';
            document.head.appendChild(t);
        });
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=2000858220840148&ev=PageView&noscript=1" alt="" /></noscript>
    <!-- End Meta Pixel Code -->
    <?php
}, 3);
