<?php
/**
 * Plan page data
 *
 * One template — template-plan.php — serves all four subscription lengths.
 * Everything that differs between the 1, 3, 6 and 12 month pages is derived
 * from a single ACF field, `plan_months`, so adding the next plan is a page,
 * a template pick and one dropdown rather than a copy of the PHP.
 *
 * Prices come from the stored table (IPTV_Currency_Settings::get_price_table()),
 * the same source the front-page configurator reads — never a live
 * recalculation on a visitor's request. Checkout is the same panel URL the
 * configurator builds: {checkout_base}?connections=N&duration=M.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_plan_durations')) {
    /**
     * The four lengths we sell, months => price-table key.
     *
     * Mirrors IPTV_Currency_Settings::$durations. Kept here as well so the
     * template still renders if WooCommerce or the currency class is inactive.
     *
     * @return array<int,string>
     */
    function iptv_plan_durations()
    {
        return array(
            1  => '1_month',
            3  => '3_months',
            6  => '6_months',
            12 => '12_months',
        );
    }
}

if (!function_exists('iptv_plan_field')) {
    /**
     * An ACF value from the plan page itself.
     *
     * iptv_text() deliberately reads the *front page*, which is right for the
     * shared site copy but wrong for anything a plan page owns. This reads the
     * current page, with the same raw-meta fallback iptv_text() uses: a field
     * added to acf-json/ but not yet synced is not registered, so get_field()
     * returns nothing while the value sits in post meta under the same key.
     *
     * @param string $key     Field name.
     * @param mixed  $default Returned when the field is empty.
     * @param int    $post_id Defaults to the current post.
     * @return mixed
     */
    function iptv_plan_field($key, $default = '', $post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id) {
            return $default;
        }

        if (function_exists('get_field')) {
            $value = get_field($key, $post_id);
            if ($value !== null && $value !== '' && $value !== false && $value !== array()) {
                return $value;
            }
        }

        // Only worth consulting for scalar fields. A repeater stores its row
        // count under the same key, so an array-shaped request would get back
        // the string "0" and every caller would have to guard against it.
        if (!is_array($default)) {
            $meta = get_post_meta($post_id, $key, true);
            if (is_string($meta) && $meta !== '') {
                return $meta;
            }
        }

        return $default;
    }
}

if (!function_exists('iptv_plan_flag')) {
    /**
     * An on/off field from the plan page.
     *
     * Separate from iptv_plan_field() because that one cannot tell "off" from
     * "not set": a true_false field returns false when switched off, which is
     * exactly the value a fallback treats as empty. Reading the toggles through
     * it left them permanently on.
     *
     * @param string $key     Field name.
     * @param bool   $default Returned only when the field has never been set.
     * @param int    $post_id Defaults to the current post.
     * @return bool
     */
    function iptv_plan_flag($key, $default = true, $post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id) {
            return $default;
        }

        if (function_exists('get_field')) {
            $value = get_field($key, $post_id);
            if (is_bool($value) || is_numeric($value)) {
                return (bool) $value;
            }
        }

        $meta = get_post_meta($post_id, $key, true);
        if ($meta === '0' || $meta === '1' || is_bool($meta) || is_int($meta)) {
            return (bool) $meta;
        }

        return $default;
    }
}

if (!function_exists('iptv_plan_months')) {
    /**
     * How long this page's plan runs for, in months.
     *
     * Clamped to a length we actually sell — an unsynced or hand-edited value
     * would otherwise index into the price table and come back empty, printing
     * a page full of zeroes.
     *
     * @param int $post_id Defaults to the current post.
     * @return int One of 1, 3, 6, 12.
     */
    function iptv_plan_months($post_id = 0)
    {
        $months = (int) iptv_plan_field('plan_months', 1, $post_id);

        return array_key_exists($months, iptv_plan_durations()) ? $months : 1;
    }
}

if (!function_exists('iptv_plan_currency')) {
    /**
     * Currency for this request, resolved server-side.
     *
     * front-page/js/currency.js derives the currency from the URL path on the
     * client. That is fine for the configurator, which repaints every price in
     * JS, but a plan page prints its prices in the HTML — for the crawler as
     * much as the visitor — so the same mapping has to exist in PHP.
     *
     * Language is the source of truth (Polylang), with the multisite slug as a
     * fallback for the subsites that still run their own blog.
     *
     * @return string Lowercase currency key: usd, eur, sek, nok, dkk or isk.
     */
    function iptv_plan_currency()
    {
        static $currency = null;

        if ($currency !== null) {
            return $currency;
        }

        $currency = 'usd';

        if (function_exists('pll_current_language') && function_exists('nordictv_lang_by_currency')) {
            $lang = pll_current_language('slug');
            $map  = array_flip(nordictv_lang_by_currency()); // slug => currency
            if ($lang && isset($map[$lang])) {
                $currency = $map[$lang];
                return $currency;
            }
        }

        if (class_exists('IPTV_Currency_Settings')) {
            $instance = IPTV_Currency_Settings::instance();
            if ($instance->is_subsite()) {
                $currency = $instance->get_subsite_currency();
            }
        }

        return $currency;
    }
}

if (!function_exists('iptv_plan_prices')) {
    /**
     * The stored price matrix: [duration_key][device_key][currency].
     *
     * @return array
     */
    function iptv_plan_prices()
    {
        static $prices = null;

        if ($prices === null) {
            $prices = class_exists('IPTV_Currency_Settings')
                ? IPTV_Currency_Settings::get_price_table()
                : array();
        }

        return $prices;
    }
}

if (!function_exists('iptv_plan_price')) {
    /**
     * Price of one plan, for one screen count, in one currency.
     *
     * @param int    $months   1, 3, 6 or 12.
     * @param int    $screens  1-4.
     * @param string $currency Defaults to the request's currency.
     * @return float 0 when the combination is missing from the table.
     */
    function iptv_plan_price($months, $screens, $currency = '')
    {
        $durations = iptv_plan_durations();

        if (!isset($durations[$months])) {
            return 0.0;
        }

        $prices     = iptv_plan_prices();
        $duration   = $durations[$months];
        $device_key = ($screens === 1) ? '1_device' : $screens . '_devices';
        $currency   = $currency ?: iptv_plan_currency();

        return isset($prices[$duration][$device_key][$currency])
            ? (float) $prices[$duration][$device_key][$currency]
            : 0.0;
    }
}

if (!function_exists('iptv_plan_currency_format')) {
    /**
     * Symbol, side and decimal rules for a currency.
     *
     * Matches front-page/js/currency.js so a price never changes shape when the
     * client-side switcher repaints it.
     *
     * @param string $currency
     * @return array{symbol:string,position:string,decimals:int}
     */
    function iptv_plan_currency_format($currency = '')
    {
        $currency = $currency ?: iptv_plan_currency();

        $symbols = array(
            'usd' => '$',
            'eur' => '€',
            'sek' => 'kr',
            'nok' => 'kr',
            'dkk' => 'kr',
            'isk' => 'kr',
        );

        $before = in_array($currency, array('usd', 'eur'), true);

        return array(
            'symbol'   => isset($symbols[$currency]) ? $symbols[$currency] : '$',
            'position' => $before ? 'before' : 'after',
            'decimals' => $before ? 2 : 0,
        );
    }
}

if (!function_exists('iptv_plan_format_price')) {
    /**
     * Render an amount in the request's currency.
     *
     * @param float  $amount
     * @param string $currency Defaults to the request's currency.
     * @return string e.g. "$16.99" or "179 kr".
     */
    function iptv_plan_format_price($amount, $currency = '')
    {
        $format = iptv_plan_currency_format($currency);
        $number = number_format((float) $amount, $format['decimals'], '.', '');

        return $format['position'] === 'before'
            ? $format['symbol'] . $number
            : $number . ' ' . $format['symbol'];
    }
}

if (!function_exists('iptv_plan_checkout_url')) {
    /**
     * Where a buy button goes.
     *
     * The panel derives the price from these two parameters, exactly as it does
     * for the front-page configurator — there is no cart and no product ID in
     * the link, so nothing here can drift out of sync with WooCommerce.
     *
     * @param int $screens 1-4.
     * @param int $months  1, 3, 6 or 12.
     * @return string
     */
    function iptv_plan_checkout_url($screens, $months)
    {
        $base = iptv_config('checkout_base_url', 'https://panel.nordictv.io/checkout');

        return add_query_arg(
            array(
                'connections' => (int) $screens,
                'duration'    => (int) $months,
            ),
            $base
        );
    }
}

if (!function_exists('iptv_plan_savings')) {
    /**
     * What this plan saves against paying month by month.
     *
     * The comparison is the same screen count bought one month at a time, so
     * the claim is true rather than flattering. A one-month plan compares with
     * itself and therefore saves nothing.
     *
     * @param int $months  1, 3, 6 or 12.
     * @param int $screens 1-4.
     * @return array{now:float,was:float,off:float,pct:int,per_month:float}
     */
    function iptv_plan_savings($months, $screens)
    {
        $now     = iptv_plan_price($months, $screens);
        $monthly = iptv_plan_price(1, $screens);
        $was     = $monthly * $months;
        $off     = max(0, $was - $now);

        return array(
            'now'       => $now,
            'was'       => $was,
            'off'       => $off,
            'pct'       => ($was > 0 && $off > 0) ? (int) round(($off / $was) * 100) : 0,
            'per_month' => $now / max(1, $months),
        );
    }
}

if (!function_exists('iptv_plan_url')) {
    /**
     * Permalink of the plan page for another length, in this language.
     *
     * Found by the template plus plan_months rather than by slug, so the four
     * pages link to each other on their own — rename or translate any of them
     * and the compare table still resolves.
     *
     * Polylang note: 'lang' => '' means *the current language*, not all of
     * them, so every language is named explicitly and pll_get_post() maps the
     * result back. This is the same trick iptv_page_url() uses, and for the
     * same reason: a translated plan page will have its own slug.
     *
     * $lang exists for inc/legacy-redirects.php, which resolves the plan page
     * for a language the request itself cannot name — a retired /da/product/…
     * URL has to land on the Danish plan page, but Polylang reads /da/ as the
     * default language. See the same parameter on iptv_page_url().
     *
     * $strict refuses to fall back to another language's plan page, for the
     * same reason iptv_page_url() has it: a cross-language 301 contradicts
     * hreflang. The compare table wants the fallback; a redirect does not.
     *
     * @param int    $months 1, 3, 6 or 12.
     * @param string $lang   Target language slug. Empty means the current one.
     * @param bool   $strict Return '' rather than another language's plan page.
     * @return string Permalink, or '' when that plan has no page yet.
     */
    function iptv_plan_url($months, $lang = '', $strict = false)
    {
        static $cache = array();

        $months = (int) $months;

        if ($lang === '') {
            $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
        }

        $key = $months . '|' . $lang . '|' . (int) $strict;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $args = array(
            'post_type'              => 'page',
            'post_status'            => 'publish',
            'posts_per_page'         => 1,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'meta_query'             => array(
                array(
                    'key'   => '_wp_page_template',
                    'value' => 'template-plan.php',
                ),
                array(
                    // String: ACF stores the select's value, not an integer.
                    'key'   => 'plan_months',
                    'value' => (string) $months,
                ),
            ),
        );

        if (function_exists('nordictv_lang_slugs')) {
            $languages = nordictv_lang_slugs();
            if (!empty($languages)) {
                $args['lang'] = implode(',', $languages);
            }
        }

        $url   = '';
        $query = new WP_Query($args);

        if (!empty($query->posts)) {
            $id = $query->posts[0]->ID;

            if (function_exists('pll_get_post')) {
                $translated = pll_get_post($id, $lang);
                if ($translated) {
                    $id = $translated;
                } elseif ($strict) {
                    $id = 0;
                }
            }

            $url = $id ? get_permalink($id) : '';
        }

        $cache[$key] = $url;

        return $url;
    }
}

if (!function_exists('iptv_plan_label')) {
    /**
     * "1 Month" / "3 Months", reusing the front page's translated labels so the
     * plan pages and the configurator never disagree on wording.
     *
     * @param int $months
     * @return string
     */
    function iptv_plan_label($months)
    {
        $defaults = array(
            1  => '1 Month',
            3  => '3 Months',
            6  => '6 Months',
            12 => '12 Months',
        );

        $months = (int) $months;

        if (!isset($defaults[$months])) {
            return '';
        }

        return iptv_text('month_' . $months . '_label', $defaults[$months]);
    }
}

if (!function_exists('iptv_plan_screens_label')) {
    /**
     * "1 Screen" / "3 Screens", from the same two strings the configurator uses.
     *
     * @param int $screens
     * @return string
     */
    function iptv_plan_screens_label($screens)
    {
        $screens = (int) $screens;
        $word    = $screens > 1
            ? iptv_text('screen_plural', 'Screens')
            : iptv_text('screen_singular', 'Screen');

        return $screens . ' ' . $word;
    }
}

if (!function_exists('iptv_plan_popular_screens')) {
    /**
     * Screen count that carries the POPULAR flag, shared with the front page.
     *
     * @return int
     */
    function iptv_plan_popular_screens()
    {
        $screens = (int) iptv_config('pricing_popular_screens', 2);

        return ($screens >= 1 && $screens <= 4) ? $screens : 2;
    }
}

if (!function_exists('iptv_plan_faq_items')) {
    /**
     * The FAQ rows for this page: the ACF repeater when filled, otherwise a
     * per-length default set. Shared by the accordion and the FAQPage schema so
     * the two can never describe different questions.
     *
     * @param int $months
     * @return array<int,array{q:string,a:string}>
     */
    function iptv_plan_faq_items($months)
    {
        $items = array();
        $rows  = iptv_plan_field('plan_faq', array());

        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['question'])) {
                    $items[] = array(
                        'q' => $row['question'],
                        'a' => isset($row['answer']) ? $row['answer'] : '',
                    );
                }
            }
        }

        if (!empty($items)) {
            return $items;
        }

        // Defaults live in plan/inc/plan-strings.php, which is also what
        // registers them with Polylang — one array, so copy edited there cannot
        // drift out of registration and silently stop translating.
        $label = iptv_plan_label($months);

        foreach (iptv_plan_faq_defaults($months) as $row) {
            $items[] = array(
                // The only row carrying a placeholder is the "what do I get"
                // one; sprintf on a string without one returns it unchanged.
                'q' => sprintf(plan_str($row['q']), $label),
                'a' => plan_str($row['a']),
            );
        }

        return $items;
    }
}
