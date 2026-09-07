<?php
/**
 * Theme SEO Manager for WordPress Multisite
 * 
 * Provides SEO meta boxes, live preview, hreflang tags, and admin columns.
 * 
 * @package Nordic_IPTV
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Theme_SEO_Manager
{

    /**
     * Hreflang mapping for Nordic countries
     */
    private $hreflang_map = array(
        'se' => 'sv-SE',  // Swedish
        'no' => 'nb-NO',  // Norwegian
        'dk' => 'da-DK',  // Danish
        'fi' => 'fi-FI',  // Finnish
        'is' => 'is-IS',  // Icelandic
        'de' => 'de-DE',  // German
    );

    /**
     * Constructor - Hook into WordPress
     */
    public function __construct()
    {
        // Meta box
        add_action('add_meta_boxes', array($this, 'add_seo_meta_box'));
        add_action('save_post', array($this, 'save_seo_meta'), 10, 2);

        // Head injection
        add_action('wp_head', array($this, 'inject_seo_head'), 1);
        add_filter('pre_get_document_title', array($this, 'custom_document_title'), 999);
        remove_action('wp_head', 'rel_canonical');

        // Admin columns
        add_filter('manage_pages_columns', array($this, 'add_seo_column'));
        add_filter('manage_posts_columns', array($this, 'add_seo_column'));
        add_action('manage_pages_custom_column', array($this, 'render_seo_column'), 10, 2);
        add_action('manage_posts_custom_column', array($this, 'render_seo_column'), 10, 2);

        // Admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Add SEO meta box to pages and posts
     */
    public function add_seo_meta_box()
    {
        $post_types = array('page', 'post');

        foreach ($post_types as $post_type) {
            add_meta_box(
                'theme_seo_meta_box',
                '🔍 SEO & Rankings',
                array($this, 'render_seo_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Render the SEO meta box content
     */
    public function render_seo_meta_box($post)
    {
        wp_nonce_field('theme_seo_meta_nonce', 'theme_seo_nonce');

        $meta_title = get_post_meta($post->ID, '_seo_meta_title', true);
        $meta_desc = get_post_meta($post->ID, '_seo_meta_description', true);
        $focus_keyword = get_post_meta($post->ID, '_seo_focus_keyword', true);

        $site_name = get_bloginfo('name');
        $permalink = get_permalink($post->ID);
        ?>
        <style>
            .seo-manager-box {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .seo-field {
                margin-bottom: 1.5rem;
            }

            .seo-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 0.5rem;
                color: #1e293b;
            }

            .seo-field input,
            .seo-field textarea {
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                font-size: 14px;
            }

            .seo-field input:focus,
            .seo-field textarea:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .seo-field .char-count {
                text-align: right;
                font-size: 12px;
                color: #64748b;
                margin-top: 0.25rem;
            }

            .seo-field .char-count.warning {
                color: #f59e0b;
            }

            .seo-field .char-count.error {
                color: #ef4444;
            }

            .seo-preview {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                padding: 1.25rem;
                margin-top: 1.5rem;
            }

            .seo-preview-header {
                font-size: 12px;
                color: #64748b;
                margin-bottom: 1rem;
                font-weight: 600;
                text-transform: uppercase;
            }

            .google-preview {
                font-family: Arial, sans-serif;
            }

            .google-preview .title {
                color: #1a0dab;
                font-size: 20px;
                line-height: 1.3;
                margin-bottom: 0.25rem;
                cursor: pointer;
            }

            .google-preview .title:hover {
                text-decoration: underline;
            }

            .google-preview .url {
                color: #006621;
                font-size: 14px;
                margin-bottom: 0.25rem;
            }

            .google-preview .desc {
                color: #545454;
                font-size: 14px;
                line-height: 1.5;
            }

            .seo-tips {
                background: #f0f9ff;
                border: 1px solid #bae6fd;
                border-radius: 0.5rem;
                padding: 1rem;
                margin-top: 1rem;
            }

            .seo-tips h4 {
                margin: 0 0 0.5rem;
                color: #0369a1;
                font-size: 13px;
            }

            .seo-tips ul {
                margin: 0;
                padding-left: 1.25rem;
                font-size: 12px;
                color: #0c4a6e;
            }

            .seo-tips li {
                margin-bottom: 0.25rem;
            }
        </style>

        <div class="seo-manager-box">
            <div class="seo-field">
                <label for="seo_meta_title">Meta Title</label>
                <input type="text" id="seo_meta_title" name="seo_meta_title" value="<?php echo esc_attr($meta_title); ?>"
                    placeholder="<?php echo esc_attr($post->post_title); ?> | <?php echo esc_attr($site_name); ?>"
                    maxlength="70">
                <div class="char-count" id="title-count">0/60 characters</div>
            </div>

            <div class="seo-field">
                <label for="seo_meta_description">Meta Description</label>
                <textarea id="seo_meta_description" name="seo_meta_description" rows="3" maxlength="170"
                    placeholder="Write a compelling description that encourages clicks..."><?php echo esc_textarea($meta_desc); ?></textarea>
                <div class="char-count" id="desc-count">0/160 characters</div>
            </div>

            <div class="seo-field">
                <label for="seo_focus_keyword">Focus Keyword</label>
                <input type="text" id="seo_focus_keyword" name="seo_focus_keyword"
                    value="<?php echo esc_attr($focus_keyword); ?>" placeholder="e.g., IPTV streaming, Nordic IPTV">
            </div>

            <div class="seo-preview">
                <div class="seo-preview-header">📱 Google Search Preview</div>
                <div class="google-preview">
                    <div class="title" id="preview-title">
                        <?php echo esc_html($post->post_title); ?> |
                        <?php echo esc_html($site_name); ?>
                    </div>
                    <div class="url" id="preview-url">
                        <?php echo esc_url($permalink); ?>
                    </div>
                    <div class="desc" id="preview-desc">No description set. Add a meta description to control what appears here.
                    </div>
                </div>
            </div>

            <div class="seo-tips">
                <h4>💡 SEO Tips</h4>
                <ul>
                    <li>Keep titles between 50-60 characters</li>
                    <li>Descriptions should be 150-160 characters</li>
                    <li>Include your focus keyword naturally in both</li>
                    <li>Make it compelling - this is your ad copy!</li>
                </ul>
            </div>
        </div>

        <script>
            (function () {
                const titleInput = document.getElementById('seo_meta_title');
                const descInput = document.getElementById('seo_meta_description');
                const titleCount = document.getElementById('title-count');
                const descCount = document.getElementById('desc-count');
                const previewTitle = document.getElementById('preview-title');
                const previewDesc = document.getElementById('preview-desc');

                const siteName = '<?php echo esc_js($site_name); ?>';
                const postTitle = '<?php echo esc_js($post->post_title); ?>';

                function updatePreview() {
                    // Title
                    const title = titleInput.value || postTitle + ' | ' + siteName;
                    previewTitle.textContent = title.substring(0, 65) + (title.length > 65 ? '...' : '');

                    const titleLen = titleInput.value.length;
                    titleCount.textContent = titleLen + '/60 characters';
                    titleCount.className = 'char-count' + (titleLen > 60 ? ' error' : (titleLen > 50 ? ' warning' : ''));

                    // Description
                    const desc = descInput.value || 'No description set. Add a meta description to control what appears here.';
                    previewDesc.textContent = desc.substring(0, 160) + (desc.length > 160 ? '...' : '');

                    const descLen = descInput.value.length;
                    descCount.textContent = descLen + '/160 characters';
                    descCount.className = 'char-count' + (descLen > 160 ? ' error' : (descLen > 150 ? ' warning' : ''));
                }

                titleInput.addEventListener('input', updatePreview);
                descInput.addEventListener('input', updatePreview);
                updatePreview();
            })();
        </script>
        <?php
    }

    /**
     * Save SEO meta data
     */
    public function save_seo_meta($post_id, $post)
    {
        if (!isset($_POST['theme_seo_nonce']) || !wp_verify_nonce($_POST['theme_seo_nonce'], 'theme_seo_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array('seo_meta_title', 'seo_meta_description', 'seo_focus_keyword');

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    /**
     * Custom document title
     */
    public function custom_document_title($title)
    {
        if (is_singular()) {
            $custom_title = get_post_meta(get_the_ID(), '_seo_meta_title', true);
            if (!empty($custom_title)) {
                return $custom_title;
            }
        }
        return $title;
    }

    /**
     * Inject SEO tags into head
     */
    public function inject_seo_head()
    {
        if (!is_singular()) {
            return;
        }

        $post_id = get_the_ID();
        $meta_desc = get_post_meta($post_id, '_seo_meta_description', true);
        $focus_keyword = get_post_meta($post_id, '_seo_focus_keyword', true);

        // Meta description
        if (!empty($meta_desc)) {
            echo '<meta name="description" content="' . esc_attr($meta_desc) . '">' . "\n";
        }

        // Keywords (if set)
        if (!empty($focus_keyword)) {
            echo '<meta name="keywords" content="' . esc_attr($focus_keyword) . '">' . "\n";
        }

        // Canonical URL
        echo '<link rel="canonical" href="' . esc_url(get_permalink($post_id)) . '">' . "\n";

        // Hreflang tags for multisite
        $this->output_hreflang_tags($post_id);
    }

    /**
     * Output hreflang tags for multisite network
     */
    private function output_hreflang_tags($post_id)
    {
        if (!is_multisite()) {
            return;
        }

        $current_post = get_post($post_id);
        $current_slug = $current_post->post_name;
        $current_blog_id = get_current_blog_id();

        // Get all sites in network
        $sites = get_sites(array('number' => 100));

        $hreflang_links = array();

        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);

            // Try to find a page with the same slug
            $matching_post = get_page_by_path($current_slug);

            if ($matching_post || $site->blog_id == $current_blog_id) {
                $url = $matching_post ? get_permalink($matching_post->ID) : get_permalink($post_id);
                $path = trim(parse_url($site->siteurl, PHP_URL_PATH), '/');

                // Determine hreflang code
                if (empty($path)) {
                    // Main site = English
                    $hreflang = 'en';
                } elseif (isset($this->hreflang_map[$path])) {
                    $hreflang = $this->hreflang_map[$path];
                } else {
                    $hreflang = $path;
                }

                $hreflang_links[$hreflang] = $url;
            }

            restore_current_blog();
        }

        // Output hreflang tags
        foreach ($hreflang_links as $lang => $url) {
            echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url($url) . '">' . "\n";
        }

        // Add x-default (usually points to main English site)
        if (isset($hreflang_links['en'])) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($hreflang_links['en']) . '">' . "\n";
        }
    }

    /**
     * Add SEO status column to admin lists
     */
    public function add_seo_column($columns)
    {
        $columns['seo_status'] = 'SEO';
        return $columns;
    }

    /**
     * Render SEO status column
     */
    public function render_seo_column($column, $post_id)
    {
        if ($column !== 'seo_status') {
            return;
        }

        $meta_title = get_post_meta($post_id, '_seo_meta_title', true);
        $meta_desc = get_post_meta($post_id, '_seo_meta_description', true);

        $has_title = !empty($meta_title);
        $has_desc = !empty($meta_desc);

        if ($has_title && $has_desc) {
            echo '<span title="SEO Complete" style="color:#22c55e;font-size:16px;">🟢</span>';
        } elseif ($has_title || $has_desc) {
            echo '<span title="SEO Partial" style="color:#f59e0b;font-size:16px;">🟡</span>';
        } else {
            echo '<span title="SEO Missing" style="color:#ef4444;font-size:16px;">🔴</span>';
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook)
    {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        // Inline styles for the column
        wp_add_inline_style('wp-admin', '
            .column-seo_status { width: 50px; text-align: center; }
        ');
    }
}

// Initialize the SEO Manager
new Theme_SEO_Manager();
