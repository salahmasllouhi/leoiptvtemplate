<?php
/**
 * TEMPORARY diagnostic — delete after use.
 *
 * Answers two questions the front end cannot: which revision of
 * inc/front-page-seo.php is actually deployed, and what Polylang's API returns
 * for each language's home URL.
 */

require_once dirname(__FILE__) . '/../../../wp-load.php';

header('Content-Type: text/plain; charset=utf-8');

$file = dirname(__FILE__) . '/inc/front-page-seo.php';
$src  = file_exists($file) ? file_get_contents($file) : '';

echo "deployed file mtime : " . (file_exists($file) ? gmdate('c', filemtime($file)) : 'MISSING') . "\n";
echo "has slug argument   : " . (strpos($src, "pll_get_post_language(\$post_id, 'slug')") !== false ? 'yes' : 'no') . "\n";
echo "has canonical filter: " . (strpos($src, 'rank_math/frontend/canonical') !== false ? 'yes' : 'no') . "\n\n";

foreach (array('en', 'sv', 'no', 'dk', 'fi', 'is') as $slug) {
    echo str_pad($slug, 4) . ' pll_home_url: '
        . (function_exists('pll_home_url') ? var_export(pll_home_url($slug), true) : 'n/a') . "\n";
}

echo "\n";
foreach (array(6, 419, 3179, 3180, 3181, 3182) as $id) {
    echo $id . ' lang=' . (function_exists('pll_get_post_language') ? var_export(pll_get_post_language($id, 'slug'), true) : 'n/a')
        . ' permalink=' . get_permalink($id) . "\n";
}
