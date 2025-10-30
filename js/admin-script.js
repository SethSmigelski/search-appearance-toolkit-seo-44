jQuery(document).ready(function($) {

    // --- NEW: Add custom class to metabox heading ---
    // Find the metabox by its ID and then find the title element inside it.
    var metabox = $('#seo44_meta_box');
    if (metabox.length) {
        metabox.find('h2.hndle').addClass('seo44-metabox-heading');
    }

    // --- Character Counter Functionality (from v1.4) ---
    function createCounter(inputId, displayId, maxLength) {
        const inputField = document.getElementById(inputId);
        const displayElement = document.getElementById(displayId);

        if (!inputField || !displayElement) {
            return;
        }

        function updateCount() {
            const currentLength = inputField.value.length;
            displayElement.textContent = currentLength + ' / ' + maxLength;
            
            if (currentLength > maxLength) {
                displayElement.classList.add('over-limit');
            } else {
                displayElement.classList.remove('over-limit');
            }
        }
        inputField.addEventListener('keyup', updateCount);
        inputField.addEventListener('paste', updateCount);
        inputField.addEventListener('change', updateCount);
        updateCount();
    }

    createCounter('seo44_title', 'seo44_title_char_count', 60);
    createCounter('seo44_description', 'seo44_description_char_count', 160);

	// --- NEW: "Use Example" Title Button ---
    $('#seo44-use-example-title').on('click', function() {
        var exampleText = $('#seo44-title-example').text();
        var titleInput = $('#seo44_title');
        titleInput.val(exampleText);
        
        // Trigger the keyup event to update the snippet preview and character counter
        titleInput.trigger('keyup'); 
    });

    // --- Snippet Preview Functionality ---
    const titleInput = $('#seo44_title');
    const descriptionInput = $('#seo44_description');
	
    const previewHeaderUrl = $('.seo44-preview-breadcrumb-url');
    const previewTitle = $('.seo44-preview-title');
    const previewDescription = $('.seo44-preview-description');

    // Get placeholder values from localized data
    const defaultTitle = seo44_data.post_title;
    const siteName = seo44_data.site_name;
    const permalink = seo44_data.permalink; 
	
    function updatePreview() {
        // Update Title
        let titleVal = titleInput.val();
		previewTitle.text(titleVal ? titleVal : defaultTitle);
		
		
       // if (titleVal) {
       //     previewTitle.text(titleVal);
       // } else {
            // If the input is empty, show "Post Title - Site Name"
        //    previewTitle.text(defaultTitle + ' - ' + siteName);
        //}

        // Update Description
        let descVal = descriptionInput.val();
        if (descVal) {
            previewDescription.text(descVal);
        } else {
            // Show a generic placeholder if the description is empty
            previewDescription.text('Enter a meta description to see how it will appear in search results...');
        }
		
		// Update Breadcrumb URL
        let breadcrumb = permalink.replace(/^https?:\/\//, '').replace(/\/$/, '');
        breadcrumb = breadcrumb.replace(/\//g, ' &rsaquo; '); // &rsaquo; is the > symbol
        previewHeaderUrl.html(breadcrumb);
    }

    // Initial update on page load
    updatePreview();

    // Update on keyup in the input fields
    titleInput.on('keyup', updatePreview);
    descriptionInput.on('keyup', updatePreview);

});
