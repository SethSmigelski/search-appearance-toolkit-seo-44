<?php
class SEO44_Sitemaps {

    /**
     * The number of URLs to include in a single sitemap file before paginating.
     * @var int
     */
    private $urls_per_sitemap = 1000;

    /**
     * Constructor. Hooks into WordPress.
     */
    public function __construct() {
        if (seo44_get_option('enable_sitemaps')) {
            add_filter('wp_sitemaps_enabled', '__return_false');
            add_filter('wp_sitemaps_redirect_sitemap_xml', '__return_false');
            add_action('init', [$this, 'init_hooks']);
            add_action('save_post', [$this, 'clear_sitemap_cache']);
            add_action('edited_term', [$this, 'clear_sitemap_cache']);
            add_action('transition_post_status', [$this, 'ping_on_publish'], 10, 3);
        }
    }

    /**
     * Adds the necessary hooks for rewrite rules and query variables.
     */
    public function init_hooks() {
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('template_redirect', [$this, 'parse_sitemap_request']);
        add_rewrite_rule('^sitemap\.xml$', 'index.php?seo44_sitemap=index', 'top');
        add_rewrite_rule('^([a-z0-9_-]+?)-sitemap([0-9]+)?\.xml$', 'index.php?seo44_sitemap_type=$matches[1]&seo44_sitemap_page=$matches[2]', 'top');
    
		// Check if we need to flush rewrite rules
		$this->maybe_flush_rewrite_rules();
	}
	
	/**
    * Flushes rewrite rules if the sitemap feature was just enabled.
    * Uses a transient flag to avoid flushing on every page load.
    */
    private function maybe_flush_rewrite_rules() {
        // Check if we have a flag indicating rules need flushing
        if (get_transient('seo44_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_transient('seo44_flush_rewrite_rules');
        }
    }

    /**
     * Adds our custom query variables so WordPress recognizes them.
     */
    public function add_query_vars($vars) {
        $vars[] = 'seo44_sitemap';
        $vars[] = 'seo44_sitemap_type';
        $vars[] = 'seo44_sitemap_page';
        return $vars;
    }

    /**
     * Checks if the current request is for a sitemap and triggers the output.
     */
    public function parse_sitemap_request() {
        global $wp_query;
        if (isset($wp_query->query_vars['seo44_sitemap']) || isset($wp_query->query_vars['seo44_sitemap_type'])) {
            $this->output_sitemap();
            exit();
        }
    }

    public function ping_on_publish($new_status, $old_status, $post) {
        if (seo44_get_option('enable_sitemap_ping') && $new_status === 'publish' && $old_status !== 'publish') {
            $sitemap_url = urlencode(home_url('/sitemap.xml'));
            wp_remote_get("https://www.google.com/ping?sitemap=" . $sitemap_url);
            wp_remote_get("https://www.bing.com/ping?sitemap=" . $sitemap_url);
        }
    }

    /**
     * Main router for sitemap output. Sets headers and calls the correct generator.
     */
    private function output_sitemap() {
        global $wp_query;
        header('Content-Type: text/xml; charset=utf-8');

        $stylesheet_url = plugins_url('../xsl/sitemap.xsl', __FILE__);
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?xml-stylesheet type="text/xsl" href="' . esc_url($stylesheet_url) . '"?>';

        if (isset($wp_query->query_vars['seo44_sitemap'])) {
            $this->generate_sitemap_index();
        } else {
            $type = sanitize_key($wp_query->query_vars['seo44_sitemap_type']);
            $page = isset($wp_query->query_vars['seo44_sitemap_page']) ? intval($wp_query->query_vars['seo44_sitemap_page']) : 1;
            $this->generate_single_sitemap($type, $page);
        }
    }

    /**
     * Gathers data for the sitemap index and then echoes the XML.
     */
    private function generate_sitemap_index() {
        $cache_key = 'seo44_sitemap_index_data';
        $sitemap_entries = get_transient($cache_key);

        if (false === $sitemap_entries) {
            $sitemap_entries = [];

            // Post Types
            $post_types = seo44_get_option('sitemap_post_types', []);
            foreach ($post_types as $post_type) {
                $count = wp_count_posts($post_type)->publish;
                if ($count > 0) {
                    $lastmod_query = new WP_Query(['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => 1, 'orderby' => 'modified', 'order' => 'DESC']);
                    $lastmod = $lastmod_query->have_posts() ? get_the_modified_time('c', $lastmod_query->posts[0]->ID) : '';
                    wp_reset_postdata();

                    $pages = ceil($count / $this->urls_per_sitemap);
                    for ($i = 1; $i <= $pages; $i++) {
                        $sitemap_entries[] = [
                            'loc' => home_url("/{$post_type}-sitemap{$i}.xml"),
                            'lastmod' => $lastmod,
                        ];
                    }
                }
            }

            // Taxonomies
            $taxonomies = seo44_get_option('sitemap_taxonomies', []);
            foreach ($taxonomies as $taxonomy) {
                if (wp_count_terms($taxonomy) > 0) {
                    $sitemap_entries[] = [
                        'loc' => home_url("/{$taxonomy}-sitemap1.xml"),
                        'lastmod' => '',
                    ];
                }
            }
            set_transient($cache_key, $sitemap_entries, DAY_IN_SECONDS);
        }

        echo "\n" . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        if (!empty($sitemap_entries)) {
            foreach ($sitemap_entries as $sitemap) {
                echo "\n\t<sitemap>";
                echo "\n\t\t<loc>" . esc_url($sitemap['loc']) . "</loc>";
                if (!empty($sitemap['lastmod'])) {
                    echo "\n\t\t<lastmod>" . esc_xml($sitemap['lastmod']) . "</lastmod>";
                }
                echo "\n\t</sitemap>";
            }
        }
        echo "\n</sitemapindex>";
    }

    /**
     * Gathers data for a single sitemap and then echoes the XML.
     */
    private function generate_single_sitemap($type, $page) {
        $cache_key = "seo44_sitemap_{$type}_{$page}_data";
        $url_entries = get_transient($cache_key);

        if (false === $url_entries) {
            $url_entries = [];

            if (post_type_exists($type)) {
                $url_entries = $this->get_post_type_sitemap_data($type, $page);
            } elseif (taxonomy_exists($type)) {
                $url_entries = $this->get_taxonomy_sitemap_data($type);
            }
            set_transient($cache_key, $url_entries, DAY_IN_SECONDS);
        }

        echo "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
        if (!empty($url_entries)) {
            foreach ($url_entries as $url) {
                echo "\n\t<url>";
                echo "\n\t\t<loc>" . esc_url($url['loc']) . "</loc>";
                if ($url['lastmod']) {
                    echo "\n\t\t<lastmod>" . esc_xml($url['lastmod']) . "</lastmod>";
                }
                if (!empty($url['images'])) {
                    foreach ($url['images'] as $image_url) {
                        echo "\n\t\t<image:image>";
                        echo "\n\t\t\t<image:loc>" . esc_url($image_url) . "</image:loc>";
                        echo "\n\t\t</image:image>";
                    }
                }
                echo "\n\t</url>";
            }
        }
        echo "\n</urlset>";
    }

    /**
     * Gets the data array for a specific post type sitemap.
     */
    private function get_post_type_sitemap_data($post_type, $page) {
        $url_entries = [];
        $query_args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $this->urls_per_sitemap,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        ];

        $exclude_ids_string = seo44_get_option('sitemap_exclude_posts', '');
        if (!empty($exclude_ids_string)) {
            $excluded_ids = array_map('intval', explode(',', $exclude_ids_string));
            $query_args['post__not_in'] = $excluded_ids;
        }

        if ($post_type === 'page' && $page == 1) {
            $front_page_id = get_option('page_on_front');
            if ($front_page_id) {
                $query_args['post__not_in'] = isset($query_args['post__not_in']) ? array_merge($query_args['post__not_in'], [$front_page_id]) : [$front_page_id];
                $latest_post_query = new WP_Query(['posts_per_page' => 1, 'orderby' => 'modified', 'order' => 'DESC']);
                $lastmod = $latest_post_query->have_posts() ? get_the_modified_time('c', $latest_post_query->posts[0]->ID) : current_time('c');
                wp_reset_postdata();
                $url_entries[] = [
                    'loc' => home_url('/'),
                    'lastmod' => $lastmod,
                    'images' => [], // Homepage images can be complex; skipping for simplicity or add logic here.
                ];
            }
        }

        $query = new WP_Query($query_args);
        $include_featured_images = seo44_get_option('sitemap_include_images');
        $include_content_images = seo44_get_option('sitemap_include_content_images');

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $image_urls = [];

                if ($include_featured_images && has_post_thumbnail($post_id)) {
                    $image_url = get_the_post_thumbnail_url($post_id, 'full');
                    if ($image_url) $image_urls[] = $image_url;
                }

                if ($include_content_images) {
                    $content = get_the_content(null, false, $post_id);
                    if (preg_match_all('/<img[^>]+>/i', $content, $matches)) {
                        foreach ($matches[0] as $img_tag) {
                            if (strpos($img_tag, 'seo44-ignore') !== false || (strpos($img_tag, 'width="1"') !== false && strpos($img_tag, 'height="1"') !== false)) {
                                continue;
                            }
                            if (preg_match('/src\s*=\s*[\'"]([^\'"]+)[\'"]/i', $img_tag, $src_matches)) {
                                $url = $src_matches[1];
                                if (!in_array($url, $image_urls)) $image_urls[] = $url;
                            }
                        }
                    }
                }
                
                $url_entries[] = [
                    'loc' => get_permalink($post_id),
                    'lastmod' => get_the_modified_time('c', $post_id),
                    'images' => array_unique($image_urls),
                ];
            }
        }
        wp_reset_postdata();
        return $url_entries;
    }

    /**
     * Gets the data array for a specific taxonomy sitemap.
     */
    private function get_taxonomy_sitemap_data($taxonomy) {
        $url_entries = [];
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $url_entries[] = [
                    'loc' => get_term_link($term),
                    'lastmod' => false,
                    'images' => [],
                ];
            }
        }
        return $url_entries;
    }

    /**
     * Clears all sitemap transients when content is updated.
     */
    public function clear_sitemap_cache() {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                $wpdb->esc_like('_transient_seo44_sitemap_') . '%',
                $wpdb->esc_like('_transient_timeout_seo44_sitemap_') . '%'
            )
        );
    }
/**
     * Static method to flush rewrite rules when sitemaps are enabled/disabled.
     * Call this from your settings save function.
     */
    public static function schedule_rewrite_flush() {
        set_transient('seo44_flush_rewrite_rules', 1, 60);
    }
    
    /**
     * Adds rewrite rules and flushes on activation.
     * Called from the plugin's activation hook.
     */
    public static function on_activation() {
        
        // Add the rewrite rules
        add_rewrite_rule('^sitemap\.xml$', 'index.php?seo44_sitemap=index', 'top');
        add_rewrite_rule('^([a-z0-9_-]+?)-sitemap([0-9]+)?\.xml$', 'index.php?seo44_sitemap_type=$matches[1]&seo44_sitemap_page=$matches[2]', 'top');
        
        // Now flush them
        flush_rewrite_rules();
    }
}