=== Search Appearance Toolkit (SEO 44) ===
Contributors: sethsm
Tags: seo, on-page seo, schema, structured data, xml sitemaps
Requires at least: 5.5
Tested up to: 6.8
Stable tag: 4.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/donate/?hosted_button_id=M3B2Q94PGVVWL
Plugin URI:  https://www.sethcreates.com/plugins-for-wordpress/search-appearance-toolkit-seo-44/
Author URI:  https://www.sethcreates.com/plugins-for-wordpress/

A lightweight, feature-packed SEO plugin for essential meta tags, advanced structured data, XML sitemaps, article jump links, and easy migration.

== Description ==

Search Appearance Toolkit (SEO 44) is a lightweight and complete SEO plugin for WordPress that improves your site's search engine visibility with a suite of powerful features and an easy migration path from other popular SEO plugins. 

Whether you're setting up a new site or looking for a lightweight alternative, SEO 44 gives you control over your on-page SEO, social media appearance, Schema.org structured data, XML sitemaps, and article jump links. 

The interface is clean and user-friendly. Under the hood, the plugin packs powerful features to automatically scan your content and generate valuable resources for SEO - leaving you with more time to create great content. Within the block editor, the SEO 44 Jump Links Block scans for headings and automatically generates customizable jump links for improved user navigation and richer search results.

== Why use SEO 44? ==

Search Appearance Toolkit (SEO 44) is a fast, no-nonsense SEO plugin for users who want a complete set of powerful, essential SEO features without the performance impact, upsells, and dashboard clutter common with larger plugins. Its intelligent schema scanner and powerful migration tools also make it an excellent choice for users looking to switch from a bloated plugin to a more lightweight solution.

== Key Features ==
 
= Use-Friendly Interface =
* **Tabbed Settings Page:**  The interface is clean and uncluttered. All plugin settings are organized into six clear, easy-to-navigate tabs: Main Settings, Social Media, Schema, XML Sitemaps, Integrations, and Migration.
* **Helpful Tooltips:**  You will find guidance on different options throughout the settings page. 
* **Easy to Use Editor Interface:** Manage SEO tags with a clean and straightforward metabox in the post editor. 
* **Search Results Snippet Preview:** See a live preview of how your content may look in Google search results. 
* **Character Counters:** Keep your titles and descriptions to the recommended length thanks to character counters that warn you if you exceed safe lengths.

= SEO Head Tags = 
* **On-Page SEO Control:** Set custom SEO Titles and Meta Descriptions for posts, pages, custom post types, categories, tags, and your homepage. 
* **Intelligent Fallback:** If you don't fill in one of the SEO tags for post, page, and taxonomy, the plugin won't output empty SEO tags; instead, it produces useful fallbacks to ensure your site is optimized, even when you don't fill out SEO fields. 
* **Optional Support for Meta Keywords:**  Include keywords if you like them, leave them out if you don't think they are valuable. The choice is yours
* **Customizable Author Tags:**  Give credit to your writers with a few formats for displaying their names. 

= Social Media Tags = 
* **Open Graph and Twitter Cards:**  Enable tags for Facebook (Open Graph) and X / Twitter Cards
* **Image Handling:**  Set a Default Social Image for content that doesn't have a featured image, and add your Twitter handle and Facebook App ID for better integration. 

= Schema Types Useds =  
* **WebSite:** Schema for your homepage, enabling Google Sitelinks Search Box potential. 
* **Article:** Rich schema for posts, with details such as publisher, images, and word count. Features an enhanced author property that links to the author's profile URL to strengthen entity connection.
* **WebPage:** Schema for pages and custom post types, including images. 
* **BreadcrumbList:** Schema for all posts, pages, and taxonomy archives to enhance your appearance in search results.
* **Other Formats:** ImageObject, FAQPage, and HowTo schemas may also be used where appropriate. 

= Advanced Schema Controls & Tools =
* **Intelligent Schema Scanner:** Scans your site for existing structured data from your theme or other plugins, helping you prevent dangerous conflicts and duplicates before you enable schema. 
* **Include Images and Videos:** A built-in tool automatically finds all images and embedded YouTube videos in your content and adds them to the schema, boosting their appearance in search results.
* **FAQ and How-To Detection:** Enable a smart scanner to detect patterns in your content for FAQ and How-To sections on your website and incorporate this useful format into the schema. 
* **Modern Output:** All structured data is generated in the modern JSON-LD format preferred by search engines, following the guidelines set by [Schema.org](https://schema.org/).
* **Granular Control:**  Taylor the Schema settings to fit your site's needs through Enable/disable settings, including on Custom Post Types and Taxonomies.

= Comprehensive XML Sitemaps =
* **Full Content Coverage:** Automatically detects all public Custom Post Types and Taxonomies and provides simple checkboxes to let you choose exactly what content to include in your sitemap. 
* **Image Sitemaps:** Includes options to add both Featured Images and images found within your post content to your sitemaps, providing more context to search engines. 
* **Custom Types:** Automatically detects and includes all public Custom Post Types and Custom Taxonomies. 
* **Performance Conscious:** The sitemap is cached for high performance and includes a "Purge Cache" button for manual regeneration. It also automatically pings search engines when you publish new content.

= Seamless Migration Tools =
* **Make the Switch:** Easily migrate from other SEO plugins without losing your work. Simply swap in the meta keys used by your old plugin to have SEO 44 pick up using your existing data. 
* **One-Click Presets:** Includes presets for popular plugins like All in One SEO, Yoast SEO, Rank Math, and more, which automatically fill in the correct meta keys for you. 
* **Database Scanner:** Not sure what meta keys your old plugin used? The built-in scanner can analyze your database, find potential SEO meta keys, and suggest the correct ones to use for a seamless transition. 

= Advanced Jump Links Block = 
* **Instant Jump Links List Creation:** Automatically generates a customizable "Table of Contents" block from your page's headings to improve user navigation and earn rich "Jump to" links in Google search results.
* **Full Customization:** Features "Viewing" and "Editing" modes right in the editor. In Editing Mode, you can change link text, reorder links with up/down arrows, and exclude specific headings from the list.
* **Complete Styling Control:** Use the sidebar controls to change the layout (vertical or horizontal), list style (bullets, numbers, or none), font size, and colors. For the horizontal layout, you can also style the link background, border, and border-radius.
* **Front-End Interactivity:** Includes built-in smooth scrolling and an optional "Show More" button to collapse long link lists elegantly.

== Integrations & Advanced Analytics ==

The Search Appearance Toolkit serves as a hub for connecting your site to essential third-party services, helping you to create valuable analytics data and site insights.

= Google Tag Manager (GTM) Integration =
Stop worrying about editing your theme's `header.php` file. Just paste your `GTM-XXXXXXX` ID into the settings, and the plugin will correctly and safely inject the GTM scripts into your site's `<head>` and `<body>` on every page.

= Sophisticated GTM Event Tracking =
Once GTM is enabled, you can flip toggles to automatically push rich, valuable events to the `dataLayer` for your analytics. This is where the power of integration shines through. 

* **Rich SEO dataLayer:** Automatically pushes critical page data (like post category, author, tags, and page type) on every page load. This is a powerful feature for advanced users who want to create granular analytics triggers in GTM (e.g., "Fire this conversion pixel *only* on the 'Products' category").
* **Scroll Depth Tracking:** Find out how much of your content is being viewed by visitors. This feature pushes `scroll_depth` events as users scroll 25%, 50%, 75%, and 100% down a page.
* **Outbound & Affiliate Link Click Tracking:** Automatically detects when a user clicks a link to leave your site and pushes an `external_link_click` event, conditionally identifying affiliate links (with `rel="sponsored"`) and then pushing a separate `affiliate_link_click` event.
* **Jump Link Click Tracking:** Tracks engagement with your Jump Links Block by pushing a `jump_link_click` event, letting you see which sections your users are most interested in.

= Webmaster Verification =
Easily verify your site with search engines. Paste your verification codes for **Google Search Console** and **Bing Webmaster Tools** into the corresponding fields in the Integrations tab. The plugin handles the rest, adding the meta tags to your site's header, so you never have to worry about them being removed during a theme update.

== Installation ==

= From the WordPress Plugin Directory File =
1.  Log in to your WordPress Admin Dashboard.
2.  Navigate to **Plugins > Add Plugin** in the left-hand menu.
3.  Search for the plugin: **Search Appearance Toolkit (SEO 44)**.
4.  Install the plugin: Once you locate the [correct plugin](https://wordpress.org/plugins/search-appearance-toolkit-seo-44/), click the **"Install Now"** button next to it.
5.  Activate the plugin: After the installation is complete, click the **"Activate Plugin"** button that appears.

= From a Zip File =
1.  Download a copy of the plugin, available in the WordPress Plugin Directory [Search Appearance Toolkit SEO 44](https://wordpress.org/plugins/search-appearance-toolkit-seo-44/) webpage. 
2.  Upload the **search-appearance-toolkit-seo-44** folder to the `/wp-content/plugins/` directory 
3.  Activate the plugin through the **Plugins** menu in WordPress.
4.  Navigate to **Settings -> SEO 44** to configure the plugin.

== Frequently Asked Questions ==

= What SEO benefits can this plugin provide? =
Search Appearance Toolkit (SEO 44) gives you control over the technical, on-page SEO factors that help search engines understand and rank your content. Key benefits include:
* **Optimized Snippets:** Control how your titles and descriptions appear in search results.
* **Rich Results:** The advanced Schema.org data helps you earn rich results like FAQs, How-Tos, and breadcrumbs in Google.
* **Crawlability:** A clean and comprehensive XML sitemap ensures search engines can find and index all of your important content and images.
* **User Engagement:** The Jump Links Block improves user experience, which is a positive ranking signal, and can help you earn "Jump to" links in search results.

= What SEO ranking factors can't this plugin control? =
While this plugin handles the crucial on-page technical SEO, it's important to remember that it cannot control other major ranking factors. These include factors such as the quality and originality of your content, your website's speed, mobile-friendliness, the number of quality backlinks pointing to your site, and your overall domain authority. 

Search Appearance Toolkit (SEO 44) contains effective features for optimizing your content, providing the foundation for a comprehensive SEO strategy. SEO 44 tools, like the Jump Links Block and the scanner for FAQs and How-Tos, reward the creation of thoughtful and organized content. 

= How do I migrate from another SEO plugin? =
SEO 44 makes migration as simple as possible. Your previous SEO plugin saved the SEO data you created in your WordPress database. SEO 44 will find and use that data. 

Before migrating, please back up your WordPress database, including the postmeta table. Deactivate your previous SEO plugin while using SEO 44.

Go to the **Migration** tab of the SEO 44 settings page. There you will see three keys where SEO 44 will store and update your SEO titles, descriptions, and keywords. By default, these keys will be seo44_title, seo44_description, and seo44_keywords, but they can be updated to match the locations where your website already manages SEO information.

**One Click Migration**: The Migration tab includes a list of buttons with the names of popular SEO plugins. With one click, you can replace the seo44 keys with the standard meta keys for that other plugin.

Your old data won't be copied or duplicated; you will simply pick up right where you left off with your previous SEO plugin.

**Two-Click Migration**: If you'd like to verify which meta keys appear in your database, click the button to **Scan for SEO Meta Keys**

The scanner will display a list of potential matching meta keys and provide suggestions for matching title, description, and keyword keys. Click **Use Selected Keys and Save** to accept the suggestions, telling SEO 44 to use your existing meta keys.

You should now see your previous SEO titles and descriptions when you look at the SEO 44 metabox in your post editor.

= Does this plugin work with the block editor (Gutenberg)? =
Yes, the SEO 44 metabox appears and functions correctly with both the block editor and the classic editor. The advanced schema detection for FAQs and How-Tos works best with the block editor but has a fallback for the classic editor. The SEO 44 Jump Links Block is a custom block for use in the block editor.

= Which social media tags does this plugin use? =
SEO 44 helps your content look great when shared on social media platforms. You can enable the automatic generation of **Open Graph** (og:) tags, which Facebook, LinkedIn, and Pinterest use, and **Twitter Card** meta tags for when your content appears on X (formerly Twitter). This ensures your posts have the correct title, description, and preview image when shared.

= How are social media images handled? =
The *featured image* of any post or page will automatically be used as the primary og:image and twitter:image tag. 

If you need a site-wide default image for pages that don't have a featured image, you can set a *Default Social Image* on the plugin's "Social Media" settings tab. This image will be used as a fallback social media image when a featured image has not been set.

= How can I see my webpage's schema? =
Your page's schema is added as a JSON-LD script in the <head> section of your page's HTML. To see it, go to a page on your website, right-click, and select "View Page Source." Search for the text application/ld+json to find the schema block. You can then copy the code and test it using a tool like the [Schema Markup Validator](https://validator.schema.org/).

= How does the plugin handle author schema? =
SEO 44 creates a Person schema for the author of each article. To enhance this, it will automatically use the "Website" URL from the author's WordPress user profile to add url and @id attributes to the schema. This creates a powerful, machine-readable link between your content and its author, which helps search engines like Google build a stronger understanding of your site's authority and expertise (E-E-A-T).

= What are the benefits of using FAQPage and HowTo schema? =
The plugin intelligently scans your content for patterns that match question-and-answer formats or step-by-step instructions (when this option is enabled). The plugin locates this content and automatically generates FAQPage or HowTo schema that presents this content within the JSON-LD.

The benefit of this is that Google can use this structured data to display your content in special, highly visible formats in the search results. An FAQ page might appear as a rich snippet with expandable questions, while a How-To article can be featured in a step-by-step guide. Rich snippets make your search results stand out, which can significantly improve your click-through rate (CTR).

= Will this plugin create duplicate schema if my theme already adds them? =
No. The Schema Scanner will detect if your theme or another plugin is already outputting JSON-LD or Microdata. If it finds existing schema, it will notify you.  

== How do I install Google Tag Manager (GTM)? ==
The Search Appearance Toolkit makes this easy. You do **not** need to copy the large code snippets from Google.
	1	In your Google Tag Manager account, find and copy your **Container** ID (it looks like `GTM-XXXXXXX`).
	2	In your WordPress dashboard, go to **Settings > SEO 44** and click the **"Integrations"** tab.
	3	Paste your Container ID into the **"Google Tag Manager ID"** field. Your plugin will even help sanitize the format for you.
	4	Check the **"Enable Google Tag Manager"** box.
	5	Click **"Save Settings."**

That's it. The plugin will now automatically add both the required `<head>` script and `<body>` `<noscript>` tag to your entire site.

**New to Google Tag Manager?** When you create a new site in Google Tag Manager, you will receive instructions to (1) paste one block of code as high in the `<head>` of the page as possible and (2) paste another block of code immediately after the opening `<body>` tag. **You do not need to follow these instructions to add code manually.** The plugin handles this assignment for you. All you need to do is copy the GTM Container ID and paste it into the field in the integrations tab.  The plugin will do the rest!

== How do I set up Google Tag Manager (GTM) tracking for events (the easy way)? ==

To save you time and eliminate errors, your plugin provides a "GTM Import File." This recipe contains all the tags, triggers, and variables needed for GTM to listen for your plugin's custom events (like `jump_link_click` and `scroll_depth`) and send them to Google Analytics.

This setup is a two-part process. First, you'll configure GTM, then you'll configure Google Analytics.

**Part 1: Configure Google Tag Manager (GTM)**

1.  **In your WordPress admin,** go to **Settings > SEO 44 > Integrations**.
2.  Click the **"Download GTM Import File"** button to save the `seo44-gtm-recipe-importer.json` file. The download button is located below the field where you enter your Google Tag Manager ID.
3.  **In Google Tab Manager,** Open the GTM container for your website and go to **Admin > Import Container**.
4.  **Choose container file:** Upload the `seo44-gtm-recipe-importer.json` file.
5.  **Choose workspace:** Select your existing workspace.
6.  **Choose an import option (CRITICAL):**
    * Select the **"Merge"** option.
    * **NEVER** select "Overwrite," as this will delete all of your existing GTM tags.
7.  **Confirm Preview:** GTM will show you a preview of all the new tags, triggers, and variables. Click **Confirm.**
8.  Go to **Variables** and click on the **"GA4 - Measurement ID"** variable. 
9.  Replace the **`PASTE-YOUR-GA4-MEASUREMENT-ID-HERE`** placeholder with your own GA4 Measurement ID (e.g., `G-XXXXXXX`).
10. Finally, **Submit** and **Publish** your container.

**Part 2: Configure Google Analytics (GA4)**

With GTM now configured using the `seo44-gtm-recipe-importer.json` file, you must next tell Google Analytics to "listen for" and "display" the custom data.

Important: You must manually register these new Custom Dimensions in GA4. If you skip this step, you will only see the count of the events, not the valuable data (like which link was clicked).

1.  Go to the **Admin** area in Google Analytics (by clicking on the gear icon in the bottom left).
2.  In the "Property" column, find **Data display > Custom definitions**.
3.  Click the blue **"Create custom dimensions"** button.
4.  You will need to create **four** new dimensions, one by one.  Use the exact "Event parameter" names listed below.

**Dimension 1:**

* **Dimension name:** `click_text`
* **Scope:** `Event`
* **Event parameter:** `click_text`
* Click **Save.**
	
**Dimension 2:**

* **Dimension name:** `click_anchor`
* **Scope:** `Event`
* **Event parameter:** `click_anchor`
* Click **Save.**
	
**Dimension 3:**

* **Dimension name:** `scroll_percentage`
* **Scope:** `Event`
* **Event parameter:** `scroll_percentage`
* Click **Save.**
	
**Dimension 4:**

* **Dimension name:** `outbound_url`
* **Scope:** `Event`
* **Event parameter:** `outbound_url`
* Click **Save.**

After completing these steps (and waiting 24-48 hours for Google Analytics to process the data), you will be able to see all your new events and their associated data (like which links were clicked and how far users scrolled) in your main Events report.

== How do I add my Google Search Console verification code? ==

This plugin uses the "HTML tag" verification method, which is the most common and reliable way. You only need to copy the code, not the full tag.

1.  Sign in to [Google Search Console](https://search.google.com/search-console).
2.  Add your website as a "Property" if you haven't already.
3.  In the verification settings for your property, find and select the **"HTML tag"** method.
4.  Google will show you a full meta tag, like this:
    `
    <meta name="google-site-verification" content="YOUR_UNIQUE_CODE_GOES_HERE" />
    `
5.  Copy **only the code** inside the `content="..."` attribute.
6.  In your WordPress admin, go to **Settings > SEO 44 > Integrations**.
7.  Paste your code into the **"Google Search Console"** field.
8.  Click **"Save Settings"** at the bottom of the page.
9.  Go back to the Google Search Console page and click the **"Verify"** button. Google will now be able to see the tag on your site.

== How do I add my Bing Webmaster Tools verification code? ==

Similar to Google, Bing uses an "HTML meta tag" to verify your site. You only need to copy the specific code from the tag.

1.  Sign in to [Bing Webmaster Tools](https://www.bing.com/webmasters/about).
2.  Add your site. When prompted for a verification method, select **"HTML meta tag"**.
3.  Bing will provide a tag that looks like this:
    `
    <meta name="msvalidate.01" content="YOUR_UNIQUE_CODE_GOES_HERE" />
    `
4.  Copy **only the code** inside the `content="..."` attribute.
5.  In your WordPress admin, go to **Settings > SEO 44 > Integrations**.
6.  Paste your code into the **"Bing Webmaster Tools"** field.
7.  Click **"Save Settings"** at the bottom of the page.
8.  Go back to the Bing Webmaster Tools page and click the **"Verify"** button.


= Why can't I see the images in my sitemap? =
You can! The XML sitemap is a code file meant for search engines, so browsers don't display images directly. To verify that your images are included, go to your sitemap (e.g., yourwebsite.com/sitemap.xml), right-click, and select "View Page Source." You will see the image URLs listed within <image:image> tags for each relevant post or page.

= My sitemap shows a "404 Not Found" error. How do I fix it? =
This can occur when WordPress has not yet recognized the new sitemap URL. To fix it, go to **Settings -> Permalinks** in your WordPress admin and click the "Save Changes" button. This will refresh your site's rewrite rules and make the sitemap visible. The sitemap generator also automatically disables the default WordPress sitemap. 

= Does SEO 44 offer template tags for theme developers? =
Yes. For advanced theme development, SEO 44 provides four template tags that allow you to place the SEO and schema output in custom locations in your theme files.

* **`get_the_seo44_tags()`:** This function **returns** the complete block of HTML meta tags as a PHP string.
* **`the_seo44_tags()`:**  This function **prints** (echoes) the complete block of HTML meta tags, wrapped in wp_kses to ensure security.
* **`get_the_seo44_schema()`:** This function **returns** the JSON-LD schema script tag as a PHP string.
* **`the_seo44_schema()`:** This function **prints** (echoes) the JSON-LD schema script tag.

= How is the "Search Appearance Toolkit (SEO 44)" plugin different from the standalone "Jump Links Block (SEO 44)" plugin? =
The main **Search Appearance Toolkit (SEO 44)** plugin is a complete suite of tools that includes meta tag optimization, schema generation, XML sitemaps, *and* the Jump Links Block. The standalone **Jump Links Block (SEO 44)** plugin offers *only* the Jump Links Block functionality for users who don't need a full SEO suite. You only need one. If you are using the Search Appearance Toolkit (SEO 44) plugin, you do not need the standalone block plugin.

= How can I save and reuse my custom styles for the Jump Links Block? =
You can save a fully customized Jump Links block as a Block Pattern to easily reuse it across your site. This is a handy WordPress feature. With Block Patterns, you don't have to repeat the process of manually setting the same style settings for Jump Links blocks on multiple pages. That work is done for you.

**Here’s how:**

1.  **Style Your Block:** Add the SEO 44 Jump Links block to a page and style it exactly how you want using the settings in the sidebar (colors, layout, border radius, etc.).

2.  **Open the Options Menu:** With the block selected, click the three-dot (⋮) options menu on the block's toolbar.

3.  **Create the Pattern:** From the dropdown menu, select Create pattern. A pop-up window will appear.

4.  **Name and Configure:** Give your pattern a name (e.g., "Super Stylish Jump Links"). You will see a toggle for **Synced**. This is an important choice:

    * **Avoid Synced (ON):** Don't use this setting, which will prevent you from accessing the Jump Link Block's customization features, like being able to re-order or rename the jump links.

    * **Use Synced (OFF):** Turn synced off before saving your custom pattern. This will create a regular pattern that, when added to other posts, renders a Jump Links Block with all your favorite styles preloaded. This independent copy of your original styled block, created through your new pattern, has full customization controls. Synced Off is the way to go!

5.  **Click Create:** Your custom pattern is now saved!

6.  **Reuse Your Pattern:** To use it on another page, click the main block inserter (+), go to the Patterns tab, and select the "My patterns" category. You will see your saved design, ready to be inserted with one click.

== For Developers ==

Search Appearance Toolkit (SEO 44) is extensible and allows for the addition of custom schema types (like LocalBusiness or Product) via a WordPress filter hook.

= Adding Custom Schema with the 'seo44_add_schema_parts' Filter =

The `seo44_add_schema_parts` filter allows for the injection of additional schema arrays into the final @graph output.

Example Usage:
The following example shows how you could create a small, separate plugin to add LocalBusiness schema to a specific "Contact Us" page.

    `
    <?php
        /**
        * Hooks into the 'seo44_add_schema_parts' filter to add custom schema.
        *
        * @param array $schema_parts The existing array of schema parts from SEO 44.
        * @param int   $post_id The ID of the current post being viewed.
        * @return array The modified array of schema parts.
        */
        function my_custom_add_local_business_schema( $schema_parts, $post_id ) {
            // The ID of the "Contact Us" page.
            $contact_page_id = 123;
            // Only add this schema on our specific contact page.
            if ( $post_id == $contact_page_id ) {
                $local_business_schema = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'LocalBusiness',
                    'name'        => 'SEO 44 Global Headquarters',
                    'address'     => '123 Main St, Anytown, USA',
                    'telephone'   => '555-555-1234'
                ];
                // Add our new schema to the array.
                $schema_parts[] = $local_business_schema;
            }
            // Always return the array.
            return $schema_parts;
        }
        add_filter( 'seo44_add_schema_parts', 'my_custom_add_local_business_schema', 10, 2 );
    ?>
    `

== Credits ==

**WordPress.org:** The default placeholder favicon used in the "Search Results Snippet Preview" is based on the official WordPress logo. The image file is included locally within the plugin to comply with repository guidelines. 

== External Services ==

Search Appearance Toolkit (SEO 44) utilizes a few external, third-party services in order to provide its full range of features. All of these features are optional.

== Google Tag Manager (GTM) Integration ==

* **Service Description:** This plugin can automatically inject the Google Tag Manager (GTM) container script into your website's `<head>` and `<body>`. This allows you to manage all your third-party tracking scripts (like Google Analytics) from a single GTM dashboard.
* **Data Sent and Conditions:** This feature is **off by default**. If you enable the "Enable Google Tag Manager" option in the Integrations tab and provide your GTM Container ID (e.g., `GTM-XXXXXXX`), the plugin will add Google's official GTM script to every page of your site. This script will then download and run tracking code from Google's servers.
* **Service Provider Links:**
    * **Google:** [Terms of Service](https://policies.google.com/terms), [Privacy Policy](https://policies.google.com/privacy)

== Site Verification Tags (Google & Bing) ==

* **Service Description:** To prove you own your site to search engines, this plugin allows you to add their required verification meta tags to your site's `<head>`.
* **Data Sent and Conditions:** This feature is **off by default**. It is only active if you paste a verification code into the "Google Search Console" or "Bing Webmaster Tools" fields in the Integrations tab. The plugin does not actively "send" this data; it simply adds a `<meta>` tag to your site's HTML, which Google's and Bing's crawlers will look for to verify your ownership.
* **Service Provider Links:**
    * **Google:** [Terms of Service](https://policies.google.com/terms), [Privacy Policy](https://policies.google.com/privacy)
    * **Bing (Microsoft):** [Microsoft Services Agreement](https://www.microsoft.com/en-us/servicesagreement/), [Microsoft Privacy Statement](https://privacy.microsoft.com/en-us/privacystatement)

== Sitemap Ping Services (Google & Bing) ==

* **Service Description:** To help your content get indexed faster, this plugin can automatically notify (or "ping") major search engines when your XML sitemap is updated. This feature uses the public ping services provided by Google and Bing.
* **Data Sent and Conditions:** This feature is **on by default** and can be disabled from the "XML Sitemaps" settings tab. If enabled, the plugin will send the URL of your website's sitemap (e.g., `https://yourwebsite.com/sitemap.xml`) to Google and Bing. This happens automatically only when you publish a new post. No other data is sent.
* **Service Provider Links:**
    * **Google:** [Terms of Service](https://policies.google.com/terms), [Privacy Policy](https://policies.google.com/privacy)
    * **Bing (Microsoft):** [Microsoft Services Agreement](https://www.microsoft.com/en-us/servicesagreement/), [Microsoft Privacy Statement](https://privacy.microsoft.com/en-us/privacystatement)

== Screenshots ==

1. The clean and simple SEO metabox in the post editor, with the live snippet preview.
2. The Main Settings tab, for controlling meta tags and homepage SEO. 
3. The Social Media settings tab.
4. The Schema Structured Data tab.
5. The intelligent Schema Scanner, showing a clean result and a preview, on the Schema Structured Data tab. 
6. The comprehensive XML Sitemaps tab.
7. The Migration settings tab.
8. The SEO Meta Keys Scanner, showing the scan on a website with no previous SEO keys,  on the Migration Settings tab. 
9. A category edit screen showing the new SEO fields.
10. Published Vertical Jump Links, expanded and collapsed.
11. Published Horizontal Jump Links, expanded and collapsed.
12. The Jump Links Block in Viewing Mode.
13. The Jump Links Block in Editing Mode.
14. The Sidebar controls for the Jump Links Block.
15. The Integrations tab with Google Tag Manager and Site Tool settings.

== Changelog ==

= 4.0.0 =
* FEATURE: Added a new "Integrations" tab for third-party services like Google Tag Manager and Webmaster Tools.
* FEATURE: Added Google Tag Manager (GTM) integration. The plugin can now automatically inject the GTM container script into the site's <head> and <body> based on your ID.
* FEATURE: Added Webmaster Verification. You can now add your Google Search Console and Bing Webmaster Tools verification codes directly from the plugin settings.
* FEATURE: Added automatic GTM event tracking. When enabled, the plugin can push the following events to the dataLayer:
    * Rich SEO dataLayer: Pushes page type, category, author, and tags on page load for advanced GTM triggers.
    * Scroll Depth Tracking: Pushes 'scroll_depth' events at 25%, 50%, 75%, and 100% of the page.
    * External & Affiliate Clicks:* Pushes 'external_link_click' or 'affiliate_link_click' (for `rel="sponsored"` links).
    * Jump Link Clicks: Pushes a 'jump_link_click' event when a user clicks a link in the Jump Links Block.
* ENHANCEMENT: Centralized all GTM event tracking into a new, efficient `global-tracker.js` file that uses event delegation for better performance.
* TWEAK: Improved the "Integrations" settings page UI for clarity, adding clarifying tooltips, and a file downloader.
* FEATURE: Added A Google Tag Manager Recipe Import file and instructions to handle click events in GTM and GA.

= 3.9.7 =
* ENHANCEMENT (Jump Links Block): Implemented a robust de-duping engine to prevent invalid HTML from duplicate headings. The block now automatically appends a number (e.g., `my-heading-2`) to any heading with a conflicting text or manual anchor ID.
* FEATURE (Jump Links Block): Added a snackbar warning that notifies the user when duplicate headings have been found and auto-corrected, prompting them to review their content for clarity.
* FIX (Jump Links Block): Ensured that all user customizations (custom link text, visibility, and ordering) are correctly preserved during the new de-duping and reconciliation process.

= 3.9.6 =
* ENHANCEMENT (Jump Links Block): Improved semantics and accessibility by wrapping Jump Links Block content in a `<nav>` landmark with a translatable aria-label.
* ENHANCEMENT (Jump Links Block): Added full ARIA support to the Jump Links Block's collapsible button, including aria-expanded, aria-controls, and a dynamic "Show More" / "Show Less" label.

= 3.9.5 =
* FIX: To eliminate user confusion and create a distinctive name and slug for the WordPress Plugin directory, the plugin has been renamed "Search Appearance Toolkit (SEO 44)" and given the slug, search-appearance-toolkit-seo-44.
* FIX: The text domain has been updated to match the new slug across all PHP files and custom block files for translation.
* FIX: Repaired the settings page tabs to correctly use the new plugin slug, resolving "Sorry, you are not allowed to access this page" errors when switching tabs.

= 3.9.4 =
* FEATURE (Jump Links Block): Added a new "Background Hover" color setting in the Inspector Controls for links in the horizontal layout.
* ENHANCEMENT (Jump Links Block): Improved the out-of-the-box appearance by setting default colors and a default border-radius in block.json, ensuring the block looks great immediately upon being added.
* REFACTOR (Jump Links Block): Removed hardcoded default styles from the stylesheets, making the block's settings the single source of truth for its design and improving consistency.
* CODE QUALITY (Jump Links Block): Refactored editor.scss to import style.scss, eliminating code duplication and making the styles easier to maintain.
* CODE QUALITY (Jump Links Block): Performed a comprehensive review and refactor of all block source files (.js, .scss, .json). This improves the block's stability, maintainability, and alignment with modern WordPress development best practices.

= 3.9.3 =
FIX: Implemented automatic rewrite rule flushing for XML sitemaps. Sitemap URLs now work immediately after plugin activation or when toggling the sitemap feature on/off, eliminating sitemap 404 errors and the need to manually save permalink settings.

= 3.9.2 =
* REFACTOR: Completely refactored the XML Sitemap generation logic to comply with WordPress.org's "late escaping" security requirements. Sitemaps are now built by echoing escaped data line-by-line instead of using an output buffer.
* FIX: Resolved unescaped variable output in the sitemap files.
* SECURITY: Enhanced sitemap security by ensuring every dynamic URL and date is individually escaped with `esc_url()` or `esc_xml()` at the moment of output.
* ENHANCEMENT: Improved sitemap caching to store structured data arrays instead of raw XML, leading to more efficient generation.
* FIX: Completed nonce verification to prevent CSRF attacks on tab switching.
* CHANGE: The default favicon, previously loaded from s.w.org, is now included locally within the plugin's 'images' folder to remove external dependencies.
* CHANGE (Readme): Removed the "WordPress.org Favicon Service" from the External Services section and added a "Credits" section to properly attribute the locally-hosted favicon.

= 3.9.1 =
* ENHANCEMENT: The Article schema now generates a more robust author profile. If an author has a website URL in their WordPress user profile, the schema will now include url and @id properties, creating a stronger connection between the content and the author's identity for search engines.
* FIX: Direct $_GET access in settings page navigation
* ENHANCEMENT: Improved input validation and escaping in admin forms  
* SECURITY: Added proper capability checks for metabox saving
* SECURITY: Better CSRF protection and data sanitization

= 3.9.0 =
* FIX: Unescaped JSON-LD output in schema generation following WordPress security standards
* FIX: Missing variable declaration in media parsing functionality
* ENHANCEMENT: Added capability checks for taxonomy meta field saving
* ENHANCEMENT: Better input validation for query parameters with proper sanitization
* SECURITY: Strengthened nonce verification with user permission checks
* CODE QUALITY: Improved phpcs compliance with proper security annotations

= 3.8.9 =
* FIX: SQL injection vulnerability in meta key scanner
* SECURITY: Enhanced SQL injection protection with proper prepared statements
* ENHANCEMENT: Explicit placeholder usage, removing the intermediate variable
* REFACTORED: Simplified SQL query construction for better maintainability
* FIX: WordPress coding standards compliance for database queries
* FIX: Unescaped variable output in sitemap generation
* SECURITY: Implemented proper wpdb::prepare() usage for sitemap transients
* ENHANCEMENT: Improved database query security following WordPress guidelines

= 3.8.8 =
* FIX (Readme): Replaced the broken Terms of Service link in the External Services section for the WordPress.org Favicon Service with a link to the wordpress.org About page.

= 3.8.7  =
* ENHANCEMENT: The Homepage SEO fields on the settings page now intelligently use the Site Title and Tagline as default fallbacks for new installations.
* FIX: Corrected an issue on the settings page where the Homepage SEO snippet preview would display raw PHP code if the fields were empty. The preview now correctly shows the fallback site title and tagline.
* FIX: Corrected a special character issue when retrieving the site title and tagline for Homepage SEO.
* ENHANCEMENT: Excluded Jump Links Block meta key from the SEO Meta Keys Scanner for a cleaner and more focused output.  

= 3.8.6  =
* ENHANCEMENT: Added a new option in the XML Sitemaps settings to exclude specific posts or pages by entering a comma-separated list of IDs.
* ENHANCEMENT (for Developers): Introduced the seo44_add_schema_parts filter, allowing developers to programmatically add custom schema types via add-on plugins.
* SECURITY: Hardened the database query in the Migration Tools' meta key scanner against SQL injection, per WordPress.org guidelines.
* SECURITY: Added proper escaping and validation to the XML sitemap and JSON-LD schema output to ensure all front-end data is secure.
* FIX: Replaced the previous conflict resolution system (which deactivated other plugins) with a new, guideline-compliant method. The main plugin now detects if the standalone Jump Links Block plugin is active and avoids loading its own version to prevent conflicts.
* FIX: Improved the code readability of the JavaScript file for the settings page (settings-script.js) to comply with WordPress.org's "human-readable" code guidelines.
* FIX (Readme): Added a required "External Services" section to the readme.txt file, documenting the use of Google and Bing's sitemap ping services and the WordPress.org favicon service.

= 3.8.5  =
* ENHANCEMENT: Improved the Homepage SEO section in the settings tab to include a live Search Results Snippet Preview and character counters, as featured in the post editor metabox.
* FEATURE: Added a conflict resolution system. The main SEO 44 plugin now automatically detects and deactivates a standalone "Jump Links Block" plugin to prevent fatal errors and ensure seamless integration for users.

= 3.8.0  =
* FEATURE: Added the "Jump Links Block." This advanced block automatically generates a customizable Table of Contents with a full suite of editing and styling controls.
* ENHANCEMENT: Updated all plugin code to a modern, object-oriented structure for better performance and maintainability.

= 3.3.0 =
* FEATURE: Added `BreadcrumbList` schema to all singular posts and pages for enhanced search results.
* ENHANCEMENT: Implemented fallback for post/page meta descriptions using the post excerpt or content.
* ENHANCEMENT: Implemented smart title truncation for post/page/taxonomy title tags to fit within Google's display limits.

= 3.2.0 =
* FEATURE: Added intelligent detection for `HowTo` schema in post content.
* FEATURE: Added the ability to exclude images from sitemaps and schema by adding a `seo44-ignore` CSS class.
* ENHANCEMENT: Added a "Use Example" button to the metabox to apply the suggested SEO title easily.
* FIX: Excluded 1x1 tracking pixels from sitemaps.

= 3.1.0 =
* FEATURE: Added intelligent detection for `FAQPage` schema in post content and combined it with the base schema using `@graph`.
* FEATURE: Added a fallback parser for the schema scanner to support the Classic Editor and page builders.

= 3.0.0 =
* FEATURE: Added an advanced content scanner to find all images and YouTube videos within a post/page and add them to the schema.

= 2.3.1 =
* FIX: Corrected an issue where the "Enable XML Sitemaps" and "Ping Search Engines" settings would not save correctly. 

= 2.1.0 =
* FEATURE: Added SEO fields to Category and Tag edit screens. 
* FEATURE: Meta tags and social tags are now output on homepage and taxonomy archive pages.
* FEATURE: Added Homepage SEO fields to the Main Settings tab. 
* FIX: Corrected a bug that caused an empty metabox in the post editor. 

= 2.0.0 =
* REFACTOR: Converted the entire plugin to a modern, class-based (object-oriented) structure for better performance and maintainability. 

= 1.5.0 =
* FEATURE: Added a new "Schema" tab with an intelligent scanner to detect and prevent conflicts with other SEO plugins or themes. 
* FEATURE: Added automatic generation of `Article`, `WebPage`, `WebSite`, and `BreadcrumbList` schema. 

= 1.4.0 =
* FEATURE: Moved beyond the metabox to create a tabbed plugin settings page with site-wide options.
* FEATURE: Added tabs for main settings, social media settings, and migration settings.
* FEATURE: Front-end output for SEO Social Media meta tags.

= 1.0.0 =
* FEATURE: Initial development of an SEO metabox for the post editor with fields for title, description and keywords.
* FEATURE: Character counters and length warnings for SEO titles and descriptions.

== Upgrade Notice ==

= 3.8.5 =
This update adds an important conflict-resolution feature. It will now automatically deactivate a standalone Jump Links Block plugin if it is detected.
