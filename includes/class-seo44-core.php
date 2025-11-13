<?php
class SEO44_Core {

    protected static $_instance = null;
    
    // Make the class instances public properties
    public $settings;
    public $metabox;
    public $frontend;
    public $sitemaps;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->includes();
        $this->init_classes(); // Load classes before hooks that use them
        $this->init_hooks();
    }

    private function includes() {
        require_once plugin_dir_path( __FILE__ ) . 'class-seo44-settings.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-seo44-metabox.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-seo44-frontend.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-seo44-sitemaps.php';
    }
    
    private function init_hooks() {
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_assets']);
        add_action('wp_ajax_seo44_scan_meta_keys', [$this, 'ajax_scan_meta_keys']);
        add_action('wp_ajax_seo44_save_social_image', [$this, 'ajax_save_social_image']);
        add_action('wp_ajax_seo44_scan_site_for_schema', [$this, 'ajax_scan_site_for_schema']);
        add_action('wp_ajax_seo44_purge_sitemap_cache', [$this, 'ajax_purge_sitemap_cache']);
        
        // --- NEW GTM & INTEGRATIONS HOOKS ---
        // Inject Webmaster Tags (high priority)
        add_action('wp_head', [$this, 'inject_webmaster_verification_tags'], 2);
        // Push SEO dataLayer (run before GTM script)
        add_action('wp_head', [$this, 'push_seo_datalayer'], 5);
        // Inject GTM <head> script
        add_action('wp_head', [$this, 'inject_gtm_head'], 10);
        // Inject GTM <body> (noscript) script
        add_action('wp_body_open', [$this, 'inject_gtm_body']);
        // Enqueue the new global JS file for tracking events
        add_action('wp_enqueue_scripts', [$this, 'enqueue_global_tracking_script']);
    }

    private function init_classes() {
        // Assign instances to the public properties
        $this->settings = new SEO44_Settings();
        $this->metabox  = new SEO44_Metabox();
        $this->frontend = new SEO44_Frontend();
        $this->sitemaps = new SEO44_Sitemaps();
    }
    
    // --- AJAX Handlers & Asset Enqueueing ---

    public function admin_enqueue_assets($hook) {
        if ('post.php' == $hook || 'post-new.php' == $hook) {
            wp_enqueue_style('seo44-admin-styles', plugins_url('../css/admin-styles.css', __FILE__), [], SEO44_VERSION);
            wp_enqueue_script('seo44-admin-script', plugins_url('../js/admin-script.js', __FILE__), ['jquery'], SEO44_VERSION, true);
            wp_localize_script('seo44-admin-script', 'seo44_data', [ 
				'post_title' => get_the_title(get_the_ID()), 
				'site_name' => get_bloginfo('name'),
                'permalink' => get_permalink(get_the_ID()) 
			]);
        }
       if ('settings_page_search-appearance-toolkit-seo-44' == $hook) {
            wp_enqueue_media();
            wp_enqueue_style('seo44-admin-styles', plugins_url('../css/admin-styles.css', __FILE__), [], SEO44_VERSION);
            wp_enqueue_script('seo44-settings-script', plugins_url('../js/settings-script.js', __FILE__), ['jquery'], SEO44_VERSION, true);
            wp_localize_script('seo44-settings-script', 'seo44_ajax_object', [ 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('seo44_scan_nonce'), 'scanning_text' => __('Scanning...', 'search-appearance-toolkit-seo-44') ]);
        }
    }

// --- NEW: INTEGRATIONS FUNCTIONS ---

    /**
     * Injects the Google Tag Manager <head> script.
     */
	public function inject_gtm_head() {
        if (seo44_get_option('enable_gtm_integration')) {
            $gtm_id = seo44_get_option('gtm_id'); // Get the RAW ID
            if (empty($gtm_id) || strpos($gtm_id, 'GTM-') !== 0) return;
            
            // We use esc_js() here because the ID is being inserted into a JavaScript string
            echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . esc_js($gtm_id) . "');</script>
            \n";
        }
    }

    /**
     * Injects the Google Tag Manager <body> (noscript) snippet.
     */
	public function inject_gtm_body() {
        if (seo44_get_option('enable_gtm_integration')) {
            $gtm_id = seo44_get_option('gtm_id'); // Get the RAW ID
            if (empty($gtm_id) || strpos($gtm_id, 'GTM-') !== 0) return;

            // Build the full URL first
            $iframe_url = 'https://www.googletagmanager.com/ns.html?id=' . $gtm_id;

            // We use esc_url() here because we are outputting a URL into an 'src' attribute
            echo '<noscript><iframe src="' . esc_url($iframe_url) . '"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            \n';
        }
    }

    /**
     * Pushes SEO-rich data to the dataLayer.
     */
    public function push_seo_datalayer() {
        if (seo44_get_option('enable_gtm_integration') && seo44_get_option('enable_seo_datalayer')) {
            $data = ['event' => 'seoDataPushed']; // Always lead with an event

            if (is_front_page()) {
                $data['page_type'] = 'front_page';
            } elseif (is_singular()) {
				
                // Get the post object directly from the main query
                $post_object = get_queried_object(); 
                
                $data['page_type'] = get_post_type();

                if (is_a($post_object, 'WP_Post')) {
                    // Use the post object's author ID to get the display name
                    $data['post_author'] = get_the_author_meta('display_name', $post_object->post_author);
                } else {
                    $data['post_author'] = ''; // Fallback in case something is wrong
                }
					
                $categories = get_the_category();
                if (!empty($categories)) {
                    $data['post_category'] = $categories[0]->name;
                }
                $tags = get_the_tags();
                if ($tags) {
                    $data['post_tags'] = wp_list_pluck($tags, 'name');
                }
            } elseif (is_category()) {
                $data['page_type'] = 'category';
                $data['category_name'] = single_cat_title('', false);
            }
            // (You can add more conditions for is_tag(), is_author(), etc.)
            
            echo "<script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(" . wp_json_encode($data) . ");
            </script>\n";
        }
    }

    /**
     * Injects Webmaster Verification meta tags into the <head>.
     */
    public function inject_webmaster_verification_tags() {
        $google_code = seo44_get_option('google_site_verification');
        $bing_code = seo44_get_option('bing_site_verification');

        if (!empty($google_code)) {
            printf(
                '<meta name="google-site-verification" content="%s" />' . "\n",
                esc_attr($google_code)
            );
        }
        if (!empty($bing_code)) {
            printf(
                '<meta name="msvalidate.01" content="%s" />' . "\n",
                esc_attr($bing_code)
            );
        }
    }

    /**
     * Enqueues the new global tracking script and passes settings to it.
     */
    public function enqueue_global_tracking_script() {
        // Only enqueue if at least one tracking feature is enabled
        if (
            !seo44_get_option('enable_jump_link_tracking') && 
            !seo44_get_option('enable_external_link_tracking') && 
            !seo44_get_option('enable_scroll_depth_tracking')
        ) {
            return;
        }

        wp_enqueue_script(
            'seo44-global-tracker', 
            plugins_url('../js/global-tracker.js', __FILE__), 
            [], 
            SEO44_VERSION, 
            true
        );

        // Localize it with ALL our GTM toggle settings
        wp_localize_script('seo44-global-tracker', 'seo44_tracking_settings', [
            'trackJumpLinks' => (bool) seo44_get_option('enable_jump_link_tracking'),
            'trackOutboundLinks' => (bool) seo44_get_option('enable_external_link_tracking'),
            'trackScrollDepth' => (bool) seo44_get_option('enable_scroll_depth_tracking')
        ]);
    }
    // End Integrations Functions

    /**
     * This function has been updated to use a fully prepared SQL query
     * and now caches the result for performance.
     * Fixed SQL injection vulnerability by properly using placeholders.
     */
    public function ajax_scan_meta_keys() {
		check_ajax_referer('seo44_scan_nonce');
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Permission denied.']);
		}

        // Add caching to address the performance warning
        $cache_key = 'seo44_meta_key_scan_results';
        $cached_results = get_transient($cache_key);
        if (false !== $cached_results) {
            wp_send_json_success($cached_results);
            return;
        }

		global $wpdb;
		$search_terms = ['seo', 'title', 'description', 'keywords', 'yoast', 'rank_math', 'schema'];
		
        // Build and execute the query with explicit placeholders to satisfy WordPress coding standards
        $results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} 
                 WHERE meta_key LIKE %s 
                    OR meta_key LIKE %s 
                    OR meta_key LIKE %s 
                    OR meta_key LIKE %s 
                    OR meta_key LIKE %s 
                    OR meta_key LIKE %s 
                    OR meta_key LIKE %s 
                 ORDER BY meta_key ASC LIMIT 100",
                '%' . $wpdb->esc_like($search_terms[0]) . '%',
                '%' . $wpdb->esc_like($search_terms[1]) . '%',
                '%' . $wpdb->esc_like($search_terms[2]) . '%',
                '%' . $wpdb->esc_like($search_terms[3]) . '%',
                '%' . $wpdb->esc_like($search_terms[4]) . '%',
                '%' . $wpdb->esc_like($search_terms[5]) . '%',
                '%' . $wpdb->esc_like($search_terms[6]) . '%'
            )
        );
	
	// For greater user clarity, exclude the Meta Keys for the Jump Links Block 
    $excluded_keys = ['_seo44_jump_link_headings'];
    // Filter the results array to remove the excluded keys.
    $filtered_results = array_diff($results, $excluded_keys);

    set_transient($cache_key, $filtered_results, HOUR_IN_SECONDS);
    
    wp_send_json_success($filtered_results);
}
    
	public function ajax_save_social_image() {
		check_ajax_referer('seo44_scan_nonce', 'nonce');
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Permission denied.']);
		}
		if (isset($_POST['image_id'])) {
			$image_id = absint($_POST['image_id']);
			$options = get_option('seo44_settings');
			if (!is_array($options)) {
				$options = [];
			}
			$options['default_social_image_id'] = $image_id;
			update_option('seo44_settings', $options);
			wp_send_json_success(['message' => 'Image saved.']);
		} else {
			wp_send_json_error(['message' => 'No image ID provided.']);
		}
	}
	public function ajax_scan_site_for_schema() {
		check_ajax_referer('seo44_scan_nonce');
		if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Permission denied.']);
		$urls_to_check = [get_home_url()];
		$latest_post_args = ['numberposts' => 1, 'post_status' => 'publish', 'post_type' => 'post'];
		$latest_post = get_posts($latest_post_args);
		if ($latest_post) $urls_to_check[] = get_permalink($latest_post[0]);
		$latest_page_args = ['number' => 1, 'sort_column' => 'post_date', 'sort_order' => 'DESC', 'post_type' => 'page'];
		$latest_page = get_pages($latest_page_args);
		if ($latest_page) $urls_to_check[] = get_permalink($latest_page[0]);
		$found_jsonld = false;
		$found_microdata = false;
		$jsonld_example = '';
		foreach(array_unique($urls_to_check) as $url) {
			$scan_url = add_query_arg('seo44_scan', 'true', $url);
			$response = wp_remote_get($scan_url, ['sslverify' => false]);
			if (is_wp_error($response)) continue;
			$html = wp_remote_retrieve_body($response);
			if (strpos($html, 'itemscope') !== false || strpos($html, 'itemprop') !== false) {
				$found_microdata = true;
			}
			if (strpos($html, '<script type="application/ld+json">') !== false) {
				$found_jsonld = true;
				preg_match('/<script type="application\/ld\+json"[^>]*>(.*?)<\/script>/is', $html, $matches);
				if (isset($matches[1])) {
					$decoded = json_decode(trim($matches[1]));
					if (json_last_error() === JSON_ERROR_NONE) {
						$jsonld_example = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
					}
				}
			}
		}
		if ($found_microdata) {
			wp_send_json_success(['status' => 'microdata_found']);
		}
		elseif($found_jsonld) {
			wp_send_json_success(['status' => 'jsonld_found', 'example' => $jsonld_example]);
		} else {
			$post_preview = '';
			if ($latest_post) {
				$post_preview = json_encode(SEO44_Frontend::get_schema_for_post($latest_post[0]->ID), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			}
			wp_send_json_success(['status' => 'clean', 'post_preview' => $post_preview]);
		}
	}
	
    // AJAX handler for purging the sitemap cache
    public function ajax_purge_sitemap_cache() {
        check_ajax_referer('seo44_scan_nonce'); // Reusing nonce for security
        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }
        
        // Call the public clear cache method from our sitemaps class
        if (isset($this->sitemaps) && method_exists($this->sitemaps, 'clear_sitemap_cache')) {
            $this->sitemaps->clear_sitemap_cache();
            wp_send_json_success(['message' => 'Sitemap cache purged!']);
        } else {
            wp_send_json_error(['message' => 'Sitemap class not available.']);
        }
    }
}
