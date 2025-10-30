<?php
class SEO44_Metabox {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box_data']);
    }

    public function add_meta_box() {
         $post_types = get_post_types(['public' => true, 'show_ui' => true]);
		// We don't need the metabox on attachments
		unset($post_types['attachment']);
	
		 foreach ($post_types as $post_type) {
            // UPDATED: Metabox title
            add_meta_box('seo44_meta_box', __('SEO 44: Customize Appearance in Search Results', 'search-appearance-toolkit-seo-44'), [$this, 'meta_box_html'], $post_type, 'normal', 'high');
        }
    }
    
    public function meta_box_html($post) {
        wp_nonce_field('seo44_save_meta_box_data', 'seo44_meta_box_nonce');
        $title = get_post_meta($post->ID, seo44_get_option('title_key'), true);
        $description = get_post_meta($post->ID, seo44_get_option('description_key'), true);
        $keywords = get_post_meta($post->ID, seo44_get_option('keywords_key'), true);
        $jump_link_headings = get_post_meta($post->ID, '_seo44_jump_link_headings', true);
        wp_nonce_field('seo44_jump_links_nonce', 'seo44_jump_links_nonce_field');
    ?>
    	<input type="hidden" id="seo44_jump_link_headings_field" name="seo44_jump_link_headings" value="<?php echo esc_attr($jump_link_headings); ?>">
        <p>
            <label for="seo44_title"><strong><?php esc_html_e('SEO Title', 'search-appearance-toolkit-seo-44'); ?></strong></label><br>
            <input type="text" id="seo44_title" name="seo44_title" value="<?php echo esc_attr($title); ?>">
            <span class="description">
                <strong><?php esc_html_e('Example: ', 'search-appearance-toolkit-seo-44'); ?></strong><span id="seo44-title-example"><?php 
					$title_words = wp_trim_words( get_the_title( $post->ID ), 5, '' );
					$site_name = get_bloginfo( 'name' );
					echo esc_html( $title_words . ' - ' . $site_name );
				?></span>
                <button type="button" class="button button-small" id="seo44-use-example-title"><?php esc_html_e('Use This Example', 'search-appearance-toolkit-seo-44'); ?></button>
            </span>
    
            
            <div id="seo44_title_char_count" class="char-count"></div>
        </p>
        <p>
            <label for="seo44_description"><strong><?php esc_html_e('Meta Description', 'search-appearance-toolkit-seo-44'); ?></strong></label><br>
            <textarea id="seo44_description" name="seo44_description" rows="4"><?php echo esc_textarea($description); ?></textarea>
            <div id="seo44_description_char_count" class="char-count"></div>
        </p>
        <?php if (seo44_get_option('include_keywords')) : ?>
        <p>
            <label for="seo44_keywords"><strong><?php esc_html_e('Meta Keywords', 'search-appearance-toolkit-seo-44'); ?></strong></label><br>
            <input type="text" id="seo44_keywords" name="seo44_keywords" value="<?php echo esc_attr($keywords); ?>">
            <small><?php esc_html_e('Separate keywords with commas.', 'search-appearance-toolkit-seo-44'); ?></small>
        </p>
        <?php endif; ?>
        <hr>
        <div id="seo44-snippet-preview">
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
    
    /**
     * FIXED: All $_POST data is now unslashed before sanitization to meet WordPress security standards.
     * ENHANCED: Switched to sanitize_textarea_field for the description for better multi-line text handling.
     */
    public function save_meta_box_data($post_id) {
        if (!isset($_POST['seo44_meta_box_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seo44_meta_box_nonce'])), 'seo44_save_meta_box_data')) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		$post_type = get_post_type($post_id);
		// Robust capability checking
		if (!current_user_can("edit_{$post_type}s")) {
			return;
		}
		if (isset($_POST['seo44_title'])) {
            update_post_meta($post_id, seo44_get_option('title_key'), sanitize_text_field(wp_unslash($_POST['seo44_title'])));
        }
		if (isset($_POST['seo44_description'])) {
            update_post_meta($post_id, seo44_get_option('description_key'), sanitize_textarea_field(wp_unslash($_POST['seo44_description'])));
        }
		if (isset($_POST['seo44_keywords'])) {
            update_post_meta($post_id, seo44_get_option('keywords_key'), sanitize_text_field(wp_unslash($_POST['seo44_keywords'])));
        }
		    // Save Logic for Hidden Box for Jump Links Block data
        if (isset($_POST['seo44_jump_links_nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seo44_jump_links_nonce_field'])), 'seo44_jump_links_nonce')) {
            if (isset($_POST['seo44_jump_link_headings'])) {
                update_post_meta($post_id, '_seo44_jump_link_headings', sanitize_text_field(wp_unslash($_POST['seo44_jump_link_headings'])));
            }
        }
    }
}