<?php
/**
 * User Guide Shortcode
 * 
 * Displays posts from 'user-guide' category as tabbed interface
 * Usage: [user_guide]
 */

function render_user_guide_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts(array(
        'category' => 'user-guide',
    ), $atts, 'user_guide');

    // Query posts from the category
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'category_name' => $atts['category'],
        'orderby' => 'menu_order date',
        'order' => 'ASC',
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No guides found in this category.</p>';
    }

    ob_start();
    ?>

    <style>
        :root {
            --ug-bg: #FFFFFF;
            --ug-tint: #F2F8FE;
            --ug-text: #1F2937;
            --ug-accent: #1089F2;
            --ug-border: #E5E7EB;
        }

        /* Container */
        .user-guide-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 1rem 0;
        }

        /* Tabs Navigation (Badges) - Wrapping */
        .ug-tabs-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--ug-tint);
            justify-content: center;
        }

        .ug-tab-badge {
            background-color: var(--ug-bg);
            border: 1px solid var(--ug-border);
            color: var(--ug-text);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .ug-tab-badge:hover {
            background-color: var(--ug-tint);
        }

        .ug-tab-badge.active {
            background-color: var(--ug-accent);
            color: #FFFFFF;
            border-color: var(--ug-accent);
        }

        /* Content Area */
        .ug-tab-content {
            display: none;
            animation: ugFadeIn 0.4s ease;
        }

        .ug-tab-content.active {
            display: block;
        }

        @keyframes ugFadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Content Styling */
        .ug-content-inner {
            color: var(--ug-text);
            line-height: 1.8;
        }

        .ug-content-inner h1,
        .ug-content-inner h2,
        .ug-content-inner h3,
        .ug-content-inner h4 {
            color: var(--ug-accent);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .ug-content-inner h2 {
            font-size: 1.5rem;
        }

        .ug-content-inner h3 {
            font-size: 1.25rem;
        }

        .ug-content-inner p {
            margin-bottom: 1rem;
        }

        .ug-content-inner img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: 1rem 0;
        }

        .ug-content-inner ul,
        .ug-content-inner ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .ug-content-inner li {
            margin-bottom: 0.5rem;
        }

        .ug-content-inner a {
            color: var(--ug-accent);
            text-decoration: underline;
        }

        .ug-content-inner blockquote {
            border-left: 4px solid var(--ug-accent);
            padding-left: 1rem;
            margin: 1rem 0;
            background: var(--ug-tint);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
        }

        .ug-content-inner pre,
        .ug-content-inner code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
        }

        .ug-content-inner pre {
            padding: 1rem;
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .ug-tabs-nav {
                gap: 8px;
            }

            .ug-tab-badge {
                padding: 8px 14px;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="user-guide-wrapper">

        <!-- Tabs Navigation -->
        <div class="ug-tabs-nav">
            <?php $i = 0;
            while ($query->have_posts()):
                $query->the_post();
                $i++; ?>
                <button class="ug-tab-badge <?php echo ($i === 1) ? 'active' : ''; ?>"
                    onclick="switchUserGuideTab(event, 'ug-content-<?php the_ID(); ?>')">
                    <?php the_title(); ?>
                </button>
            <?php endwhile; ?>
        </div>

        <!-- Guide Contents -->
        <?php $j = 0;
        $query->rewind_posts();
        while ($query->have_posts()):
            $query->the_post();
            $j++; ?>
            <div id="ug-content-<?php the_ID(); ?>" class="ug-tab-content <?php echo ($j === 1) ? 'active' : ''; ?>">
                <div class="ug-content-inner">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>

    </div>

    <script>
        function switchUserGuideTab(evt, contentId) {
            // Hide all contents
            var contents = document.querySelectorAll('.ug-tab-content');
            contents.forEach(function (div) { div.classList.remove('active'); });

            // Deactivate all tabs
            var tabs = document.querySelectorAll('.ug-tab-badge');
            tabs.forEach(function (btn) { btn.classList.remove('active'); });

            // Activate selected
            document.getElementById(contentId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('user_guide', 'render_user_guide_shortcode');
