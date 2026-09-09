<?php
/**
 * One-shot: who keeps reverting posts 3428 and 3430?
 *
 * Both posts accept a content write — the REST response confirms it, and WP
 * even stores a revision — and then snap back to an earlier body within about
 * ninety seconds. Three different write paths behave the same way
 * (wp_update_post, a literal find/replace, and restoring a revision), and the
 * four sibling posts written once each held fine, so it is second writes to
 * these two that get undone.
 *
 * WP Activity Log records every post change with an actor, so this reads its
 * tables directly for events tagged with either post ID. inc/activity-log-
 * reader.php already does this shape of query but filters by username; this
 * filters by PostID instead, which is the axis that matters here.
 *
 * Read-only against wsal. Delete this file and its require once the answer is
 * in hand.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Bump to re-run. */
define('POST_REVERT_AUDIT_BUILD', 1);

/** Posts under investigation. */
function iptv_revert_audit_post_ids()
{
    return array('3428', '3430');
}

/**
 * Pull the most recent activity-log events touching those posts.
 *
 * @return array
 */
function iptv_revert_audit_collect()
{
    global $wpdb;

    $occurrences = $wpdb->base_prefix . 'wsal_occurrences';
    $metadata    = $wpdb->base_prefix . 'wsal_metadata';

    if ($occurrences !== $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $occurrences))) {
        return array('error' => 'wsal_occurrences not found');
    }

    $ids = iptv_revert_audit_post_ids();
    $in  = implode(',', array_fill(0, count($ids), '%s'));

    // Occurrence ids whose metadata names either post.
    $occ_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT occurrence_id FROM `{$metadata}`
             WHERE name IN ('PostID','post_id') AND value IN ({$in})
             ORDER BY occurrence_id DESC LIMIT 40",
            $ids
        )
    );

    if (!$occ_ids) {
        return array('events' => array(), 'note' => 'no wsal events reference these post ids');
    }

    $columns = array();
    foreach ((array) $wpdb->get_col("SHOW COLUMNS FROM `{$occurrences}`") as $column) {
        $columns[$column] = true;
    }

    $ph   = implode(',', array_fill(0, count($occ_ids), '%d'));
    $rows = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM `{$occurrences}` WHERE id IN ({$ph}) ORDER BY created_on DESC", $occ_ids),
        ARRAY_A
    );

    $meta = $wpdb->get_results(
        $wpdb->prepare("SELECT occurrence_id, name, value FROM `{$metadata}` WHERE occurrence_id IN ({$ph})", $occ_ids),
        ARRAY_A
    );

    $by_occ = array();
    foreach ($meta as $m) {
        // Only the fields that identify the actor and the target.
        if (in_array($m['name'], array('PostID', 'post_id', 'PostTitle', 'Username', 'CurrentUserID', 'ClientIP', 'UserAgent', 'PostStatus', 'RevisionLink'), true)) {
            $by_occ[$m['occurrence_id']][$m['name']] = is_string($m['value'])
                ? substr(trim($m['value'], '"'), 0, 120)
                : $m['value'];
        }
    }

    $events = array();
    foreach ($rows as $row) {
        $events[] = array(
            'when'     => wp_date('Y-m-d H:i:s', (int) $row['created_on']),
            'alert_id' => isset($row['alert_id']) ? $row['alert_id'] : null,
            'user'     => isset($row['username']) ? $row['username'] : null,
            'meta'     => isset($by_occ[$row['id']]) ? $by_occ[$row['id']] : array(),
        );
    }

    return array('events' => array_slice($events, 0, 25), 'columns' => array_keys($columns));
}

add_action('init', function () {
    if ((int) get_option('revertaudit_built') === POST_REVERT_AUDIT_BUILD) {
        return;
    }

    update_option('revertaudit_built', POST_REVERT_AUDIT_BUILD, false);
    update_option('revertaudit_report', iptv_revert_audit_collect(), false);
}, 99);
