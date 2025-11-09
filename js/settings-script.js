jQuery(document).ready(function($) {
	// ADDED: HOMEPAGE PREVIEW CODE ---
	// 1. Character Counter Functionality (borrowed from admin-script.js)
    function createCounter(inputId, displayId, maxLength) {
        const inputField = document.getElementById(inputId);
        const displayElement = document.getElementById(displayId);
        if (!inputField || !displayElement) return;

        function updateCount() {
            const currentLength = inputField.value.length;
            displayElement.textContent = currentLength + ' / ' + maxLength;
            displayElement.classList.toggle('over-limit', currentLength > maxLength);
        }
        inputField.addEventListener('keyup', updateCount);
        updateCount(); // Initial count
    }

    createCounter('homepage_title', 'homepage_title_char_count', 60);
    createCounter('homepage_description', 'homepage_description_char_count', 160);

    // 2. Snippet Preview Functionality (adapted from admin-script.js)
    const titleInput = $('#homepage_title');
    const descriptionInput = $('#homepage_description');
    
    if (titleInput.length) { // Only run if the homepage fields are on the current tab
		const previewContainer = $('#seo44-homepage-snippet-preview');
		const previewHeaderUrl = $('.seo44-preview-breadcrumb-url');
		const previewTitle = $('.seo44-preview-title');
		const previewDescription = $('.seo44-preview-description');

        // Get placeholder values from WordPress
		const siteName = previewContainer.data('sitename');
		const siteUrl = previewContainer.data('siteurl');
		const tagline = previewContainer.data('tagline');

        function updatePreview() {
            // Update Title
            let titleVal = titleInput.val();
            previewTitle.text(titleVal ? titleVal : siteName);

            // Update Description
            let descVal = descriptionInput.val();
            previewDescription.text(descVal ? descVal : tagline);

			// Update Breadcrumb URL
			if (siteUrl) {
				let breadcrumb = siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
				previewHeaderUrl.html(breadcrumb);
			}
        }

        updatePreview(); // Initial update
        titleInput.on('keyup', updatePreview);
        descriptionInput.on('keyup', updatePreview);
    }
	// --- End Homepage SEO Functionality ---

    // --- Tab Functionality ---
    function activateTab(tabId) {
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $('a[href="?page=search-appearance-toolkit-seo-44&tab=' + tabId + '"]').addClass('nav-tab-active');
        $('.tab-content').hide();
        $('#' + tabId).show();
    }
    $('.nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        var urlParams = new URLSearchParams($(this).attr('href'));
        var targetTab = urlParams.get('tab');
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=search-appearance-toolkit-seo-44&tab=' + targetTab;
        if (history.pushState) { history.pushState({path:newUrl}, '', newUrl); }
        $('input[name="_wp_http_referer"]').val(newUrl);
        activateTab(targetTab);
    });
    var initialParams = new URLSearchParams(window.location.search);
    var initialTab = initialParams.get('tab') || 'main_settings';
    activateTab(initialTab);

    // --- Migration Preset Buttons ---
    $('.migration-presets .button').on('click', function() {
        var presets = $(this).data('presets');
        $('#title_key').val(presets.title);
        $('#description_key').val(presets.desc);
        $('#keywords_key').val(presets.keys);
    });

    // --- Meta Key Scanner with Suggestion Engine ---
    $('#seo44_scan_meta_keys_btn').on('click', function() {
        var button = $(this);
        var original_text = button.text();
        var results_container = $('#seo44_scan_results_container');
        var results_list = $('#seo44_scan_results_list');
        var suggestions_container = $('#seo44_suggested_keys_container');
        button.text(seo44_ajax_object.scanning_text).prop('disabled', true);
        results_list.empty();
        suggestions_container.hide();
        $.ajax({
            url: seo44_ajax_object.ajax_url,
            type: 'POST',
            data: { action: 'seo44_scan_meta_keys', _ajax_nonce: seo44_ajax_object.nonce },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    $.each(response.data, function(index, key) { results_list.append('<li>' + key + '</li>'); });
                    analyzeAndSuggest(response.data);
                } else {
                    results_list.append('<li>No potential SEO meta keys found.</li>');
                }
                results_container.show();
            },
            error: function() { results_list.append('<li>An error occurred. Please try again.</li>'); results_container.show(); },
            complete: function() { button.text(original_text).prop('disabled', false); }
        });
    });

    function analyzeAndSuggest(keys) {
        const suggestionsList = $('#seo44_suggestions_list');
        const suggestedContainer = $('#seo44_suggested_keys_container');
        suggestionsList.html('');
        const titleTerms = ['title', 'headline'];
        const descTerms = ['description', 'desc', 'excerpt'];
        const keywTerms = ['keywords', 'keyword', 'tags'];
        const suggestedTitle = findBestMatch(keys, titleTerms);
        const suggestedDesc = findBestMatch(keys, descTerms);
        const suggestedKeys = findBestMatch(keys, keywTerms);
        let hasSuggestions = false;
        if (suggestedTitle) { hasSuggestions = true; suggestionsList.append('<li data-type="title_key" data-key="' + suggestedTitle + '"><strong>Title:</strong> ' + suggestedTitle + '</li>'); }
        if (suggestedDesc) { hasSuggestions = true; suggestionsList.append('<li data-type="description_key" data-key="' + suggestedDesc + '"><strong>Description:</strong> ' + suggestedDesc + '</li>'); }
        if (suggestedKeys) { hasSuggestions = true; suggestionsList.append('<li data-type="keywords_key" data-key="' + suggestedKeys + '"><strong>Keywords:</strong> ' + suggestedKeys + '</li>'); }
        if (hasSuggestions) { suggestedContainer.show(); }
    }

    function findBestMatch(allKeys, searchTerms) { let bestMatch = ''; for (const key of allKeys) { if (key.includes('seo')) { for (const term of searchTerms) { if (key.includes(term)) { bestMatch = key; break; } } } if (bestMatch) break; } if (!bestMatch) { for (const key of allKeys) { for (const term of searchTerms) { if (key.includes(term)) { bestMatch = key; break; } } if (bestMatch) break; } } return bestMatch; }

    $('#seo44_use_suggested_keys_btn').on('click', function() {
        $('#seo44_suggestions_list li').each(function() {
            const type = $(this).data('type');
            const key = $(this).data('key');
            $('#' + type).val(key);
        });
    	$('#seo44_scan_results_container').slideUp();
    
    	// Pause briefly, then simulate a click on the main "Save Settings" button
    	setTimeout(function() {
        $('#submit').click(); 
    	}, 25); // 25ms pause
	});

    // --- Image Uploader with Auto-Save ---
    var mediaUploader;

    // Handle the "Upload Image" button click
    $('#default_social_image_id_upload_btn').on('click', function(e) {
        e.preventDefault();
        var base_id = 'default_social_image_id';

        // If the media frame already exists, reopen it.
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media frame.
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Default Social Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false // We only want one image
        });

        // When an image is selected, run a callback.
        mediaUploader.on('select', function() {
            // Get the selected image's data.
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Update the hidden input field with the new image ID.
            $('#' + base_id).val(attachment.id);

            // Display a preview of the selected image.
            $('#' + base_id + '_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" alt="Image preview" style="max-width:150px;"/>');

            // Show the "Remove Image" button.
            $('#' + base_id + '_remove_btn').show();

            // Auto-save the new image ID via AJAX.
            $.ajax({
                url: seo44_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'seo44_save_social_image',
                    nonce: seo44_ajax_object.nonce,
                    image_id: attachment.id
                }
            });
        });

        // Open the media frame.
        mediaUploader.open();
    });

    // Handle the "Remove Image" button click
    $('#default_social_image_id_remove_btn').on('click', function(e) {
        e.preventDefault();
        var base_id = 'default_social_image_id';

        // Clear the hidden input field.
        $('#' + base_id).val('');

        // Remove the image preview.
        $('#' + base_id + '_preview').html('');

        // Hide the "Remove Image" button.
        $(this).hide();

        // Auto-save the change (image ID = 0) via AJAX.
        $.ajax({
            url: seo44_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'seo44_save_social_image',
                nonce: seo44_ajax_object.nonce,
                image_id: 0
            }
        });
    });

    // --- Schema Scanner ---
    $('#seo44_scan_schema_btn').on('click', function() {
        var button = $(this);
        var original_text = button.text();
        var results_container = $('#seo44_schema_scan_results');

        button.text('Scanning...').prop('disabled', true);
        results_container.html('<p><em>Checking your homepage, latest post, and latest page...</em></p>').show();

        $.ajax({
            url: seo44_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'seo44_scan_site_for_schema',
                _ajax_nonce: seo44_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    var status = response.data.status;
                    var html = '';
                    button.hide(); 

                    if (status === 'jsonld_found') {
                        html += '<div class="notice notice-info inline"><p><strong>Status:</strong> We found existing JSON-LD schema on your site, likely from another plugin or your theme. SEO 44 will not add a duplicate. Here is an example we found:</p></div>';
                        if (response.data.example) {
                            html += '<pre class="schema-preview">' + $('<div/>').text(response.data.example).html() + '</pre>';
                        }
                    } 
                    else if (status === 'microdata_found') {
                        html += '<div class="notice notice-warning inline"><p><strong>Action Recommended:</strong> We found outdated Microdata schema on your site. To convert this to the modern JSON-LD format, we recommend the <a href="https://wordpress.org/plugins/microdata-to-json-ld-converter/" target="_blank" rel="noopener">Microdata to JSON-LD Converter</a> plugin. SEO 44 will not add a conflicting schema.</p></div>';
                    } 
                    else if (status === 'clean') {
                        html += '<div class="notice notice-success inline"><p><strong>Ready to go!</strong> We didn\'t find any existing schema. You can enable Article schema below.</p></div>';
                        if(response.data.post_preview) {
                            html += '<h4>Preview for your latest post:</h4>';
                            html += '<pre class="schema-preview">' + $('<div/>').text(response.data.post_preview).html() + '</pre>';
                        }
                    }
                    results_container.html(html);
                } else {
                     results_container.html('<div class="notice notice-error inline"><p>An error occurred during the scan. Please try again.</p></div>');
                     button.show();
                }
            },
            error: function() {
                results_container.html('<div class="notice notice-error inline"><p>An error occurred during the scan. Please try again.</p></div>');
                button.show();
            },
            complete: function() {
                button.text(original_text).prop('disabled', false);
            }
        });
    });

    // --- Sitemap Cache Purge Button ---
    $('#seo44_purge_sitemap_cache_btn').on('click', function() {
        var button = $(this);
        var original_text = button.text();

        button.text('Purging...').prop('disabled', true);

        $.ajax({
            url: seo44_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'seo44_purge_sitemap_cache',
                _ajax_nonce: seo44_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.text('Cache Purged!');
                } else {
                    button.text('Error!');
                }
            },
            error: function() {
                button.text('Error!');
            },
            complete: function() {
                setTimeout(function() {
                    button.text(original_text).prop('disabled', false);
                }, 2000); // Reset button text after 2 seconds
            }
        });
    });

    // --- GTM Auto-Enable Logic ---
    
    // 1. Define all the relevant elements
    const $mainGtmToggle = $('#enable_gtm_integration');
    const $gtmIdField = $('#gtm_id');
    const $subToggles = $('#enable_seo_datalayer, #enable_jump_link_tracking, #enable_external_link_tracking, #enable_scroll_depth_tracking');

    // 2. Trigger 1: When user types/pastes in the ID field
    $gtmIdField.on('keyup paste change', function() {
        // If the field has a value (and it's longer than just "GTM-")
        if ($(this).val().length > 4) {
             $mainGtmToggle.prop('checked', true);
        }
    });

    // 3. Trigger 2: When user clicks a sub-toggle
    $subToggles.on('change', function() {
        // If *any* of the sub-toggles are now checked
        if ($subToggles.is(':checked')) {
            $mainGtmToggle.prop('checked', true);
        }
    });
    // --- End GTM Logic ---
});
