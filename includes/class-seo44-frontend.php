<?php
class SEO44_Frontend {
    public function __construct() {
        add_filter('document_title_parts', [$this, 'filter_document_title'], 20);
        add_action('wp_head', [$this, 'output_header_tags']);
        add_action('wp_head', [$this, 'output_schema_json_ld'], 99);

        $taxonomies = get_taxonomies(['public' => true]);
        foreach ($taxonomies as $taxonomy) {
            add_action("{$taxonomy}_edit_form_fields", [$this, 'add_term_meta_fields'], 10, 2);
            add_action("edited_{$taxonomy}", [$this, 'save_term_meta']);
        }
    }

    public function filter_document_title($title_parts) {
    	if (!seo44_get_option('enable_tags')) return $title_parts;
		
        $custom_title = '';
        $fallback_title = '';
        $final_title = '';

        if (is_front_page()) {
            $custom_title = seo44_get_option('homepage_title');
        } elseif (is_singular()) {
			$custom_title = get_post_meta(get_the_ID(), seo44_get_option('title_key'), true);
            $fallback_title = get_the_title(get_the_ID());
    	} elseif (is_category() || is_tag() || is_tax()) {
            $term_id = get_queried_object_id();
            $custom_title = get_term_meta($term_id, 'seo44_title', true);
            $fallback_title = single_term_title('', false);
        }

        if (!empty($custom_title)) {
            $final_title = $custom_title;
        } elseif (!empty($fallback_title)) {
            $separator = ' - ';
            $site_name = get_bloginfo('name');
            $maxLength = 60 - (strlen($separator) + strlen($site_name));
            
            if (strlen($fallback_title) > $maxLength) {
                $truncated = substr($fallback_title, 0, $maxLength);
                $last_space = strrpos($truncated, ' ');
                $final_title = substr($truncated, 0, $last_space) . '...';
            } else {
                $final_title = $fallback_title;
            }
            $final_title .= $separator . $site_name;
        }

        if (!empty($final_title)) {
            $title_parts['title'] = html_entity_decode($final_title, ENT_QUOTES, 'UTF-8');
            unset($title_parts['site'], $title_parts['tagline']);
        }
    	return $title_parts;
    }
	
	public function add_term_meta_fields($term, $taxonomy) {
        $title = get_term_meta($term->term_id, 'seo44_title', true);
        $description = get_term_meta($term->term_id, 'seo44_description', true);
        wp_nonce_field('seo44_save_term_meta', 'seo44_term_meta_nonce');
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="seo44_title"><?php esc_html_e('SEO Title', 'search-appearance-toolkit-seo-44'); ?></label></th>
            <td>
                <input type="text" name="seo44_title" id="seo44_title" value="<?php echo esc_attr($title); ?>" class="large-text"/>
                <p class="description"><?php esc_html_e('The title displayed in search engine results.', 'search-appearance-toolkit-seo-44'); ?></p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="seo44_description"><?php esc_html_e('Meta Description', 'search-appearance-toolkit-seo-44'); ?></label></th>
            <td>
                <textarea name="seo44_description" id="seo44_description" rows="5" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                <p class="description"><?php esc_html_e('The description for search engines.', 'search-appearance-toolkit-seo-44'); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_term_meta($term_id) {
        if (!isset($_POST['seo44_term_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seo44_term_meta_nonce'])), 'seo44_save_term_meta')) {
            return;
        }
        
        // Add capability check for better security
        if (!current_user_can('manage_categories')) {
            return;
        }
        
        if (isset($_POST['seo44_title'])) {
            update_term_meta($term_id, 'seo44_title', sanitize_text_field(wp_unslash($_POST['seo44_title'])));
        }
        if (isset($_POST['seo44_description'])) {
            update_term_meta($term_id, 'seo44_description', sanitize_textarea_field(wp_unslash($_POST['seo44_description'])));
        }
    }
    
	public function output_header_tags() {
		if (!seo44_get_option('enable_tags')) { return; }
		$description = '';
		$social_title = '';
		$current_url = '';
	
		if (is_front_page()) {
			$description = seo44_get_option('homepage_description');
			$social_title = seo44_get_option('homepage_title') ?: get_bloginfo('name');
			$current_url = home_url('/');
		} elseif (is_singular()) {
			$post_id = get_the_ID();
			$description = get_post_meta($post_id, seo44_get_option('description_key'), true);
			$custom_title = get_post_meta($post_id, seo44_get_option('title_key'), true);
			$social_title = $custom_title ?: get_the_title($post_id);
			$current_url = get_permalink($post_id);
            if (empty($description)) {
				$post_content = get_the_content(null, false, get_the_ID());
                $description = wp_trim_words(strip_shortcodes(wp_strip_all_tags($post_content)), 25, '...');
			}
			
		} elseif (is_category() || is_tag() || is_tax()) {
			$term = get_queried_object();
			$description = get_term_meta($term->term_id, 'seo44_description', true);
			$custom_title = get_term_meta($term->term_id, 'seo44_title', true);
			$social_title = $custom_title ?: single_term_title('', false) . ' - ' . get_bloginfo('name');
			$current_url = get_term_link($term);
            if (empty($description)) {
                $term_description = trim(wp_strip_all_tags($term->description));
                if (!empty($term_description)) {
                    $description = $term_description;
                } else {
                    $description = "This is a taxonomy page for the " . esc_html($term->taxonomy) . " " . esc_html($term->name) . " on " . esc_html(get_bloginfo('name')) . ".";
                }
            }
		}
	
		if (!empty($description)) {
			printf('<meta name="description" content="%s">' . "\n", esc_attr(wp_strip_all_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'))));
		}
	
		if (is_singular()) {
			$post_id = get_the_ID();
			$keywords = get_post_meta($post_id, seo44_get_option('keywords_key'), true);
			if (seo44_get_option('include_keywords') && !empty($keywords)) {
				printf('<meta name="keywords" content="%s">' . "\n", esc_attr(wp_strip_all_tags(html_entity_decode($keywords, ENT_QUOTES, 'UTF-8'))));
			}
			if (is_singular() && seo44_get_option('include_author')) {
				$author_id = get_post_field('post_author', $post_id);
				if ($author_id) {
					printf('<meta name="author" content="%s">' . "\n", esc_attr($this->get_author_name($author_id)));
				}
			}
		}
	
		if (seo44_get_option('enable_og_tags') || seo44_get_option('enable_twitter_tags')) {
			$clean_social_title = esc_attr(wp_strip_all_tags(html_entity_decode($social_title, ENT_QUOTES, 'UTF-8')));
			$clean_description = !empty($description) ? esc_attr(wp_strip_all_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'))) : '';
			
			if (empty($clean_description) && is_singular()) {
				 $clean_description = esc_attr(wp_strip_all_tags(get_the_excerpt(get_the_ID())));
			}
	
			$image_url = ''; $image_width = ''; $image_height = '';
			$image_id = 0;
			if (is_singular() && has_post_thumbnail()) {
				$image_id = get_post_thumbnail_id();
			}
			if (!$image_id) {
				$image_id = seo44_get_option('default_social_image_id', 0);
			}
			if ($image_id) {
				$image_data = wp_get_attachment_image_src($image_id, 'full');
				if ($image_data) {
					list($image_url, $image_width, $image_height) = $image_data;
				}
			}
	
			if (seo44_get_option('enable_og_tags')) {
				printf('<meta property="og:title" content="%s">' . "\n", esc_attr($clean_social_title));
				printf('<meta property="og:url" content="%s">' . "\n", esc_url($current_url));
				printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(get_bloginfo('name')));
				if (!empty($clean_description)) { printf('<meta property="og:description" content="%s">' . "\n", esc_attr($clean_description)); }
				if (is_singular('post')) {
					 echo '<meta property="og:type" content="article">' . "\n";
				} else {
					 echo '<meta property="og:type" content="website">' . "\n";
				}
				if (!empty($image_url)) {
					printf('<meta property="og:image" content="%s">' . "\n", esc_url($image_url));
					if (!empty($image_width)) { printf('<meta property="og:image:width" content="%s">' . "\n", esc_attr($image_width)); }
					if (!empty($image_height)) { printf('<meta property="og:image:height" content="%s">' . "\n", esc_attr($image_height)); }
				}
				$fb_app_id = seo44_get_option('fb_app_id');
				if (!empty($fb_app_id)) { printf('<meta property="fb:app_id" content="%s">' . "\n", esc_attr($fb_app_id)); }
			}
	
			if (seo44_get_option('enable_twitter_tags')) {
				echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
				$twitter_handle = seo44_get_option('twitter_handle');
				if (!empty($twitter_handle)) {
					$handle = esc_attr(str_replace('@', '', $twitter_handle));
					printf('<meta name="twitter:site" content="@%s">' . "\n", esc_attr($handle));
					printf('<meta name="twitter:creator" content="@%s">' . "\n", esc_attr($handle));
				}
				printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($clean_social_title));
				if (!empty($clean_description)) { printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($clean_description)); }
				if (!empty($image_url)) { printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($image_url)); }
			}
		}
	}

    public function output_schema_json_ld() {
        // Note: The following line is intentionally not nonce-checked.
        // This is a safe, read-only check used by the admin-side scanner to get a clean view of the page
        // without our own schema being output. It does not process or save any data.
        $scan_param = isset($_GET['seo44_scan']) ? sanitize_key(wp_unslash($_GET['seo44_scan'])) : '';
        if ($scan_param === 'true') { return; }
        
        if (!seo44_get_option('enable_schema')) { return; }

        $base_schema = [];
        $special_schemas = [];
		$breadcrumb_schema = []; // New variable for breadcrumbs
        $post_id = get_the_ID();

        // 1. Generate the base schema
        if (is_front_page()) {
            $base_schema = $this->get_schema_for_website();

			// NEW: Add Organization Schema to homepage
	        if (seo44_get_option('enable_organization_schema')) {
	            $org_schema = $this->get_schema_for_organization();
	            if (!empty($org_schema)) {
	                $special_schemas[] = $org_schema;
	            }
	        }
			
        } elseif ( (is_category() || is_tag() || is_tax()) && seo44_get_option('enable_schema_on_taxonomies') ) {
            $base_schema = $this->get_schema_for_taxonomy();
        } elseif (is_page() || (is_singular() && !is_singular('post') && seo44_get_option('enable_schema_on_cpts'))) {
            $base_schema = $this->get_schema_for_page($post_id);
        } elseif (is_singular('post')) {
            $base_schema = self::get_schema_for_post($post_id);
        }

        // 2. Generate Breadcrumb schema for all singular pages
        if (is_singular()) {
            $breadcrumb_schema = $this->get_schema_for_breadcrumbs($post_id);
        }

        // 3. Attempt to generate special schema
        if (is_singular() && seo44_get_option('enable_advanced_schema')) {
            $special_schemas = $this->detect_and_generate_special_schema($post_id);
        }
		
		
		// 4.  Hook for Add-ons ---
		// This filter allows other plugins to add their own custom schema parts to the graph.
		$addon_schema_parts = apply_filters( 'seo44_add_schema_parts', [], $post_id );
		// --- End Hook ---

		// 5. Combine all schemas for output
		$final_schema_parts = [];
        if (!empty($base_schema)) {
            $final_schema_parts[] = $base_schema;
        }
        if (!empty($breadcrumb_schema)) {
            $final_schema_parts[] = $breadcrumb_schema;
        }
        if (!empty($special_schemas)) {
            $final_schema_parts = array_merge($final_schema_parts, $special_schemas);
        }
		
		// include any add-on schemas that pass is_array sanity check
		if ( ! empty( $addon_schema_parts ) && is_array( $addon_schema_parts ) ) {
			$final_schema_parts = array_merge( $final_schema_parts, $addon_schema_parts );
		}
        
        $final_schema = [];
        if (count($final_schema_parts) > 1) {
            $final_schema = [
                '@context' => 'https://schema.org',
                '@graph' => $final_schema_parts
            ];
        } elseif (!empty($final_schema_parts)) {
            $final_schema = $final_schema_parts[0];
        }

        if (!empty($final_schema)) {
            // wp_json_encode already produces safe JSON output
            $json_ld_string = wp_json_encode($final_schema);
            
            if ($json_ld_string !== false) {
                echo "\n\n" . '<script type="application/ld+json">' . "\n";
                // Since wp_json_encode produces safe JSON, we can output it directly with a phpcs ignore
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $json_ld_string;
                echo "\n" . '</script>' . "\n\n";
            }
        }
    }
	
	// --- Helper Functions ---

    // --- Generate BreadcrumbList Schema for Singular Content ---
    public function get_schema_for_breadcrumbs($post_id) {
        $list_items = [];
        $position = 1;

        // 1. Home
        $list_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => 'Home',
            'item' => home_url('/')
        ];
        
        // 2. Ancestors (for pages) or Category (for posts)
        if (is_page($post_id) && wp_get_post_parent_id($post_id)) {
            $ancestors = get_post_ancestors($post_id);
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $position++;
                $list_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => get_the_title($ancestor_id),
                    'item' => get_permalink($ancestor_id)
                ];
            }
        } elseif (is_singular('post')) {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                // Find the primary category if possible, otherwise use the first one
                $category = $categories[0];
                // Check if the category is "Uncategorized"
                if (strtolower($category->name) !== 'uncategorized') {
                    $position++;
                    $list_items[] = [
                        '@type' => 'ListItem',
                        'position' => $position,
                        'name' => $category->name,
                        'item' => get_category_link($category->term_id)
                    ];
                }
            }
        }
        
        // 3. Current Page/Post
        $position++;
        $list_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title($post_id)
            // The current item doesn't get an 'item' property
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list_items
        ];
    }
	
    // --- Helper Function For Types ---
    public static function get_schema_for_post($post_id) {
    	global $post;
		$post = get_post($post_id);
		setup_postdata($post);
		
		// Get the author's ID and website URL to refer authorship
		$author_id = $post->post_author;
		$author_url = get_the_author_meta('user_url', $author_id);
	
		// Build the author schema array
		$author_schema = [
			'@type' => 'Person',
			'name'  => self::get_author_name_static( $author_id ), // Pass the author ID
		];
	
		// If the author has a website, add the @id and url properties
		if ( ! empty( $author_url ) ) {
			// Ensure the URL has a trailing slash before adding the fragment.
			$canonical_author_url = trailingslashit( $author_url );
		
			$author_schema['@id'] = $canonical_author_url . '#person';
			$author_schema['url'] = $canonical_author_url;
		}
	
		$schema = [
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink($post_id)],
			'headline'         => get_the_title($post_id),
			'datePublished'    => get_the_date('c', $post_id),
			'dateModified'     => get_the_modified_date('c', $post_id),
			'author'           => $author_schema, // Use the author schema array
			'publisher'        => ['@type' => 'Organization', 'name' => get_bloginfo('name')]
		];
		if (get_site_icon_url()) { $schema['publisher']['logo'] = ['@type' => 'ImageObject', 'url' => get_site_icon_url()]; }
		if (has_post_thumbnail($post_id)) {
			$image_id = get_post_thumbnail_id($post_id);
			$image_data = wp_get_attachment_image_src($image_id, 'full');
			if ($image_data) {
				$schema['image'] = [
					'@type' => 'ImageObject',
					'url' => $image_data[0],
					'width' => $image_data[1],
					'height' => $image_data[2]
				];
			}
		}
		// NEW: Parse content for additional media if the setting is enabled
        if (seo44_get_option('scan_content_for_schema')) {
            $media_schema = self::parse_content_for_media_schema($post_id);
			if (!empty($media_schema['images'])) {
			    // Check if a featured image (or other image) already exists
			    $existing_images = isset($schema['image']) ? (array)$schema['image'] : [];
			    
			    // Merge existing images with content images
			    $schema['image'] = array_merge($existing_images, $media_schema['images']);
			}
            if (!empty($media_schema['videos'])) {
                $schema['video'] = $media_schema['videos'];
            }
        }
		
		$description = get_post_meta($post_id, seo44_get_option('description_key'), true);
		if (!empty($description)) { $schema['description'] = esc_html(wp_strip_all_tags($description)); }
		$excerpt = get_the_excerpt($post_id);
		if (!empty($excerpt)) { $schema['abstract'] = esc_html(wp_strip_all_tags($excerpt)); }
		$content = get_the_content(null, false, $post_id);
		$schema['wordCount'] = str_word_count(wp_strip_all_tags($content));
	
		$category = get_the_category($post_id);
		if (!empty($category) && $category[0]->name !== 'Uncategorized') {
			$schema['articleSection'] = esc_html($category[0]->name);
		}
	
		$tags = get_the_tags($post_id);
		if ($tags) {
			$keywords = [];
			foreach ($tags as $tag) { $keywords[] = $tag->name; }
			$schema['keywords'] = esc_html(implode(', ', $keywords));
		}
	
		wp_reset_postdata();
		return $schema;
    }
    
    public function get_schema_for_page($post_id) {
    	$schema = [
			'@context' => 'https://schema.org',
			'@type' => 'WebPage',
			'url' => get_permalink($post_id),
			'headline' => get_the_title($post_id),
			'datePublished' => get_the_date('c', $post_id),
			'dateModified' => get_the_modified_date('c', $post_id),
		];
	
		// Add the meta description
		$description = get_post_meta($post_id, seo44_get_option('description_key'), true);
		if (!empty($description)) {
			$schema['description'] = esc_html(wp_strip_all_tags($description));
		}
	
		// Add the featured image as a detailed ImageObject
		if (has_post_thumbnail($post_id)) {
			$image_id = get_post_thumbnail_id($post_id);
			$image_data = wp_get_attachment_image_src($image_id, 'full');
			if ($image_data) {
				$schema['primaryImageOfPage'] = [
					'@type' => 'ImageObject',
					'url' => $image_data[0],
					'width' => $image_data[1],
					'height' => $image_data[2]
				];
			}
		}
	// Parse content for additional media if the setting is enabled
	// NEW CODE for get_schema_for_page
	if (seo44_get_option('scan_content_for_schema')) {
	    $media_schema = self::parse_content_for_media_schema($post_id);
	    
	    if (!empty($media_schema['images'])) {
	        // 1. Start with the content images found
	        $all_images = $media_schema['images'];
	
	        // 2. If a Featured Image (primaryImageOfPage) exists, add it to the start of the list
	        if (isset($schema['primaryImageOfPage'])) {
	            array_unshift($all_images, $schema['primaryImageOfPage']);
	        }
	
	        // 3. Set the 'image' property to the complete list
	        $schema['image'] = $all_images;
	
	        // Optional: You can choose to keep or unset primaryImageOfPage. 
	        // Keeping it is usually better for SEO so Google knows which one is "Main".
	        // This would be the code to cleanup the unset:
	        // unset($schema['primaryImageOfPage']); 
	    }
	    
	    if (!empty($media_schema['videos'])) {
	        $schema['video'] = $media_schema['videos'];
	    }
	}
	
		return $schema;
    }
    public function get_schema_for_website() {
   		$schema = [
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
			'url' => home_url('/'),
			'name' => get_bloginfo('name'),
			'description' => get_bloginfo('description'),
			'potentialAction' => [
				'@type' => 'SearchAction',
				'target' => home_url('/?s={search_term_string}'),
				'query-input' => 'required name=search_term_string',
			],
		];
		return $schema;
    }

	// --- Assemble Organization Schema ---
	public function get_schema_for_organization() {
        // 1. Name & URL
        $name = seo44_get_option('org_name') ?: get_bloginfo('name');
        $url = home_url('/');

        // 2. Logo (Plugin Setting -> Theme Mod -> Fallback)
        $logo_url = '';
        $org_logo_id = seo44_get_option('org_logo');
        if ($org_logo_id) {
            $image_data = wp_get_attachment_image_src($org_logo_id, 'full');
            $logo_url = $image_data ? $image_data[0] : '';
        } else {
            // Try Customizer Logo
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $image_data = wp_get_attachment_image_src($custom_logo_id, 'full');
                $logo_url = $image_data ? $image_data[0] : '';
            }
        }

        // 3. SameAs Links (Gather from Social Tab)
        $same_as = [];
        $social_keys = ['twitter_handle', 'social_facebook', 'social_instagram', 'social_linkedin', 'social_tiktok', 'social_youtube'];
        
        
        // For sameAs, a URL is desired. Add URLs from fields and construct Twitter / X and Facebook URL

		// Twitter/X: Handle logic
	    $twitter_handle = seo44_get_option('twitter_handle');
	    if ($twitter_handle) {
	        // Clean handle just in case they added @
	        $clean_handle = str_replace('@', '', $twitter_handle);
	        $same_as[] = 'https://x.com/' . esc_attr($clean_handle);
	    }
        
        $extras = ['social_facebook', 'social_instagram', 'social_linkedin', 'social_youtube', 'social_tiktok'];
        foreach($extras as $key) {
            $val = seo44_get_option($key);
            if ($val) $same_as[] = esc_url($val);
        }
		// NEW: Process Additional URLs (One per line)
	    $additional_urls = seo44_get_option('social_additional');
	    if (!empty($additional_urls)) {
	        // Split by newline, trim whitespace, and filter empty lines
	        $urls = array_filter(array_map('trim', explode("\n", $additional_urls)));
	        
	        foreach ($urls as $raw_url) {
	            // Validate it is a real URL before adding
	            $clean_url = esc_url_raw($raw_url);
	            if (!empty($clean_url)) {
	                $same_as[] = $clean_url;
	            }
	        }
	    }

        // 4. Build the Schema
        $schema = [
            '@type' => 'Organization',
            '@id'   => $url . '#organization',
            'name'  => $name,
            'url'   => $url,
        ];

		// Add Alternate Name
	    $alt_name = seo44_get_option('org_alternate_name');
	    if ($alt_name) {
	        $schema['alternateName'] = $alt_name;
	    }

        // Add Tagline (Slogan)
        $tagline = get_bloginfo('description');
        if ($tagline) {
            $schema['slogan'] = $tagline;
        }

        if ($logo_url) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                'url'   => $logo_url
            ];
        }

        if (!empty($same_as)) {
            $schema['sameAs'] = $same_as;
        }

        // Contact Point (Updated to include Email)
	    $phone = seo44_get_option('org_phone');
	    $email = seo44_get_option('org_email');
	    
	    if ($phone || $email) {
	        $contact_point = ['@type' => 'ContactPoint'];
	        if ($phone) {
	            $contact_point['telephone'] = $phone;
	            $contact_point['contactType'] = 'customer service';
	        }
	        if ($email) {
	            $contact_point['email'] = $email;
	        }
	        $schema['contactPoint'] = $contact_point;
	    }
	    
	    // Email at the top level is also good practice
	    if ($email) {
	        $schema['email'] = $email;
	    }

		// 5. Address
	    $street = seo44_get_option('org_address_street');
	    $city   = seo44_get_option('org_address_city');
	    if ($street && $city) {
	        $schema['address'] = [
	            '@type'           => 'PostalAddress',
	            'streetAddress'   => $street,
	            'addressLocality' => $city,
	            'addressRegion'   => seo44_get_option('org_address_state'),
	            'postalCode'      => seo44_get_option('org_address_zip'),
	            'addressCountry'  => seo44_get_option('org_address_country')
	        ];
	    }

		// Service Area (New)
	    $area_served = seo44_get_option('org_area_served');
	    if ($area_served) {
	        $schema['areaServed'] = [
	            '@type' => 'Place',
	            'name'  => $area_served
	        ];
	    }
		// 6. Founder
	    $founder = seo44_get_option('org_founder');
	    if ($founder) {
	        $schema['founder'] = [
	            '@type' => 'Person',
	            'name'  => $founder
	        ];
	    }
	
	    // 7. Founding Date
	    $founding_date = seo44_get_option('org_founding_date');
	    if ($founding_date) {
	        // Basic validation: ensure it looks somewhat like a year or date
	        // You can leave it as raw string, Google parses ISO 8601 (YYYY-MM-DD) well.
	        $schema['foundingDate'] = strip_tags($founding_date);
	    }
		// 8. Professional License (New)
	    $license = seo44_get_option('org_license');
	    if ($license) {
	        // "hasCredential" is the modern schema property for this
	        $schema['hasCredential'] = [
	            '@type' => 'EducationalOccupationalCredential',
	            'credentialCategory' => 'license',
	            'name' => $license, // e.g., "Contractor License #123456"
	            'recognizedBy' => [
	                '@type' => 'Organization',
	                'name' => 'State Licensing Board' // Generic fallback since we don't ask for the issuer
	            ]
	        ];
	        
	        // Also add it as a simple identifier for wider compatibility
	        $schema['identifier'] = $license; 
	    }
		// FINAL STEP: Apply Filters for Extensibility using 'seo44_organization_schema'
        // Allows developers to add properties like 'duns', 'naics', 'awards', etc.
    	return apply_filters('seo44_organization_schema', $schema);
    }

	// --- Intelligent Schema Detection ---
    
    /**
     * Detects if the post content matches a special schema type like FAQ or HowTo.
     * @param int $post_id The ID of the post to parse.
     * @return array The generated schema array, or an empty array if no pattern is matched.
     */
    public function detect_and_generate_special_schema($post_id) {
        $post = get_post($post_id);
        if (!$post) return [];

        if (has_blocks($post->post_content)) {
            // Use the precise block-based parser for modern content
            return $this->parse_blocks_for_special_schema($post_id, parse_blocks($post->post_content));
        } else {
            // Use the HTML fallback for classic editor or page builder content
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $rendered_content = apply_filters('the_content', $post->post_content);
            return $this->parse_html_for_special_schema($post_id, $rendered_content);
        }
    }
	
	
    /**
     * NEW: Parses HTML content for FAQ and How-To patterns (the fallback method).
     */
    private function parse_html_for_special_schema($post_id, $html) {
        $final_schemas = [];
        
        // FAQ Detection in HTML
        if (preg_match('/<h[2-4][^>]*>(frequently asked questions|faqs|q&a)<\/h[2-4]>/i', $html, $faq_heading_match)) {
            $faq_questions = [];
            // Find all subsequent headings (potential questions) until the next major heading
            preg_match_all('/<h[3-6][^>]*>(.*?\?)<\/h[3-6]>(.*?)((?=<h[1-3])|$)/is', $html, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $question_text) {
                    $faq_questions[] = [
                        'question' => $question_text,
                        'answer_html' => $matches[2][$index]
                    ];
                }
            }
            if(!empty($faq_questions)) {
                $main_entity = [];
                foreach($faq_questions as $item) {
                    $main_entity[] = [
                        '@type' => 'Question',
                        'name' => esc_html(wp_strip_all_tags($item['question'])),
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $this->clean_and_format_answer($item['answer_html'])]
                    ];
                }
                $final_schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $main_entity];
            }
        }
        
        // HowTo Detection in HTML
        if (preg_match('/<h[2-4][^>]*>(installation|directions|instructions)<\/h[2-4]>/i', $html, $howto_heading_match)) {
            preg_match('/' . preg_quote($howto_heading_match[0], '/') . '.*?<ol.*?>(.*?)<\/ol>/is', $html, $ol_match);
            if(isset($ol_match[1])) {
                preg_match_all('/<li.*?>(.*?)<\/li>/is', $ol_match[1], $li_matches);
                if(!empty($li_matches[1])) {
                    $howto_steps = [];
                    foreach($li_matches[1] as $step_text) {
                        $howto_steps[] = ['@type' => 'HowToStep', 'text' => esc_html(wp_strip_all_tags($step_text))];
                    }
                    if(!empty($howto_steps)) {
                         $final_schemas[] = [
                            '@context' => 'https://schema.org',
                            '@type' => 'HowTo',
                            'name' => 'How to ' . get_the_title($post_id),
                            'step' => $howto_steps
                        ];
                    }
                }
            }
        }

        return $final_schemas;
    }

    /**
     * The original block-based parser, now in its own function.
     */
    private function parse_blocks_for_special_schema($post_id, $blocks) {        
        $faq_questions = [];
        $howto_steps = [];
        $is_faq_section = false;
        $is_howto_section = false; // Flag to track if we're in a HowTo section
        $howto_heading_text = ''; // Variable to store the heading text
        $current_answer_blocks = [];
        $final_schemas = [];

		foreach ($blocks as $block) {
            $heading_text = strtolower(wp_strip_all_tags($block['innerHTML']));
            
            // --- Stop condition for all sections ---
            if (($is_faq_section || $is_howto_section) && $block['blockName'] === 'core/heading' && preg_match('/<h[1-2]/i', $block['innerHTML'])) {
                if (!empty($current_answer_blocks) && !empty($faq_questions)) {
                    $last_question_index = count($faq_questions) - 1;
                    $faq_questions[$last_question_index]['answer_blocks'] = $current_answer_blocks;
                }
                $is_faq_section = false;
                $is_howto_section = false; // Stop looking for HowTo steps as well
                $current_answer_blocks = [];
                continue;
            }

            // --- FAQPage Detection ---
            if (!$is_howto_section && $block['blockName'] === 'core/heading' && preg_match('/(frequently asked questions|faqs|q&a)/i', $heading_text)) {
                $is_faq_section = true;
                continue;
            }
            if ($is_faq_section && $block['blockName'] === 'core/heading' && substr(trim($heading_text), -1) === '?') {
                if (!empty($current_answer_blocks)) {
                     $last_question_index = count($faq_questions) - 1;
                     $faq_questions[$last_question_index]['answer_blocks'] = $current_answer_blocks;
                }
                $current_answer_blocks = [];
                $faq_questions[] = ['question' => $block['innerHTML'], 'answer_blocks' => []];
                continue;
            }
            if ($is_faq_section && !empty($faq_questions)) {
                $current_answer_blocks[] = $block;
            }

           // --- HowTo Detection ---
            if (!$is_faq_section && $block['blockName'] === 'core/heading' && preg_match('/(installation|directions|instructions)/i', $heading_text)) {
                $is_howto_section = true;
				// Use trim() to remove leading/trailing whitespace and newlines
                $howto_heading_text = trim(wp_strip_all_tags($block['innerHTML'])); 
                continue; // Find the heading, then look for the list in subsequent blocks
            }
            // If we are in a how-to section and find the first ordered list, process it.
            if ($is_howto_section && $block['blockName'] === 'core/list' && isset($block['attrs']['ordered']) && $block['attrs']['ordered']) {
				
                 if (!empty($block['innerBlocks'])) {
                    foreach($block['innerBlocks'] as $list_item_block) {
                        if ($list_item_block['blockName'] === 'core/list-item') {
                            $howto_steps[] = ['@type' => 'HowToStep', 'text' => esc_html(wp_strip_all_tags($list_item_block['innerHTML']))];
                        }
                    }
                }
                 $is_howto_section = false; // Stop after finding the first ordered list
            }
        }
        
        if ($is_faq_section && !empty($current_answer_blocks) && !empty($faq_questions)) {
            $last_question_index = count($faq_questions) - 1;
            $faq_questions[$last_question_index]['answer_blocks'] = $current_answer_blocks;
        }

        if (!empty($faq_questions)) {
            $main_entity = [];
            foreach ($faq_questions as $item) {
                $answer_text = $this->clean_and_format_answer($item['answer_blocks']);
                if (empty($answer_text)) continue;
                $main_entity[] = [
                    '@type' => 'Question',
                    'name' => esc_html(wp_strip_all_tags($item['question'])),
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer_text]
                ];
            }
            if (!empty($main_entity)) {
                $final_schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $main_entity];
            }
        }

        if (!empty($howto_steps)) {
            // Use the captured heading text to create a more descriptive name
            $howto_name = !empty($howto_heading_text) ? get_the_title($post_id) . ': ' . $howto_heading_text : 'How to ' . get_the_title($post_id);
            $final_schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'HowTo',
                'name' => $howto_name,
                'step' => $howto_steps,
            ];
        }
        
        return $final_schemas;
    }

    /**
     *  HELPER: Cleans and formats block content for an answer.
     */
    private function clean_and_format_answer($blocks) {
        $answer_html = '';
        foreach ($blocks as $block) {
            $answer_html .= render_block($block);
        }

        // Handle lists specifically
        $answer_html = preg_replace_callback('/<ul.*?>(.*?)<\/ul>/is', function($matches) {
            $list_content = '';
            preg_match_all('/<li.*?>(.*?)<\/li>/is', $matches[1], $li_matches);
            foreach($li_matches[1] as $li) {
                // Add a space before the bullet for better separation
                $list_content .= "\n - " . trim(wp_strip_all_tags($li));
            }
            return $list_content;
        }, $answer_html);

        $answer_html = preg_replace_callback('/<ol.*?>(.*?)<\/ol>/is', function($matches) {
            $list_content = '';
            $i = 1;
            preg_match_all('/<li.*?>(.*?)<\/li>/is', $matches[1], $li_matches);
            foreach($li_matches[1] as $li) {
                // Add a space after the number for better separation
                $list_content .= " #" . $i . ". " . trim(wp_strip_all_tags($li));
                $i++;
            }
            return $list_content;
        }, $answer_html);

        // Clean up remaining HTML, extra whitespace, and decode entities
        $clean_text = trim(wp_strip_all_tags(html_entity_decode($answer_html)));
        return preg_replace('/\n\s*\n/', "\n", $clean_text); // Collapse multiple newlines
    }
	
    // --- MEDIA PARSING FUNCTION ---

    /**
     * Parses post content to find image and video blocks for schema.
     * @param int $post_id The ID of the post to parse.
     * @return array An array containing 'images' and 'videos' schema objects.
     */
    public static function parse_content_for_media_schema($post_id) {
        $post = get_post($post_id);
        if (!$post) { return ['images' => [], 'videos' => []]; }

        $images = [];
        $videos = [];
        $content = $post->post_content;
        $found_block_image_urls = [];
        $found_video_urls = []; // Fixed: Added missing variable declaration

        if (has_blocks($content)) {
            $blocks = parse_blocks($content);
            foreach ($blocks as $block) {
                // Handle Image Blocks
                if ($block['blockName'] === 'core/image' && !empty($block['attrs']['id'])) {
                    $image_id = $block['attrs']['id'];
                    $image_data = wp_get_attachment_image_src($image_id, 'full');
                    if ($image_data) {
                        $image_object = [
                            '@type' => 'ImageObject',
                            'url' => $image_data[0],
                            'width' => $image_data[1],
                            'height' => $image_data[2]
                        ];
                        if (preg_match('/<figcaption[^>]*>(.*?)<\/figcaption>/', $block['innerHTML'], $caption_matches)) {
                            $image_object['caption'] = esc_html(wp_strip_all_tags($caption_matches[1]));
                        }
                        $images[] = $image_object;
                        $found_block_image_urls[] = $image_data[0]; // Keep track of URLs we've already added
                    }
                }

                // Handle YouTube Embed Blocks using the oEmbed API
                if ($block['blockName'] === 'core/embed' && isset($block['attrs']['providerNameSlug']) && $block['attrs']['providerNameSlug'] === 'youtube') {
                    $oembed_url = 'https://www.youtube.com/oembed?format=json&url=' . urlencode($block['attrs']['url']);
                    $response = wp_remote_get($oembed_url);
                    if (!is_wp_error($response)) {
                        $data = json_decode(wp_remote_retrieve_body($response), true);
                        if ($data) {
                            $videos[] = [
                                '@type' => 'VideoObject',
                                'name' => $data['title'],
                                'thumbnailUrl' => $data['thumbnail_url'],
                                'embedUrl' => $block['attrs']['url'],
                                'author' => ['@type' => 'Person', 'name' => $data['author_name']]
                            ];
                            $found_video_urls[] = $block['attrs']['url']; // Track processed videos
                        }
                    }
                }
            }
        } 
        
 // --- Universal Fallback for <img> and <iframe> tags in Classic Editor or HTML Blocks---
        
        // Find Images (logic is unchanged)
        if (preg_match_all('/<img[^>]+>/i', $content, $img_matches)) {
            foreach ($img_matches[0] as $img_tag) {
                if (strpos($img_tag, 'seo44-ignore') !== false || (strpos($img_tag, 'width="1"') !== false && strpos($img_tag, 'height="1"') !== false)) {
                    continue;
                }
                if (preg_match('/src\s*=\s*[\'"]([^\'"]+)[\'"]/i', $img_tag, $src_matches)) {
                    $url = $src_matches[1];
                    // Add the image ONLY if we haven't already added it from a core/image block
                    if (!in_array($url, $found_block_image_urls)) {
                        $images[] = ['@type' => 'ImageObject', 'url' => $url];
                    }
                }
			}
        }

        // RESTORED: Find iframes
        if (preg_match_all('/<iframe[^>]+src="([^"]+)"[^>]*>/i', $content, $iframe_matches)) {
            foreach($iframe_matches[1] as $iframe_src) {
                // Skip if we already processed this video from a block
                if (in_array($iframe_src, $found_video_urls)) {
                    continue;
                }

                if (strpos($iframe_src, 'youtube.com/embed') !== false) {
                    preg_match('/embed\/([a-zA-Z0-9_-]+)/i', $iframe_src, $id_matches);
                    $video_id = $id_matches[1] ?? null;
                    if ($video_id) {
                        $videos[] = [
                            '@type' => 'VideoObject',
                            'name' => 'Embedded YouTube Video', // Title isn't available from a raw iframe
                            'thumbnailUrl' => 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg',
                            'embedUrl' => 'https://www.youtube.com/watch?v=' . $video_id,
                        ];
                    }
                }
            }
        }

        return ['images' => $images, 'videos' => $videos];
    }
	
	//  Function to generate BreadcrumbList schema for Taxonomy pages (not posts & Pages)
    public function get_schema_for_taxonomy() {
        $term = get_queried_object();
        $list_items = [];
        $position = 1;

        // 1. Add Home
        $list_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => 'Home',
            'item' => home_url('/')
        ];

        // 2. Add ancestors if they exist (for hierarchical taxonomies like categories)
        if (is_taxonomy_hierarchical($term->taxonomy)) {
            $ancestors = get_ancestors($term->term_id, $term->taxonomy, 'taxonomy');
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $position++;
                $ancestor = get_term($ancestor_id, $term->taxonomy);
                $list_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $ancestor->name,
                    'item' => get_term_link($ancestor)
                ];
            }
        }

        // 3. Add the current term
        $position++;
        $list_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $term->name,
            'item' => get_term_link($term)
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list_items,
        ];

        return $schema;
    }
	
	// Function to create author name
    
	public function get_author_name( $author_id ) {
		$format = seo44_get_option('author_format', 'display_name');
		switch ($format) {
			case 'first_last': return get_the_author_meta('first_name', $author_id) . ' ' . get_the_author_meta('last_name', $author_id);
			case 'last_first': return get_the_author_meta('last_name', $author_id) . ', ' . get_the_author_meta('first_name', $author_id);
			default: return get_the_author_meta('display_name', $author_id);
		}
	}
	public static function get_author_name_static( $author_id ) {
		$frontend = new self();
		return $frontend->get_author_name( $author_id );
	}
}
