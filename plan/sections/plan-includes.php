<?php
/**
 * What every plan includes
 *
 * Deliberately identical on all four pages: the length changes what you pay,
 * never what you get. Falls back to the same plan_includes_* strings the
 * front-page configurator prints, so the two lists cannot drift apart.
 */

$includes_title = iptv_plan_field(
    'plan_includes_title',
    iptv_text('plan_includes_title', 'Every plan is fully loaded')
);

$defaults = array(
    1  => '40,000+ Live TV Channels',
    2  => '200,000+ Movies & Series (VOD)',
    3  => '4K, Ultra HD & HD quality',
    4  => 'Stable, fast servers',
    5  => 'Full TV guide (EPG)',
    6  => 'Anti-Buffer™ 9.8',
    7  => 'Live hockey, football & handball',
    8  => 'Pay-Per-View (PPV) events',
    9  => 'Auto-updating channels & VOD',
    10 => '24/7 support',
);

$items = array();
$rows  = iptv_plan_field('plan_includes', array());

if (is_array($rows)) {
    foreach ($rows as $row) {
        if (!empty($row['item'])) {
            $items[] = $row['item'];
        }
    }
}

if (empty($items)) {
    foreach ($defaults as $n => $item) {
        $items[] = iptv_text("plan_includes_{$n}", $item);
    }
}
?>
<section class="plan-includes dv2-section">
    <div class="container">
        <div class="dv2-loaded plan-includes-panel">
            <h2 class="dv2-loaded-title">
                <span aria-hidden="true">⚡</span>
                <?php echo esc_html($includes_title); ?>
            </h2>
            <ul class="dv2-loaded-list">
                <?php foreach ($items as $item) : ?>
                    <li><?php echo esc_html($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
