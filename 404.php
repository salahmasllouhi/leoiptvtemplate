<?php
/**
 * 404 Error Page Template
 */

// Set proper 404 HTTP status
http_response_code(404);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Nordic IPTV</title>
    <?php wp_head(); ?>

    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/variables.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/redesign-theme.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/base.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/header.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/footer.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/responsive.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/design-v2.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/front-page/css/design-v2-sections.css">

    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
            background: var(--bg-alt, #F2F8FE);
            color: var(--text, #1F2937);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 1.5rem;
        }

        .error-box {
            text-align: center;
            max-width: 560px;
        }

        .error-code {
            font-size: clamp(6rem, 20vw, 10rem);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #1089F2 0%, #095BAA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 1rem;
        }

        .error-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark, #0f172a);
            margin: 0 0 1rem;
        }

        .error-description {
            font-size: 1rem;
            color: var(--text-secondary, #4B5563);
            line-height: 1.7;
            margin: 0 0 2.5rem;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #1089F2 0%, #095BAA 100%);
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.75rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: opacity 0.2s;
        }

        .btn-primary:hover {
            opacity: 0.88;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            color: var(--blue-600, #095BAA);
            text-decoration: none;
            padding: 0.75rem 1.75rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.95rem;
            border: 2px solid var(--blue-600, #095BAA);
            transition: background 0.2s, color 0.2s;
        }

        .btn-secondary:hover {
            background: var(--blue-600, #095BAA);
            color: #fff;
        }
    </style>
</head>

<body <?php body_class('error404'); ?>>
    <?php wp_body_open(); ?>

    <?php include get_template_directory() . '/inc/universal-header.php'; ?>

    <div class="error-wrapper">
        <div class="error-box">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-description">
                The page you're looking for doesn't exist or has been moved.<br>
                Let's get you back on track.
            </p>
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">
                    ← Back to Home
                </a>
                <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn-secondary">
                    Visit Blog
                </a>
            </div>
        </div>
    </div>

    <?php include get_template_directory() . '/front-page/sections/footer.php'; ?>

    <script src="<?php echo get_template_directory_uri(); ?>/front-page/js/currency.js"></script>

    <?php wp_footer(); ?>
</body>

</html>
