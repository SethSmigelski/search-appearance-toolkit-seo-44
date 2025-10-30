<?php
/**
 * Plugin Name:       Search Appearance Toolkit (SEO 44)
 * Plugin URI:        https://www.sethcreates.com/plugins-for-wordpress/seo-44/
 * Description:       A lightweight, powerful SEO plugin for essential meta tags, advanced schema, XML sitemaps, jump links, and easy migration from other plugins.
 * Version:           3.9.5
 * Author:            Seth Smigelski
 * Author URI:  	  https://www.sethcreates.com/plugins-for-wordpress/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       search-appearance-toolkit-seo-44
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'SEO44_VERSION', '3.9.5' );
define( 'SEO44_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the main Core class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo44-core.php';

/**
 * The main function for returning the singleton instance of the Core class.
 *
 * @return SEO44_Core
 */
function seo44() {
    return SEO44_Core::instance();
}

// Initialize the plugin.
seo44();

// --- Global Helper Functions ---

/**
 * Helper to get a specific plugin option.
 */
function seo44_get_option($key, $default = '') {
    $options = get_option('seo44_settings');
	$defaults = [
		'title_key' => 'seo44_title',
		'description_key' => 'seo44_description',
        'keywords_key' => 'seo44_keywords', 
        'enable_tags' => 1, 
        'include_keywords' => 1, 
        'include_author' => 1, 
        'enable_og_tags' => 1, 
        'enable_twitter_tags' => 1,
		'enable_schema' => 0,
        'enable_schema_on_taxonomies' => 0,
        'enable_schema_on_cpts' => 0,
		'enable_sitemaps' => 1,
        'enable_sitemap_ping' => 1,
        'sitemap_post_types' => ['post', 'page'],
        'sitemap_taxonomies' => ['category', 'post_tag'],
        'sitemap_include_images' => 1,
        'sitemap_include_content_images' => 0 // ADD THIS LINE (Default to OFF)
    ];
    $default = isset($defaults[$key]) ? $defaults[$key] : $default;
    return isset($options[$key]) && $options[$key] !== '' ? $options[$key] : $default;
}

/**
 * Helper to render a tooltip.
 */
function seo44_render_tooltip($text) {
    echo '<span class="seo44-tooltip">
        <span class="seo44-tooltip-icon">?</span>
        <span class="seo44-tooltip-text">' . esc_html($text) . '</span>
    </span>';
}

/**
 * Checks if the standalone Jump Links Block is active to prevent conflicts.
 * If found, it shows a persistent admin notice to the user.
 */
function seo44_check_for_standalone_block() {
    // The path to the standalone plugin's main file.
    $standalone_plugin = 'jump-links-block/jump-links-block.php';

    if ( is_plugin_active( $standalone_plugin ) ) {
        // Add a notice to inform the user about the redundancy.
        add_action( 'admin_notices', 'seo44_show_standalone_active_notice' );
    }
}
add_action( 'admin_init', 'seo44_check_for_standalone_block' );

/**
 * Displays the admin notice when both plugins are active.
 */
function seo44_show_standalone_active_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong>SEO 44 Notice:</strong> The standalone <strong>Jump Links Block</strong> plugin is currently active. To prevent conflicts, the Jump Links Block from the main SEO 44 plugin has not been loaded. You may safely deactivate the standalone plugin; all features are included in SEO 44.
        </p>
    </div>
    <?php
}

/**
* Registers the custom Jump Links Block, but only if the standalone plugin is not active.
*/
function seo44_register_blocks() {
    // Check if the standalone plugin's main function exists.
    // This is a safe way to check without knowing the file path.
    if ( ! function_exists('jump_links_block_register') ) {
        register_block_type( __DIR__ . '/build' );
    }
}
add_action('init', 'seo44_register_blocks');	


/**
 * Template tag to get the SEO 44 meta tags as a string.
 */
function get_the_seo44_tags() {
    // We need to call the method from our frontend class instance
    ob_start();
    if ( function_exists('seo44') && ! empty( seo44()->frontend ) ) {
        seo44()->frontend->output_header_tags();
    }
    return ob_get_clean();
}

/**
 * Template tag to echo the SEO 44 meta tags.
 */
function the_seo44_tags() {
	$allowed_tags = [
		'meta' => [
			'name'     => [],
			'property' => [],
			'content'  => [],
		],
	];
	echo wp_kses( get_the_seo44_tags(), $allowed_tags );
}

/**
 * Template tag to get the SEO 44 Schema JSON-LD as a string.
 */
function get_the_seo44_schema() {
    ob_start();
    if ( function_exists('seo44') && ! empty( seo44()->frontend ) ) {
        seo44()->frontend->output_schema_json_ld();
    }
    return ob_get_clean();
}

/**
 * Template tag to echo the SEO 44 Schema JSON-LD.
 */
function the_seo44_schema() {
    $allowed_tags = [
        'script' => [
            'type' => [],
        ],
    ];
    // We can't escape the content of the script tag, so we just allow the tag itself.
    // The content is already safe from wp_json_encode().
    echo wp_kses( get_the_seo44_schema(), $allowed_tags );
}

// --- Activation & Deactivation Hooks ---

/**
 * Function to run on plugin activation.
 * Properly registers and flushes rewrite rules.
 */
function seo44_activate() {
    // Call the static method that handles activation
    require_once plugin_dir_path(__FILE__) . 'includes/class-seo44-sitemaps.php';
    SEO44_Sitemaps::on_activation();
}
register_activation_hook(__FILE__, 'seo44_activate');

/**
 * Function to run on plugin deactivation.
 * Flushes rewrite rules to clean up.
 */
function seo44_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'seo44_deactivate');