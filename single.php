<?php
/**
 * Single Blog Post Template
 * Clean, readable design with sidebar
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?> - Nordic IPTV</title>
    <?php wp_head(); ?>

    <!-- Include Front Page CSS Files -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/variables.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/redesign-theme.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/base.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/header.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/footer.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/blog.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/responsive.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/design-v2.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/design-v2-sections.css">

    <style>
        /* Minimal overrides if needed */
        body {
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
            background: var(--bg-section);
            color: var(--text-primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
    </style>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- Include Universal Header -->
    <?php include get_template_directory() . '/inc/universal-header.php'; ?>

    <?php while (have_posts()):
        the_post();
        $categories = get_the_category();
        $category_name = !empty($categories) ? $categories[0]->name : 'General';
        $tags = get_the_tags();
        ?>

        <!-- POST HEADER -->
        <div class="post-header">
            <div class="container">
                <div class="post-header-content">
                    <span class="post-category">
                        <?php echo esc_html($category_name); ?>
                    </span>
                    <h1>
                        <?php the_title(); ?>
                    </h1>
                    <div class="post-meta">
                        <span>📅
                            <?php echo get_the_date('F j, Y'); ?>
                        </span>
                        <span>👤
                            <?php the_author(); ?>
                        </span>
                        <span>⏱
                            <?php echo ceil(str_word_count(get_the_content()) / 200); ?> min read
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST CONTENT -->
        <main class="container">
            <div class="post-layout">
                <article class="post-content">
                    <?php if (has_post_thumbnail()): ?>
                        <?php
                        // The attachment's own alt text describes the picture; the
                        // post title only describes the article, and using it here
                        // threw away whatever was written in the media library.
                        // Fall back to the title so the attribute is never empty.
                        $featured_alt = trim((string) get_post_meta(
                            get_post_thumbnail_id(),
                            '_wp_attachment_image_alt',
                            true
                        ));

                        if ($featured_alt === '') {
                            $featured_alt = get_the_title();
                        }
                        ?>
                        <img src="<?php the_post_thumbnail_url('large'); ?>"
                            alt="<?php echo esc_attr($featured_alt); ?>"
                            class="post-featured-image">
                    <?php endif; ?>

                    <div class="post-body">
                        <?php the_content(); ?>
                    </div>

                    <?php if ($tags): ?>
                        <div class="post-tags">
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo get_tag_link($tag->term_id); ?>" class="post-tag">#
                                    <?php echo $tag->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- CTA Widget -->
                    <div class="sidebar-widget cta-widget">
                        <h3>Get Nordic IPTV</h3>
                        <p>40,000+ channels, 200,000+ movies. Start streaming today!</p>
                        <a href="<?php echo home_url('/#pricing'); ?>" class="cta-btn">View Plans</a>
                    </div>

                    <!-- Related Posts -->
                    <div class="sidebar-widget">
                        <h3>Related Posts</h3>
                        <?php
                        $related = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => 3,
                            'post__not_in' => array(get_the_ID()),
                            'category__in' => wp_get_post_categories(get_the_ID()),
                        ));
                        if ($related->have_posts()):
                            while ($related->have_posts()):
                                $related->the_post();
                                ?>
                                <div class="related-post">
                                    <?php if (has_post_thumbnail()): ?>
                                        <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" class="related-post-image" alt="">
                                    <?php else: ?>
                                        <div class="related-post-image"></div>
                                    <?php endif; ?>
                                    <div class="related-post-content">
                                        <h4><a href="<?php the_permalink(); ?>">
                                                <?php the_title(); ?>
                                            </a></h4>
                                        <span class="related-post-date">
                                            <?php echo get_the_date('M j, Y'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile;
                            wp_reset_postdata();
                        endif; ?>
                    </div>
                </aside>
            </div>

            <!-- Post Navigation -->
            <div class="post-nav">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                <div>
                    <?php if ($prev_post): ?>
                        <a href="<?php echo get_permalink($prev_post); ?>" class="post-nav-item prev">
                            <div class="post-nav-label">← Previous</div>
                            <div class="post-nav-title">
                                <?php echo get_the_title($prev_post); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($next_post): ?>
                        <a href="<?php echo get_permalink($next_post); ?>" class="post-nav-item next">
                            <div class="post-nav-label">Next →</div>
                            <div class="post-nav-title">
                                <?php echo get_the_title($next_post); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>

    <?php endwhile; ?>

    <!-- Universal Footer -->
    <?php include get_template_directory() . '/front-page/sections/footer.php'; ?>

    <!-- Include Currency JS -->
    <script src="<?php echo get_template_directory_uri(); ?>/front-page/js/currency.js"></script>

    <?php wp_footer(); ?>
</body>

</html>