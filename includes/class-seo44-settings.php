<?php
class SEO44_Settings {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function add_admin_menu() {
        add_options_page(__('SEO 44 Settings', 'search-appearance-toolkit-seo-44'), __('SEO 44', 'search-appearance-toolkit-seo-44'), 'manage_options', 'search-appearance-toolkit-seo-44', [$this, 'settings_page_html']);
    }
	
	public function settings_init() {
        register_setting('seo44_settings_group', 'seo44_settings', [$this, 'sanitize_settings']);

        // Main Settings
        add_settings_section('seo44_main_settings_section', __('Main Settings', 'search-appearance-toolkit-seo-44'), [$this, 'main_section_callback'], 'seo-44_main');
        add_settings_field('enable_tags', __('Enable Meta Tags', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_main', 'seo44_main_settings_section', ['id' => 'enable_tags', 'label' => __('Add Meta tags (and Social Media tags if selected) to the site\'s head section.', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('include_keywords', __('Include Meta Keywords', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_main', 'seo44_main_settings_section', ['id' => 'include_keywords', 'label' => __('Enable the meta keywords field in the editor.', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('include_author', __('Include Author Tag', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_main', 'seo44_main_settings_section', ['id' => 'include_author', 'label' => __('Add the author meta tag to single posts.', 'search-appearance-toolkit-seo-44')]);
		add_settings_field('author_format', __('Author Name Format', 'search-appearance-toolkit-seo-44'), [$this, 'render_author_format_field'], 'seo-44_main', 'seo44_main_settings_section', ['tooltip' => 'The "Public Display Name" can be set in the post author\'s WordPress user profile.']);
        add_settings_section('seo44_homepage_settings_section', __('Homepage SEO', 'search-appearance-toolkit-seo-44'), [$this, 'homepage_section_callback'], 'seo-44_main');
	// Updated 'homepage_title' field with dedicated render function
	add_settings_field(
		'homepage_title', 
		__('Homepage SEO Title', 'search-appearance-toolkit-seo-44'), 
		[$this, 'render_homepage_title_field'], 
		'seo-44_main', 
		'seo44_homepage_settings_section'
	);
	// Updated 'homepage_description' field with dedicated render function
	add_settings_field(
		'homepage_description', 
		__('Homepage Meta Description', 'search-appearance-toolkit-seo-44'), 
		[$this, 'render_homepage_description_field'], 
		'seo-44_main', 
		'seo44_homepage_settings_section'
	);
			
        // Social Media Settings
        add_settings_section('seo44_social_settings_section', __('Social Media Settings', 'search-appearance-toolkit-seo-44'), [$this, 'social_section_callback'], 'seo-44_social');
        add_settings_field('enable_og_tags', __('Enable Facebook Open Graph', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'enable_og_tags', 'label' => __('Add Open Graph (og:) tags for Facebook, LinkedIn, etc.', 'search-appearance-toolkit-seo-44')]);
    	add_settings_field('fb_app_id', __('Facebook App ID', 'search-appearance-toolkit-seo-44'),  [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'fb_app_id', 'desc' => __('Optional. Enter your Facebook App ID.', 'search-appearance-toolkit-seo-44'), 'tooltip' => 'A Facebook App ID is required for features like domain insights. Go to the Facebook Developers website to create or lookup your Facebook App ID.']);
        add_settings_field('enable_twitter_tags', __('Enable Twitter Cards', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'enable_twitter_tags', 'label' => __('Add Twitter Card meta tags.', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('twitter_handle', __('Twitter Username', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'twitter_handle', 'desc' => __('Enter your @username for the twitter:site and twitter:creator tags.', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('default_social_image_id', __('Default Social Image', 'search-appearance-toolkit-seo-44'), [$this, 'render_image_upload_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'default_social_image_id', 'desc' => __('Upload a default image for social sharing. This image will be used when a post does not have a set featured image.', 'search-appearance-toolkit-seo-44'), 'tooltip' => 'A high-quality image of at least 1200x630 pixels is recommended.']);
		// Social Media Links used for Organization Schema - Google Knowledge Graph
		add_settings_field('social_facebook', __('Facebook Page URL', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_facebook', 'desc' => __('Enter your full Facebook Page URL (required for Schema).', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('social_instagram', __('Instagram URL', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_instagram', 'desc' => __('Enter the URL of your official Instagram page.', 'search-appearance-toolkit-seo-44')]);
		add_settings_field('social_linkedin', __('LinkedIn URL', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_linkedin', 'desc' => __('Enter your full LinkedIn Company or Profile URL.', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('social_tiktok', __('TikTok URL', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_tiktok', 'desc' => __('Enter the URL of your official TikTok page.', 'search-appearance-toolkit-seo-44')]);
		add_settings_field('social_youtube', __('YouTube URL', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_youtube', 'desc' => __('Enter your full YouTube Channel URL.', 'search-appearance-toolkit-seo-44')]);
		add_settings_field('social_additional', __('Additional Social URLs', 'search-appearance-toolkit-seo-44'), [$this, 'render_textarea_field'], 'seo-44_social', 'seo44_social_settings_section', ['id' => 'social_additional','desc' => __('Enter any additional profile URLs (one per line) to include in the schema SameAs property.<br>Examples: Wikipedia, BlueSky, Mastodon, Pinterest, etc.', 'search-appearance-toolkit-seo-44')]);
        // Schema Settings
        add_settings_section('seo44_schema_settings_section', __('Schema Structured Data Settings', 'search-appearance-toolkit-seo-44'), [$this, 'schema_section_callback'], 'seo-44_schema');
        add_settings_field('seo44_schema_tools', __('Schema Scanner', 'search-appearance-toolkit-seo-44'), [$this, 'render_schema_tools'], 'seo-44_schema', 'seo44_schema_settings_section');
        add_settings_field('seo44_enable_schema', __('Enable Schema', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_schema', 'seo44_schema_settings_section', ['id' => 'enable_schema', 'label' => __('Output JSON-LD to your webpages.', 'search-appearance-toolkit-seo-44')]);
		// NEW: Add checkbox for scanning content
        add_settings_field('seo44_scan_content_for_schema', __('Scan Content for Media', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_schema', 'seo44_schema_settings_section', [
            'id' => 'scan_content_for_schema', 
            'label' => __('Scan post and page content to add all images and videos to the schema.', 'search-appearance-toolkit-seo-44'),
            'tooltip' => 'This provides more detail to search engines but can be slightly more resource-intensive.'
        ]);
		// NEW: Add checkbox for advanced schema detection
        add_settings_field('seo44_enable_advanced_schema', __('Enable Advanced Schema Detection', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_schema', 'seo44_schema_settings_section', [
            'id' => 'enable_advanced_schema', 
            'label' => __('Scan content for patterns to generate FAQ and How-To schema.', 'search-appearance-toolkit-seo-44'),
            'tooltip' => 'If special formats are detected, they will be added to your page\'s schema structured data.'
        ]);
		
        add_settings_field('seo44_enable_schema_on_taxonomies', __('Enable Schema on Taxonomies', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_schema', 'seo44_schema_settings_section', [
            'id' => 'enable_schema_on_taxonomies', 
            'label' => __('Output BreadcrumbList schema on categories, tags, and other archives.', 'search-appearance-toolkit-seo-44'),
            'tooltip' => 'This helps Google understand your site structure and can create breadcrumb links in search results.'
        ]);
		add_settings_field('seo44_enable_schema_on_cpts', __('Enable Schema on Custom Post Types', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_schema', 'seo44_schema_settings_section', ['id' => 'enable_schema_on_cpts', 'label' => __('Also output WebPage schema on public Custom Post Types (e.g., Products, Events).', 'search-appearance-toolkit-seo-44')]);
        add_settings_field('seo44_schema_disclaimer', '', [$this, 'render_schema_disclaimer'], 'seo-44_schema', 'seo44_schema_settings_section');
		// --- NEW: Organization Schema Section ---
        add_settings_section('seo44_organization_schema_section', __('Organization & Knowledge Graph', 'search-appearance-toolkit-seo-44'), [$this, 'organization_section_callback'], 'seo-44_schema');
        add_settings_field(
            'enable_organization_schema', 
            __('Enable Organization Schema', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_schema', 
            'seo44_organization_schema_section', 
            ['id' => 'enable_organization_schema', 'label' => __('Output rich Organization schema for the homepage (recommended).', 'search-appearance-toolkit-seo-44')]
        );
        add_settings_field(
            'org_name', 
            __('Organization Name', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_text_field'], 
            'seo-44_schema', 
            'seo44_organization_schema_section', 
            ['id' => 'org_name', 'desc' => __('Leave empty to use the Site Title: <strong>' . esc_html(get_bloginfo('name')) . '</strong>', 'search-appearance-toolkit-seo-44')]
        );
		add_settings_field(
		    'org_alternate_name', 
		    __('Alternate Name / Acronym', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_alternate_name', 'desc' => __('Optional. An acronym or shorter name your organization is known by.', 'search-appearance-toolkit-seo-44')]
		);
		// 3. Add Address Fields (Crucial for Local SEO & Disambiguation)
		add_settings_field(
		    'org_address_street', 
		    __('Street Address', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_address_street']
		);
		add_settings_field(
		    'org_address_city', 
		    __('City', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_address_city']
		);
		add_settings_field(
		    'org_address_state', 
		    __('State / Province', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_address_state']
		);
		add_settings_field(
		    'org_address_zip', 
		    __('Zip / Postal Code', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_address_zip']
		);
		add_settings_field(
		    'org_address_country', 
		    __('Country', 'search-appearance-toolkit-seo-44'), 
		    [$this, 'render_text_field'], 
		    'seo-44_schema', 
		    'seo44_organization_schema_section', 
		    ['id' => 'org_address_country']
		);
		
        // REUSING THE IMAGE UPLOADER!
        add_settings_field(
            'org_logo', 
            __('Organization Logo', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_image_upload_field'], 
            'seo-44_schema', 
            'seo44_organization_schema_section', 
            [
                'id' => 'org_logo', 
                'desc' => __('Upload a specific logo for schema. If empty, we will try to use the Site Logo from your Customizer settings.', 'search-appearance-toolkit-seo-44'),
                'tooltip' => 'Google prefers images that are 112x112px or larger, in JPG, PNG, or WebP format.'
            ]
        );
        add_settings_field(
            'org_phone', 
            __('Contact Phone Number', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_text_field'], 
            'seo-44_schema', 
            'seo44_organization_schema_section', 
            ['id' => 'org_phone', 'desc' => __('Optional. Include the country code (e.g., +1-555-0199).', 'search-appearance-toolkit-seo-44')]
        );

        // Migration Settings
        add_settings_section('seo44_migration_settings_section', __('Custom Meta Key Configuration', 'search-appearance-toolkit-seo-44'), [$this, 'migration_section_callback'], 'seo-44_migration');
        add_settings_field('seo44_title_key', __('Title Meta Key', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_migration', 'seo44_migration_settings_section', ['id' => 'title_key', 'tooltip' => 'The database key used by your previous plugin to store the SEO title.']);
        add_settings_field('seo44_description_key', __('Description Meta Key', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_migration', 'seo44_migration_settings_section', ['id' => 'description_key', 'tooltip' => 'The database key used by your previous plugin to store the meta description.']);
        add_settings_field('seo44_keywords_key', __('Keywords Meta Key', 'search-appearance-toolkit-seo-44'), [$this, 'render_text_field'], 'seo-44_migration', 'seo44_migration_settings_section', ['id' => 'keywords_key', 'tooltip' => 'The database key used by your previous plugin to store meta keywords.']);
        add_settings_field('seo44_migration_tools', __('Migration Tools', 'search-appearance-toolkit-seo-44'), [$this, 'render_migration_tools'], 'seo-44_migration', 'seo44_migration_settings_section');
        add_settings_field('seo44_migration_disclaimer', '', [$this, 'render_migration_disclaimer'], 'seo-44_migration', 'seo44_migration_settings_section');
		
        // NEW: Sitemaps Settings
        add_settings_section('seo44_sitemaps_section', __('XML Sitemap Settings', 'search-appearance-toolkit-seo-44'), [$this, 'sitemaps_section_callback'], 'seo-44_sitemaps');
        
         add_settings_field('enable_sitemaps', __('Enable XML Sitemaps', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section', [
            'id' => 'enable_sitemaps', 
            'label' => 'Generate XML sitemaps for your site.',
            'tooltip' => 'Turn this setting off if you are creating sitemaps with another plugin.'
        ]);
        add_settings_field('sitemap_content_types', __('Include in Sitemaps', 'search-appearance-toolkit-seo-44'), [$this, 'render_sitemap_content_types_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section');
        add_settings_field('sitemap_include_images', __('Include Images', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section', [
            'id' => 'sitemap_include_images', 
            'label' => 'Include Featured Images in sitemaps.'
        ]);
        add_settings_field('sitemap_include_content_images', '', [$this, 'render_checkbox_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section', [
            'id' => 'sitemap_include_content_images', 
            'label' => 'Scan post content for additional images to include in sitemaps (can be slow to generate).',
        ]);
        add_settings_field('sitemap_image_explainer', '', [$this, 'render_sitemap_image_explainer'], 'seo-44_sitemaps', 'seo44_sitemaps_section');
		
		add_settings_field(
			'sitemap_exclude_posts',
			__('Exclude Posts/Pages by ID', 'search-appearance-toolkit-seo-44'),
			[$this, 'render_text_field'],
			'seo-44_sitemaps',
			'seo44_sitemaps_section',
			[
				'id' => 'sitemap_exclude_posts',
				'desc' => __('Not Required: Enter a list of Post IDs or Page IDs to exclude from sitemaps (eg: 44,100,123).', 'search-appearance-toolkit-seo-44')
			]
		);

        add_settings_field('enable_sitemap_ping', __('Ping Search Engines', 'search-appearance-toolkit-seo-44'), [$this, 'render_checkbox_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section', [
            'id' => 'enable_sitemap_ping', 
            'label' => 'Automatically ping Google and Bing when you publish new content.'
        ]);
        add_settings_field('sitemap_instructions', __('Submission Instructions', 'search-appearance-toolkit-seo-44'), [$this, 'render_sitemap_instructions'], 'seo-44_sitemaps', 'seo44_sitemaps_section');
        add_settings_field('sitemap_purge_cache', __('Sitemap Cache', 'search-appearance-toolkit-seo-44'), [$this, 'render_purge_cache_field'], 'seo-44_sitemaps', 'seo44_sitemaps_section');
    
        // --- NEW: INTEGRATIONS SETTINGS ---
        add_settings_section(
            'seo44_integrations_section', 
            __('Analytics Integrations', 'search-appearance-toolkit-seo-44'), 
            [$this, 'integrations_section_callback'], 
            'seo-44_integrations'
        );
        add_settings_field(
            'seo44_gtm_header',
            '', // An empty string for the left-column label
            [$this, 'render_gtm_header_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section'
        );
        add_settings_field(
            'enable_gtm_integration', 
            __('Enable Google Tag Manager', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'enable_gtm_integration', 
                'label' => 'Inject the GTM container script into your site.',
                'tooltip' => 'This will add the GTM scripts to your site\'s <head> and <body>.'
            ]
        );
        add_settings_field(
            'gtm_id', 
            __('Google Tag Manager ID', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_text_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'gtm_id', 
                'desc' => __('Enter your GTM Container ID (e.g., GTM-XXXXXXX).', 'search-appearance-toolkit-seo-44')
            ]
        );
        // --- GTM Event Tracking Header ---
        add_settings_field(
            'seo44_gtm_tracking_header', 
            '', // Empty left-column label
            [$this, 'render_gtm_tracking_header_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section'
        );
        add_settings_field(
            'enable_seo_datalayer', 
            __('Enable Rich SEO dataLayer', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'enable_seo_datalayer', 
                'label' => 'Push page data (category, author, etc.) to the dataLayer.',
                'tooltip' => 'This is required for advanced users who want to create granular analytics triggers in GTM.'
            ]
        );
        add_settings_field(
            'enable_jump_link_tracking', 
            __('Enable Jump Link Click Tracking', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'enable_jump_link_tracking', 
                'label' => 'Push a "jump_link_click" event when a Jump Links Block link is clicked.',
                'tooltip' => 'Adds a "jump_link_click" event. Use this in GTM to see which jump links your visitors are using the most.'
            ]
        );
        add_settings_field(
            'enable_external_link_tracking', 
            __('Enable External Link Tracking', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'enable_external_link_tracking', 
                'label' => 'Push an "external_link_click" or "affiliate_link_click" event when a user clicks a link to another website.',
                'tooltip' => 'Adds an "external_link_click" event. Use this to track when users click links to leave your site.'
            ]
        );
        add_settings_field(
            'enable_scroll_depth_tracking', 
            __('Enable Scroll Depth Tracking', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_checkbox_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'enable_scroll_depth_tracking', 
                'label' => 'Push "scroll_depth" events as users scroll 25%, 50%, 75%, and 100% down a page.',
                'tooltip' => 'Pushes "scroll_depth" events (e.g., 25%, 50%). Use this to see how far users read your content and judge engagement.'
            ]
        );
        // --- Webmaster Verification Header ---
        add_settings_field(
            'seo44_webmaster_header', 
            '', // Empty left-column label
            [$this, 'render_webmaster_header_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section'
        );
        add_settings_field(
            'google_site_verification', 
            __('Google Search Console', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_text_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'google_site_verification', 
                'desc' => __('Enter your Google site verification code.', 'search-appearance-toolkit-seo-44'),
                'tooltip' => 'This will add a <meta> tag to your site\'s head for permanent verification.'
            ]
        );
        add_settings_field(
            'bing_site_verification', 
            __('Bing Webmaster Tools', 'search-appearance-toolkit-seo-44'), 
            [$this, 'render_text_field'], 
            'seo-44_integrations', 
            'seo44_integrations_section', 
            [
                'id' => 'bing_site_verification', 
                'desc' => __('Enter your Bing site verification code.', 'search-appearance-toolkit-seo-44'),
                'tooltip' => 'This will add a <meta> tag to your site\'s head for permanent verification.'
            ]
        );
        // --- end of settings_init ---
    }
	
    public function sanitize_settings($input) {
		$new_input = [];
        $old_sitemap_enabled = seo44_get_option('enable_sitemaps');
		$checkboxes = [
            'enable_tags', 'include_keywords', 'include_author', 
            'enable_og_tags', 'enable_twitter_tags', 
            'enable_schema', 'scan_content_for_schema', 'enable_advanced_schema', 'enable_schema_on_cpts', 'enable_schema_on_taxonomies','enable_organization_schema', 
            'enable_sitemaps', 'enable_sitemap_ping', 
            'sitemap_include_images', 'sitemap_include_content_images',
            'enable_gtm_integration', 'enable_seo_datalayer', 'enable_jump_link_tracking', 
            'enable_external_link_tracking', 'enable_scroll_depth_tracking'
        ];
		foreach ($checkboxes as $cb) { 
            $new_input[$cb] = isset($input[$cb]) ? 1 : 0; 
        }
		
        $text_fields = [
            'fb_app_id',
            'twitter_handle',
            'title_key',
            'description_key',
            'keywords_key',
            'homepage_title',
            'sitemap_exclude_posts',
            'gtm_id',
            'google_site_verification',
            'bing_site_verification'
			'social_facebook', 'social_instagram', 'social_linkedin', 'social_tiktok', 'social_youtube', 
			'org_name', 'org_phone','org_alternate_name', 
			'org_address_street', 'org_address_city', 'org_address_state', 'org_address_zip', 'org_address_country'
        ];
		foreach ($text_fields as $tf) { 
			if (isset($input[$tf])) { 
				// A SPECIAL CHECK FOR THE SITEMAP EXCLUSION FIELD
				if ($tf === 'sitemap_exclude_posts') {
					// Replace spaces with commas
					$value = str_replace(' ', ',', $input[$tf]);
					// Remove any characters that are not numbers or commas
					$value = preg_replace('/[^0-9,]/', '', $value);
					// Remove any duplicate commas
					$new_input[$tf] = preg_replace('/,{2,}/', ',', $value);
				} else if ($tf === 'gtm_id') {
                    // Sanitize GTM ID: remove whitespace, force uppercase
                    $value = strtoupper(trim(sanitize_text_field($input[$tf])));
                    // Prepend GTM- if it's missing but looks like a GTM ID
                    if (strpos($value, 'GTM-') !== 0) {
                        $value = 'GTM-' . $value;
                    }
                    // If it's just "GTM-", clear it.
                    $new_input[$tf] = ($value === 'GTM-') ? '' : $value;
                } else {
                    $new_input[$tf] = sanitize_text_field($input[$tf]);
                }
			} 
		}
        
        $textarea_fields = ['homepage_description', 'social_additional'];
        foreach ($textarea_fields as $ta) { 
            if (isset($input[$ta])) { 
                $new_input[$ta] = sanitize_textarea_field($input[$ta]); 
            } 
        }
		
		if (isset($input['author_format']) && in_array($input['author_format'], ['display_name', 'first_last', 'last_first'])) { $new_input['author_format'] = $input['author_format']; }
		if (isset($input['default_social_image_id'])) { $new_input['default_social_image_id'] = absint($input['default_social_image_id']); }
		if (isset($input['org_logo'])) { $new_input['org_logo'] = absint($input['org_logo']); }

        $new_input['sitemap_post_types'] = isset($input['sitemap_post_types']) && is_array($input['sitemap_post_types']) ? array_map('sanitize_key', $input['sitemap_post_types']) : [];
        $new_input['sitemap_taxonomies'] = isset($input['sitemap_taxonomies']) && is_array($input['sitemap_taxonomies']) ? array_map('sanitize_key', $input['sitemap_taxonomies']) : [];
		
		// Flush when sitemaps Eanbled
		$new_sitemap_enabled = isset($new_input['enable_sitemaps']) ? $new_input['enable_sitemaps'] : 0;
		if ($old_sitemap_enabled != $new_sitemap_enabled) {
			SEO44_Sitemaps::schedule_rewrite_flush();
		}

		return $new_input;
    }	
	

    // --- All settings page section and field rendering functions go here ---
	public function main_section_callback() { 
		echo '<p>' . esc_html__('With these settings, you can enable the plugin\'s primary function and customize how SEO tags appear on your website.', 'search-appearance-toolkit-seo-44') . '</p>'; 
	}
    public function social_section_callback() { 
		echo '<p>' . esc_html__('With these settings, you can activate and customize the meta tags that impact the way your links appear on social media.', 'search-appearance-toolkit-seo-44') . '</p>';
	}
    public function schema_section_callback() { 
        echo '<p>' . esc_html__('Schema.org markup helps search engines understand your website\'s content, potentially leading to better search ranking and more informative search results. SEO 44 can help you by adding structured data to your webpages in a modern JSON-LD format.', 'search-appearance-toolkit-seo-44') . '</p>';
    }
	public function organization_section_callback() {
        echo '<p>' . esc_html__('Provide details about your organization to help Google populate its Knowledge Graph.', 'search-appearance-toolkit-seo-44') . '</p>';
    }
    public function migration_section_callback() {  echo '<p><strong>' . esc_html__('Migration made seamless:', 'search-appearance-toolkit-seo-44') . '</strong> ' . esc_html__('With these settings, you can adjust the SEO 44 plugin to fit the way your website already manages SEO information. Locate where your SEO data is stored so that you can continue using the same meta keys and pick up right where you left off with your previous SEO plugin.', 'search-appearance-toolkit-seo-44') . '</p>';  }
    
    // FIXED: Rewrote all render functions to use printf for proper escaping.
    public function render_checkbox_field($args) {
        $id = $args['id'];
        $label = $args['label'];
        $tooltip = isset($args['tooltip']) ? $args['tooltip'] : '';
        $checked = seo44_get_option($id);
        printf(
            '<label for="%s"><input type="checkbox" id="%s" name="seo44_settings[%s]" value="1" %s /> %s</label>',
            esc_attr($id),
            esc_attr($id),
            esc_attr($id),
            checked($checked, 1, false),
            esc_html($label)
        );

        if ($tooltip) {
            seo44_render_tooltip($tooltip);
        }
    }
	
    public function render_text_field($args) { 
		$id = $args['id'];
		$value = seo44_get_option($id);
		$tooltip = isset($args['tooltip']) ? $args['tooltip'] : '';
		printf(
            '<input type="text" id="%s" name="seo44_settings[%s]" value="%s" class="regular-text" />',
            esc_attr($id), esc_attr($id), esc_attr($value)
        );
		if ($tooltip) { seo44_render_tooltip($tooltip); }
		if (isset($args['desc'])) { echo '<p class="description">' . esc_html($args['desc']) . '</p>'; }
	}
    
    public function render_author_format_field($args) { 
		$value = seo44_get_option('author_format', 'display_name');
		$formats = ['display_name' => __('Public Display Name (eg: admin / John Doe)', 'search-appearance-toolkit-seo-44'), 'first_last' => __('FirstName LastName (eg: Jane Doe)', 'search-appearance-toolkit-seo-44'), 'last_first' => __('LastName, FirstNamee (eg: Doe, John)', 'search-appearance-toolkit-seo-44')];
		$tooltip = isset($args['tooltip']) ? $args['tooltip'] : '';
		
		echo '<select id="author_format" name="seo44_settings[author_format]">';
		foreach ($formats as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key), selected($value, $key, false), esc_html($label)
            );
		}
		echo "</select>";
		if ($tooltip) { seo44_render_tooltip($tooltip); }
	}
	
	// --- NEW and UPDATED Render Functions ---
public function homepage_section_callback() { 
    echo '<p>' . esc_html__('Set the title and meta description for your site\'s front page.', 'search-appearance-toolkit-seo-44') . '</p>'; 
}

// NEW: Dedicated render function for the Homepage Title
public function render_homepage_title_field() {
    $value = seo44_get_option('homepage_title');
    // Fallback to Site Title if empty
    if ( empty($value) ) {
        // Decode the value first in case it contains HTML entities
        // Added escaping
		$value = esc_attr(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
    }
    ?>
    <input type="text" id="homepage_title" name="seo44_settings[homepage_title]" value="<?php echo esc_attr($value); ?>" class="large-text" />
    <div id="homepage_title_char_count" class="char-count"></div>
    <?php
}

// NEW: Dedicated render function for the Homepage Description
public function render_homepage_description_field() {
    $value = seo44_get_option('homepage_description');
    // Fallback to Tagline if empty
    if ( empty($value) ) {
        // Decode the value first in case it contains HTML entities
        $value = wp_specialchars_decode(get_bloginfo('description'), ENT_QUOTES);
    }
    ?>
    <textarea id="homepage_description" name="seo44_settings[homepage_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <div id="homepage_description_char_count" class="char-count"></div>
    
    <?php // ADDED: Snippet Preview HTML right after the description field ?>
    <div id="seo44-homepage-snippet-preview" 
     data-sitename="<?php echo esc_attr(get_bloginfo('name')); ?>"
     data-siteurl="<?php echo esc_attr(home_url('/')); ?>"
     data-tagline="<?php echo esc_attr(get_bloginfo('description')); ?>">
        <hr>
        <h3><?php esc_html_e('Search Results Snippet Preview', 'search-appearance-toolkit-seo-44'); ?></h3>
        
        <div class="seo44-preview-header">
            <div class="seo44-preview-favicon">
            	<img src="<?php echo esc_url(get_site_icon_url(32, plugins_url('../images/default-wordpress-favicon.ico', __FILE__))); ?>" width="18" height="18" alt="Site Favicon"/>
            </div>
            <div class="seo44-preview-site-info">
                <div class="seo44-preview-site-name"><?php echo esc_html(get_bloginfo('name')); ?></div>
                <div class="seo44-preview-breadcrumb-url"></div>
            </div>
        </div>

        <div class="seo44-preview-title"></div>
        <div class="seo44-preview-description"></div>
    </div>
    <?php
}

    public function render_textarea_field($args) {
        $id = $args['id'];
        $value = seo44_get_option($id);
        printf(
            '<textarea id="%s" name="seo44_settings[%s]" rows="3" class="large-text">%s</textarea>',
            esc_attr($id), esc_attr($id), esc_textarea($value)
        );
        if (isset($args['desc'])) { echo '<p class="description">' . esc_html($args['desc']) . '</p>'; }
    }
	
    public function render_image_upload_field($args) { 
		$id = $args['id'];
		$image_id = seo44_get_option($id, 0);
		$tooltip = isset($args['tooltip']) ? $args['tooltip'] : '';
		?>
		<div>
			<button type="button" class="button button-primary" id="<?php echo esc_attr($id); ?>_upload_btn"><?php esc_html_e('Upload Image', 'search-appearance-toolkit-seo-44'); ?></button>
			<button type="button" class="button" id="<?php echo esc_attr($id); ?>_remove_btn" style="<?php echo ($image_id ? '' : 'display:none;'); ?>"><?php esc_html_e('Remove Image', 'search-appearance-toolkit-seo-44'); ?></button>
			<?php if ($tooltip) seo44_render_tooltip($tooltip); ?>
			<input type="hidden" id="<?php echo esc_attr($id); ?>" name="seo44_settings[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($image_id); ?>" />
		</div>
		<div id="<?php echo esc_attr($id); ?>_preview" class="image-preview-wrapper">
			<?php if ($image_id) echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
		</div>
		<?php if (isset($args['desc'])) {
			echo "<p class='description'>" . esc_html($args['desc']) . "</p>";
		}
	}
    public function render_schema_tools() {
		?>
            <div id="seo44-schema-scanner-ui">
                <p><?php esc_html_e('This tool will scan your site to see if your theme or another plugin is already creating structured data. This helps prevent conflicts.', 'search-appearance-toolkit-seo-44'); ?></p>
                <button type="button" class="button button-primary" id="seo44_scan_schema_btn"><?php esc_html_e('Scan Site for Schema', 'search-appearance-toolkit-seo-44'); ?></button>
                <div id="seo44_schema_scan_results" class="scan-results-container" style="display:none;"></div>
            </div>
    	<?php
    }
    public function render_schema_disclaimer() { 
		/* translators: %1$s: URL to Article schema, %2$s: URL to WebPage schema, %3$s: URL to WebSite schema. */
		$text = sprintf(
			wp_kses(
				__(' Schema.org structured data will be applied using the <a href="%1$s" target="_blank">Article type</a> for posts, the <a href="%2$s" target="_blank">WebPage type</a> for pages, and the <a href="%3$s" target="_blank">WebSite type</a> for your index homepage.', 'search-appearance-toolkit-seo-44'),
				[ 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'https://schema.org/Article',
			'https://schema.org/WebPage',
			'https://schema.org/WebSite'
		);
		printf(
			'<hr><p><strong>%s</strong> %s</p>',
			esc_html__('Formatting Note:', 'search-appearance-toolkit-seo-44'),
			wp_kses_post($text)
		);
	}
    public function render_migration_tools() {
		?> <p class="description"><?php esc_html_e('Click a button below to automatically fill in the standard meta keys for that plugin.', 'search-appearance-toolkit-seo-44'); ?></p> <div class="migration-presets"> <button type="button" class="button" data-presets='{"title":"seo44_title", "desc":"seo44_description", "keys":"seo44_keywords"}'>SEO 44 (Default)</button> <button type="button" class="button" data-presets='{"title":"_aioseo_title", "desc":"_aioseo_description", "keys":"_aioseo_keywords"}'>All in One SEO</button> <button type="button" class="button" data-presets='{"title":"_yoast_wpseo_title", "desc":"_yoast_wpseo_metadesc", "keys":""}'>Yoast SEO</button> <button type="button" class="button" data-presets='{"title":"rank_math_title", "desc":"rank_math_description", "keys":"rank_math_focus_keyword"}'>Rank Math</button> <button type="button" class="button" data-presets='{"title":"_genesis_title", "desc":"_genesis_description", "keys":"_genesis_keywords"}'>The SEO Framework</button> <button type="button" class="button" data-presets='{"title":"slim_seo_title", "desc":"slim_seo_description", "keys":""}'>Slim SEO</button> </div> <hr> <h4><?php esc_html_e('Scan Database for Meta Keys', 'search-appearance-toolkit-seo-44'); ?></h4> <p class="description"><?php esc_html_e("Not sure which meta keys your site uses? Scan the database to find potential candidates.", 'search-appearance-toolkit-seo-44'); ?></p> <button type="button" class="button button-primary" id="seo44_scan_meta_keys_btn"><?php esc_html_e('Scan for SEO Meta Keys', 'search-appearance-toolkit-seo-44'); ?></button> <div id="seo44_scan_results_container" style="display:none; margin-top: 10px;"> <p><strong><?php esc_html_e('Found potential meta keys:', 'search-appearance-toolkit-seo-44'); ?></strong></p> <ul id="seo44_scan_results_list" style="border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow-y: auto;"></ul> <div id="seo44_suggested_keys_container" style="display:none; margin-top: 10px;"> <h4><?php esc_html_e('Suggested Keys', 'search-appearance-toolkit-seo-44'); ?></h4> <p><?php esc_html_e('Based on the scan, we suggest the following keys. Click the button to apply and save.', 'search-appearance-toolkit-seo-44'); ?></p> <ul id="seo44_suggestions_list"></ul> <button type="button" class="button button-primary" id="seo44_use_suggested_keys_btn"><?php esc_html_e('Use Suggested Keys & Save', 'search-appearance-toolkit-seo-44'); ?></button> </div> </div> 
		<?php 
	}
    public function render_migration_disclaimer() {
		echo '<hr><p><strong>' . esc_html__('To be safe:', 'search-appearance-toolkit-seo-44') . '</strong> ' . esc_html__('Before migrating, please back up your WordPress database, including the postmeta table. Deactivate your previous SEO plugin while using SEO 44.', 'search-appearance-toolkit-seo-44') . '</p>'; 
	}
	
	    // --- NEW SITEMAP RENDER FUNCTIONS ---
	public function render_sitemap_image_explainer() {
        /* translators: %s: An example URL showing how to view the source of a sitemap. */
        $text = sprintf(
            __('To see the &lt;image:image&gt; elements in the sitemap, you will need to open a sitemap and then view its source, for example: %s', 'search-appearance-toolkit-seo-44'),
            '<code>' . esc_html('view-source:' . home_url('/page-sitemap1.xml/')) . '</code>'
        );
        echo "<p class='description' style='margin-top:-1em; margin-bottom:1em;'>" . wp_kses($text, ['code' => []]) . "</p>";
    }
		
    public function render_purge_cache_field() {
        echo '<button type="button" class="button" id="seo44_purge_sitemap_cache_btn">' . esc_html__('Purge Sitemap Cache', 'search-appearance-toolkit-seo-44') . '</button>';
        echo '<p class="description">' . esc_html__('Click this button to immediately clear the sitemap cache and force regeneration on the next visit.', 'search-appearance-toolkit-seo-44') . '</p>';
    }	
	
    public function sitemaps_section_callback() {
        $sitemap_url = home_url('/sitemap.xml');
        /* translators: %s: The URL of the sitemap index. */
        $text = sprintf(
            wp_kses(
                __('Configure the XML sitemaps generated for your website. Your sitemap index can be found at: %s', 'search-appearance-toolkit-seo-44'),
                [
                    'a'    => ['href' => [], 'target' => []],
                    'code' => [],
                ]
            ),
            '<code><a href="' . esc_url($sitemap_url) . '" target="_blank">' . esc_html($sitemap_url) . '</a></code>'
        );
        printf('<p>%s</p>', wp_kses_post($text));
    }

    public function render_sitemap_content_types_field() {
        $saved_post_types = seo44_get_option('sitemap_post_types', ['post', 'page']);
        $saved_taxonomies = seo44_get_option('sitemap_taxonomies', ['category', 'post_tag']);

        echo '<h4>' . esc_html__('Post Types', 'search-appearance-toolkit-seo-44') . '</h4>';
        $post_types = get_post_types(['public' => true], 'objects');
        unset($post_types['attachment']);
        foreach ($post_types as $pt) {
            printf(
                '<label class="sitemap-label"><input type="checkbox" name="seo44_settings[sitemap_post_types][]" value="%s" %s /> %s</label>',
                esc_attr($pt->name),
                checked(in_array($pt->name, $saved_post_types), true, false),
                esc_html($pt->label)
            );
        }

        echo '<h4 style="margin-top: 1.5em;">' . esc_html__('Taxonomies', 'search-appearance-toolkit-seo-44') . '</h4>';
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        unset($taxonomies['post_format']);
        foreach ($taxonomies as $tax) {
            printf(
                '<label class="sitemap-label"><input type="checkbox" name="seo44_settings[sitemap_taxonomies][]" value="%s" %s /> %s</label>',
                esc_attr($tax->name),
                checked(in_array($tax->name, $saved_taxonomies), true, false),
                esc_html($tax->label)
            );
        }
    }

    public function render_sitemap_instructions() {
        echo '<hr><p><strong>' . esc_html__('Next Steps:', 'search-appearance-toolkit-seo-44') . '</strong> ' . esc_html__('After saving your settings, you should submit your sitemap URL to search engines to ensure they can find and crawl all of your content.', 'search-appearance-toolkit-seo-44') . '</p>';
        echo '<ul>';
        echo '<li><a href="https://search.google.com/search-console" target="_blank" rel="noopener">Submit your sitemap to Google Search Console</a></li>';
        echo '<li><a href="https://www.bing.com/webmasters/" target="_blank" rel="noopener">Submit your sitemap to Bing Webmaster Tools</a></li>';
        echo '</ul>';
    }

    // --- NEW: INTEGRATIONS CALLBACKS ---
    public function integrations_section_callback() {
        echo '<p>' . esc_html__('Configure integrations with third-party services like Google Tag Manager and Webmaster Tools.', 'search-appearance-toolkit-seo-44') . '</p>';
    }
    public function render_gtm_header_field() {
    echo '<h3>' . esc_html__('Google Tag Manager (GTM)', 'search-appearance-toolkit-seo-44') . '</h3>';
	}
    public function render_gtm_tracking_header_field() {
        echo '<hr><h4>' . esc_html__('Automatic GTM Event Tracking for Google Analytics', 'search-appearance-toolkit-seo-44') . '</h4>';
        echo '<p class="description">' . esc_html__('When checked, these options will automatically push events to the dataLayer if GTM is enabled.', 'search-appearance-toolkit-seo-44') . '</p>';
    
        // --- Download Button & Instructions ---
        // 1. Get the URL to your new JSON file
        $recipe_url = plugins_url('json/seo44-gtm-recipe-importer.json', dirname(__FILE__));
        // 2. Create the download button and instruction link
        $download_text = sprintf(
            wp_kses(
                /* translators: %1$s: Download button, %2$s: Link to FAQ. */
                __('%1$s to streamline the setup process, following these %2$s.', 'search-appearance-toolkit-seo-44'),
                [
                    'a' => ['href' => [], 'class' => [], 'download' => []],
                    'strong' => [],
                ]
            ),
            // The Download Button
            sprintf(
                '<a href="%s" class="button button-secondary" download="seo44-gtm-recipe-importer.json">%s</a>',
                esc_url($recipe_url),
                esc_html__('Download the GTM Import File', 'search-appearance-toolkit-seo-44')
            ),
            // The Instructions Link
            sprintf(
                '<a href="%s" target="_blank">%s</a>',
                // This creates a link to YOUR plugin's FAQ tab on wordpress.org
                esc_url('https://seo44plugin.com/search-appearance-toolkit-seo-44/integrations-setup-guide/'),
                esc_html__('setup instructions', 'search-appearance-toolkit-seo-44')
            )
        );
        // 3. Print the Download Button & Instructions section
        echo '<div class="gtm-recipe-helper">';
		echo '<p class="description">';
        echo '<strong>' . esc_html__('Google Tag Manager Configure Required:', 'search-appearance-toolkit-seo-44') . '</strong> ';
        echo '<span class="description-body">' . wp_kses_post( $download_text ) . '</span>';
        echo '</p>';
		echo '</div>';
    }
    public function render_webmaster_header_field() {
        echo '<h3>' . esc_html__('Site Verification Tags', 'search-appearance-toolkit-seo-44') . '</h3>';
        echo '<p class="description">' . esc_html__('These tags are used to prove you own your site to search engines. Paste in your verification codes here and they will be added to your site\'s <head>.', 'search-appearance-toolkit-seo-44') . '</p>';
    }

	public function settings_page_html() {
		if (!current_user_can('manage_options')) {
			return;
		}
	
		// --- START: SECURE TAB SWITCHING LOGIC ---
	
		// 1. Define the nonce action and name for clarity
		$nonce_action = 'seo44_switch_tab';
		$nonce_name   = 'seo44_tab_nonce';
		
		// 2. Verify the nonce if a tab is being set. Default to 'main_settings' if invalid.
		if (isset($_GET['tab']) && isset($_GET[$nonce_name])) {
			// 'check_admin_referer' handles the nonce verification and dies on failure.
			check_admin_referer($nonce_action, $nonce_name);
			$active_tab = sanitize_key($_GET['tab']);
		} else {
			$active_tab = 'main_settings';
		}
	
		$allowed_tabs = ['main_settings', 'social_settings', 'schema_settings', 'sitemaps_settings', 'integrations_settings', 'migration_settings'];
		if (!in_array($active_tab, $allowed_tabs)) {
			$active_tab = 'main_settings';
		}
		?>
		<div class="wrap seo44-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2 class="nav-tab-wrapper">
                <?php
                // 3. Create the tab links WITH the nonce included using wp_nonce_url()
                $main_url     = wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=main_settings'), $nonce_action, $nonce_name);
                $social_url   = wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=social_settings'), $nonce_action, $nonce_name);
                $schema_url   = wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=schema_settings'), $nonce_action, $nonce_name);
                $sitemaps_url = wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=sitemaps_settings'), $nonce_action, $nonce_name);
                $integrations_url = wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=integrations_settings'), $nonce_action, $nonce_name);
                $migration_url= wp_nonce_url(admin_url('options-general.php?page=search-appearance-toolkit-seo-44&tab=migration_settings'), $nonce_action, $nonce_name);
                ?>
                <a href="<?php echo esc_url($main_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'main_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('Main Settings', 'search-appearance-toolkit-seo-44'); ?></a>
                <a href="<?php echo esc_url($social_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'social_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('Social Media', 'search-appearance-toolkit-seo-44'); ?></a>
                <a href="<?php echo esc_url($schema_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'schema_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('Schema Structured Data', 'search-appearance-toolkit-seo-44'); ?></a>
                <a href="<?php echo esc_url($sitemaps_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'sitemaps_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('XML Sitemaps', 'search-appearance-toolkit-seo-44'); ?></a>
                <a href="<?php echo esc_url($integrations_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'integrations_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('Integrations', 'search-appearance-toolkit-seo-44'); ?></a>
                <a href="<?php echo esc_url($migration_url); ?>" class="nav-tab <?php echo esc_attr($active_tab === 'migration_settings' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e('Migration Settings', 'search-appearance-toolkit-seo-44'); ?></a>
            </h2>
            <form action="options.php" method="post" class="seo44-form">
                <?php settings_fields('seo44_settings_group'); ?>
                <div id="main_settings" class="tab-content" style="display: <?php echo $active_tab === 'main_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_main'); ?></table></div>
                <div id="social_settings" class="tab-content" style="display: <?php echo $active_tab === 'social_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_social'); ?></table></div>
                <div id="schema_settings" class="tab-content" style="display: <?php echo $active_tab === 'schema_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_schema'); ?></table></div>
                <div id="sitemaps_settings" class="tab-content" style="display: <?php echo $active_tab === 'sitemaps_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_sitemaps'); ?></table></div>
                <div id="integrations_settings" class="tab-content" style="display: <?php echo $active_tab === 'integrations_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_integrations'); ?></table></div>
                <div id="migration_settings" class="tab-content" style="display: <?php echo $active_tab === 'migration_settings' ? 'block' : 'none'; ?>;"><table class="form-table"><?php do_settings_sections('seo-44_migration'); ?></table></div>
                <?php submit_button(__('Save Settings', 'search-appearance-toolkit-seo-44')); ?>
            </form>
        </div>
        <?php
    }
}
