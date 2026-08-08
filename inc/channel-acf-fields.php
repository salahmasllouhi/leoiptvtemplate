<?php
/**
 * ACF Field Group: Channel Details
 * Registers custom fields for the Channels custom post type
 * Fields are meaningful per-channel data — not generic quality/country fields
 *
 * DISABLED: Field group is now managed via acf-json/group_channel_details.json
 * Re-enable by removing the early return below if needed.
 */

if (!defined('ABSPATH'))
    exit;

// Field group managed by acf-json — PHP registration disabled to allow editing in ACF UI
return;

add_action('acf/init', 'iptv_register_channel_acf_fields');


function iptv_register_channel_acf_fields()
{
    if (!function_exists('acf_add_local_field_group'))
        return;

    acf_add_local_field_group([
        'key' => 'group_channel_details',
        'title' => 'Channel Details',
        'fields' => [

            // ── Channel Type ─────────────────────────────────────────────────
            [
                'key' => 'field_channel_type',
                'label' => 'Channel Type',
                'name' => 'channel_type',
                'type' => 'select',
                'instructions' => 'What kind of content does this channel broadcast?',
                'required' => 1,
                'choices' => [
                    'Sports' => '⚽ Sports',
                    'News' => '📰 News',
                    'Entertainment' => '🎬 Entertainment',
                    'Movies' => '🎥 Movies',
                    'Kids' => '🧸 Kids',
                    'Music' => '🎵 Music',
                    'General' => '📺 General',
                    'Documentary' => '🎞️ Documentary',
                    'Lifestyle' => '🌿 Lifestyle',
                ],
                'default_value' => 'General',
                'allow_null' => 0,
                'ui' => 1,
            ],

            // ── Channel Tagline ───────────────────────────────────────────────
            [
                'key' => 'field_channel_tagline',
                'label' => 'Channel Tagline',
                'name' => 'channel_tagline',
                'type' => 'text',
                'instructions' => 'Short marketing line shown under the title in the hero. E.g. "The World\'s #1 Sports Network"',
                'required' => 0,
                'placeholder' => 'e.g. The Worldwide Leader in Sports',
                'maxlength' => 100,
            ],

            // ── Networks / Leagues ────────────────────────────────────────────
            [
                'key' => 'field_channel_networks',
                'label' => 'Networks & Leagues',
                'name' => 'channel_networks',
                'type' => 'text',
                'instructions' => 'Comma-separated list of the sports or genres on this channel. E.g. "football, basketball, motorsport, combat sports"',
                'required' => 0,
                'placeholder' => 'e.g. football, basketball, motorsport, combat sports',
            ],

            // ── Launch Year ───────────────────────────────────────────────────
            [
                'key' => 'field_channel_launch_year',
                'label' => 'Launch Year',
                'name' => 'channel_launch_year',
                'type' => 'number',
                'instructions' => 'The year this channel was founded/launched.',
                'required' => 0,
                'placeholder' => 'e.g. 1979',
                'min' => 1900,
                'max' => 2030,
            ],

            // ── Official Website ──────────────────────────────────────────────
            [
                'key' => 'field_channel_website',
                'label' => 'Official Website',
                'name' => 'channel_website',
                'type' => 'url',
                'instructions' => 'Link to the channel\'s official website (used in schema markup).',
                'required' => 0,
                'placeholder' => 'https://espn.com',
            ],

            // ── Popular Shows / Events ────────────────────────────────────────
            [
                'key' => 'field_channel_popular_shows',
                'label' => 'Popular Shows & Events',
                'name' => 'channel_popular_shows',
                'type' => 'repeater',
                'instructions' => 'Add up to 6 popular shows, leagues or events this channel is known for.',
                'required' => 0,
                'min' => 0,
                'max' => 6,
                'layout' => 'table',
                'button_label' => 'Add Show / Event',
                'sub_fields' => [
                    [
                        'key' => 'field_show_name',
                        'label' => 'Show / Event Name',
                        'name' => 'show_name',
                        'type' => 'text',
                        'required' => 1,
                        'placeholder' => 'e.g. Monday Night Football',
                        'column_width' => 50,
                    ],
                    [
                        'key' => 'field_show_schedule',
                        'label' => 'When / Schedule',
                        'name' => 'show_schedule',
                        'type' => 'text',
                        'required' => 0,
                        'placeholder' => 'e.g. Every Monday 8PM ET',
                        'column_width' => 50,
                    ],
                ],
            ],

        ],

        // Location: show only on Channel CPT (ACF post type key = 'channel')
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'channel',
                ],
            ],
        ],

        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
}
