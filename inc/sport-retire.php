<?php
/**
 * Retire the `sport` post type.
 *
 * The CPT was registered through ACF PRO's post-type UI rather than a
 * register_post_type() call, so there is nothing in this theme to delete and
 * nothing in version control that records the section being switched off.
 * Unregistering it here takes its 145 posts off the front end and out of the
 * Rank Math sitemap while leaving the ACF definition untouched, so the section
 * can be brought back by removing this file.
 *
 * The posts are moved to draft as well, once, guarded by an option: without
 * that, re-registering the type later would silently republish 145 URLs.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Move every published `sport` post to draft.
 *
 * Runs at init priority 50 — after ACF has registered the type, so the query
 * still resolves, and before the unregister below at 99.
 *
 * Restricted to admin, REST and cron requests. It is a one-shot migration over
 * ~145 posts, and front-end visitors should not be the ones paying for it.
 */
function iptv_retire_sport_posts()
{
    if (get_option('iptv_sport_retired') === '1') {
        return;
    }

    $privileged = is_admin()
        || wp_doing_cron()
        || (defined('REST_REQUEST') && REST_REQUEST);

    if (!$privileged || !post_type_exists('sport')) {
        return;
    }

    // 'lang' => '' switches off Polylang's language filter, which would
    // otherwise return only the posts in the request's current language and
    // leave the other translations published.
    $ids = get_posts(array(
        'post_type'        => 'sport',
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'lang'             => '',
        'suppress_filters' => false,
    ));

    foreach ($ids as $id) {
        wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
    }

    // Written only after the loop finishes. If the request times out partway,
    // the option stays unset and the next one picks up the posts still on
    // 'publish' — the query is the progress marker, so retries are safe.
    update_option('iptv_sport_retired', '1', true);
}
add_action('init', 'iptv_retire_sport_posts', 50);

/**
 * Take `sport` off the front end: no single views, no archive, no sitemap.
 *
 * Priority 99 so ACF (init 5) has registered the type by the time we get here.
 */
add_action('init', function () {
    if (post_type_exists('sport')) {
        unregister_post_type('sport');
    }
}, 99);
