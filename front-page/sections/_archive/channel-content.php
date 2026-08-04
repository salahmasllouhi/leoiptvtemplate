<?php
/**
 * Channel Details / Content Section
 * Simple strings: Polylang String Translations via tpl_str().
 * URL fields: ACF Options via tpl_ch().
 * Dynamic: ACF per-post fields (channel_type, channel_networks, channel_launch_year, channel_website, channel_popular_shows)
 */

// ── Per-post ACF fields ─────────────────────────────────────────────────────
$has_acf = function_exists('get_field');
$channel_type = $has_acf ? get_field('channel_type') : '';
$channel_networks = $has_acf ? get_field('channel_networks') : '';
$channel_launch_year = $has_acf ? get_field('channel_launch_year') : '';
$channel_website = $has_acf ? get_field('channel_website') : '';
$popular_shows = $has_acf ? get_field('channel_popular_shows') : [];

// Networks → badges array
$networks_array = [];
if ($channel_networks) {
    $networks_array = array_filter(array_map('trim', explode(',', $channel_networks)));
}

// ── Template strings (Polylang String Translations) ──────────────────────────
$tag_label = tpl_str('About This Channel');
$title_suffix = tpl_str('Live Stream');
$networks_label = tpl_str('Available on this channel:');
$shows_title_tpl = tpl_str('Popular on %s');
$cta_text_tpl = tpl_str('Start Watching %s');

// ── URL (CTA link) ───────────────────────────────────────────────────────────
$cta_url = '#pricing';



// ── Type icon map ───────────────────────────────────────────────────────────
$type_icons = [
    'Sports' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><line x1="2" y1="12" x2="22" y2="12"/></svg>',
    'News' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><line x1="18" y1="14" x2="10" y2="14"/><line x1="15" y1="18" x2="10" y2="18"/><rect x="10" y="6" width="8" height="4"/></svg>',
    'Entertainment' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
    'Movies' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="17" x2="22" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/></svg>',
    'Kids' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>',
    'Music' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
    'Documentary' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    'Lifestyle' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
    'General' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>',
];
$type_icon = $channel_type && isset($type_icons[$channel_type]) ? $type_icons[$channel_type] : $type_icons['General'];
?>

<section class="content-showcase">
    <div class="showcase-container" style="display:block;">
        <div style="max-width:860px;margin:0 auto;">

            <div class="section-tag"><?php echo esc_html($tag_label); ?></div>
            <h2 class="section-title">
                <?php echo esc_html($channel_name); ?>
                <span class="gradient-text"><?php echo esc_html($title_suffix); ?></span>
            </h2>

            <?php if ($channel_type || $channel_launch_year || $channel_website): ?>
                <div class="ch-info-strip">
                    <?php if ($channel_type): ?>
                        <div class="ch-info-pill">
                            <?php echo $type_icon; ?>
                            <span><?php echo esc_html($channel_type); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($channel_launch_year): ?>
                        <div class="ch-info-pill">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <span>Since <?php echo esc_html($channel_launch_year); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($channel_website): ?>
                        <div class="ch-info-pill">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="2" y1="12" x2="22" y2="12" />
                                <path
                                    d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                            </svg>
                            <a href="<?php echo esc_url($channel_website); ?>" target="_blank"
                                rel="noopener noreferrer nofollow">Official Website</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($networks_array)): ?>
                <div class="ch-networks">
                    <span class="ch-networks-label"><?php echo esc_html($networks_label); ?></span>
                    <div class="ch-networks-list">
                        <?php foreach ($networks_array as $network): ?>
                            <span class="ch-network-badge"><?php echo esc_html($network); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- WordPress Editor Content (SEO) -->
            <div class="channel-content">
                <?php the_content(); ?>
            </div>

            <?php if (!empty($popular_shows)): ?>
                <div class="ch-shows">
                    <h3 class="ch-shows-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon
                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                        <?php echo esc_html(sprintf($shows_title_tpl, $channel_name)); ?>
                    </h3>
                    <div class="ch-shows-grid">
                        <?php foreach ($popular_shows as $show): ?>
                            <div class="ch-show-card">
                                <div class="ch-show-dot"></div>
                                <div>
                                    <div class="ch-show-name"><?php echo esc_html($show['show_name']); ?></div>
                                    <?php if (!empty($show['show_schedule'])): ?>
                                        <div class="ch-show-schedule"><?php echo esc_html($show['show_schedule']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?php echo esc_url($cta_url); ?>" class="btn btn-primary"
                style="margin-top:2rem;display:inline-flex;">
                <?php echo esc_html(sprintf($cta_text_tpl, $channel_name)); ?>
            </a>

        </div>
    </div>
</section>

<style>
    .ch-info-strip {
        display: flex;
        flex-wrap: wrap;
        gap: .625rem;
        margin: 1.25rem 0 1.75rem;
    }

    .ch-info-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--bg-section);
        border: 1px solid var(--border-light, #e2e8f0);
        border-radius: var(--radius-full, 100px);
        padding: .375rem .875rem;
        font-size: .875rem;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .ch-info-pill svg {
        flex-shrink: 0;
        opacity: .7;
    }

    .ch-info-pill a {
        color: var(--color-teal);
        text-decoration: none;
        font-weight: 600;
    }

    .ch-networks {
        margin-bottom: 1.75rem;
    }

    .ch-networks-label {
        display: block;
        font-size: .8125rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
        margin-bottom: .625rem;
    }

    .ch-networks-list {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .ch-network-badge {
        background: linear-gradient(135deg, rgba(16, 137, 242, .12), rgba(61, 122, 184, .12));
        border: 1px solid rgba(16, 137, 242, .3);
        color: var(--text-primary);
        border-radius: var(--radius-sm, 8px);
        padding: .3rem .75rem;
        font-size: .8125rem;
        font-weight: 600;
    }

    .channel-content {
        color: var(--text-secondary);
        font-size: 1.0625rem;
        line-height: 1.8;
        margin-bottom: 2rem;
    }

    .channel-content h2,
    .channel-content h3 {
        color: var(--text-primary);
        margin-top: 1.5rem;
        margin-bottom: .75rem;
        font-weight: 700;
    }

    .channel-content p {
        margin-bottom: 1rem;
    }

    .channel-content ul,
    .channel-content ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .channel-content li {
        margin-bottom: .5rem;
    }

    .channel-content a {
        color: var(--color-teal);
        text-decoration: underline;
    }

    .channel-content strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .ch-shows {
        background: var(--bg-section);
        border-radius: var(--radius-lg, 20px);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-light, #e2e8f0);
    }

    .ch-shows-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 1rem;
    }

    .ch-shows-title svg {
        color: var(--color-gold, #FFB830);
    }

    .ch-shows-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: .75rem;
    }

    .ch-show-card {
        display: flex;
        align-items: flex-start;
        gap: .625rem;
        background: var(--bg-card);
        border-radius: var(--radius-md, 12px);
        padding: .75rem 1rem;
        border: 1px solid var(--border-light, #e2e8f0);
    }

    .ch-show-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--color-teal);
        flex-shrink: 0;
        margin-top: 5px;
    }

    .ch-show-name {
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .ch-show-schedule {
        font-size: .8rem;
        color: var(--text-muted);
        margin-top: .2rem;
    }
</style>