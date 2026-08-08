<?php
/**
 * ACF Field Group: Sport Details
 * Registers custom fields for the Sports custom post type.
 *
 * DISABLED: Field group is managed via ACF Pro UI (already created).
 * The acf-json/ file in this folder is the source of truth.
 * Re-enable the PHP registration below by removing the early return if needed.
 */

if (!defined('ABSPATH'))
    exit;

// Field group managed by ACF UI — PHP registration disabled to avoid conflicts
return;

add_action('acf/init', 'iptv_register_sport_acf_fields');

function iptv_register_sport_acf_fields()
{
    if (!function_exists('acf_add_local_field_group'))
        return;

    acf_add_local_field_group([
        'key' => 'group_sport_details',
        'title' => 'Sport Details',
        'fields' => [

            // ── Sport Type ────────────────────────────────────────────────────
            [
                'key' => 'field_sport_type',
                'label' => 'Sport Type',
                'name' => 'sport_type',
                'type' => 'select',
                'instructions' => 'What kind of sport is this?',
                'required' => 1,
                'choices' => [
                    'Football' => '🏈 Football',
                    'Soccer' => '⚽ Soccer',
                    'Basketball' => '🏀 Basketball',
                    'Baseball' => '⚾ Baseball',
                    'Hockey' => '🏒 Hockey',
                    'MMA/Boxing' => '🥊 MMA/Boxing',
                    'Motorsport' => '🏎️ Motorsport',
                    'Tennis' => '🎾 Tennis',
                    'General' => '🏆 General',
                ],
                'default_value' => 'General',
                'allow_null' => 0,
                'ui' => 1,
            ],

            // ── Sport Tagline ─────────────────────────────────────────────────
            [
                'key' => 'field_sport_tagline',
                'label' => 'Sport Tagline',
                'name' => 'sport_tagline',
                'type' => 'text',
                'instructions' => 'Short marketing line shown under the title in the hero. E.g. "The World\'s #1 Football League"',
                'required' => 0,
                'placeholder' => 'e.g. The Premier Football Destination',
                'maxlength' => 100,
            ],

            // ── Short Description ─────────────────────────────────────────────
            [
                'key' => 'field_sport_short_description',
                'label' => 'Short Description',
                'name' => 'sport_short_description',
                'type' => 'textarea',
                'instructions' => 'Short description shown below the headline in the hero section. Keep it under 200 characters for best display.',
                'required' => 0,
                'placeholder' => 'e.g. Stream live sport with NordicTV. No cable required. No blackouts. No restrictions.',
                'maxlength' => 250,
                'rows' => 3,
                'new_lines' => 'br',
            ],

            // ── Networks / Leagues ────────────────────────────────────────────
            [
                'key' => 'field_sport_networks',
                'label' => 'Networks & Leagues',
                'name' => 'sport_networks',
                'type' => 'text',
                'instructions' => 'Comma-separated list of the sports or genres covered. E.g. "football, basketball, motorsport"',
                'required' => 0,
                'placeholder' => 'e.g. football, basketball, motorsport',
            ],

            // ── Launch Year ───────────────────────────────────────────────────
            [
                'key' => 'field_sport_launch_year',
                'label' => 'Launch Year',
                'name' => 'sport_launch_year',
                'type' => 'number',
                'instructions' => 'The year this league/sport organization was founded.',
                'required' => 0,
                'placeholder' => 'e.g. 1920',
                'min' => 1800,
                'max' => 2030,
            ],

            // ── Official Website ──────────────────────────────────────────────
            [
                'key' => 'field_sport_website',
                'label' => 'Official Website',
                'name' => 'sport_website',
                'type' => 'url',
                'instructions' => 'Link to the official website (used in schema markup).',
                'required' => 0,
                'placeholder' => 'https://nfl.com',
            ],

        ],

        // Location: show only on Sport CPT
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sport',
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
